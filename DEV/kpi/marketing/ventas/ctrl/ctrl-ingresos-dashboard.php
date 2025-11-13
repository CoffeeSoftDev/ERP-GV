<?php
session_start();
if (empty($_POST['opc'])) exit(0);


header("Access-Control-Allow-Origin: *"); // Permite solicitudes de cualquier origen
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // Métodos permitidos
header("Access-Control-Allow-Headers: Content-Type"); // Encabezados permitidos

require_once '../mdl/mdl-dashboard.php';
require_once('../../../../conf/coffeSoft.php');

class ctrl extends mdl {

    public function apiPromediosDiarios() {
        $response = [];

        $anio         = isset($_POST['anio1']) ? (int) $_POST['anio1'] : date('Y');
        $anioAnterior = $anio - 1;
        $mes          = isset($_POST['mes1']) ? (int) $_POST['mes1'] : date('m');
        $udn          = isset($_POST['udn']) ? (int) $_POST['udn'] : 1;

        $meses = [
            'actual'   => ['year' => $_POST['anio1'],        'mes' => $_POST['mes1']],
            'anterior' => ['year' => $_POST['anio2'],'mes' =>$_POST['mes2']]
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
                    'ventas'  =>  [$udn, $fecha['year'], $fecha['mes']],
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
            'meses'     => $meses,
            'dashboard' => $this->apiDashBoard($response, $udn),
            'barras'    => $this->comparativaChequePromedio(),
            'linear'    => $this->apiLinearPromediosDiario($anio, $mes, $udn),
            // 'barDays'   => $this->apiIngresosComparativoSemana(),
            // 'topDays'   => $this->apiTopDiasMes(),
            'topWeek'   => $this->apiTopDiasSemanaPromedio($anio, $mes, $udn),
            'topWeekCheque'=> $this->apiTopChequePromedioSemanal($anio, $mes, $udn)

        ];
    }

    // Cards.

    public function apiDashBoard($response, $udn) {
        $ventaMesActual       = 0;
        $ventaMesAnterior     = 0;
        $clientesActual       = 0;
        $clientesAnterior     = 0;
        $chequePromedioActual = 0;
        $chequePromedioAnterior = 0;

        foreach ($response as $item) {
            switch ($item['id']) {
                case 'totalGeneral':
                case 'totalGralAyB':
                    $ventaMesActual   = $item['actual']['valor'];
                    $ventaMesAnterior = $item['anterior']['valor'];
                    break;
                case 'totalHabitaciones':
                    $clientesActual   = $item['actual']['valor'];
                    $clientesAnterior = $item['anterior']['valor'];
                    break;
                case 'chequePromedio':
                case 'chequePromedioAyB':
                    $chequePromedioActual   = $item['actual']['valor'];
                    $chequePromedioAnterior = $item['anterior']['valor'];
                    break;
            }
        }

        $ventasDia = $this->getVentasDelDia([$udn]);
        $fechaAyer = date('d/m/Y', strtotime('-1 day'));

        $variacionVentas = $this->calcularVariacion($ventaMesActual, $ventaMesAnterior);
        $variacionClientes = $this->calcularVariacion($clientesActual, $clientesAnterior);
        $variacionCheque = $this->calcularVariacion($chequePromedioActual, $chequePromedioAnterior);

        return [
            'ventaDia' => [
                'valor' => $ventasDia,
                'fecha' => $fechaAyer,
                'titulo' => 'Venta del día de ayer',
                'color' => 'text-[#8CC63F]'
            ],
            'ventaMes' => [
                'valor' => evaluar($ventaMesActual),
                'variacion' => $variacionVentas['porcentaje'],
                'mensaje' => $variacionVentas['mensaje'],
                'tendencia' => $variacionVentas['tendencia'],
                'titulo' => 'Venta del Mes',
                'color' => $variacionVentas['tendencia'] === 'up' ? 'text-green-800' : 'text-red-600'
            ],
            'clientes' => [
                'valor' => number_format($clientesActual, 0),
                'variacion' => $variacionClientes['porcentaje'],
                'mensaje' => $variacionClientes['mensaje'],
                'tendencia' => $variacionClientes['tendencia'],
                'titulo' => 'Clientes',
                'color' => 'text-[#103B60]'
            ],
            'chequePromedio' => [
                'valor' => evaluar($chequePromedioActual),
                'variacion' => $variacionCheque['porcentaje'],
                'mensaje' => $variacionCheque['mensaje'],
                'tendencia' => $variacionCheque['tendencia'],
                'titulo' => 'Cheque Promedio',
                'color' => $variacionCheque['tendencia'] === 'up' ? 'text-green-600' : 'text-red-600'
            ]
        ];
    }

