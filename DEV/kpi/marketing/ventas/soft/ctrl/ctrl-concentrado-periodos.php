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
        $year    = $_POST['anio'];
        $mes     = $_POST['mes'];
        $periodo = $_POST['periodo'];

        $params = [];
        
        if ($grupo !== 'all') {
            $params['grupo'] = $grupo;
        }
        
         $ls = $this->getListSubClasificacion(0,13);

        $grupoActual = '';
        $theadGroups = [];
        // $thead       = ['Producto', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic', 'Total'];

        foreach ($ls as $sub) { 
          
            $productos        = $this->listDishes([$mes, $year, $sub['id']]);
            $totalesCategoria = array_fill(0, 12, 0);
            $totalGeneral     = 0;
            $rowsProductos    = [];

            $fechas     = [];
            $fechasName = [];

            // 📜 Generar los últimos 6 meses
            for ($i = 0; $i <  $periodo ; $i++) {

                $time         = strtotime("-$i months", strtotime("$year-$mes-01"));
                $monthName    = date('M', $time);
                $yearName     = date('Y', $time);
                $fechasName[] = "$monthName/$yearName";
                $monthNumber  = date('m', $time);
                $yearNumber   = date('Y', $time);
                $fechas[]     = "$monthNumber/$yearNumber";
            }



            // 📜 Fila de encabezado
            $campos = [
                'id'     => $sub['id'],
                'clave'  => '',
                'nombre' => $sub['nombre'],
            ];

            $dates = [];
            foreach ($fechasName as $fechaName) {
                $dates[$fechaName] = ''; // se actualizará luego con totales
            }



            $__row[] = array_merge($campos, $dates, ['opc' => 1]);



            foreach ($productos as $_key) {
                $campos = [
                    'id'     => $sub['id'],
                    'clave'  => $_key['idDishes'],
                    'nombre' => $_key['nombre'],
                ];

                $dates = [];
                $totalProducto = 0;


                foreach ($fechas as $index => $fecha) {
                      list($m, $y) = explode("/", $fecha);
                    // $costo_potencial = $this->selectDatosCostoPotencial([$_key['idReceta'], $m, $y]);


                    //    $valor                              = floatval($costo_potencial['desplazamiento'] ?? 0);
                    //     $totalesGrupo[$fechasName[$index]] += $valor; // 🔵 Acumular total por grupo
                    //      $mostrar_valor                      = ($valor == 0) ? '-' : $valor;  // 📌 Mostrar guion si es cero
                        $dates[$fechasName[$index]] = [
                            // 'html'  => $mostrar_valor,
                            // 'val'   => $valor,
                            'class' => 'text-end'
                        ];

                      

                }



                
                $__row[] = array_merge($campos, $dates, ['opc' => 0]);
          
            }
        }

        return [
            // 'thead' => $thead,
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
