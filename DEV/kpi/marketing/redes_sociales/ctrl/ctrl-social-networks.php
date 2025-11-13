<?php

if (empty($_POST['opc'])) exit(0);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

require_once '../mdl/mdl-social-networks.php';
require_once '../../../../conf/coffeSoft.php';

class ctrl extends mdl {

    function init() {
        
        return [
            'udn'            => $this->lsUDN(),
            'lsudn'          => $this->lsUDN(),
            'socialNetworks' => $this->lsSocialNetworksFilter([ 1]),
            'metrics'        => $this->lsMetricsFilter([ 1])
        ];
    }

    function apiDashboardMetrics() {
        $udn = $_POST['udn'];
        $month = $_POST['mes'];
        $year = $_POST['anio'];

        $metrics            = $this->getDashboardMetrics([$udn, $year, $month]);
        $trendData          = $this->getTrendData([$udn, $year, $month]);
        $monthlyComparative = $this->getMonthlyComparativeData([$udn, $year, $month]);
        $comparativeTable   = $this->getComparativeTableData([$udn, $year, $month]);

        return [
            'dashboard' => [
                'totalReach'      => evaluar($metrics['total_reach'] ?? 0),
                'interactions'    => evaluar($metrics['total_interactions'] ?? 0),
                'monthViews'      => evaluar($metrics['total_views'] ?? 0),
                'totalInvestment' => evaluar($metrics['total_investment'] ?? 0)
            ],
            'trendData' => $this->formatTrendChartData($trendData),
            'monthlyComparative' => $this->formatMonthlyComparativeData($monthlyComparative),
            'comparativeTable' => $comparativeTable
        ];
    }

    function lsSocialNetworks() {
        $__row  = [];
        $active = $_POST['active'];

        $ls = $this->listSocialNetworks([ $active]);

        foreach ($ls as $key) {
            $a = [];

            if ($key['active'] == 1) {
                $a[] = [
                    'class'   => 'btn btn-sm btn-primary me-1',
                    'html'    => '<i class="icon-pencil"></i>',
                    'onclick' => 'adminSocialNetWork.editSocialNetwork(' . $key['id'] . ')'
                ];

                $a[] = [
                    'class'   => 'btn btn-sm btn-danger',
                    'html'    => '<i class="icon-toggle-on"></i>',
                    'onclick' => 'adminSocialNetWork.statusSocialNetwork(' . $key['id'] . ', ' . $key['active'] . ')'
                ];
            } else {
                $a[] = [
                    'class'   => 'btn btn-sm btn-outline-danger',
                    'html'    => '<i class="icon-toggle-off"></i>',
                    'onclick' => 'adminSocialNetWork.statusSocialNetwork(' . $key['id'] . ', ' . $key['active'] . ')'
                ];
            }

            $__row[] = [
                'id'     => $key['id'],
                'Icono'  => '<i class="' . $key['icono'] . '" style="color:' . $key['color'] . '; font-size: 24px;"></i>',
                'Nombre' => $key['nombre'],
                'Estado' => renderStatus($key['active']),
                'a' => $a
            ];
        }

        return [
            'row' => $__row,
            'ls' => $ls
        ];
    }

