<?php

if (empty($_POST['opc'])) exit(0);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

require_once '../mdl/mdl-productos-soft.php';
require_once '../../../../../conf/coffeSoft.php';

class ctrl extends mdlProductosSoft {

    function init() {
        return [
            'udn' => $this->lsUDN(),
            'grupos' => $this->lsGrupos()
        ];
    }

    function getGruposByUdn() {
        $udn = $_POST['udn'] ?? 'all';
        
        $params = [];
        if ($udn !== 'all') {
            $params['udn'] = $udn;
        }

        $grupos = $this->lsGrupos($params);

        return [
            'status' => 200,
            'grupos' => $grupos,
            'debug' => [
                'udn_recibida' => $udn,
                'params' => $params,
                'total_grupos' => count($grupos)
            ]
        ];
    }

    function lsGroups() {
        $__row = [];
        
        $udn = $_POST['udn'] ?? 'all';
        
        if ($udn !== 'all' && !is_numeric($udn)) {
            return [
                'status' => 400,
                'message' => 'UDN inválida',
                'grupos' => []
            ];
        }
        
        $params = [];
        if ($udn !== 'all') {
            $params['udn'] = $udn;
        }
        
        $ls = $this->listGrupos($params);
        
        foreach ($ls as $key) {
            $__row[] = [
                'id' => $key['id'],
                'valor' => $key['grupoproductos'],
                'cantidad_productos' => intval($key['cantidad_productos'])
            ];
        }
        
        return [
            'status' => 200,
            'grupos' => $__row,
            'total' => count($__row)
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

            $nombre_costsys = $this->select_homologar([$key['id']]);
            $totalEnlaces   = count($nombre_costsys);
            $enlaces        = [];

            foreach ($nombre_costsys as $costsys) {
                $enlaces[] = $costsys['idhomologado'];
            }

            $linkHtml = renderEstadoHomologacion($key['id'], $enlaces, $totalEnlaces);

            $__row[] = [
                'id' => $key['id'],
                'Descripción' => htmlspecialchars($key['descripcion'] ?? '', ENT_QUOTES, 'UTF-8'),
                'Grupo' => htmlspecialchars($key['grupoproductos'] ?? 'Sin grupo', ENT_QUOTES, 'UTF-8'),

                'Homologación' => [
                    'html'  => $linkHtml,
                    'class' => 'text-center '
                ],
                'Cantidad' => [
                    'html' => number_format($key['cantidad_vendida'] ?? 0, 0, '.', ','),
                    'class' => 'text-center font-semibold '
                ],
                'Costo' => [
                    'html' => evaluar($key['costo'] ?? 0),
                    'class' => 'text-end '
                ],
                'Precio Venta' => [
                    'html' => evaluar($key['precio_venta'] ?? 0),
                    'class' => 'text-end '
                ],
                'cantidad_vendida' => [
                    'html' => evaluar($key['cantidad_vendida'] ?? 0),
                    'class' => 'text-end '
                ]
            ];
        }

        return [
            'row' => $__row,
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

    function getGruposConHomologacion() {
        $udn = $_POST['udn'] ?? 'all';
        
        $params = [];
        if ($udn !== 'all') {
            $params['udn'] = $udn;
        }

        $grupos = $this->lsGrupos($params);
        $gruposConEstadisticas = [];

        foreach ($grupos as $grupo) {
            $estadisticas = $this->getEstadisticasHomologacionGrupo($grupo['id'], $udn);
            
            $gruposConEstadisticas[] = [
                'idgrupo' => $grupo['id'],
                'grupoproductos' => $grupo['valor'],
                'total_productos' => $estadisticas['total'],
                'productos_homologados' => $estadisticas['homologados'],
                'productos_sin_homologar' => $estadisticas['sin_homologar']
            ];
        }

        return [
            'status' => 200,
            'grupos' => $gruposConEstadisticas
        ];
    }

    function getProductosByGrupo() {
        $__row = [];
        $grupo = $_POST['grupo'] ?? null;
        $udn = $_POST['udn'] ?? 'all';

        if (!$grupo) {
            return [
                'status' => 400,
                'row' => [],
                'message' => 'Grupo no especificado'
            ];
        }

        $params = ['grupo' => $grupo];
        if ($udn !== 'all') {
            $params['udn'] = $udn;
        }

        $ls = $this->listProductos($params);

        foreach ($ls as $key) {
            $nombre_costsys = $this->select_homologar(array($key['id']));
            $totalEnlaces = count($nombre_costsys);
            $enlaces = [];

            foreach ($nombre_costsys as $costsys) {
                $enlaces[] = $costsys['idhomologado'];
            }

            $linkHtml = renderEstadoHomologacion($key['id'], $enlaces, $totalEnlaces);

            $__row[] = [
                'id' => $key['id_Producto'],
                'Descripción' => htmlspecialchars($key['descripcion'] ?? '', ENT_QUOTES, 'UTF-8'),
                'Grupo' => htmlspecialchars($key['grupoproductos'] ?? 'Sin grupo', ENT_QUOTES, 'UTF-8'),
                'Homologación' => [
                    'html' => $linkHtml,
                    'class' => 'text-center'
                ],
                'Costo' => [
                    'html' => evaluar($key['costo'] ?? 0),
                    'class' => 'text-end'
                ],
                'Precio Venta' => [
                    'html' => evaluar($key['precio_venta'] ?? 0),
                    'class' => 'text-end'
                ]
            ];
        }

        return [
            'status' => 200,
            'row' => $__row,
            'ls' => $ls
        ];
    }

  


}

// Complements

function renderEstadoHomologacion($idProducto, $enlaces, $totalEnlaces) {
    if ($totalEnlaces === 0) {
        return '
            <div class="flex items-center justify-center gap-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-200 text-gray-700">
                    <i class="icon-link-off mr-1"></i>
                    Sin homologar
                </span>
            </div>
        ';
    }

    if ($totalEnlaces === 1) {
        return '
            <div class="flex items-center justify-center gap-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                    <i class="icon-check-circle mr-1"></i>
                    Homologado
                </span>
                <span class="text-xs text-gray-600"></span>
            </div>
        ';
    }

    $enlacesTexto = implode(', ', $enlaces);
    return '
        <div class="flex items-center justify-center gap-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700 cursor-pointer" 
                  onclick="mostrarAlertaMultiplesEnlaces(' . $idProducto . ', \'' . $enlacesTexto . '\', ' . $totalEnlaces . ')">
                <i class="icon-alert-triangle mr-1"></i>
                ' . $totalEnlaces . ' Enlaces
            </span>
        </div>
    ';
}

$obj = new ctrl();
echo json_encode($obj->{$_POST['opc']}());
