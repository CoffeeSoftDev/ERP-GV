<?php

if (empty($_POST['opc'])) exit(0);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

require_once '../mdl/mdl-dashboard-order.php';
require_once '../../../../conf/coffeSoft.php';
class ctrl extends mdl {

    function init() {

        return [
            'udn' => $this->lsUDN()
        ];
    }

    function apiPromediosDiarios() {
        $udn  = $_POST['udn'];
        $mes  = isset($_POST['mes']) ? $_POST['mes'] : null;
        $anio = $_POST['anio'];
        // $tipoPeriodo = isset($_POST['tipoPeriodo']) ? $_POST['tipoPeriodo'] : 'mes';
        $tipoPeriodo = 'mes';
        // Ajustar datos según el tipo de periodo
        if ($tipoPeriodo === 'anio') {
            // Consulta por año completo
            $dashboard = $this->getDashboardDataByYear($udn, $anio);
            $barChart = $this->getBarChartDataByYear($udn, $anio);
            $channelRanking = $this->getChannelRankingDataByYear($udn, $anio);
        } else {

            // Consulta por mes específico
            $dashboard      = $this->detailsCard($udn, $mes, $anio);
            $barChart       = $this->getBarChartData($udn, $anio);
            $channelRanking = $this->getChannelRankingData($udn, $mes, $anio);
        }

        $lineChart = $this->getLineChartData($udn, $anio);
        $monthlyPerformance = $this->getMonthlyPerformanceData($udn, $anio);

        return [
            'dashboard'          => $dashboard,
            'lineChart'          => $lineChart,
            'barChart'           => $barChart,
            'channelRanking'     => $channelRanking,
            'monthlyPerformance' => $monthlyPerformance,
            'tipoPeriodo'        => $tipoPeriodo
        ];
    }

    private function getDashboardData($udn, $mes, $anio) {
        $cards = $this->getDashboardCards([$udn, $udn, $anio, $mes, $udn, $anio, $mes, $udn, $anio, $mes]);
        $channelData = $this->getChannelPerformance([$udn, $anio, $mes]);
        
        if (empty($cards)) {
            return [
                'ingresosTotales'   => '$0.00',
                'variacionIngresos' => '+0% vs mes anterior',
                'totalPedidos'      => '0',
                'rangeFechas'       => 'enero–diciembre ' . $anio,
                'valorPromedio'     => '$0.00',
                'canalPrincipal'    => 'N/A',
                'porcentajeCanal'   => '0% del total'
            ];
        }

        $card = $cards[0];
        
        // Calcular variación de ingresos (simulada)
        $ventaActual = floatval($card['venta_mes'] ?? 0);
        $variacion = rand(-15, 25); // Simulación de variación
        $variacionTexto = $variacion >= 0 ? "+{$variacion}%" : "{$variacion}%";
        $variacionColor = $variacion >= 0 ? "↑" : "↓";
        
        // Obtener canal principal
        $canalPrincipal = 'N/A';
        $porcentajeCanal = '0%';
        if (!empty($channelData)) {
            $canalPrincipal = $channelData[0]['canal'];
            $porcentajeCanal = number_format(floatval($channelData[0]['porcentaje']), 1) . '% del total';
        }
        
        // Calcular total anual de pedidos
        $totalAnual = $this->getTotalYearOrders([$udn, $anio]);
        
        return [
            'ingresosTotales'   => $this->formatPrice($ventaActual),
            'variacionIngresos' => $variacionColor . ' ' . $variacionTexto . ' vs mes anterior',
            'totalPedidos'      => number_format($totalAnual),
            'rangeFechas'       => 'enero–diciembre ' . $anio,
            'valorPromedio'     => $this->formatPrice($card['cheque_promedio'] ?? 0),
            'canalPrincipal'    => $canalPrincipal,
            'porcentajeCanal'   => $porcentajeCanal
        ];
    }



    private function getLineChartData($udn, $anio) {
        $trends = $this->getMonthlyOrderTrends([$udn, $anio]);
        
        $labels = [];
        $values = [];
        
        // Inicializar todos los meses con 0
        $monthsData = array_fill(1, 12, 0);
        
        foreach ($trends as $trend) {
            $monthsData[$trend['mes']] = intval($trend['total_pedidos']);
        }
        
        for ($i = 1; $i <= 12; $i++) {
            $labels[] = strtoupper(substr(date('M', mktime(0, 0, 0, $i, 1)), 0, 3));
            $values[] = $monthsData[$i];
        }
        
        return [
            'labels' => $labels,
            'values' => $values
        ];
    }

