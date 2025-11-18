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

    function lsConcentrado() {
        $__row = [];
        $row = [];
        
        $udn     = $_POST['udnConcentrado'] ?? 'all';
        $grupo   = $_POST['grupoConcentrado'] ?? 'all';
        $anio    = intval($_POST['anioConcentrado'] ?? date('Y'));
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
        
        $fechasName = [];
        $mesesData = [];
        
        for ($i = 0; $i < $periodo; $i++) {
            $mesIndex = $mesActual - $i - 1;
            $anioColumna = $anio;
            
            if ($mesIndex < 0) {
                $mesIndex += 12;
                $anioColumna--;
            }
            
            $nombreMes = $mesesNombres[$mesIndex];
            $fechasName[] = strtoupper($nombreMes) . '/' . $anioColumna;
            $mesesData[] = ['mes' => $mesIndex + 1, 'anio' => $anioColumna];
        }

        $gruposData = [];
        foreach ($ls as $key) {
            $grupoNombre = $key['grupo_nombre'] ?? 'Sin grupo';
            if (!isset($gruposData[$grupoNombre])) {
                $gruposData[$grupoNombre] = [];
            }
            $gruposData[$grupoNombre][] = $key;
        }

        foreach ($gruposData as $grupoNombre => $productos) {
            $row = [];
            
            $totalesGrupo = array_fill_keys($fechasName, 0);

            $campos = [
                'id'     => 0,
                'clave'  => '',
                'nombre' => strtoupper($grupoNombre),
            ];

            $dates = [];
            foreach ($fechasName as $fechaName) {
                $dates[$fechaName] = '';
            }

            $indexEncabezado = count($__row);
            $__row[] = array_merge($campos, $dates, ['opc' => 1]);

            foreach ($productos as $_key) {
                $campos = [
                    'id'     => $_key['id_Producto'],
                    'clave'  => htmlspecialchars($_key['clave_producto'] ?? '', ENT_QUOTES, 'UTF-8'),
                    'nombre' => htmlspecialchars($_key['descripcion'] ?? '', ENT_QUOTES, 'UTF-8'),
                ];

                $dates = [];

                foreach ($fechasName as $index => $fechaName) {
                    $cantidad = $_key['mes_' . $index] ?? 0;
                    $totalesGrupo[$fechaName] += $cantidad;
                    
                    $mostrar_valor = ($cantidad == 0) ? '-' : number_format($cantidad, 0, '.', ',');
                    
                    $dates[$fechaName] = [
                        'html'  => $mostrar_valor,
                        'val'   => $cantidad,
                        'class' => 'text-center'
                    ];
                }

                $row[] = array_merge($campos, $dates, ['opc' => 0]);
            }

            $__row = array_merge($__row, $row);

            foreach ($fechasName as $fechaName) {
                $totalGroup = ($totalesGrupo[$fechaName] > 0) ? number_format($totalesGrupo[$fechaName], 0, '.', ',') : '-';

                $__row[$indexEncabezado][$fechaName] = [
                    'html'  => '<strong>' . $totalGroup . '</strong>',
                    'class' => 'text-center bg-gray-200 font-bold'
                ];
            }
        }

        return [
            'row' => $__row,
            'endpoint' =>$ls,
            'thead' => ''
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

    //  Concentrado por periodo
    
    // function lsMenuCostsys(){

    //     $calculo = new aux_cp;

    //     // 📏 Declarar variables
    //     $__row   = [];
    //     $__lsMod = [];
    //     $row     = [];

    //     $mes                  = $_POST['Mes'];
    //     $name_month           = $_POST['name_month'];
    //     $year                 = $_POST['Anio'];
    //     $idClasificacion      = $_POST['Clasificacion'];
    //     $subClasificacion     = $_POST['Subclasificacion'];
    //     $productosModificados = false;
    //     $mostrar              = $_POST['mostrar'];
    //     $type                 = $_POST['type'];



    //     // 📜 Obtener subclasificaciones
    //     $subClasificacion = $this->listMenu([$idClasificacion]);

    //     foreach ($subClasificacion as $sub) {
    //         $row = [];
    //         $productos = $this->listDishes([$mes, $year, $sub['id']]);

    //         $fechas     = [];
    //         $fechasName = [];

    //         // 📜 Generar los últimos 6 meses
    //         for ($i = 0; $i < 6; $i++) {
    //             $time         = strtotime("-$i months", strtotime("$year-$mes-01"));
    //             $monthName    = date('M', $time);
    //             $yearName     = date('Y', $time);
    //             $fechasName[] = "$monthName/$yearName";
    //             $monthNumber  = date('m', $time);
    //             $yearNumber   = date('Y', $time);
    //             $fechas[]     = "$monthNumber/$yearNumber";
    //         }

    //         // 📌 Inicializar acumulador por columna (mes)
    //         $totalesGrupo = array_fill_keys($fechasName, 0);

    //         // 📜 Fila de encabezado
    //         $campos = [
    //             'id'     => $sub['id'],
    //             'clave'  => '',
    //             'nombre' => $sub['nombre'],
    //         ];

    //         $dates = [];
    //         foreach ($fechasName as $fechaName) {
    //             $dates[$fechaName] = ''; // se actualizará luego con totales
    //         }

    //         $indexEncabezado = count($__row); // 📌 Guardar índice para editar después
    //         $__row[] = array_merge($campos, $dates, ['opc' => 1]);

    //         // 📜 Fila de productos
    //         foreach ($productos as $_key) {

    //             $campos = [
    //                 'id'     => $sub['id'],
    //                 'clave'  => $_key['idDishes'],
    //                 'nombre' => $_key['nombre'],
    //             ];

    //             $dates = [];

    //             foreach ($fechas as $index => $fecha) {

    //                 list($m, $y) = explode("/", $fecha);
    //                 $costo_potencial = $this->selectDatosCostoPotencial([$_key['idReceta'], $m, $y]);



    //                 if($type == 3){
    //                     $valor = $costo_potencial['costo'] * $costo_potencial['desplazamiento'];
    //                     $totalesGrupo[$fechasName[$index]] += $valor; // 🔵 Acumular total por grupo
    //                      $dates[$fechasName[$index]] = [
    //                         'html'  => evaluar($valor),
    //                         'val'   => $valor,
    //                         'class' => ' text-end '
    //                     ];
    //                 }else{
    //                     $valor                              = floatval($costo_potencial['desplazamiento'] ?? 0);
    //                     $totalesGrupo[$fechasName[$index]] += $valor; // 🔵 Acumular total por grupo
    //                      $mostrar_valor                      = ($valor == 0) ? '-' : $valor;  // 📌 Mostrar guion si es cero
    //                     $dates[$fechasName[$index]] = [
    //                         'html'  => $mostrar_valor,
    //                         'val'   => $valor,
    //                         'class' => 'text-end'
    //                     ];
    //                 }





    //             }

    //             $row[] = array_merge($campos, $dates, ['opc' => 0]);
    //         }

    //         $res   = pintarValPromedios($row,$fechasName);
    //         $__row = array_merge($__row,$res);

    //         // 📌 Insertar totales al encabezado del grupo
    //         foreach ($fechasName as $fechaName) {

    //             $totalGroup = ($totalesGrupo[$fechaName]) ? $totalesGrupo[$fechaName] : '-';
    //             if($type == 3){
    //                 $totalGroup = evaluar($totalesGrupo[$fechaName]);
    //             }


    //             $__row[$indexEncabezado][$fechaName] = [
    //                 'html'  => '<strong>' .$totalGroup. '</strong>',
    //                 'class' => 'text-end bg-disabled2'
    //             ];
    //         }
    //     }




    //     // 📦 Devolver datos
    //     return [
    //         "thead" => '',
    //         "row"   => $__row
    //     ];
    // }


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
