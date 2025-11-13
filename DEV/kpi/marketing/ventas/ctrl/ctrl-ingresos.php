<?php
session_start();
if (empty($_POST['opc'])) exit(0);


header("Access-Control-Allow-Origin: *"); // Permite solicitudes de cualquier origen
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // Métodos permitidos
header("Access-Control-Allow-Headers: Content-Type"); // Encabezados permitidos

require_once '../mdl/mdl-ingresos.php';
require_once('../../../../conf/coffeSoft.php');

class ctrl extends mdl {

    function init(){

        $lsClasificacion    = $this->lsClasificacion();


        return [
            'udn'           => $this -> lsUDN(),
            'lsudn'         => $this -> salesUDN(),
            'clasification' => $lsClasificacion,
        ];
    }

    // Módulo de ingresos diarios.
    function list() {
        $type = $_POST['type'];

        switch ($type) {
            case 1:
                return $this->resumenIngresosPorDia();
            case 2:
                return $this->lsIngresosCaptura();
            case 3:
                return $this->PromediosDiarios();
            default:
                return ["error" => "Tipo no reconocido"];
        }
    }

    function resumenIngresosPorDia() {
        $__row = [];
        $days = [2 => 'Lunes', 3 => 'Martes', 4 => 'Miércoles', 5 => 'Jueves', 6 => 'Viernes', 7 => 'Sábado', 1 => 'Domingo'];

        // Inicializar totales
        $totalFields = [
            'Hospedaje'    => 0,
            'AyB'          => 0,
            'Diversos'     => 0,
            'alimentos'    => 0,
            'bebidas'      => 0,
            'complementos' => 0,
            'total'        => 0,
            'totalGral'    => 0,
            'noHabitaciones' => 0
        ];

        foreach ($days as $noDias => $Days) {
            $lsDays = $this->getIngresosDayOfWeek([$_POST['udn'], $_POST['anio'], $_POST['mes'], $noDias]);

            foreach ($lsDays as $key) {
                if ($_POST['udn'] == 1) {
                    $totalFields['Hospedaje']   += $key['Hospedaje'];
                    $totalFields['AyB']         += $key['AyB'];
                    $totalFields['Diversos']    += $key['Diversos'];
                    $totalFields['total']       += $key['total'];
                    $totalFields['noHabitaciones'] += $key['noHabitaciones'];

                    $__row[] = [
                        'id'               => $noDias,
                        'fecha'            => $key['fecha'],
                        'Dia de la semana' => $Days,
                        'Hospedaje'        => evaluar($key['Hospedaje']),
                        'AyB'              => evaluar($key['AyB']),
                        'Diversos'         => evaluar($key['Diversos']),
                        'No habitaciones'  => $key['noHabitaciones'],
                        'Total'            => evaluar($key['total']),
                        'opc'              => 0
                    ];
                } elseif ($_POST['udn'] == 5) {
                    $totalFields['alimentos']    += $key['alimentos'];
                    $totalFields['bebidas']      += $key['bebidas'];
                    $totalFields['complementos'] += $key['complementos'];
                    $totalFields['total']        += $key['total'];

                    $__row[] = [
                        'id'               => $noDias,
                        'fecha'            => $key['fecha'],
                        'Dia de la semana' => $Days,
                        'alimentos'        => evaluar($key['alimentos']),
                        'bebidas'          => evaluar($key['bebidas']),
                        'complementos'     => evaluar($key['complementos']),
                        'Total'            => evaluar($key['total']),
                        'opc'              => 0
                    ];
                } else {
                    $totalFields['alimentos']    += $key['alimentos'];
                    $totalFields['bebidas']      += $key['bebidas'];
                    $totalFields['totalGral']    += $key['totalGral'];
                    $totalFields['noHabitaciones'] += $key['noHabitaciones'];

                    $__row[] = [
                        'id'               => $noDias,
                        'fecha'            => $key['fecha'],
                        'Dia de la semana' => $Days,
                        'alimentos'        => evaluar($key['alimentos']),
                        'bebidas'          => evaluar($key['bebidas']),
                        'No habitaciones'  => ['html'=>$key['noHabitaciones'],'class'=>'text-center'],
                        'total'            => evaluar($key['totalGral']),
                        'opc'              => 0
                    ];
                }
            }

            $__row[] = [
                'id'       => '',
                'fecha'    => '',
                'colgroup' => true
            ];
        }

        // Agregar fila de totales al final
        if ($_POST['udn'] == 1) {
            $__row[] = [
                'id'               => '',
                'fecha'            => 'Totales',
                'Dia de la semana' => '',
                'Hospedaje'        => evaluar($totalFields['Hospedaje']),
                'AyB'              => evaluar($totalFields['AyB']),
                'Diversos'         => evaluar($totalFields['Diversos']),
                'No habitaciones'  => $totalFields['noHabitaciones'],
                'Total'            => evaluar($totalFields['total']),
                'opc'              => 1
            ];
        } elseif ($_POST['udn'] == 5) {
            $__row[] = [
                'id'               => '',
                'fecha'            => 'Totales',
                'Dia de la semana' => '',
                'alimentos'        => evaluar($totalFields['alimentos']),
                'bebidas'          => evaluar($totalFields['bebidas']),
                'complementos'     => evaluar($totalFields['complementos']),
                'Total'            => evaluar($totalFields['total']),
                'opc'              => 1
            ];
        } else {
            $__row[] = [
                'id'               => '',
                'fecha'            => 'Totales',
                'Dia de la semana' => '',
                'alimentos'        => evaluar($totalFields['alimentos']),
                'bebidas'          => evaluar($totalFields['bebidas']),
                'No habitaciones'  => ['html'=>$totalFields['noHabitaciones'],'class'=>'text-center'],
                'total'            => evaluar($totalFields['totalGral']),
                'opc'              => 1
            ];
        }

        return [
            "thead"    => $this->get_th_ingresos(),
            "row"      => $__row,
            "frm_head" => ''
        ];
    }