    private function calcularVariacion($actual, $anterior) {
        if ($anterior == 0) {
            return [
                'porcentaje' => '0%',
                'mensaje' => 'Sin datos del año anterior',
                'tendencia' => 'neutral'
            ];
        }

        $diferencia = $actual - $anterior;
        $porcentaje = ($diferencia / $anterior) * 100;
        $signo = $porcentaje >= 0 ? '+' : '';
        $tendencia = $porcentaje > 0 ? 'up' : ($porcentaje < 0 ? 'down' : 'neutral');

        return [
            'porcentaje' => $signo . number_format($porcentaje, 1) . '%',
            'mensaje' => $signo . number_format($porcentaje, 1) . '% comparado con el año pasado',
            'tendencia' => $tendencia
        ];
    }

    private function getCalculoPorConcepto($clave, $ventas, $totalDias) {
        switch ($clave) {
            case 'totalGeneral':
                return $ventas['totalGeneral'] ?? 0;
            case 'totalHospedaje':
                return $ventas['totalHospedaje'] ?? 0;
            case 'totalAyB':
                return $ventas['totalAyB'] ?? 0;
            case 'totalGralAyB':
                return $ventas['totalGralAyB'] ?? 0;
            case 'totalAlimentos':
                return $ventas['totalAlimentos'] ?? 0;
            case 'totalBebidas':
                return $ventas['totalBebidas'] ?? 0;
            case 'totalDiversos':
                return $ventas['totalDiversos'] ?? 0;
            case 'totalHabitaciones':
                return $ventas['totalHabitaciones'] ?? 0;
            case 'porcAgrupacion':
                $habitaciones = $ventas['totalHabitaciones'] ?? 0;
                return $habitaciones > 0 ? ($habitaciones / (12 * $totalDias)) * 100 : 0;
            case 'tarifaEfectiva':
                $hospedaje = $ventas['totalHospedaje'] ?? 0;
                $habitaciones = $ventas['totalHabitaciones'] ?? 0;
                return $habitaciones > 0 ? $hospedaje / $habitaciones : 0;
            case 'chequePromedio':
                $total = $ventas['totalGeneral'] ?? 0;
                $habitaciones = $ventas['totalHabitaciones'] ?? 0;
                return $habitaciones > 0 ? $total / $habitaciones : 0;
            case 'chequePromedioHospedaje':
                $hospedaje = $ventas['totalHospedaje'] ?? 0;
                $habitaciones = $ventas['totalHabitaciones'] ?? 0;
                return $habitaciones > 0 ? $hospedaje / $habitaciones : 0;
            case 'chequePromedioAyB':
                $ayb = $ventas['totalAyB'] ?? $ventas['totalGralAyB'] ?? 0;
                $habitaciones = $ventas['totalHabitaciones'] ?? 0;
                return $habitaciones > 0 ? $ayb / $habitaciones : 0;
            case 'chequePromedioAlimentos':
                $alimentos = $ventas['totalAlimentos'] ?? 0;
                $habitaciones = $ventas['totalHabitaciones'] ?? 0;
                return $habitaciones > 0 ? $alimentos / $habitaciones : 0;
            case 'chequePromedioBebidas':
                $bebidas = $ventas['totalBebidas'] ?? 0;
                $habitaciones = $ventas['totalHabitaciones'] ?? 0;
                return $habitaciones > 0 ? $bebidas / $habitaciones : 0;
            case 'chequePromedioDiversos':
                $diversos = $ventas['totalDiversos'] ?? 0;
                $habitaciones = $ventas['totalHabitaciones'] ?? 0;
                return $habitaciones > 0 ? $diversos / $habitaciones : 0;
            default:
                return 0;
        }
    }


