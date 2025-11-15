<?php

if (empty($_POST['opc'])) exit(0);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

require_once '../mdl/mdl-ventas2.php';
require_once '../../../../conf/coffeSoft.php';


class ctrl extends mdl {

    function init() {
        
        return [
            'udn'        => $this->lsUDN(),
            'categorias' => $this->lsVentas()
        ];

    }

    function lsSales() {
        
        $__row          = [];
        $udn            = $_POST['udn'];
        $anio           = $_POST['anio'];
        $mes            = $_POST['mes'];

        $ls = $this->listSales([$udn, $anio, $mes]);

        $categorias     = [];
        $ventasPorFecha = [];

        foreach ($ls as $key) {
            $fecha     = $key['fecha'];
            $categoria = $key['categoria'];

            if (!in_array($categoria, $categorias)) {
                $categorias[] = $categoria;
            }

            if (!isset($ventasPorFecha[$fecha])) {
                $ventasPorFecha[$fecha] = [
                    'id'         => $key['id'],
                    'fecha'      => $fecha,
                    'dia'        => traducirDia($key['dia']),
                    'estado'     => $key['estado'],
                    'categorias' => []
                ];
            }

            $ventasPorFecha[$fecha]['categorias'][$categoria] = floatval($key['cantidad']);
        }

        $thead = ['Fecha'];
        
        if ($udn == 1) {
            $thead[] = 'Habitaciones';
        }
        
        foreach ($categorias as $cat) {
            $thead[] = $cat;
        }

        $thead[] = 'Total Ventas';
        $thead[] = '';

        foreach ($ventasPorFecha as $venta) {
            $total = 0;

            $a = [];

              $a[] = [
                    'class'   => 'btn btn-sm bg-green-600 text-white hover:bg-green-800 me-1',
                    'html'    => '<i class="icon-upload"></i> Subir ',
                    'onclick' => 'app.syncToFolio(\'' . $venta['fecha'] . '\', ' . $udn . ')'
                ];


            $row   = [
                'id'     => $venta['id'],
                'Fecha'  => formatSpanishDate($venta['fecha'],'short'),
            ];

            if ($udn == 1) {
                $suitesOcupadas = $this->getSuitesOcupadasByFecha($venta['fecha']);
                $row['Habitaciones'] = [
                    'html'  => '<div class="text-center font-semibold text-blue-700">' . $suitesOcupadas . '</div>',
                    'class' => 'text-center bg-blue-50'
                ];
            }

            foreach ($categorias as $cat) {
                $cantidadSinImpuestos = isset($venta['categorias'][$cat]) ? $venta['categorias'][$cat] : 0;
                $iva                  = $cantidadSinImpuestos * 0.08;
                $ieps                 = 0;
                $porcentajeTotal      = '8%';
                $desglose             = 'IVA 8%';
                
                if (strtolower($cat) === 'hospedaje') {
                    $ieps            = $cantidadSinImpuestos * 0.02;
                    $porcentajeTotal = '10%';
                    $desglose        = 'IVA 8% + IEPS 2%';
                }
                
                $cantidadConImpuestos = $cantidadSinImpuestos + $iva + $ieps;
                
                $total     += $cantidadConImpuestos;
                $row[$cat]  = [
                    'html' => '
                        <div class="text-end">
                            <div class="font-bold text-green-700">' . evaluar($cantidadConImpuestos) . '</div>
                            <div class="text-xs text-gray-500">
                                Base: ' . evaluar($cantidadSinImpuestos) . ' 
                                <span class="text-blue-600">+' . $porcentajeTotal . '</span>
                            </div>
                        
                        </div>
                    '
                ];
            }

            $row['Total Ventas'] = [
                'html'  => evaluar($total),
                'title' =>  $cantidadSinImpuestos,
                'class' => 'text-end bg-gray-300 font-bold '
            ];

            $row['a'] = $a;

            $__row[] = $row;
        }

        return [
            'row'        => $__row,
            'thead'      => $thead,
            'categorias' => $categorias,
            'ls'         => $ls
        ];
    }


    function getSale() {
        $id      = $_POST['id'];
        $status  = 500;
        $message = 'Error al obtener los datos';
        $data    = null;

        $sale = $this->getSaleById($id);

        if ($sale) {
            $status  = 200;
            $message = 'Datos obtenidos correctamente';
            $data    = $sale;
        }

        return [
            'status'  => $status,
            'message' => $message,
            'data'    => $data
        ];
    }