    function lsIngresosCaptura() {
        $fi = new DateTime($_POST['anio'] . '-' . $_POST['mes'] . '-01');
        $hoy = clone $fi;
        $hoy->modify('last day of this month');

        $__row = [];
        $idRow = 0;

        while ($fi <= $hoy) {
            $idRow++;
            $fecha = $fi->format('Y-m-d');

            $softVentas = $this->getsoftVentas([$_POST['udn'], $fecha]);
            $idVentas   = $softVentas['id_venta'];

           $row = [
                'id'    => $idRow,
                'fecha' => $fecha,
                'dia'   => [
                    'html'  => formatSpanishDay($fecha),
                    'class' => 'text-gray-600 text-center '
                ],
               'Estado' => [
                    'html' => $softVentas['id_venta']
                        ? '<span class="px-3 py-1 w-[150px] mx-auto rounded-full text-xs font-semibold bg-green-200 text-green-800 flex items-center justify-center gap-1"><i class="icon-ok-circled-2 text-green-500"></i> Capturado</span>'
                        : '<span class="px-3 py-1 w-[150px] mx-auto rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 flex items-center justify-center gap-1"><i class="icon-info-circled-3 text-yellow-500"></i> Pendiente</span>',
                    'class' => 'text-center'
                ]
            ];

            if ($_POST['udn'] == 1) {
                $total = $softVentas['Hospedaje'] + $softVentas['AyB'] + $softVentas['Diversos'];
                $grupo = [
                    'clientes'   => $softVentas['noHabitaciones'],
                    'Hospedaje'  => $softVentas['Hospedaje'],
                    'AyB'        => $softVentas['AyB'],
                    'Diversos'   => $softVentas['Diversos'],
                    'total'      => evaluar($total),
                    'opc'        => 0
                ];
            } elseif ($_POST['udn'] == 5) {
                $total = $softVentas['alimentos'] + $softVentas['bebidas'] + $softVentas['guarniciones'] + $softVentas['sales'] + $softVentas['domicilio'];
                $grupo = [
                    'clientes'     => $softVentas['noHabitaciones'],
                    'alimentos'    => ['html' => evaluar($softVentas['alimentos']),    'class' => 'text-end '],
                    'bebidas'      => ['html' => evaluar($softVentas['bebidas']),      'class' => 'text-end '],
                    'guarniciones' => ['html' => evaluar($softVentas['guarniciones']), 'class' => 'text-end '],
                    'sales'        => ['html' => evaluar($softVentas['sales']),        'class' => 'text-end '],
                    'domicilio'    => ['html' => evaluar($softVentas['domicilio']),    'class' => 'text-end '],
                    'total'        => ['html' => evaluar($total),                      'class' => 'text-end font-bold '],
                    'opc'          => 0
                ];
            } else {
                $grupo = [
                    'clientes'  => $softVentas['noHabitaciones'],
                    'alimentos' => ['html' => evaluar($softVentas['alimentos']), 'class' => 'text-end '],
                    'bebidas'   => ['html' => evaluar($softVentas['bebidas']),   'class' => 'text-end '],
                    'Total'     => ['html' => evaluar($softVentas['bebidas'] + $softVentas['alimentos']), 'class' => 'text-end font-bold '],
                    'opc'       => 0
                ];
            }

            $__row[] = array_merge($row, $grupo);
            $fi->modify('+1 day');
        }

        return [
            "row"      => $__row,
            "thead"    => $this->getSalesTitle(),
            "frm_head" => "<strong>Conectado a: </strong> {$this->bd}"
        ];
    }

    function lsIngresosCaptura2() {
        $fi = new DateTime($_POST['anio'].'-' . $_POST['mes'] . '-01');
        $hoy = clone $fi;
        $hoy->modify('last day of this month');

        $__row = [];

        while ($fi <= $hoy) {
            $idRow++;
            $fecha = $fi->format('Y-m-d');


            $softVentas = $this->getsoftVentas([$_POST['udn'], $fecha]);
            $idVentas   = $softVentas['id_venta'];

            $row = [
                'id'     => $idRow,
                'fecha'  => $fecha,
                'dia'    => formatSpanishDay($fecha),
                'Estado' => $softVentas['id_venta']
                    ? '<i class="icon-ok-circled-2 text-success"></i>'
                    : '<i class="icon-info-circled-3 text-orange-500"></i>',
            ];

            if ($_POST['udn'] == 1) {
                $total = $softVentas['Hospedaje'] + $softVentas['AyB'] + $softVentas['Diversos'];
                $grupo = $this->createdGroups(['noHabitaciones', 'Hospedaje', 'AyB', 'Diversos'], $softVentas, $idVentas);
                $grupo['total'] = evaluar($total);
                $grupo['opc'] = 0;
            } elseif ($_POST['udn'] == 5) {
                $total          = $softVentas['alimentos'] + $softVentas['bebidas'] + $softVentas['guarniciones'] + $softVentas['sales'] + $softVentas['domicilio'];
                $grupo          = createdGroups(['noHabitaciones', 'alimentos', 'bebidas', 'guarniciones', 'sales', 'domicilio'], $softVentas, $idVentas);
                $grupo['total'] = evaluar($total);
                $grupo['opc']   = 0;

            } else {
                $grupo = [
                     'No habitaciones' => createElement('input', [
                        'name' => 'noHabitaciones',
                        'value' => $softVentas['noHabitaciones'],
                        'onkeyup' => "ingresosDiarios.setVentas(event, $idVentas)"
                    ]),
                    'alimentos' => createElement('input', [
                        'name' => 'alimentos',
                        'value' => number_format($softVentas['alimentos'], 2, '.', ''),
                        'onkeyup' => "ingresosDiarios.setVentas(event, $idVentas)"
                    ]),
                    'bebidas' => createElement('input', [
                        'name' => 'bebidas',
                        'value' => number_format($softVentas['bebidas'], 2, '.', ''),
                        'onkeyup' => "ingresosDiarios.setVentas(event, $idVentas)"
                    ]),

                    'Total' => evaluar($softVentas['bebidas'] + $softVentas['alimentos']),
                    'opc' => 0
                ];
            }

            $__row[] = array_merge($row, $grupo);
            $fi->modify('+1 day');
        }

        return [
            "row" => $__row,
            "thead" => $this->getSalesTitle(),
            "frm_head" => "<strong>Conectado a: </strong> {$this->bd}"
        ];
    }

    function PromediosDiarios(){
         # Declarar variables
        $__row        = [];
        $mesCompleto  = $_POST['monthText'];
        $Anio         = $_POST['anio'];
        $AnioAnterior = $Anio-1;
        $udn          = $_POST['udn'];
        $__row        = [];
        $days         = listDays();

        $month = [
            'currentMonth'  => ['year'=> $Anio, 'month'=>$_POST['mes']],
            'previousMonth' => ['year'=> $AnioAnterior, 'month'=>$_POST['mes']],
        ];

        // consultas individuales.
        if($udn == 1):
            $consultas = array(
                'totalGeneral'      => 'Suma de ingresos',
                'totalHospedaje'    => 'ingreso de Hospedaje',
                'totalAyB'          => 'ingreso AyB',
                'totalDiversos'     => 'ingreso Diversos',
                'totalHabitaciones' => 'Habitaciones',
                'group'             => '',
                'porcAgrupacion'          => '% Ocupacion',
                'tarifaEfectiva'          => 'Tarifa efectiva acumulada',
                'chequePromedio'          => 'Cheque Promedio',
                'chequePromedioHospedaje' => 'chequePromedioHospedaje',
                'chequePromedioAyB'       => 'cheque Promedio AyB',
                'chequePromedioDiversos'  => 'cheque Promedio Diversos',
            );
        else:

              $consultas = array(
                  'totalHabitaciones'       => 'Clientes',
                  'totalGralAyB'            => 'Ventas AyB',
                  'totalAlimentos'          => 'Ventas alimentos',
                  'totalBebidas'            => 'Ventas bebidas',
                  'group'                   => '',
                  'chequePromedioAyB'       => 'Cheque Promedio AyB',
                  'chequePromedioAlimentos' => 'Cheque Promedio Alimentos',
                  'chequePromedioBebidas'   => 'Cheque Promedio Bebidas',
                  'group'                   => '',

              );
        endif;

        foreach ($consultas as $key => $titulo) {

            $row   = [];
            $meses = [];

            if ($key != 'group'):

                // Datos base del concepto
                $base = [
                    'id'       => $key,
                    'concepto' => $titulo
                ];

                // Recorrer los meses
                foreach ($month as $_key => $fecha):

                    $total_days = cal_days_in_month(CAL_GREGORIAN, $fecha['month'], $fecha['year']);
                    $ventas     = $this->ingresosMensuales([$udn, $fecha['year'], $fecha['month']]);

                    $value = $this->getCalculoPorConcepto($key, $ventas, $total_days);

                    $meses[$_key] = [
                        'val'   => $value,
                        'text'  => ($key == 'totalHabitaciones') ? $value : evaluar($value),
                        'class' => ' text-end '
                    ];

                endforeach;

                // Calcular diferencia actual - anterior
                $diferencia    = $meses['currentMonth']['val'] - $meses['previousMonth']['val'];
                $meses['dif']  = ($key == 'totalHabitaciones') ? $diferencia : evaluar($diferencia);
                $meses['opc']  = 0;

                // Combinar datos base con meses
                $row[]  = array_merge($base, $meses);
                $__row  = array_merge($__row, $row);

            else:
                $__row[] = [
                    'id'       => 0,
                    'Concepto' => '',
                    'colgroup' => true
                ];
            endif;
        }


        // Encapsular arreglos
        return [
            "thead" => [
                'Concepto ',
                $mesCompleto . ' / ' . $Anio,
                $mesCompleto . ' / ' . $AnioAnterior,
                'Diferencia'
            ],
            "row" => $__row,
        ];
    }

   