    // Graficos cheque promedio.
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

            // Si no hay datos, crear un registro vacío
            if ($softVentas === null) {
                $softVentas = [
                    'id_venta'       => null,
                    'noHabitaciones' => 0,
                    'Hospedaje'      => 0,
                    'AyB'            => 0,
                    'Diversos'       => 0,
                    'alimentos'      => 0,
                    'bebidas'        => 0,
                    'guarniciones'   => 0,
                    'sales'          => 0,
                    'domicilio'      => 0
                ];
            }

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
    
    function getDailyCheck() {
        $udn      = $_POST['udn']    ?? null;
        $anio1    = $_POST['anio1']  ?? date('Y');
        $mes1     = $_POST['mes1']   ?? date('m');
        $anio2    = $_POST['anio2']  ?? date('Y') - 1;
        $mes2     = $_POST['mes2']   ?? date('m');
        $category = strtolower(trim($_POST['category'] ?? 'todas'));

        $apiActual   = $this->apiIngresosTotales($udn, $anio1, $mes1);
        $apiAnterior = $this->apiIngresosTotales($udn, $anio2, $mes2);

        $rowsActual   = $apiActual['data'] ?? [];
        $rowsAnterior = $apiAnterior['data'] ?? [];

        // Días en español
        $diasES = [
            'Monday'    => 'Lunes',
            'Tuesday'   => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday'  => 'Jueves',
            'Friday'    => 'Viernes',
            'Saturday'  => 'Sábado',
            'Sunday'    => 'Domingo'
        ];

        $weeklyActual   = array_fill_keys(array_values($diasES), ['total' => 0, 'clientes' => 0]);
        $weeklyAnterior = array_fill_keys(array_values($diasES), ['total' => 0, 'clientes' => 0]);

        $processData = function ($rows, &$weeklyData) use ($category, $diasES) {
            foreach ($rows as $row) {
                if (empty($row['fecha'])) continue;

                $dayEnglish = ucfirst(strtolower(date('l', strtotime($row['fecha']))));
                $dayName    = $diasES[$dayEnglish] ?? $dayEnglish;

                $clientes = isset($row['clientes']) ? intval($row['clientes']) : 0;
                $total    = 0;

                if ($category == 'todas' || $category == '') {
                    $total = isset($row['total']) ? floatval($row['total']) : 0;
                } else {
                    foreach ($row as $key => $value) {
                        if (strtolower($key) == $category) {
                            $total = floatval($value);
                            break;
                        }
                    }
                }

                if (isset($weeklyData[$dayName])) {
                    $weeklyData[$dayName]['total']    += $total;
                    $weeklyData[$dayName]['clientes'] += $clientes;
                }
            }
        };

        $processData($rowsActual, $weeklyActual);
        $processData($rowsAnterior, $weeklyAnterior);

        $labels = [];
        $dataA  = [];
        $dataB  = [];

        foreach ($diasES as $en => $es) {
            $labels[] = $es;

            $avgActual = $weeklyActual[$es]['clientes'] > 0
                ? round($weeklyActual[$es]['total'] / $weeklyActual[$es]['clientes'], 2)
                : 0;

            $avgAnterior = $weeklyAnterior[$es]['clientes'] > 0
                ? round($weeklyAnterior[$es]['total'] / $weeklyAnterior[$es]['clientes'], 2)
                : 0;

            $dataA[] = $avgAnterior;
            $dataB[] = $avgActual;
        }

        return [
            'status'  => 200,
            'message' => 'Cheque promedio diario comparativo generado correctamente',
            'filter'  => $category,
            'labels'  => $labels,
            'dataA'   => $dataA,
            'dataB'   => $dataB,
            'yearA'   => intval($anio2),
            'yearB'   => intval($anio1),
            'api'     => [
                'actual'   => $apiActual,
                'anterior' => $apiAnterior
            ]
        ];
    }