    function addSale() {
        $status  = 500;
        $message = 'No se pudo agregar la venta';
        
        $_POST['Fecha_Venta'] = $_POST['fecha'];
        $_POST['Cantidad'] = $_POST['cantidad'];

        $idFolio = $this->createVentaUDN($this->util->sql([
            'id_UDN'   => $_POST['udn'],
            'id_Venta' => $_POST['categoria'],
            'Stado'    => 1,
            'creacion' => date('Y-m-d H:i:s')
        ]));

        if ($idFolio) {
            $_POST['id_UV'] = $idFolio;
            $create = $this->createSale($this->util->sql($_POST));

            if ($create) {
                $status  = 200;
                $message = 'Venta agregada correctamente';
            }
        }

        return [
            'status'  => $status,
            'message' => $message
        ];
    }

    function editSale() {
        $id      = $_POST['id'];
        $status  = 500;
        $message = 'Error al editar la venta';

        $_POST['Fecha_Venta'] = $_POST['fecha'];
        $_POST['Cantidad'] = $_POST['cantidad'];

        $edit = $this->updateSale($this->util->sql($_POST, 1));

        if ($edit) {
            $status  = 200;
            $message = 'Venta editada correctamente';
        }

        return [
            'status'  => $status,
            'message' => $message
        ];
    }

    function statusSale() {
        $status  = 500;
        $message = 'No se pudo actualizar el estado';

        $sale = $this->getSaleById($_POST['id']);
        
        if ($sale && isset($sale['id_Folio'])) {
            $update = $this->updateVentaUDN($this->util->sql([
                'Stado' => $_POST['active'],
                'idUV'  => $sale['id_Folio']
            ], 1));

            if ($update) {
                $status  = 200;
                $message = 'Estado actualizado correctamente';
            }
        }

        return [
            'status'  => $status,
            'message' => $message
        ];
    }

    function syncToFolio() {
        $status  = 500;
        $message = 'Error al sincronizar';
        $fecha   = $_POST['fecha'];
        $udn     = $_POST['udn'];

        $folioExistente = $this->getFolioByFechaUdn([$fecha, $udn]);

        if (!$folioExistente) {
            $createFolio = $this->createFolio($this->util->sql([
                'fecha_folio'             => $fecha,
                'id_udn'                  => $udn,
                'file_productos_vendidos' => 0,
                'file_ventas_dia'         => 0,
                'monto_productos_vendidos'=> 0,
                'monto_ventas_dia'        => 0
            ]));

            if (!$createFolio) {
                return [
                    'status'  => 500,
                    'message' => 'Error al crear el folio'
                ];
            }

            $folioExistente = $this->getFolioByFechaUdn([$fecha, $udn]);
        }

        $ventasDelDia = $this->listSales([$udn, date('Y', strtotime($fecha)), date('m', strtotime($fecha))]);
        
        $alimentos = 0;
        $bebidas   = 0;
        $otros     = 0;
        $hospedaje = 0;
        $ayb       = 0;
        $diversos  = 0;

        foreach ($ventasDelDia as $venta) {
            if ($venta['fecha'] === $fecha) {
                $cantidadSinImpuestos = floatval($venta['cantidad']);
                $categoria            = strtolower(trim($venta['categoria']));
                
                $iva  = $cantidadSinImpuestos * 0.08;
                $ieps = 0;
                
                if ($categoria === 'hospedaje') {
                    $ieps = $cantidadSinImpuestos * 0.02;
                }
                
                $cantidadConImpuestos = $cantidadSinImpuestos + $iva + $ieps;
                
                switch ($categoria) {
                    case 'alimentos':
                        $alimentos += $cantidadConImpuestos;
                        break;
                    case 'bebidas':
                        $bebidas += $cantidadConImpuestos;
                        break;
                    case 'otros':
                        $otros += $cantidadConImpuestos;
                        break;
                    case 'hospedaje':
                        $hospedaje += $cantidadConImpuestos;
                        break;
                    case 'ayb':
                    case 'a&b':
                        $ayb += $cantidadConImpuestos;
                        break;
                    case 'diversos':
                    case 'misceláneos':
                        $diversos += $cantidadConImpuestos;
                        break;
                }
            }
        }

        $subtotal = $alimentos + $bebidas + $otros + $hospedaje + $ayb + $diversos;
        $total    = $subtotal;

        $suitesOcupadas = 0;
        if ($udn == 1) {
            $suitesOcupadas = $this->getSuitesOcupadasByFecha($fecha);
        }

        $ventaExistente = $this->getVentaByFolioId($folioExistente['id_folio']);

        if ($ventaExistente) {
            $update = $this->updateVenta($this->util->sql([
                'alimentos'  => $alimentos,
                'bebidas'    => $bebidas,
                'AyB'       => $alimentos + $bebidas ,
                'otros'      => $otros,
                'Diversos'   => $diversos,
                'Hospedaje'  => $hospedaje,
                'subtotal'   => $subtotal,
                'iva'        => 0,
                'personas'        => $suitesOcupadas,
                'noHabitaciones'        => $suitesOcupadas,
                'total'      => $total,
                'id_venta'   => $ventaExistente['id_venta']
            ], 1));

            if ($update) {
                $status  = 200;
                $message = 'Ventas actualizadas correctamente en soft_restaurant_ventas';
            }
        } else {
            $create = $this->createVenta($this->util->sql([
                'soft_ventas_fecha' => date('Y-m-d H:i:s'),
                'soft_folio'        => $folioExistente['id_folio'],
                'alimentos'         => $alimentos,
                'bebidas'           => $bebidas,
                'AyB'               => $ayb,
                'otros'             => $otros,
                'Diversos'          => $diversos,
                'Hospedaje'         => $hospedaje,
                'subtotal'          => $subtotal,
                'iva'               => 0,
                'personas'        => $suitesOcupadas,
                'noHabitaciones'        => $suitesOcupadas,


                'total'             => $total
            ]));

            if ($create) {
                $status  = 200;
                $message = 'Ventas creadas correctamente en soft_restaurant_ventas';
            }
        }

        $responseData = [
            'folio_id'  => $folioExistente['id_folio'],
            'alimentos' => $alimentos,
            'bebidas'   => $bebidas,
            'otros'     => $otros,
            'hospedaje' => $hospedaje,
            'AyB'       => $alimentos + $bebidas,
            'diversos'  => $diversos,
            'subtotal'  => $subtotal,
            'total'     => $total
        ];

        if ($udn == 1) {
            $responseData['habitaciones'] = $suitesOcupadas;
        }

        return [
            'status'  => $status,
            'message' => $message,
            'list'    => $categoria,
            'data'    => $responseData
        ];
    }