    function comparativaByCategory() {
        $anioNow = $_POST['anio1'];
        $mesNow  = $_POST['mes1'];
        $anioOld = $_POST['anio2'];
        $mesOld  = $_POST['mes2'];
        $udn     = $_POST['udn'];
        $categoria = strtolower(trim($_POST['category'] ?? 'todas'));

        // Obtener datos filtrados por año y mes
        $_POST['anio'] = $anioNow;
        $_POST['mes']  = $mesNow;
        $_POST['category'] = $categoria;
        $datosNow = $this->apiIngresosTotales($udn, $anioNow, $mesNow)['data'];

        $_POST['anio'] = $anioOld;
        $_POST['mes']  = $mesOld;
        $_POST['category'] = $categoria;
        $datosOld = $this->apiIngresosTotales($udn, $anioOld, $mesOld)['data'];

        // Formato de etiquetas tipo "01 Oct", "02 Oct", etc.
        $labels = array_map(function ($d) {
            return date('d', strtotime($d['fecha']));
        }, $datosNow);

        $tooltips = array_map(function ($d) {
            return formatSpanishDay($d['fecha']) . ' ' . date('d', strtotime($d['fecha']));
        }, $datosNow);

        $valuesNow = array_map(fn($d) => floatval($d['total'] ?? 0), $datosNow);
        $valuesOld = array_map(fn($d) => floatval($d['total'] ?? 0), $datosOld);

        return [
            'labels'  => $labels,
            'tooltip' => $tooltips,
            'datasets' => [
                [
                    'label' => $anioNow,
                    'data'  => $valuesNow,
                    'borderColor' => "#3B82F6", // azul
                    'backgroundColor' => "rgba(59, 130, 246, 0.2)",
                    'fill' => true,
                    'tension' => 0.3,
                    'pointRadius' => 4,
                    'pointBackgroundColor' => "#3B82F6"
                ],
                [
                    'label' => $anioOld,
                    'data'  => $valuesOld,
                    'borderColor' => "#EC4899", // rosa
                    'backgroundColor' => "rgba(236, 72, 153, 0.2)",
                    'fill' => true,
                    'tension' => 0.3,
                    'pointRadius' => 4,
                    'pointBackgroundColor' => "#EC4899"
                ]
            ]
        ];
    }



    // Api
    function apiIngresosTotales($udn, $anio, $mes) {
        $fi = new DateTime($anio . '-' . $mes . '-01');
        $hoy = clone $fi;
        $hoy->modify('last day of this month');

        $__row = [];
        $idRow = 0;
        
        // Obtener la categoría seleccionada y normalizarla
        $categoriaSeleccionada = isset($_POST['category']) ? strtolower(trim($_POST['category'])) : 'todas';

        while ($fi <= $hoy) {
            $idRow++;
            $fecha = $fi->format('Y-m-d');

            $softVentas = $this->getsoftVentas([$udn, $fecha]);

            $row = [
                'id'    => $idRow,
                'fecha' => $fecha,
                'estado' => $softVentas['id_venta'] ? 'Capturado' : 'Pendiente'
            ];

            if ($udn == 1) {
                $row['clientes'] = $softVentas['noHabitaciones'];
                
                // Filtrar por categoría o mostrar todas
                if ($categoriaSeleccionada == 'todas' || $categoriaSeleccionada == '') {
                    $row['Hospedaje'] = $softVentas['Hospedaje'];
                    $row['AyB']       = $softVentas['AyB'];
                    $row['Diversos']  = $softVentas['Diversos'];
                    $row['total']     = $softVentas['Hospedaje'] + $softVentas['AyB'] + $softVentas['Diversos'];
                } elseif ($categoriaSeleccionada == 'hospedaje') {
                    $row['Hospedaje'] = $softVentas['Hospedaje'];
                    $row['total']     = $softVentas['Hospedaje'];
                } elseif ($categoriaSeleccionada == 'ayb') {
                    $row['AyB']   = $softVentas['AyB'];
                    $row['total'] = $softVentas['AyB'];
                } elseif ($categoriaSeleccionada == 'diversos') {
                    $row['Diversos'] = $softVentas['Diversos'];
                    $row['total']    = $softVentas['Diversos'];
                }

            } elseif ($udn == 5) {
                $row['clientes'] = $softVentas['noHabitaciones'];
                
                if ($categoriaSeleccionada == 'todas' || $categoriaSeleccionada == '') {
                    $row['alimentos']    = $softVentas['alimentos'];
                    $row['bebidas']      = $softVentas['bebidas'];
                    $row['guarniciones'] = $softVentas['guarniciones'];
                    $row['sales']        = $softVentas['sales'];
                    $row['domicilio']    = $softVentas['domicilio'];
                    $row['total']        = $softVentas['alimentos'] + $softVentas['bebidas'] + $softVentas['guarniciones'] + $softVentas['sales'] + $softVentas['domicilio'];
                } elseif ($categoriaSeleccionada == 'alimentos' || $categoriaSeleccionada == 'cortes') {
                    $row['alimentos'] = $softVentas['alimentos'];
                    $row['total']     = $softVentas['alimentos'];
                } elseif ($categoriaSeleccionada == 'bebidas') {
                    $row['bebidas'] = $softVentas['bebidas'];
                    $row['total']   = $softVentas['bebidas'];
                } elseif ($categoriaSeleccionada == 'guarniciones') {
                    $row['guarniciones'] = $softVentas['guarniciones'];
                    $row['total']        = $softVentas['guarniciones'];
                } elseif ($categoriaSeleccionada == 'sales' || $categoriaSeleccionada == 'sales y condimentos') {
                    $row['sales'] = $softVentas['sales'];
                    $row['total'] = $softVentas['sales'];
                }

            } else {
                $row['clientes'] = $softVentas['noHabitaciones'];
                
                if ($categoriaSeleccionada == 'todas' || $categoriaSeleccionada === '') {
                    $row['alimentos'] = $softVentas['alimentos'];
                    $row['bebidas']   = $softVentas['bebidas'];
                    $row['total']     = $softVentas['alimentos'] + $softVentas['bebidas'];
                } elseif ($categoriaSeleccionada == 'alimentos') {
                    $row['alimentos'] = $softVentas['alimentos'];
                    $row['total']     = $softVentas['alimentos'];
                } elseif ($categoriaSeleccionada == 'bebidas') {
                    $row['bebidas'] = $softVentas['bebidas'];
                    $row['total']   = $softVentas['bebidas'];
                }
            }

            $__row[] = $row;
            $fi->modify('+1 day');
        }

        return ['data' => $__row];
    }