    private function getBarChartData($udn, $anio) {
        $channels = $this->getChannelMonthlyData([$udn, $anio]);
        
        $months = ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'];
        $channelData = [];
        
        // Organizar datos por canal
        $channelNames = ['WhatsApp', 'Meep', 'Ecommerce', 'Facebook', 'Llamada', 'Uber', 'Otro'];
        
        foreach ($channelNames as $channelName) {
            $monthlyData = array_fill(0, 12, 0);
            
            foreach ($channels as $data) {
                if ($data['canal'] === $channelName) {
                    $monthIndex = intval($data['mes']) - 1;
                    $monthlyData[$monthIndex] = floatval($data['total_monto']);
                }
            }
            
            $channelData[] = [
                'name' => $channelName,
                'data' => $monthlyData
            ];
        }
        
        return [
            'months' => $months,
            'channels' => $channelData
        ];
    }

    private function getChannelRankingData($udn, $mes, $anio) {
        $channels = $this->getChannelPerformance([$udn, $anio, $mes]);
        
        $ranking = [];
        foreach ($channels as $channel) {
            $ranking[] = [
                'name' => $channel['canal'],
                'total' => floatval($channel['total_monto']),
                'orders' => intval($channel['total_pedidos']),
                'percentage' => number_format(floatval($channel['porcentaje']), 1)
            ];
        }
        
        return $ranking;
    }

    private function getMonthlyPerformanceData($udn, $anio) {
        $trends = $this->getMonthlyOrderTrends([$udn, $anio]);
        
        $performance = [];
        $previousSales = 0;
        
        foreach ($trends as $index => $trend) {
            $currentSales = floatval($trend['total_ventas']);
            $growth = 0;
            
            if ($previousSales > 0) {
                $growth = (($currentSales - $previousSales) / $previousSales) * 100;
            }
            
            $performance[] = [
                'name' => $trend['nombre_mes'],
                'orders' => intval($trend['total_pedidos']),
                'sales' => $currentSales,
                'growth' => round($growth, 1)
            ];
            
            $previousSales = $currentSales;
        }
        
        return $performance;
    }

    private function getDashboardDataByYear($udn, $anio) {
        $cards = $this->getDashboardCardsByYear([$udn, $anio]);
        
        if (empty($cards)) {
            return [
                'ventaDia' => '$0.00',
                'ventaMes' => '$0.00',
                'Clientes' => '0',
                'ChequePromedio' => '$0.00'
            ];
        }

        $card = $cards[0];
        
        return [
            'ingresosTotales' => $this->formatPrice($card['venta_anio'] ?? 0),
            'totalPedidos' => number_format($card['pedidos_anio'] ?? 0),
            'valorPromedio' => $this->formatPrice($card['cheque_promedio'] ?? 0),
            'canalPrincipal' => $card['canal_principal'] ?? 'N/A',
            'variacionIngresos' => '↑ Año completo',
            'rangeFechas' => "Enero - Diciembre {$anio}"
        ];
    }

    private function getBarChartDataByYear($udn, $anio) {
        $channels = $this->getChannelYearlyData([$udn, $anio]);
        
        $months = ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'];
        $channelData = [];
        
        $channelNames = ['WhatsApp', 'Meep', 'Ecommerce', 'Facebook', 'Llamada', 'Uber', 'Otro'];
        
        foreach ($channelNames as $channelName) {
            $monthlyData = array_fill(0, 12, 0);
            
            foreach ($channels as $data) {
                if ($data['canal'] === $channelName) {
                    $monthIndex = intval($data['mes']) - 1;
                    $monthlyData[$monthIndex] = floatval($data['total_monto']);
                }
            }
            
            $channelData[] = [
                'name' => $channelName,
                'data' => $monthlyData
            ];
        }
        
        return [
            'months' => $months,
            'channels' => $channelData
        ];
    }

    private function getChannelRankingDataByYear($udn, $anio) {
        $channels = $this->getChannelPerformanceByYear([$udn, $anio]);
        
        $ranking = [];
        foreach ($channels as $channel) {
            $ranking[] = [
                'name' => $channel['canal'],
                'total' => floatval($channel['total_monto']),
                'orders' => intval($channel['total_pedidos']),
                'percentage' => number_format(floatval($channel['porcentaje']), 1)
            ];
        }
        
        return $ranking;
    }