    function getSocialNetwork() {
        $id = $_POST['id'];
        $status = 404;
        $message = 'Red social no encontrada';
        $data = null;

        $network = $this->getSocialNetworkById([$id]);

        if ($network) {
            $status = 200;
            $message = 'Red social encontrada';
            $data = $network;
        }

        return [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
    }

    function addSocialNetwork() {
        $status = 500;
        $message = 'No se pudo agregar la red social';
        $_POST['active'] = 1;
      
        $exists = $this->existsSocialNetworkByName([$_POST['nombre']]);

        if (!$exists) {
            $create = $this->createSocialNetwork($this->util->sql($_POST));
            if ($create) {
                $status = 200;
                $message = 'Red social agregada correctamente';
            }
        } else {
            $status = 409;
            $message = 'Ya existe una red social con ese nombre.';
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }

    function editSocialNetwork() {
        $id = $_POST['id'];
        $status = 500;
        $message = 'Error al editar red social';
        
     

        $edit = $this->updateSocialNetwork($this->util->sql($_POST, 1));


        if ($edit) {
            $status = 200;
            $message = 'Red social editada correctamente';
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }

    function statusSocialNetwork() {
        $status = 500;
        $message = 'No se pudo actualizar el estado';

        $update = $this->updateSocialNetwork($this->util->sql($_POST, 1));

        if ($update) {
            $status = 200;
            $message = 'El estado se actualizó correctamente';
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }

    // Metrics.

    function lsMetrics() {
        $__row = [];
        $active = $_POST['active'];
        $socialNetworkId = isset($_POST['socialNetwork']) && $_POST['socialNetwork'] !== '' ? $_POST['socialNetwork'] : null;

        $ls = $this->listMetrics([$active, $socialNetworkId]);

        foreach ($ls as $key) {
            $a = [];

            if ($key['active'] == 1) {
                $a[] = [
                    'class' => 'btn btn-sm btn-primary me-1',
                    'html' => '<i class="icon-pencil"></i>',
                    'onclick' => 'adminMetrics.editMetric(' . $key['id'] . ')'
                ];

                $a[] = [
                    'class' => 'btn btn-sm btn-danger',
                    'html' => '<i class="icon-toggle-on"></i>',
                    'onclick' => 'adminMetrics.statusMetric(' . $key['id'] . ', ' . $key['active'] . ')'
                ];
            } else {
                $a[] = [
                    'class' => 'btn btn-sm btn-outline-danger',
                    'html' => '<i class="icon-toggle-off"></i>',
                    'onclick' => 'adminMetrics.statusMetric(' . $key['id'] . ', ' . $key['active'] . ')'
                ];
            }

            $__row[] = [
                'id'      => $key['id'],
                'Metrica' => $key['name'],
                'Red Social' => [
                    'class' => 'text-center ',
                    'html'  => '<div class="flex items-center gap-2">
                                    <i class="' . $key['social_network_icon'] . ' text-xs" 
                                    style="color:' . $key['social_network_color'] . '; font-size: 15px;"></i>
                                    <span>' . $key['social_network_name'] . '</span>
                                </div>'
                ],
                'Estado' => renderStatus($key['active']),
                'a'      => $a
            ];
        }

        return [
            'row' => $__row,
            'ls' => $ls
        ];
    }

    function getMetric() {
        $id = $_POST['id'];
        $status = 404;
        $message = 'Métrica no encontrada';
        $data = null;

        $metric = $this->getMetricById([$id]);

        if ($metric) {
            $status = 200;
            $message = 'Métrica encontrada';
            $data = $metric;
        }

        return [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
    }

    function addMetric() {
        $status = 500;
        $message = 'No se pudo agregar la métrica';
        $_POST['active'] = 1;
    
        $exists = $this->existsMetricByName([
            $_POST['nombre'],
            $_POST['red_social_id']
        ]);

        if (!$exists) {
            $create = $this->createMetric($this->util->sql($_POST));
            if ($create) {
                $status = 200;
                $message = 'Métrica agregada correctamente';
            }
        } else {
            $status = 409;
            $message = 'Ya existe una métrica con ese nombre para esta red social.';
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }

    function editMetric() {
        $id = $_POST['id'];
        $status = 500;
        $message = 'Error al editar métrica';
        
       
        $edit = $this->updateMetric($this->util->sql($_POST, 1));
        if ($edit) {
            $status = 200;
            $message = 'Métrica editada correctamente';
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }

    function statusMetric() {
        $status = 500;
        $message = 'No se pudo actualizar el estado';

        $update = $this->updateMetric($this->util->sql($_POST, 1));

        if ($update) {
            $status = 200;
            $message = 'El estado se actualizó correctamente';
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }

    private function formatTrendChartData($data) {
        $labels = [];
        $datasets = [];

        foreach ($data as $item) {
            $labels[] = $item['month_name'];
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Interacciones',
                    'data' => array_column($data, 'interactions'),
                    'backgroundColor' => '#103B60',
                    'borderColor' => '#103B60',
                    'fill' => false
                ]
            ]
        ];
    }

    private function formatMonthlyComparativeData($data) {
        $labels = [];
        $currentMonth = [];
        $previousMonth = [];

        foreach ($data as $item) {
            $labels[] = $item['social_network'];
            $currentMonth[] = $item['current_month'];
            $previousMonth[] = $item['previous_month'];
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Mes Actual',
                    'data' => $currentMonth,
                    'backgroundColor' => '#103B60'
                ],
                [
                    'label' => 'Mes Anterior',
                    'data' => $previousMonth,
                    'backgroundColor' => '#8CC63F'
                ]
            ]
        ];
    }

    function getMetricsByNetwork() {
        $networkId = $_POST['social_network_id'];
        $metrics = $this->lsMetricsByNetwork([$networkId, 1]);

        return [
            'status' => 200,
            'metrics' => $metrics
        ];
    }

    function addCapture() {
        $status  = 500;
        $message = 'No se pudo guardar la captura';

        $month   = $_POST['month'];
        $year    = $_POST['year'];
        $udn     = $_POST['udn'];
        $socialNetwork     = $_POST['social_network_id'];
        $metrics = json_decode($_POST['metrics'], true);
        $udn_id = $_SESSION['SUB'] ?? 5;

        $exists = $this->existsCapture([
            $udn,
            $year,
            $month,
            $socialNetwork
        ]);

        if ($exists) {
            return [
                'status' => 409,
                'message' => 'Ya existe una captura para este mes y año'
            ];
        }

        $captureData = [
            'udn_id'         => $udn,
            'año'            => $year,
            'mes'            => $month,
            'fecha_creacion' => $_POST['fecha_creacion'],
            'active'         => 1
        ];

        $captureId = $this->createCapture($this->util->sql($captureData));
        $history   = $this->getHistoryNetworkByID([  $udn,   $year,  $month ]);

        // if ($captureId) {

        $data = [];

            foreach ($metrics as $metric) {
                $data[] = [
                      'historial_id' => $history['id'],
                    'metrica_id'   => $metric['metric_id'],
                    'cantidad'     => $metric['value']
                ];

                $movementData = [

                    'historial_id' => $history['id'],
                    'metrica_id'   => $metric['metric_id'],
                    'cantidad'     => $metric['value']
                ];

              $success =  $this->createMetricMovement($this->util->sql($movementData));
            }

            $status = 200;
            $message = 'Captura guardada correctamente';
        // }

        return [
            'status' => $status,
            'message' => $message,
          $data
        ];
    }

    function apiAnnualReport() {
        $__row = [];
        $udn = $_POST['udn'];
        $networkId = $_POST['social_network_id'];
        $year = $_POST['year'];

        $data = $this->getAnnualReport([$udn, $networkId, $year]);

        foreach ($data as $key) {
            $__row[] = [
                'Métrica'    => $key['metric_name'],
                'Enero'      => formatNumber($key['month_1']),
                'Febrero'    => formatNumber($key['month_2']),
                'Marzo'      => formatNumber($key['month_3']),
                'Abril'      => formatNumber($key['month_4']),
                'Mayo'       => formatNumber($key['month_5']),
                'Junio'      => formatNumber($key['month_6']),
                'Julio'      => formatNumber($key['month_7']),
                'Agosto'     => formatNumber($key['month_8']),
                'Septiembre' => formatNumber($key['month_9']),
                'Octubre'    => formatNumber($key['month_10']),
                'Noviembre'  => formatNumber($key['month_11']),
                'Diciembre'  => formatNumber($key['month_12']),
                'Total'      => formatNumber($key['total'])
            ];
        }

        return [
            'row' => $__row
        ];
    }

    function apiMonthlyComparative() {
        $__row = [];
        $udn = $_POST['udn'];
        $networkId = $_POST['social_network_id'];
        $year = $_POST['year'];

        $data = $this->getMonthlyComparativeReport([$udn, $networkId, $year]);

        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        $mesActual = date('n');
        $mesAnterior = $mesActual > 1 ? $mesActual - 1 : 12;

        $nombreMesAnterior = $meses[$mesAnterior];
        $nombreMesActual = $meses[$mesActual];

        foreach ($data as $key) {
            $comparison = $key['current_value'] - $key['previous_value'];
            $percentage = $key['previous_value'] > 0 
                ? (($comparison / $key['previous_value']) * 100) 
                : 0;

            $__row[] = [
                'Métrica'           => $key['metric_name'],
                $nombreMesAnterior  => formatNumber($key['previous_value']),
                $nombreMesActual    => formatNumber($key['current_value']),
                'Comparación'       => formatNumber($comparison),
                'Porcentaje'        => $percentage == 0 ? '-' : number_format($percentage, 2) . '%'
            ];
        }

        return [
            'row' => $__row
        ];
    }

    function apiAnnualComparative() {
        $__row = [];
        $udn = $_POST['udn'];
        $networkId = $_POST['social_network_id'];
        $year = $_POST['year'];

        $data = $this->getAnnualComparativeReport([$udn, $networkId, $year]);

        foreach ($data as $key) {
            $comparison = $key['current_year'] - $key['previous_year'];
            $percentage = $key['previous_year'] > 0 
                ? (($comparison / $key['previous_year']) * 100) 
                : 0;

            $__row[] = [
                              'Métrica'     => $key['metric_name'],
                       'Año ' . ($year - 1) => formatNumber($key['previous_year']),
                       'Año ' . $year       => formatNumber($key['current_year']),
                              'Comparación' => formatNumber($comparison),
                              'Porcentaje'  => $percentage == 0 ? '-' : number_format($percentage, 2) . '%'
            ];
        }

        return [
            'row' => $__row
        ];
    }

    function apiGetHistoryMetrics() {
        $udn     = $_POST['udn'];
        $year    = isset($_POST['year']) ? $_POST['year'] : date('Y');
        $data    = $this->getHistoryMetrics([$udn, $year]);
        $history = [];

        foreach ($data as $item) {
            $metrics = [];
            if (!empty($item['metrics_data'])) {
                $metricsArray = explode('|', $item['metrics_data']);
                foreach ($metricsArray as $metric) {
                    $parts = explode(':', $metric);
                    if (count($parts) == 2) {
                        $metrics[] = [
                            'name' => $parts[0],
                            'value' => $parts[1]
                        ];
                    }
                }
            }

            $history[] = [
                'id' => $item['id'],
                'network' => $item['social_network_name'],
                'icon'    => $item['social_network_icon'],
                'color'   => $item['social_network_color'],
                'date'    => formatSpanishDate($item['fecha_creacion']),
                'year'    => $item['year'],
                'month'   => $item['month'],
                'metrics' => $metrics
            ];
        }

        return [
            'status' => 200,
            'data' => $history,
            $data
        ];
    }

    function deleteCapture() {
        $status = 500;
        $message = 'No se pudo eliminar la captura';

        $delete = $this->deleteCaptureById($this->util->sql(['id'=>$_POST['id']],1));

        if ($delete) {
            $status = 200;
            $message = 'Captura eliminada correctamente';
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }

    function getCapture() {
        $id = $_POST['id'];
        $status = 404;
        $message = 'Captura no encontrada';
        $data = null;

        $capture = $this->getCaptureById([$id]);

        if ($capture) {
            $metrics = $this->getMetricsByHistorialId([$id]);
            $capture['metrics'] = $metrics;
            $capture['date'] = formatSpanishDate($capture['fecha_creacion']);

            $status = 200;
            $message = 'Captura encontrada';
            $data = $capture;
        }

        return [
            'status' => $status,
            'message' => $message,
            'data' => $data,
            $capture
        ];
    }

    function updateCaptureMetrics() {
        $status = 500;
        $message = 'No se pudo actualizar la captura';
        $id = $_POST['id'];
        $metrics = json_decode($_POST['metrics'], true);

        if (empty($metrics)) {
            return [
                'status' => 400,
                'message' => 'No se recibieron métricas para actualizar'
            ];
        }

        $updated = true;
        foreach ($metrics as $metric) {
            $updateData = [
                'cantidad' => $metric['value'],
                'id' => $metric['historial_metric_id']
            ];
            
            $result = $this->updateMetricHistorial($this->util->sql($updateData, 1));
            if (!$result) {
                $updated = false;
                break;
            }
        }

        if ($updated) {
            $status = 200;
            $message = 'Captura actualizada correctamente';
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }
}

function renderStatus($status) {
    switch ($status) {
        case 1:
            return '<span class="inline-flex items-center gap-2 px-4 py-1 rounded text-sm font-medium bg-green-100 text-green-700">
                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                        Activo
                    </span>';
        case 0:
            return '<span class="inline-flex items-center gap-2 px-4 py-1 rounded text-sm font-medium bg-red-100 text-red-700">
                        <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                        Inactivo
                    </span>';
        default:
            return '<span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium bg-gray-100 text-gray-700">
                        <span class="w-2 h-2 bg-gray-500 rounded-full"></span>
                        Desconocido
                    </span>';
    }
}

function formatNumber($value) {
    return ($value == 0 || $value === null || $value === '') ? '-' : $value;
}

$obj = new ctrl();
echo json_encode($obj->{$_POST['opc']}());