    // Api Calendar ---
    
    function getCalendarioVentas() {
        $udn = $_POST['udn'] ?? 1;
        
        $hoy = new DateTime();
        
        $diaSemanaHoy = (int)$hoy->format('N');
        
        $fechaInicio = clone $hoy;
        $diasHastaLunes = ($diaSemanaHoy == 1) ? 0 : $diaSemanaHoy - 1;
        $fechaInicio->modify('-' . ($diasHastaLunes + 28) . ' days');
        
        $fechaFin = clone $fechaInicio;
        $fechaFin->modify('+34 days');

        $semanas = [];
        $semanaActual = [];
        $totalSemana = 0;
        $contadorDias = 0;
        $numeroSemana = 1;

        $fechaTemp = clone $fechaInicio;

        while ($fechaTemp <= $fechaFin) {
            $fecha = $fechaTemp->format('Y-m-d');
            $ventas = $this->getsoftVentas([$udn, $fecha]);

            $total = 0;
            $clientes = isset($ventas['noHabitaciones']) ? intval($ventas['noHabitaciones']) : 0;

            if ($udn == 1) {
                $total = ($ventas['Hospedaje'] ?? 0) + ($ventas['AyB'] ?? 0) + ($ventas['Diversos'] ?? 0);
            } elseif ($udn == 5) {
                $total = ($ventas['alimentos'] ?? 0) + ($ventas['bebidas'] ?? 0) + 
                         ($ventas['guarniciones'] ?? 0) + ($ventas['sales'] ?? 0) + 
                         ($ventas['domicilio'] ?? 0);
            } else {
                $total = ($ventas['alimentos'] ?? 0) + ($ventas['bebidas'] ?? 0);
            }

            $chequePromedio = $clientes > 0 ? $total / $clientes : 0;

            // Obtener mes abreviado en español
            $mesesAbreviados = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
            $mesAbreviado = $mesesAbreviados[(int)$fechaTemp->format('m') - 1];

            $dia = [
                'dia' => $fechaTemp->format('d'),
                'mesAbreviado' => $mesAbreviado,
                'mes' => $fechaTemp->format('m'),
                'fecha' => $fecha,
                'diaSemana' => formatSpanishDay($fecha),
                'total' => $total,
                'totalFormateado' => evaluar($total),
                'clientes' => $clientes,
                'chequePromedio' => evaluar($chequePromedio)
            ];

            $semanaActual[] = $dia;
            $totalSemana += $total;
            $contadorDias++;

            if ($contadorDias == 7 || $fechaTemp == $fechaFin) {
                $semanas[] = [
                    'numero' => $numeroSemana,
                    'totalSemana' => evaluar($totalSemana),
                    'totalSemanaRaw' => $totalSemana,
                    'dias' => $semanaActual
                ];

                $semanaActual = [];
                $totalSemana = 0;
                $contadorDias = 0;
                $numeroSemana++;
            }

            $fechaTemp->modify('+1 day');
        }

        return [
            'status' => 200,
            'title' => 'Calendario de Ventas - Últimas 5 Semanas',
            'semanas' => $semanas,
            'fechaInicio' => $fechaInicio->format('Y-m-d'),
            'fechaFin' => $fechaFin->format('Y-m-d')
        ];
    }






    // Dashboard -Promedios diarios
    public function apiPromediosDiarios() {
        $response = [];

        $anio         = isset($_POST['anio']) ? (int) $_POST['anio'] : date('Y');
        $anioAnterior = $anio - 1;
        $mes          = isset($_POST['mes']) ? (int) $_POST['mes'] : date('m');
        $udn          = isset($_POST['udn']) ? (int) $_POST['udn'] : 1;

        $meses = [
            'actual'   => ['year' => $anio,        'mes' => $mes],
            'anterior' => ['year' => $anioAnterior,'mes' => $mes]
        ];

        if ($udn == 1) {
            $consultas = [
                'totalGeneral'              => 'Suma de ingresos',
                'totalHospedaje'            => 'Ingreso de Hospedaje',
                'totalAyB'                  => 'Ingreso AyB',
                'totalDiversos'             => 'Ingreso Diversos',
                'totalHabitaciones'         => 'Habitaciones',
                'porcAgrupacion'            => '% Ocupación',
                'tarifaEfectiva'            => 'Tarifa efectiva acumulada',
                'chequePromedio'            => 'Cheque Promedio',
                'chequePromedioHospedaje'   => 'Cheque Promedio Hospedaje',
                'chequePromedioAyB'         => 'Cheque Promedio AyB',
                'chequePromedioDiversos'    => 'Cheque Promedio Diversos',
            ];
        } else {
            $consultas = [
                'totalHabitaciones'         => 'Clientes',
                'totalGralAyB'              => 'Ventas AyB',
                'totalAlimentos'            => 'Ventas Alimentos',
                'totalBebidas'              => 'Ventas Bebidas',
                'chequePromedioAyB'         => 'Cheque Promedio AyB',
                'chequePromedioAlimentos'   => 'Cheque Promedio Alimentos',
                'chequePromedioBebidas'     => 'Cheque Promedio Bebidas',
            ];
        }

        foreach ($consultas as $clave => $concepto) {
            $datos = [
                'id'         => $clave,
                'concepto'   => $concepto,
                'anterior'   => ['valor' => 0, 'formato' => 0],
                'actual'     => ['valor' => 0, 'formato' => 0],
                'diferencia' => 0
            ];

            foreach ($meses as $tipo => $fecha) {
                $totalDias = cal_days_in_month(CAL_GREGORIAN, $fecha['mes'], $fecha['year']);
                $ventas    = $this->ingresosMensuales([$udn, $fecha['year'], $fecha['mes']]);

                $valor = $this->getCalculoPorConcepto($clave, $ventas, $totalDias);

                $datos[$tipo] = [
                    'valor'   => $valor,
                    'formato' => ($clave === 'totalHabitaciones') ? $valor : evaluar($valor),
                ];
            }

            // Validar que existan valores antes de calcular diferencia
            $valorActual   = isset($datos['actual']['valor']) ? $datos['actual']['valor'] : 0;
            $valorAnterior = isset($datos['anterior']['valor']) ? $datos['anterior']['valor'] : 0;

            $dif = $valorActual - $valorAnterior;
            $datos['diferencia'] = ($clave === 'totalHabitaciones') ? $dif : evaluar($dif);

            $response[] = $datos;
        }

        return [
            'status'    => 200,
            'data'      => $response,
            'dashboard' => $this->apiDashBoard($response, $udn),
            'barras'    => $this->comparativaChequePromedio(),
            'linear'    => $this->apiLinearPromediosDiario($anio, $mes, $udn),
            'barDays'   => $this->apiIngresosComparativoSemana(),
            'topDays'   => $this->apiTopDiasMes(),
            'topWeek'   => $this->apiTopDiasSemanaPromedio($anio, $mes, $udn)
        ];
    }

