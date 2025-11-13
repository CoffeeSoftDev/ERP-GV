<?php
if (empty($_POST['opc'])) exit(0);
require_once '../mdl/mdl-history.php';

class ctrl extends mdl {

    function lsCPC() {
        $__row = [];
        $año = $_POST['año'];
        $udn_id = $_POST['udn_id'] ;
        $red_social_id = $_POST['red_social_id'];

        $data = $this->getCPCHistory([$año, $udn_id, $red_social_id]);
        $months = $this->getMonthsArray();

        $dataByMonth = [];
        foreach ($data as $item) {
            $dataByMonth[$item['mes']] = $item;
        }

        for ($mes = 1; $mes <= 12; $mes++) {
            if (isset($dataByMonth[$mes])) {
                $item = $dataByMonth[$mes];
                $__row[] = [
                    'Mes'              => $months[$mes],
                    'Inversión Total'  => [
                        'html'  => evaluar($item['inversion_total']),
                    ],
                    'Total Clics'      => number_format($item['total_clics'], 0, '.', ','),
                    'CPC Promedio'     => [
                        'html'  => evaluar($item['cpc_promedio']),
                        
                    ]
                ];
            } else {
                $__row[] = [
                    'Mes'              => $months[$mes],
                    'Inversión Total'  => [
                        'html'  => '$0.00',
                        'class' => 'text-end text-gray-500 '
                    ],
                    'Total Clics'      => [
                        'html'  => '0',
                        'class' => 'text-end text-gray-500 '
                    ],
                    'CPC Promedio'     => [
                        'html'  => '$0.00',
                        'class' => 'text-end text-gray-500 '
                    ]
                ];
            }
        }

        return [
            'row' => $__row,
            'data' => $data
        ];
    }

    function lsCAC() {
        $__row = [];
        $año = $_POST['año'];
        $udn_id = $_POST['udn_id'];
        $red_social_id = $_POST['red_social_id'];

        $data = $this->getCACHistory([$año, $udn_id, $red_social_id]);
        $months = $this->getMonthsArray();

        $dataByMonth = [];
        foreach ($data as $item) {
            $dataByMonth[$item['mes']] = $item;
        }

        for ($mes = 1; $mes <= 12; $mes++) {
            if (isset($dataByMonth[$mes])) {
                $item = $dataByMonth[$mes];
                $__row[] = [
                    'Mes'              => $months[$mes],
                    'Inversión Total'  => [
                        'html'  => evaluar($item['inversion_total']),
                    ],
                    'Número Clientes'  => number_format($item['numero_clientes'], 0, '.', ','),
                    'CAC'              => [
                        'html'  => $item['numero_clientes'] > 0 ? evaluar($item['cac']) : 'N/A',
                    ]
                ];
            } else {
                $__row[] = [
                    'Mes'              => $months[$mes],
                    'Inversión Total'  => [
                        'html'  => '$0.00',
                        'class' => 'text-end text-gray-500 '
                    ],
                    'Número Clientes'  => [
                        'html'  => '0',
                        'class' => 'text-end text-gray-500 '
                    ],
                    'CAC'              => [
                        'html'  => 'N/A',
                        'class' => 'text-end text-gray-500 '
                    ]
                ];
            }
        }

        return [
            'row' => $__row,
            'data' => $data
        ];
    }
}

function evaluar($value) {
    return '$' . number_format($value, 2, '.', ',');
}

$obj = new ctrl();
echo json_encode($obj->{$_POST['opc']}());