    function syncMonthToFolio() {
        $status  = 500;
        $message = 'Error al sincronizar el mes';
        $udn     = $_POST['udn'];
        $anio    = $_POST['anio'];
        $mes     = $_POST['mes'];

        $ventasDelMes = $this->listSales([$udn, $anio, $mes]);
        
        if (empty($ventasDelMes)) {
            return [
                'status'  => 404,
                'message' => 'No se encontraron ventas para el mes seleccionado',
                'data'    => []
            ];
        }

        $fechasUnicas = [];
        foreach ($ventasDelMes as $venta) {
            $fecha = $venta['fecha'];
            if (!in_array($fecha, $fechasUnicas)) {
                $fechasUnicas[] = $fecha;
            }
        }

        $resultados = [];
        $exitosos   = 0;
        $fallidos   = 0;

        foreach ($fechasUnicas as $fecha) {
            $folioExistente = $this->getFolioByFechaUdn([$fecha, $udn]);

            if (!$folioExistente) {
                $createFolio = $this->createFolio($this->util->sql([
                    'fecha_folio'             => $fecha,
                    'id_udn'                  => $udn,
                    'file_productos_vendidos' => 0,
                    'file_ventas_dia'         => 0,
                    'monto_productos_vendidos'=> 0,
                    'monto_ventas_dia'        => 0
                ]));

                if (!$createFolio) {
                    $fallidos++;
                    $resultados[] = [
                        'fecha'   => $fecha,
                        'status'  => 'error',
                        'message' => 'Error al crear el folio'
                    ];
                    continue;
                }

                $folioExistente = $this->getFolioByFechaUdn([$fecha, $udn]);
            }

            $alimentos = 0;
            $bebidas   = 0;
            $otros     = 0;
            $hospedaje = 0;
            $ayb       = 0;
            $diversos  = 0;

            foreach ($ventasDelMes as $venta) {
                if ($venta['fecha'] === $fecha) {
                    $cantidadSinImpuestos = floatval($venta['cantidad']);
                    $categoria            = strtolower(trim($venta['categoria']));
                    
                    $iva  = $cantidadSinImpuestos * 0.08;
                    $ieps = 0;
                    
                    if ($categoria === 'hospedaje') {
                        $ieps = $cantidadSinImpuestos * 0.02;
                    }
                    
                    $cantidadConImpuestos = $cantidadSinImpuestos + $iva + $ieps;
                    
                    switch ($categoria) {
                        case 'alimentos':
                            $alimentos += $cantidadConImpuestos;
                            break;
                        case 'bebidas':
                            $bebidas += $cantidadConImpuestos;
                            break;
                        case 'otros':
                            $otros += $cantidadConImpuestos;
                            break;
                        case 'hospedaje':
                            $hospedaje += $cantidadConImpuestos;
                            break;
                        case 'ayb':
                        case 'a&b':
                            $ayb += $cantidadConImpuestos;
                            break;
                        case 'diversos':
                        case 'misceláneos':
                            $diversos += $cantidadConImpuestos;
                            break;
                    }
                }
            }

            $subtotal = $alimentos + $bebidas + $otros + $hospedaje + $ayb + $diversos;
            $total    = $subtotal;

            $suitesOcupadas = 0;
            if ($udn == 1) {
                $suitesOcupadas = $this->getSuitesOcupadasByFecha($fecha);
            }

            $ventaExistente = $this->getVentaByFolioId($folioExistente['id_folio']);

            if ($ventaExistente) {
                $update = $this->updateVenta($this->util->sql([
                    'alimentos'      => $alimentos,
                    'bebidas'        => $bebidas,
                    'AyB'            => $alimentos + $bebidas,
                    'otros'          => $otros,
                    'Diversos'       => $diversos,
                    'Hospedaje'      => $hospedaje,
                    'subtotal'       => $subtotal,
                    'iva'            => 0,
                    'personas'       => $suitesOcupadas,
                    'noHabitaciones' => $suitesOcupadas,
                    'total'          => $total,
                    'id_venta'       => $ventaExistente['id_venta']
                ], 1));

                if ($update) {
                    $exitosos++;
                    $resultados[] = [
                        'fecha'   => $fecha,
                        'status'  => 'success',
                        'message' => 'Actualizado',
                        'total'   => $total
                    ];
                } else {
                    $fallidos++;
                    $resultados[] = [
                        'fecha'   => $fecha,
                        'status'  => 'error',
                        'message' => 'Error al actualizar'
                    ];
                }
            } else {
                $create = $this->createVenta($this->util->sql([
                    'soft_ventas_fecha' => date('Y-m-d H:i:s'),
                    'soft_folio'        => $folioExistente['id_folio'],
                    'alimentos'         => $alimentos,
                    'bebidas'           => $bebidas,
                    'AyB'               => $ayb,
                    'otros'             => $otros,
                    'Diversos'          => $diversos,
                    'Hospedaje'         => $hospedaje,
                    'subtotal'          => $subtotal,
                    'iva'               => 0,
                    'personas'          => $suitesOcupadas,
                    'noHabitaciones'    => $suitesOcupadas,
                    'total'             => $total
                ]));

                if ($create) {
                    $exitosos++;
                    $resultados[] = [
                        'fecha'   => $fecha,
                        'status'  => 'success',
                        'message' => 'Creado',
                        'total'   => $total
                    ];
                } else {
                    $fallidos++;
                    $resultados[] = [
                        'fecha'   => $fecha,
                        'status'  => 'error',
                        'message' => 'Error al crear'
                    ];
                }
            }
        }

        if ($exitosos > 0) {
            $status  = 200;
            $message = "Sincronización completada: $exitosos exitosos, $fallidos fallidos";
        }

        return [
            'status'     => $status,
            'message'    => $message,
            'exitosos'   => $exitosos,
            'fallidos'   => $fallidos,
            'resultados' => $resultados
        ];
    }
}