    public function apiLinearPromediosDiario($anio = null, $mes = null, $udn = null) {
        $anio = $anio ?? (isset($_POST['anio']) ? (int) $_POST['anio'] : date('Y'));
        $mes  = $mes  ?? (isset($_POST['mes'])  ? (int) $_POST['mes']  : date('m'));
        $udn  = $udn  ?? (isset($_POST['udn'])  ? (int) $_POST['udn']  : 1);

        $diasMes = cal_days_in_month(CAL_GREGORIAN, $mes, $anio);
        $labels  = [];
        $tooltip = [];
        $dataAlimentos = [];
        $dataBebidas   = [];

        // Días de la semana en español (Lunes = 1 según ISO-8601)
        $diasSemana = [
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
            7 => 'Domingo'
        ];

        for ($dia = 1; $dia <= $diasMes; $dia++) {
            $fecha = sprintf('%04d-%02d-%02d', $anio, $mes, $dia);

            // Traer ingresos de ese día
            $ventas = $this->getsoftVentas([$udn, $fecha]);

            // Labels (solo el número del día)
            $labels[] = str_pad($dia, 2, "0", STR_PAD_LEFT);

            // Tooltip: "Lunes 09"
            $fechaObj = new DateTime($fecha);
            $diaSemana = $diasSemana[(int)$fechaObj->format('N')]; // N = 1 (Lunes) a 7 (Domingo)
            $tooltip[] = $diaSemana . " " . $fechaObj->format('d');

            // Valores
            $dataAlimentos[] = isset($ventas['alimentos']) ? (float)$ventas['alimentos'] : 0;
            $dataBebidas[]   = isset($ventas['bebidas'])   ? (float)$ventas['bebidas']   : 0;
        }

        return [
            'labels' => $labels,
            'tooltip' => $tooltip,
            'datasets' => [
                [
                    'label' => 'Alimentos',
                    'data'  => $dataAlimentos,
                    'borderColor' => '#4CAF50',
                    'backgroundColor' => 'rgba(76, 175, 80, 0.2)',
                    'fill' => true,
                    'tension' => 0.3,
                    'pointRadius' => 4,
                    'pointBackgroundColor' => '#4CAF50'
                ],
                [
                    'label' => 'Bebidas',
                    'data'  => $dataBebidas,
                    'borderColor' => '#2196F3',
                    'backgroundColor' => 'rgba(33, 150, 243, 0.2)',
                    'fill' => true,
                    'tension' => 0.3,
                    'pointRadius' => 4,
                    'pointBackgroundColor' => '#2196F3'
                ]
            ]
        ];
    }


    public function apiDashBoard($response, $udn) {
        // Inicializar variables
        $ventaMes       = 0;
        $clientes       = 0;
        $chequePromedio = 0;

        foreach ($response as $item) {
            switch ($item['id']) {
                case 'totalGeneral': // Hotel
                case 'totalGralAyB': // Restaurantes
                    $ventaMes = $item['actual']['valor'];
                    break;
                case 'totalHabitaciones': // Clientes
                    $clientes = $item['actual']['valor'];
                    break;
                case 'chequePromedio':      // Hotel
                case 'chequePromedioAyB':   // Restaurante
                    $chequePromedio = $item['actual']['valor'];
                    break;
            }
        }

         $ventasDia    = $this->getVentasDelDia([$_POST['udn']]);

        return [
            'ventaMes'       => evaluar($ventaMes),
            'Clientes'       => $clientes,
            'ChequePromedio' => evaluar($chequePromedio),
            'ventaDia'      =>  '$ '.$ventasDia,
        ];
    }

    function comparativaChequePromedio() {

        $mesActual = $_POST['mes1'];
        $yearNow   = $_POST['anio1'];
        $yearOld   = $_POST['anio2'];

        $dataA = $this->getComparativaChequePromedio([$_POST['mes1'], $yearNow,$_POST['udn']]);
        $dataB = $this->getComparativaChequePromedio([$_POST['mes2'], $yearOld,$_POST['udn']]);

        $dataset = [
            'labels' => ['A&B', 'Alimentos', 'Bebidas'],
            'A' => [
                (float) $dataA['AyB'],
                (float) $dataA['Alimentos'],
                (float) $dataA['Bebidas']
            ],
            'B' => [
                (float) $dataB['AyB'],
                (float) $dataB['Alimentos'],
                (float) $dataB['Bebidas']
            ]
        ];


        return [
            'dataset' => $dataset,
            'anioA' => $yearNow,
            'anioB' => $yearOld,
        ];


       
    }


    public function apiResumenIngresosPorDia($anio = null, $mes = null, $udn = null) {
        $rows = [];
        $anio = $anio ?? (isset($_POST['anio']) ? (int) $_POST['anio'] : date('Y'));
        $mes  = $mes  ?? (isset($_POST['mes'])  ? (int) $_POST['mes']  : date('m'));
        $udn  = $udn  ?? (isset($_POST['udn'])  ? (int) $_POST['udn']  : 1);

        $days = [
            2 => 'Lunes',
            3 => 'Martes',
            4 => 'Miércoles',
            5 => 'Jueves',
            6 => 'Viernes',
            7 => 'Sábado',
            1 => 'Domingo'
        ];

        foreach ($days as $noDia => $dayName) {
            $lsDays = $this->getIngresosDayOfWeek([$udn, $anio, $mes, $noDia]);

            foreach ($lsDays as $item) {
                if ($udn == 1) {
                    $rows[] = [
                        'id'        => $noDia,
                        'fecha'     => $item['fecha'],
                        'dia'       => $dayName,
                        'Hospedaje' => $item['Hospedaje'],
                        'AyB'       => $item['AyB'],
                        'Diversos'  => $item['Diversos'],
                        'clientes'  => $item['noHabitaciones'],
                        'total'     => $item['total']
                    ];
                } 
                // elseif ($udn == 5) {
                //     $rows[] = [
                //         'id'          => $noDia,
                //         'fecha'       => $item['fecha'],
                //         'dia'         => $dayName,
                //         'alimentos'   => $item['alimentos'],
                //         'bebidas'     => $item['bebidas'],
                //         // 'complementos'=> $item['complementos'],
                //         'total'       => $item['total']
                //     ];
                // }
                
                else {
                    $rows[] = [
                        'id'        => $noDia,
                        'fecha'     => $item['fecha'],
                        'dia'       => $dayName,
                        'alimentos' => $item['alimentos'],
                        'bebidas'   => $item['bebidas'],
                        'clientes'  => $item['noHabitaciones'],
                        'total'     => $item['totalGral'] ?? $item['total'] // fallback por seguridad
                    ];
                }
            }
        }

        return [
            'status' => 200,
            'data'   => $rows
        ];
    }