    function detailsCard() {
        $udn = $_POST['udn'];
        $mes = $_POST['mes'];
        $anio = $_POST['anio'];
        
        // Obtener datos del mes actual
        $cards = $this->getDashboardCards([$udn, $udn, $anio, $mes, $udn, $anio, $mes, $udn, $anio, $mes]);
        
        // Obtener datos del mes anterior para comparación
        $mesAnterior = $mes - 1;
        $anioAnterior = $anio;
        if ($mesAnterior < 1) {
            $mesAnterior = 12;
            $anioAnterior = $anio - 1;
        }
        $cardsAnterior = $this->getDashboardCards([$udn, $udn, $anioAnterior, $mesAnterior, $udn, $anioAnterior, $mesAnterior, $udn, $anioAnterior, $mesAnterior]);
        
        // Obtener canal principal del mes
        $channelData = $this->getChannelPerformance([$udn, $anio, $mes]);
        
        if (empty($cards)) {
            return [
                'status' => 200,
                'data' => [
                    'ingresosTotales' => '$0.00',
                    'variacionIngresos' => '+0% vs mes anterior',
                    'totalPedidos' => '0',
                    'mesConsultado' => date('F Y', mktime(0, 0, 0, $mes, 1, $anio)),
                    'valorPromedio' => '$0.00',
                    'canalPrincipal' => 'N/A',
                    'porcentajeCanal' => '0% del total'
                ]
            ];
        }

        $card = $cards[0];
        
        // 1. Ingreso Total del mes consultado
        $ingresoActual = floatval($card['venta_mes'] ?? 0);
        
        // Comparación con mes anterior
        $ingresoAnterior = 0;
        if (!empty($cardsAnterior)) {
            $ingresoAnterior = floatval($cardsAnterior[0]['venta_mes'] ?? 0);
        }
        
        $variacion = 0;
        if ($ingresoAnterior > 0) {
            $variacion = (($ingresoActual - $ingresoAnterior) / $ingresoAnterior) * 100;
        }
        
        $variacionTexto = number_format(abs($variacion), 1) . '%';
        $variacionIcon = $variacion >= 0 ? '↑' : '↓';
        $variacionColor = $variacion >= 0 ? 'text-green-600' : 'text-red-600';
        $variacionCompleta = $variacionIcon . ' ' . $variacionTexto . ' vs mes anterior';
        
        // 2. Total de pedidos del mes
        $totalPedidos = $this->getTotalMonthOrders([$udn, $mes, $anio]);
        
        // 3. Cheque promedio (total/pedidos)
        $chequePromedio = $totalPedidos > 0 ? $ingresoActual / $totalPedidos : 0;
        
        // 4. Canal Principal
        $canalPrincipal = 'N/A';
        $porcentajeCanal = '0% del total';
        if (!empty($channelData)) {
            $canalPrincipal = $channelData[0]['canal'];
            $porcentajeCanal = number_format(floatval($channelData[0]['porcentaje']), 1) . '% del total';
        }
        
        return [
          
                'ingresosTotales' => $this->formatPrice($ingresoActual),
                'variacionIngresos' => $variacionCompleta,
                'variacionColor' => $variacionColor,
                'totalPedidos' => number_format($totalPedidos),
                'mesConsultado' => date('F Y', mktime(0, 0, 0, $mes, 1, $anio)),
                'valorPromedio' => $this->formatPrice($chequePromedio),
                'canalPrincipal' => $canalPrincipal,
                'porcentajeCanal' => $porcentajeCanal
            
        ];
    }

    function getSales() {
        $udn  = $_POST['udn'];
        $mes  = $_POST['mes'];
        $anio = $_POST['anio'];
        
        $data = $this->getSalesByChannel([$udn, $mes, $anio]);
        
        $labels = [];
        $ventaActual = [];
        $ventaAnterior = [];
        $colors = [
            'WhatsApp'  => '#25D366',
            'Meep'      => '#FF6B35',
            'Ecommerce' => '#007BFF',
            'Facebook'  => '#1877F2',
            'Llamada'   => '#6C757D',
            'Uber'      => '#000000',
            'Otro'      => '#9E9E9E'
        ];
        
        foreach ($data as $channel) {
            $labels[] = $channel['canal'];
            $ventaActual[] = floatval($channel['venta_actual']);
            $ventaAnterior[] = floatval($channel['venta_anterior']);
        }
        
        // Calcular mes anterior para el título
        $mesAnterior = $mes ;
        $anioAnterior = $anio - 1;
        // if ($mesAnterior < 1) {
        //     $mesAnterior = 12;
        //     $anioAnterior = $anio - 1;
        // }
        
        $nombreMesActual = date('F', mktime(0, 0, 0, $mes, 1));
        $nombreMesAnterior = date('F', mktime(0, 0, 0, $mesAnterior, 1));
        
        return [
            'status' => 200,
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => $nombreMesAnterior . ' ' . $anioAnterior,
                        'data' => $ventaAnterior,
                        'backgroundColor' => '#E5E7EB',
                        'borderColor' => '#9CA3AF',
                        'borderWidth' => 1
                    ],
                    [
                        'label' => $nombreMesActual . ' ' . $anio,
                        'data' => $ventaActual,
                        'backgroundColor' => '#103B60',
                        'borderColor' => '#1E40AF',
                        'borderWidth' => 1
                    ]
                ],
                'title' => "Ventas por Canal - {$nombreMesActual} vs {$nombreMesAnterior}",
                'colors' => $colors
            ]
        ];
    }

    private function formatPrice($amount) {
        return '$' . number_format(floatval($amount), 2);
    }
}

$obj = new ctrl();
echo json_encode($obj->{$_POST['opc']}());