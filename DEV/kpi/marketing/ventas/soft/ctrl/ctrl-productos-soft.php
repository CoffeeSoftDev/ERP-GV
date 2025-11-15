<?php

if (empty($_POST['opc'])) exit(0);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

require_once '../mdl/mdl-productos-soft.php';
require_once '../../../../../conf/coffeSoft.php';

class ctrl extends mdl {

    function init() {
        return [
            'udn' => $this->lsUDN(),
            'grupos' => $this->lsGrupos()
        ];
    }

    function lsProductos() {
        $__row = [];
        
        $udn = $_POST['udn'] ?? 'all';
        $grupo = $_POST['grupo'] ?? 'all';
        $anio = $_POST['anio'] ?? '';
        $mes = $_POST['mes'] ?? '';

        $params = [];
        if ($udn !== 'all') {
            $params['udn'] = $udn;
        }
        if ($grupo !== 'all') {
            $params['grupo'] = $grupo;
        }
        if (!empty($anio)) {
            $params['anio'] = $anio;
        }
        if (!empty($mes)) {
            $params['mes'] = $mes;
        }

        $ls = $this->listProductos($params);

        foreach ($ls as $key) {
            $__row[] = [
                'id' => $key['id_Producto'],
                // 'Clave' => htmlspecialchars($key['clave_producto'] ?? '', ENT_QUOTES, 'UTF-8'),
                'Descripción' => htmlspecialchars($key['descripcion'] ?? '', ENT_QUOTES, 'UTF-8'),
                'Grupo' => htmlspecialchars($key['grupo_nombre'] ?? 'Sin grupo', ENT_QUOTES, 'UTF-8'),
                'UDN' => htmlspecialchars($key['udn_nombre'] ?? '', ENT_QUOTES, 'UTF-8'),
                'Cantidad' => [
                    'html' => number_format($key['cantidad_vendida'] ?? 0, 0, '.', ','),
                    'class' => 'text-center font-semibold'
                ],
                'Costo' => [
                    'html' => evaluar($key['costo'] ?? 0),
                    'class' => 'text-end'
                ],
                'Precio Venta' => [
                    'html' => evaluar($key['precio_venta'] ?? 0),
                    'class' => 'text-end'
                ],
                'Precio Licencia' => [
                    'html' => evaluar($key['precio_licencia'] ?? 0),
                    'class' => 'text-end'
                ]
            ];
        }

        return [
            'row' => $__row,
            'ls' => $ls
        ];
    }

    function lsConcentrado() {
        $__row = [];
        $thead = [];
        
        $udn = $_POST['udn'] ?? 'all';
        $grupo = $_POST['grupo'] ?? 'all';
        $anio = intval($_POST['anio'] ?? date('Y'));
        $periodo = intval($_POST['periodo'] ?? 6);

        $params = [];
        if ($udn !== 'all') {
            $params['udn'] = $udn;
        }
        if ($grupo !== 'all') {
            $params['grupo'] = $grupo;
        }
        $params['anio'] = $anio;
        $params['periodo'] = $periodo;

        $ls = $this->listConcentrado($params);

        $mesesNombres = ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'];
        $mesActual = intval(date('n'));
        
        $thead = ['CLAVE', 'NOMBRE'];
        $mesesData = [];
        
        for ($i = 0; $i < $periodo; $i++) {
            $mesIndex = $mesActual - $i - 1;
            $anioColumna = $anio;
            
            if ($mesIndex < 0) {
                $mesIndex += 12;
                $anioColumna--;
            }
            
            $nombreMes = $mesesNombres[$mesIndex];
            $thead[] = strtoupper($nombreMes) . '/' . $anioColumna;
            $mesesData[] = ['mes' => $mesIndex + 1, 'anio' => $anioColumna];
        }

        $grupoActual = '';
        foreach ($ls as $key) {
            if ($grupoActual !== $key['grupo_nombre']) {
                $grupoActual = $key['grupo_nombre'];
                $__row[] = [
                    'id'    => 0,
                    'Grupo' => [
                        'html' => strtoupper($grupoActual),
                        'class' => 'font-bold bg-gray-300 text-left px-2',
                        'colspan' => $periodo + 2
                    ],
                    'col_group' => true,
                ];
            }

            $rowData = [
                'id' => $key['id_Producto'],
                'CLAVE' => htmlspecialchars($key['clave_producto'] ?? '', ENT_QUOTES, 'UTF-8'),
                'NOMBRE' => htmlspecialchars($key['descripcion'] ?? '', ENT_QUOTES, 'UTF-8')
            ];

            for ($i = 0; $i < $periodo; $i++) {
                $cantidad = $key['mes_' . $i] ?? 0;
                $bgColor = ($i % 2 == 0) ? 'bg-green-100' : 'bg-pink-100';
                
                $rowData[$thead[$i + 2]] = [
                    'html' => $cantidad > 0 ? number_format($cantidad, 0, '.', ',') : '-',
                    'class' => 'text-center font-semibold ' . ($cantidad > 0 ? $bgColor : '')
                ];
            }

            $__row[] = $rowData;
        }

        return [
            'row' => $__row,
            'thead' => $thead,
            'ls' => $ls
        ];
    }

    function getProducto() {
        $id = isset($_POST['id']) ? $_POST['id'] : null;
        
        if (!$id || !is_numeric($id) || $id < 0) {
            return [
                'status' => 400,
                'message' => 'ID de producto inválido',
                'data' => null
            ];
        }

        try {
            $producto = $this->getProductoById($id);
            
            if ($producto) {
                return [
                    'status' => 200,
                    'message' => 'Producto obtenido correctamente',
                    'data' => $producto
                ];
            } else {
                return [
                    'status' => 404,
                    'message' => 'Producto no encontrado',
                    'data' => null
                ];
            }
        } catch (Exception $e) {
            $this->writeToLog("Error en getProducto: " . $e->getMessage());
            
            return [
                'status' => 500,
                'message' => 'Error al obtener el producto',
                'data' => null
            ];
        }
    }

}


$obj = new ctrl();
echo json_encode($obj->{$_POST['opc']}());