    // Graficos cheque Prom.
    function apiPromediosDiariosRange() {
        $__row        = [];
        $mesCompleto  = $_POST['monthText'];
        $Anio         = $_POST['anio'];
        $AnioAnterior = $Anio - 1;
        $udn          = $_POST['udn'];
        $mesActual    = $_POST['mes'];
        $rangoMeses   = $_POST['rango'] ?? 1;

        $consultas = [];

        if ($udn == 1):
            $consultas = [
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
            ];
        elseif ($udn == 5):
            $consultas = [
                'totalHabitaciones' => 'Clientes',
                'totalAlimentos'    => 'Cortes',
                'totalBebidas'      => 'Bebidas',
                'totalGuarniciones' => 'Guarniciones',
                'totalSales'        => 'Sales y condimentos',
                'totalDomicilio'    => 'Domicilio',
                'totalGral'         => 'Total',
                'group'             => '',
                'porcAgrupacion'          => '% Ocupacion',
                'tarifaEfectiva'          => 'Tarifa efectiva acumulada',
                'chequePromedio'          => 'Cheque Promedio',
                'chequePromedioHospedaje' => 'chequePromedioHospedaje',
                'chequePromedioAyB'       => 'cheque Promedio AyB',
                'chequePromedioDiversos'  => 'cheque Promedio Diversos',
            ];
        else:
            $consultas = [
                'totalHabitaciones'       => 'Clientes',
                'totalGralAyB'            => 'Ventas AyB',
                'totalAlimentos'          => 'Alimentos',
                'totalBebidas'            => 'Bebidas',
                'group'                   => '',
                'chequePromedioAyB'       => 'Cheque Promedio AyB',
                'chequePromedioAlimentos' => 'Cheque Promedio Alimentos',
                'chequePromedioBebidas'   => 'Cheque Promedio Bebidas',
            ];
        endif;

        $thead = ['Concepto'];
        $month = [];

        for ($i = 0; $i < $rangoMeses; $i++) {
            $currTime = mktime(0, 0, 0, $mesActual - $i, 1, $Anio);
            $prevTime = mktime(0, 0, 0, $mesActual - $i, 1, $AnioAnterior);

            $currYear = date('Y', $currTime);
            $currMonth = date('n', $currTime);
            $prevYear = date('Y', $prevTime);
            $prevMonth = date('n', $prevTime);
            $textMes = ucfirst(strftime('%B', $currTime));

            $thead[] = "$textMes / $currYear";
            $thead[] = "$textMes / $prevYear";

            $month[] = [
                'label'        => $textMes,
                'currentMonth' => ['year' => $currYear, 'month' => $currMonth],
                'previousMonth'=> ['year' => $prevYear, 'month' => $prevMonth],
            ];
        }

        foreach ($consultas as $key => $titulo) {
            $row = [];

            if ($key != 'group') {
                $base = [ 'id' => $key, 'concepto' => $titulo ];

                foreach ($month as $block) {
                    $ventasCurr = $this->ingresosMensuales([$udn, $block['currentMonth']['year'], $block['currentMonth']['month']]);
                    $ventasPrev = $this->ingresosMensuales([$udn, $block['previousMonth']['year'], $block['previousMonth']['month']]);

                    $totalCurr = $this->getCalculoPorConcepto($key, $ventasCurr, cal_days_in_month(CAL_GREGORIAN, $block['currentMonth']['month'], $block['currentMonth']['year']));
                    $totalPrev = $this->getCalculoPorConcepto($key, $ventasPrev, cal_days_in_month(CAL_GREGORIAN, $block['previousMonth']['month'], $block['previousMonth']['year']));

                    $row["{$block['label']}_current"] = [
                        'val'   => $totalCurr,
                        'text'  => ($key == 'totalHabitaciones') ? $totalCurr : evaluar($totalCurr),
                        'class' => 'text-end'
                    ];
                    $row["{$block['label']}_previous"] = [
                        'val'   => $totalPrev,
                        'text'  => ($key == 'totalHabitaciones') ? $totalPrev : evaluar($totalPrev),
                        'class' => 'text-end'
                    ];
                }

                $__row[] = array_merge($base, $row);
            } else {
                $__row[] = ['id' => 0, 'Concepto' => '', 'colgroup' => true];
            }
        }

        return [
            'thead' => $thead,
            'row'   => $__row
        ];
    }