// Complements

function dropdown($id, $estado) {
    $options = [];

    if ($estado == 1) {
        $options[] = [
            'icon'    => 'icon-pencil',
            'text'    => 'Editar',
            'onclick' => "sales.editSale($id)"
        ];

        $options[] = [
            'icon'    => 'icon-toggle-on',
            'text'    => 'Desactivar',
            'onclick' => "sales.statusSale($id, 0)"
        ];
    } else {
        $options[] = [
            'icon'    => 'icon-toggle-off',
            'text'    => 'Activar',
            'onclick' => "sales.statusSale($id, 1)"
        ];
    }

    return $options;
}

function renderStatus($estado) {
    switch ($estado) {
        case 1:
            return '<span class="px-2 py-1 rounded-md text-sm font-semibold bg-[#014737] text-[#3FC189]">Capturado</span>';
        case 0:
            return '<span class="px-2 py-1 rounded-md text-sm font-semibold bg-[#721c24] text-[#ba464d]">Inactivo</span>';
        default:
            return '<span class="px-2 py-1 rounded-md text-sm font-semibold bg-gray-500 text-white">Desconocido</span>';
    }
}

function traducirDia($dia) {
    $dias = [
        'Monday'    => 'Lunes',
        'Tuesday'   => 'Martes',
        'Wednesday' => 'Miércoles',
        'Thursday'  => 'Jueves',
        'Friday'    => 'Viernes',
        'Saturday'  => 'Sábado',
        'Sunday'    => 'Domingo'
    ];

    return $dias[$dia] ?? $dia;
}

$obj = new ctrl();
echo json_encode($obj->{$_POST['opc']}());