   public function apiIngresosComparativoSemana($anio1 = null, $mes1 = null, $anio2 = null, $mes2 = null, $udn = null) {
        $anio1 = $anio1 ?? (isset($_POST['anio1']) ? (int) $_POST['anio1'] : date('Y'));
        $mes1  = $mes1  ?? (isset($_POST['mes1'])  ? (int) $_POST['mes1']  : date('m'));
        $anio2 = $anio2 ?? (isset($_POST['anio2']) ? (int) $_POST['anio2'] : (date('Y') - 1));
        $mes2  = $mes2  ?? (isset($_POST['mes2'])  ? (int) $_POST['mes2']  : date('m'));
        $udn   = $udn   ?? (isset($_POST['udn'])   ? (int) $_POST['udn']   : 1);

        // Período 1
        $apiA = $this->apiResumenIngresosPorDia($anio1, $mes1, $udn);
        $totalesA = [];
        $conteosA = [];
        foreach ($apiA['data'] as $row) {
            $dia = $row['dia'];
            if (!isset($totalesA[$dia])) {
                $totalesA[$dia] = 0;
                $conteosA[$dia] = 0;
            }
            $totalesA[$dia] += $row['total'];
            $conteosA[$dia]++;
        }

        // Período 2
        $apiB = $this->apiResumenIngresosPorDia($anio2, $mes2, $udn);
        $totalesB = [];
        $conteosB = [];
        foreach ($apiB['data'] as $row) {
            $dia = $row['dia'];
            if (!isset($totalesB[$dia])) {
                $totalesB[$dia] = 0;
                $conteosB[$dia] = 0;
            }
            $totalesB[$dia] += $row['total'];
            $conteosB[$dia]++;
        }

        // Etiquetas en orden fijo
        $labels = ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"];
        $dataA  = [];
        $dataB  = [];

        foreach ($labels as $dia) {
            $promedioA = isset($conteosA[$dia]) && $conteosA[$dia] > 0 
                ? $totalesA[$dia] / $conteosA[$dia] 
                : 0;
            $promedioB = isset($conteosB[$dia]) && $conteosB[$dia] > 0 
                ? $totalesB[$dia] / $conteosB[$dia] 
                : 0;
                
            $dataA[] = round($promedioA, 2);
            $dataB[] = round($promedioB, 2);
        }

        return [
            'labels' => $labels,
            'dataA'  => $dataA,
            'dataB'  => $dataB,
            'yearA'  => $anio2,
            'yearB'  => $anio1

        ];
    }

    public function apiTopDiasMes($anio = null, $mes = null, $udn = null) {
        $anio = $anio ?? (isset($_POST['anio']) ? (int) $_POST['anio'] : date('Y'));
        $mes  = $mes  ?? (isset($_POST['mes'])  ? (int) $_POST['mes']  : date('m'));
        $udn  = $udn  ?? (isset($_POST['udn'])  ? (int) $_POST['udn']  : 1);

        // Obtener todos los registros diarios
        $apiData = $this->apiResumenIngresosPorDia($anio, $mes, $udn);
        $rows = $apiData['data'];

        // Ordenar por total descendente
        usort($rows, function($a, $b) {
            return $b['total'] <=> $a['total'];
        });

        // Tomar solo los 5 primeros
        $top5 = array_slice($rows, 0, 5);

        // Definir notas según ranking
        $notas = ["Mejor día", "Excelente", "Muy bueno", "Bueno", "Regular"];

        // Armar estructura final
        $data = [];
        foreach ($top5 as $i => $item) {
            $fechaObj = new DateTime($item['fecha']);
            $data[] = [
                'fecha'    => $fechaObj->format('d M'),
                'dia'      => $item['dia'],
                'clientes' => $item['clientes'],
                'total'    => $item['total'],
                'nota'     => $notas[$i] ?? ""
            ];
        }

        // Texto del subtítulo
        $mesTexto = strftime('%B', mktime(0, 0, 0, $mes, 1));
        $subtitle = ucfirst($mesTexto) . " " . $anio . " - Top 5";

        return $data;
    }

    public function apiTopDiasSemanaPromedio($anio = null, $mes = null, $udn = null) {
        $anio = $anio ?? (isset($_POST['anio']) ? (int) $_POST['anio'] : date('Y'));
        $mes  = $mes  ?? (isset($_POST['mes'])  ? (int) $_POST['mes']  : date('m'));
        $udn  = $udn  ?? (isset($_POST['udn'])  ? (int) $_POST['udn']  : 1);

        // Obtener todos los registros diarios del mes
        $apiData = $this->apiResumenIngresosPorDia($anio, $mes, $udn);
        $rows = $apiData['data'];

        // Agrupar por día de la semana
        $diasSemana = [
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
            7 => 'Domingo'
        ];

        $acumulados = [];
        $clientes   = [];
        $conteos    = [];

        foreach ($rows as $item) {
            $fechaObj = new DateTime($item['fecha']);
            $diaNum   = (int)$fechaObj->format('N'); // 1 (Lunes) ... 7 (Domingo)

            $total    = isset($item['total']) ? (float)$item['total'] : 0;
            $cltes    = isset($item['clientes']) ? (int)$item['clientes'] : 0;

            if (!isset($acumulados[$diaNum])) {
                $acumulados[$diaNum] = 0;
                $clientes[$diaNum]   = 0;
                $conteos[$diaNum]    = 0;
            }

            $acumulados[$diaNum] += $total;
            $clientes[$diaNum]   += $cltes;
            $conteos[$diaNum]    += 1;
        }

        // Calcular promedios
        $promedios = [];
        foreach ($acumulados as $diaNum => $suma) {
            $promedioTotal    = $conteos[$diaNum] > 0 ? $suma / $conteos[$diaNum] : 0;
            $promedioClientes = $conteos[$diaNum] > 0 ? $clientes[$diaNum] / $conteos[$diaNum] : 0;

            $promedios[] = [
                'dia'        => $diasSemana[$diaNum],
                'promedio'   => round($promedioTotal, 2),
                'clientes'   => (int)$clientes[$diaNum],
                'promCltes'  => round($promedioClientes, 2),
                'veces'      => $conteos[$diaNum]
            ];
        }

        // Ordenar por promedio descendente
        usort($promedios, function($a, $b) {
            return $b['promedio'] <=> $a['promedio'];
        });

        return $promedios;
    }










    // Comparativas Mensuales.