    public function getDatasetRangeConcepto($concepto = null) {
        $res = $this->apiPromediosDiariosRange();
        
        $mesesES = [
            'January' => 'Enero', 'February' => 'Febrero', 'March' => 'Marzo',
            'April' => 'Abril', 'May' => 'Mayo', 'June' => 'Junio',
            'July' => 'Julio', 'August' => 'Agosto', 'September' => 'Septiembre',
            'October' => 'Octubre', 'November' => 'Noviembre', 'December' => 'Diciembre'
        ];
        
        $labels = [];
        $dataA = [];
        $dataB = [];
        $nombre = '';
        $yearA = $_POST['anio'] ?? date('Y');
        $yearB = $yearA - 1;

        $clientesData = [];
        $conceptoData = [];

        foreach ($res['row'] as $item) {
            if (strcasecmp($item['concepto'], 'Clientes') === 0 || strcasecmp($item['concepto'], 'Habitaciones') === 0) {
                foreach ($item as $key => $value) {
                    if (is_array($value) && isset($value['val'])) {
                        $clientesData[$key] = floatval($value['val']);
                    }
                }
            }

            if (strcasecmp($item['concepto'], $concepto) === 0) {
                $nombre = $item['concepto'];

                foreach ($item as $key => $value) {
                    if (is_array($value) && isset($value['val'])) {
                        $conceptoData[$key] = floatval($value['val']);
                        
                        if (strpos($key, '_current') !== false) {
                            $mesLabel = str_replace('_current', '', $key);
                            if (!in_array($mesLabel, $labels)) {
                                $labels[] = isset($mesesES[$mesLabel]) ? $mesesES[$mesLabel] : $mesLabel;
                            }
                        }
                    }
                }
            }
        }

        foreach ($conceptoData as $key => $totalVentas) {
            $clientes = isset($clientesData[$key]) ? $clientesData[$key] : 0;
            
            if ($clientes > 0) {
                $chequePromedio = $totalVentas / $clientes;
            } else {
                $chequePromedio = 0;
            }

            if (strpos($key, '_current') !== false) {
                $dataB[] = round($chequePromedio, 2);
            } elseif (strpos($key, '_previous') !== false) {
                $dataA[] = round($chequePromedio, 2);
            }
        }

        $labels = array_reverse($labels);
        $dataA = array_reverse($dataA);
        $dataB = array_reverse($dataB);

        return [
            'title'  => "Comparativa Anual de Cheque Promedio ( $nombre ) ",
            'labels' => $labels,
            'dataA'  => $dataB,
            'dataB'  => $dataA,
            'yearA'  => intval($yearB),
            'yearB'  => intval($yearA)
        ];
    }

    function getPromediosDiariosRange(){
        $response = $this->apiPromediosDiariosRange();
        $concepto = $_POST['concepto'] ;
        $concepto = ucfirst(strtolower(trim($concepto)));
        $grafica  = $this->getDatasetRangeConcepto($concepto);

        return[
            'dataset' => $grafica,
            'range'   => $response,
            'concepto' => $concepto
        ];
    }

    // graficos barra.
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

    // Grafico Linear.
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

