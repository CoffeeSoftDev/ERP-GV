<?php

if (empty($_POST['opc'])) exit(0);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

require_once '../mdl/mdl-dashboard.php';

class ctrl extends mdl {

    function apiDashboard() {
        $udn_id        = $_POST['udn_id'] ;
        $red_social_id = $_POST['red_social_id'];
        $año           = $_POST['año'];
        $mes           = $_POST['mes'];

        $dashboard    = $this->getDashboardData([$udn_id, $red_social_id, $año, $mes]);
        $trends       = $this->getMonthlyTrends([$udn_id, $red_social_id, $año, $mes]);
        $comparative  = $this->getComparativeData([$udn_id, $red_social_id, $mes, $año, $año - 1]);
        $topCampaigns = $this->getTopCampaigns([$udn_id, $red_social_id, $año, $mes]);
        $byType       = $this->getAnnouncementsByType([$udn_id, $red_social_id, $año, $mes]);

        $labels = [];
        $dataInversion = [];
        $dataClics = [];
        foreach ($trends as $item) {
            $labels[] = "Día " . $item['dia'];
            $dataInversion[] = floatval($item['inversion']);
            $dataClics[] = intval($item['clics']);
        }

        $comparativeData = [
            'labels' => ['Inversión', 'Clics', 'CPC'],
            'A' => [],
            'B' => []
        ];

        foreach ($comparative as $item) {
            if ($item['año'] == $año) {
                $comparativeData['A'] = [
                    floatval($item['inversion_total']),
                    intval($item['total_clics']),
                    floatval($item['cpc_promedio'])
                ];
            } else {
                $comparativeData['B'] = [
                    floatval($item['inversion_total']),
                    intval($item['total_clics']),
                    floatval($item['cpc_promedio'])
                ];
            }
        }

        $typeLabels = [];
        $typeData = [];
        foreach ($byType as $item) {
            $typeLabels[] = $item['tipo'];
            $typeData[] = floatval($item['inversion']);
        }

        return [
            'dashboard' => [
                'inversion_total' => formatPrice($dashboard['inversion_total']),
                'total_clics' => number_format($dashboard['total_clics'], 0, '.', ','),
                'cpc_promedio' => formatPrice($dashboard['cpc_promedio']),
                'total_campañas' => $dashboard['total_campañas'],
                'total_anuncios' => $dashboard['total_anuncios']
            ],
            'linear' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Inversión ($)',
                        'data' => $dataInversion,
                        'borderColor' => '#103B60',
                        'backgroundColor' => 'rgba(16, 59, 96, 0.1)',
                        'tension' => 0.4
                    ],
                    [
                        'label' => 'Clics',
                        'data' => $dataClics,
                        'borderColor' => '#8CC63F',
                        'backgroundColor' => 'rgba(140, 198, 63, 0.1)',
                        'tension' => 0.4
                    ]
                ]
            ],
            'barras' => $comparativeData,
            'topCampaigns' => $topCampaigns,
            'byType' => [
                'labels' => $typeLabels,
                'data' => $typeData
            ]
        ];
    }
}

function formatPrice($value) {
    return '$' . number_format($value, 2, '.', ',');
}

$obj = new ctrl();
echo json_encode($obj->{$_POST['opc']}());
