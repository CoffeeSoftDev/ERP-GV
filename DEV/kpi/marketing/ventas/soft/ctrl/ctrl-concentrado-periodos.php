<?php

if (empty($_POST['opc'])) exit(0);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

require_once '../mdl/mdl-concentrado-periodos.php';
require_once '../../../../../conf/coffeSoft.php';

class ctrl extends mdlConcentradoPeriodos {

    function init() {
        return [
            'udn' => $this->lsUDN()
        ];
    }

    function getGruposByUdn() {
        $udn = $_POST['udn'];
        
        $params = [];
        if ($udn !== 'all') {
            $params['udn'] = $udn;
        }

        $grupos = $this->lsGrupos($params);

        return [
            'status' => 200,
            'grupos' => $grupos
        ];
    }

    function lsConcentrado() {
        $__row = [];
        
        $udn     = $_POST['udn'];
        $grupo   = $_POST['grupo'];
        $anio    = $_POST['anio'];
        $mes     = $_POST['mes'];
        $periodo = $_POST['periodo'];

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

        $ls = $this->getListSubClasificacion(0,13);

        $grupoActual = '';
        $theadGroups = [];
        $thead       = ['Producto', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic', 'Total'];

        foreach ($ls as $key) {
          
                
                $__row[] = [
                    'id' => $key['id'],
                    'Producto' =>  $key['nombre'],
                    'Ene' => '',
                    'Feb' => '',
                    'Mar' => '',
                    'Abr' => '',
                    'May' => '',
                    'Jun' => '',
                    'Jul' => '',
                    'Ago' => '',
                    'Sep' => '',
                    'Oct' => '',
                    'Nov' => '',
                    'Dic' => '',
                    'Total' => '',
                    'opc' => 2,
                    'colGroup' => true
                ];
          

            // $__row[] = [
            //     'id' => $key['id_Producto'],
            //     'Producto' => htmlspecialchars($key['descripcion'] ?? '', ENT_QUOTES, 'UTF-8'),
            //     'Ene' => [
            //         'html' => number_format($key['cantidad_3_meses'] ?? 0, 0, '.', ','),
            //         'class' => 'text-center'
            //     ],
            //     'Feb' => [
            //         'html' => number_format($key['cantidad_3_meses'] ?? 0, 0, '.', ','),
            //         'class' => 'text-center'
            //     ],
            //     'Mar' => [
            //         'html' => number_format($key['cantidad_3_meses'] ?? 0, 0, '.', ','),
            //         'class' => 'text-center'
            //     ],
            //     'Abr' => [
            //         'html' => number_format($key['cantidad_6_meses'] ?? 0, 0, '.', ','),
            //         'class' => 'text-center'
            //     ],
            //     'May' => [
            //         'html' => number_format($key['cantidad_6_meses'] ?? 0, 0, '.', ','),
            //         'class' => 'text-center'
            //     ],
            //     'Jun' => [
            //         'html' => number_format($key['cantidad_6_meses'] ?? 0, 0, '.', ','),
            //         'class' => 'text-center'
            //     ],
            //     'Jul' => [
            //         'html' => number_format($key['cantidad_9_meses'] ?? 0, 0, '.', ','),
            //         'class' => 'text-center'
            //     ],
            //     'Ago' => [
            //         'html' => number_format($key['cantidad_9_meses'] ?? 0, 0, '.', ','),
            //         'class' => 'text-center'
            //     ],
            //     'Sep' => [
            //         'html' => number_format($key['cantidad_9_meses'] ?? 0, 0, '.', ','),
            //         'class' => 'text-center'
            //     ],
            //     'Oct' => [
            //         'html' => number_format($key['cantidad_12_meses'] ?? 0, 0, '.', ','),
            //         'class' => 'text-center'
            //     ],
            //     'Nov' => [
            //         'html' => number_format($key['cantidad_12_meses'] ?? 0, 0, '.', ','),
            //         'class' => 'text-center'
            //     ],
            //     'Dic' => [
            //         'html' => number_format($key['cantidad_12_meses'] ?? 0, 0, '.', ','),
            //         'class' => 'text-center'
            //     ],
            //     'Total' => [
            //         'html' => number_format($key['cantidad_total'] ?? 0, 0, '.', ','),
            //         'class' => 'text-center font-semibold'
            //     ],
            //     'opc' => 0,
            //     'subrow' => true
            // ];
        }

        return [
            'thead' => $thead,
            'theadGroups' => $theadGroups,
            'row' => $__row,
            'ls' => $ls
        ];
    }


      function getListSubClasificacion($type, $idClasificacion) {

        // Realizar cambio de consulta por tipo
        switch ($type) {
            case 0:
                return $this->listMenu([$idClasificacion]);
                break;
            case 1:
                $subClasificacion = $this->listSubClasificacion([$idClasificacion]);
                array_unshift($subClasificacion, ['id' => 0, 'nombre' => 'SIN SUBCLASIFICACION']);
                return $subClasificacion;
                break;
            default:
                return [];
        }
    }
}

$obj = new ctrl();
echo json_encode($obj->{$_POST['opc']}());