    // Top dias 
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
                } elseif ($udn == 5) {
                    $rows[] = [
                        'id'          => $noDia,
                        'fecha'       => $item['fecha'],
                        'dia'         => $dayName,
                        'alimentos'   => $item['alimentos'],
                        'bebidas'     => $item['bebidas'],
                        'complementos'=> $item['complementos'],
                        'total'       => $item['total']
                    ];
                } else {
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

    // Top cheque promedio

    public function apiTopChequePromedioSemanal($anio = null, $mes = null, $udn = null) {

        $anio = $anio ?? (isset($_POST['anio']) ? (int) $_POST['anio'] : date('Y'));
        $mes  = $mes  ?? (isset($_POST['mes'])  ? (int) $_POST['mes']  : date('m'));
        $udn  = $udn  ?? (isset($_POST['udn'])  ? (int) $_POST['udn']  : 1);

        $apiData = $this->apiResumenIngresosPorDia($anio, $mes, $udn);
        $rows = $apiData['data'];

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
            $diaNum   = (int)$fechaObj->format('N');

            $total = isset($item['total']) ? (float)$item['total'] : 0;
            $cltes = isset($item['clientes']) ? (int)$item['clientes'] : 0;

            if (!isset($acumulados[$diaNum])) {
                $acumulados[$diaNum] = 0;
                $clientes[$diaNum]   = 0;
                $conteos[$diaNum]    = 0;
            }

            $acumulados[$diaNum] += $total;
            $clientes[$diaNum]   += $cltes;
            $conteos[$diaNum]    += 1;
        }

        $ranking = [];
        foreach ($acumulados as $diaNum => $sumaTotal) {
            $totalClientes = $clientes[$diaNum];
            $chequePromedio = $totalClientes > 0 ? $sumaTotal / $totalClientes : 0;

            $ranking[] = [
                'dia'              => $diasSemana[$diaNum],
                'cheque_promedio'  => round($chequePromedio, 2),
                'clientes'         => $totalClientes,
                'veces'            => $conteos[$diaNum]
            ];
        }

        usort($ranking, function($a, $b) {
            return $b['cheque_promedio'] <=> $a['cheque_promedio'];
        });

        return $ranking;
    }

    function getClientesPorSemana() {
        $udn   = $_POST['udn']   ?? null;
        $anio1 = $_POST['anio1'] ?? date('Y');
        $mes1  = $_POST['mes1']  ?? date('m');
        $anio2 = $_POST['anio2'] ?? date('Y') - 1;
        $mes2  = $_POST['mes2']  ?? date('m');

        $apiActual   = $this->apiIngresosTotales($udn, $anio1, $mes1);
        $apiAnterior = $this->apiIngresosTotales($udn, $anio2, $mes2);

        $rowsActual   = $apiActual['data'] ?? [];
        $rowsAnterior = $apiAnterior['data'] ?? [];

        $diasES = [
            'Monday'    => 'Lunes',
            'Tuesday'   => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday'  => 'Jueves',
            'Friday'    => 'Viernes',
            'Saturday'  => 'Sábado',
            'Sunday'    => 'Domingo'
        ];

        $weeklyActual   = array_fill_keys(array_values($diasES), 0);
        $weeklyAnterior = array_fill_keys(array_values($diasES), 0);

        $processData = function ($rows, &$weeklyData) use ($diasES) {
            foreach ($rows as $row) {
                if (empty($row['fecha'])) continue;

                $dayEnglish = ucfirst(strtolower(date('l', strtotime($row['fecha']))));
                $dayName    = $diasES[$dayEnglish] ?? $dayEnglish;

                $clientes = isset($row['clientes']) ? intval($row['clientes']) : 0;

                if (isset($weeklyData[$dayName])) {
                    $weeklyData[$dayName] += $clientes;
                }
            }
        };

        $processData($rowsActual, $weeklyActual);
        $processData($rowsAnterior, $weeklyAnterior);

        $labels = [];
        $dataA  = [];
        $dataB  = [];

        foreach ($diasES as $en => $es) {
            $labels[] = $es;
            $dataA[]  = $weeklyAnterior[$es];
            $dataB[]  = $weeklyActual[$es];
        }

        return [
            'status'  => 200,
            'message' => 'Total de clientes por día de la semana generado correctamente',
            'labels'  => $labels,
            'dataA'   => $dataA,
            'dataB'   => $dataB,
            'yearA'   => intval($anio2),
            'yearB'   => intval($anio1)
        ];
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