    function getCalculoPorConcepto($key, $ventas, $days) {

        $chequePromedioHospedaje = ($ventas['totalHabitaciones'] != 0) ? $ventas['totalHospedaje'] / $ventas['totalHabitaciones'] : 0;
        $chequePromedioAyB       = ($ventas['totalHabitaciones'] != 0) ? $ventas['totalAyB'] / $ventas['totalHabitaciones'] : 0;
        $chequeDiversos          = ($ventas['totalHabitaciones'] != 0) ? $ventas['totalDiversos'] / $ventas['totalHabitaciones'] : 0;
        $chequePromedio          = ($ventas['totalHabitaciones'] != 0) ? $ventas['totalGeneral'] / $ventas['totalHabitaciones'] : 0;

        switch ($key) {

            case 'porcAgrupacion':
                return ($days != 0) ? ($ventas['totalHabitaciones'] / $days / 12) * 100 : 0;

            case 'chequeDiversos':
                return $chequeDiversos;

            case 'chequePromedioHospedaje':
                return $chequePromedioHospedaje;

            case 'chequePromedioAyB':
                return $chequePromedioAyB;

            case 'chequePromedioAlimentos':
                return ($ventas['totalHabitaciones'] != 0) ? $ventas['totalAlimentos'] / $ventas['totalHabitaciones'] : 0;

            case 'chequePromedioBebidas':
                return ($ventas['totalHabitaciones'] != 0) ? $ventas['totalBebidas'] / $ventas['totalHabitaciones'] : 0;

            case 'chequePromedio':
                return $chequePromedio;

            case 'tarifaEfectiva':
                return ($days != 0) ? ($ventas['totalGeneral'] / 12) / $days : 0;

            default:
                return isset($ventas[$key]) ? $ventas[$key] : null;
        }
    }




    // Comparativas mensuales.
    function listComparative() {
        $type    = $_POST['type'];


        $data = ($type == "1")
            ? $this->ComparativaMensual()
            :  $this->ComparativaMensualPromedios();

        return $data;
    }

    function ComparativaMensual(){

        $Mes          = $_POST['mes'];
        $mesCompleto  = $_POST['mesCompleto'];
        $Anio         = $_POST['anio'];
        $AnioAnterior = $Anio - 1;
        $days         = listDays();
        $__row        = [];

        if($_POST['udn'] == 1){
            $consultas = [
                'totalGeneral'   => 'totalGeneral',
                'totalHospedaje' => 'totalHospedaje',
                'totalAyB'       => 'AyB',
                'totalDiversos'  => 'totalDiversos'
            ];
        } else {
            $consultas = [
                'totalAyB'       => 'AyB',
                'totalAlimentos' => 'total Alimentos',
                'totalBebidas'   => 'total Bebidas',
            ];
        }

        $month = [
            'currentMonth'   => ['year'=> $Anio, 'month'=>$_POST['mes']],
            'previousMonth'  => ['year'=> $AnioAnterior, 'month'=>$_POST['mes']],
        ];

        foreach ($consultas as $key => $value) {
            $__row[] = ['id' => $key, 'dayOfWeek' => $value, 'colgroup' => true];

            foreach ($days as $noDias => $Days){
                $campos = ['id' => $noDias, 'dayOfWeek' => $Days];
                $meses = [];

                foreach($month as $titulo => $_date){

                    $ingresoDiario = $this->ingresoPorDia([$_POST['udn'],$_date['year'],$_date['month'], $noDias]);

                    $promedio = ($ingresoDiario['totalDias'] != 0)
                        ? $ingresoDiario[$key] / $ingresoDiario['totalDias']
                        : $ingresoDiario[$key];

                    $meses[$titulo] = ['text' => evaluar($promedio), 'val' => $promedio,'class' => 'text-end'];
                }

                $diferencia   = evaluar($meses['currentMonth']['val'] - $meses['previousMonth']['val']);

                $meses['dif'] = [ 'html'=> $diferencia , 'class' => 'text-end px-2'];
                $meses['opc'] = 0;

                $__row[] = array_merge($campos, $meses);
            }
        }

        return [
            'view'  => $data,
            // 'thead' => [],
            'thead' => ['DIA', $mesCompleto . ' / ' . $Anio, $mesCompleto . ' / ' . $AnioAnterior, 'DIFERENCIA'],
            'row'   => $__row
        ];
    }

    function ComparativaMensualPromedios(){
          $Mes          = $_POST['mes'];
          $mesCompleto  = $_POST['mesCompleto'];
          $Anio         = $_POST['anio'];
          $AnioAnterior = $Anio - 1;
          $days         = listDays();
          $__row = [];

        $month = [
            'currentMonth' => ['year'=>$Anio, 'month'=>$_POST['mes']],
            'previousMonth'=> ['year'=>$AnioAnterior, 'month'=>$_POST['mes']],
        ];

        $consultas = ['Cheque Prom. Hosp' => 'chequePromHospedaje'];

        foreach ($consultas as $key => $value) {
            $row  = [];
            $row[] = ['id'=> 0,'dayOfWeek'=> $key,'colgroup'=> true];

            foreach ($days as $noDias => $Days){
                $campos = ['id' => $noDias, 'dayOfWeek' => $Days];
                $meses = [];

                foreach($month as $titulo => $getFecha){
        //     //         $lsPromedios = $this->lsPromediosAcomulados(['Anio' => $getFecha['year'], 'Mes' => $getFecha['month']]);
        //     //         $val = getPromedioDia(($noDias-1),$lsPromedios,$value);

                    $meses[$titulo] = ['text' => evaluar($val,''), 'val' => $val];
                }

                $meses['dif'] = 0;
                $meses['opc'] = 0;
                $row[] = array_merge($campos,$meses);
            }

            $__row = $row;


        //     // $res = pintarValPromedios($row,['currentMonth','previousMonth']);
            // $__row = array_merge($__row, $res);
        }

        return [
            // 'thead' => ['DIA', $mesCompleto . ' / ' . $Anio, $mesCompleto . ' / ' . $AnioAnterior, 'DIFERENCIA'],
            'row'   => $__row,
            // 'data'  => $lsPromedios,
            'ok'    => $ok
        ];
    }

     function lsPromediosAcomulados($array){


        $udn = $_POST['UDN'];
        # -- variables para fechas
        $fi = new DateTime($array['Anio'] . '-' . $array['Mes'] . '-01');

        $hoy = clone $fi;

        $hoy->modify('last day of this month');
        $__row = [];


        while ($fi <= $hoy) {
            $idRow++;
            $fecha = $fi->format('Y-m-d');

            // $softVentas = $this->getsoft_ventas([$udn,$fecha]);
            // $opc        = ($softVentas['noHabitaciones']) ? 0 : 1;


            // $noHabitaciones       += $softVentas['noHabitaciones'];
            // $total                += $softVentas['Hospedaje'] + $softVentas['AyB'] + $softVentas['Diversos'];

            // $hospedaje         += $softVentas['Hospedaje'];
            // $PromedioHospedaje  = $hospedaje / $noHabitaciones;

            // $AyB               += $softVentas['AyB'];
            // $PromedioAyB        = $AyB / $noHabitaciones;

            // $tarifaEfectiva     = ($idRow ==1 ) ? ($total/12) : (($total/12)/ ($idRow-1)) ;

            // $ingresosDiversos  += $softVentas['Diversos'];
            // $PromedioDiversos   = $ingresosDiversos / $noHabitaciones;


            // $tarifaEfectivaDiaria  = $total / 12;
            // $porcentajeOcupacion   = evaluar($noHabitaciones / 12, '%');

            $__row[] = array(

                'id'                    => $idRow,
                'fecha'                 => $fecha,
                'dia'                   => formatSpanishNoDay($fecha),

                'Hospedaje'              => $hospedaje,
                'chequePromHospedaje'    => $PromedioHospedaje,
                'chequePromedioAyB'      => $PromedioAyB,
                'chequePromedioDiversos' => $PromedioDiversos,

                'tarifaEfectiva'         => $tarifaEfectiva,




            );

        //     // endif;

            $fi->modify('+1 day');
        }


        #encapsular datos
        return $__row;




    }


    // Promedios Diarios


    // Promedios acomulados.
    function listAcumulados(){
        $udn = $_POST['udn'];
        # -- variables para fechas
        $fi = new DateTime($_POST['anio'] . '-' . $_POST['mes'] . '-01');

        $hoy = clone $fi;

        $hoy->modify('last day of this month');
        $__row = [];


        while ($fi <= $hoy) {
            $idRow++;
            $fecha = $fi->format('Y-m-d');

            $softVentas = $this->getsoft_ventas([$udn,$fecha]);
            $opc        = ($softVentas['noHabitaciones']) ? 0 : 1;


            $noHabitaciones    += $softVentas['noHabitaciones'];


        //     $PromedioDiversos = $softVentas['Diversos'] / $noHabitaciones;

        //     $tarifaEfectivaDiaria = $total / 12;
        //     $porcentajeOcupacion = evaluar($noHabitaciones / 12, '%');


           if($udn == 1):

                $total             += $softVentas['Hospedaje'] + $softVentas['AyB'] + $softVentas['Diversos'];
                $hospedaje         += $softVentas['Hospedaje'];
                $PromedioHospedaje  = $hospedaje / $noHabitaciones;
                $AyB               += $softVentas['AyB'];
                $PromedioAyB        = $AyB / $noHabitaciones;
                $tarifaEfectiva     = ($idRow ==1 ) ? evaluar($total/12) : evaluar(($total/12)/ ($idRow-1)) ;
                $ingresosDiversos  += $softVentas['Diversos'];
                $PromedioDiversos   = $ingresosDiversos / $noHabitaciones;


                $__row[] = array(

                    'id'                       => $idRow,
                    'fecha'                    => $fecha,
                    'dia'                      => formatSpanishDate($fecha),
                    'Habitaciones'             => $noHabitaciones,
                    'Suma de ingresos'         => $total,
                    'Hospedaje'                => $hospedaje,
                    'chequePromHospedaje'      => ['text'=>evaluar($PromedioHospedaje),'value'=>$PromedioHospedaje],
                    'Tarifa efectiva acum.'    => $tarifaEfectiva,

                    'Ingreso AyB'              => evaluar($AyB),
                    'Cheque Promedio AyB'      => evaluar($PromedioAyB),
                    'Ingreso Diversos'         => evaluar($ingresosDiversos),
                    'Cheque Promedio Diversos' => evaluar($PromedioDiversos),
                    // 'Costo de amenididad'      => '0.00',
                    // 'Costo de AyB '            => '0.00',
                    // 'Costo Diversos diario'    => '0.00',


                    'opc' => $opc
                );

            else:
                  // Calculo.
                  $total           += $softVentas['totalAyB'];
                  $ventasAlimentos += $softVentas['alimentos'];
                  $ventasBebidas   += $softVentas['bebidas'];




                  $__row[] = array(

                    'id'                    => $idRow,
                    'fecha'                 => $fecha,
                    'dia'                   => formatSpanishDate($fecha),
                    'Clientes'              => $noHabitaciones,

                    'Ventas AyB'            => evaluar($total),
                    'Ventas Alimentos'      => evaluar($ventasAlimentos),
                    'Cheque Prom Alimentos' => evaluar(0),

                    'Ventas Bebidas'      => evaluar($ventasBebidas),
                    'Cheque Prom Bebidas' => '',



                    // 'Costo de amenididad'      => '0.00',
                    // 'Costo de AyB '            => '0.00',
                    // 'Costo Diversos diario'    => '0.00',


                    'opc' => 0
                );



            endif;

            $fi->modify('+1 day');
        }


        #encapsular datos
        return [

            "row" => $__row,
            "thead" => ''
        ];


    }




    // Aux.

    function get_th_ingresos() {
        switch ($_POST['udn']) {
            case 1:
                return ['Fecha', 'Dia', 'Hospedaje', 'AYB', 'DIVERSOS', 'No. Habitaciones', 'Total'];
            case 5:
                return ['Fecha', 'Dia', 'Alimentos', 'Bebidas', 'Complementos', 'Total'];
            default:
                return ['Fecha', 'Dia', 'Alimentos', 'Bebidas', 'Clientes', 'Total'];
        }

    }

     function getSalesTitle() {
        switch ($_POST['udn']) {
            case 1:
                return ['Fecha', 'Dia', 'Estado', 'No. Habitaciones','Hospedaje', 'AYB', 'DIVERSOS', 'Total'];
            case 5:
                return ['Fecha', 'Dia', 'Estado','Clientes','Alimentos', 'Bebidas', 'Guarniciones','Sales', 'Domicilio','Total'];
            default:
                return ['Fecha', 'Dia', 'Estado', 'Clientes','Alimentos', 'Bebidas', 'Total'];
        }

    }

}


// Complements.
function createdGroups($groups, $ventas, $id) {
    $row = [];

    foreach ($groups as $key => $nameGroup) {
        $value = evaluar($ventas[$nameGroup] ?? '', '');
        if ($key == 0) $value = $ventas[$nameGroup];

        $nameKey = $nameGroup === 'No habitaciones' ? 'clientes' : $nameGroup;

        $row[$nameKey] = [
            'html' => createElement('input', [
                'name'    => $nameKey,
                'value'   => $value,
                'onkeyup' => "ingresosDiarios.setVentas(event, $id)",
            ]),
            'style' => 'padding:0; margin:0;'
        ];
    }

    return $row;
}


function createElement($tag, $attributes = [], $text = null) {
    $defaultAttributes = [
        'placeholder' => '',
        'class'       => '
            w-full bg-gray-50
            text-slate-700 text-end text-sm  px-3 py-2
            focus:border-gray-400
            hover:border-slate-300 hover:bg-gray-100
        ',
    ];

    $attributes = array_merge($defaultAttributes, $attributes);
    $element = "<$tag";

    foreach ($attributes as $key => $value) {
        $element .= " $key=\"" . htmlspecialchars($value) . "\"";
    }

    $element .= ">";

    if ($text !== null) {
        $element .= htmlspecialchars($text);
    }

    // Cierra la etiqueta si no es self-closing
    if (!in_array($tag, ['input', 'img', 'br', 'hr', 'meta', 'link'])) {
        $element .= "</$tag>";
    }

    return $element;
}




// ✅ Instancia final del controlador
$ctrl = new ctrl();
echo json_encode($ctrl->{$_POST['opc']}());
