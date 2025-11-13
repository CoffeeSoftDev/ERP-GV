<?php
// ctrl-tabulation.php
session_start();
if (empty($_POST['opc'])) exit(0);

// conf. api
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

require_once '../mdl/mdl-tabulacion-calificaciones.php';

$encode = [];

class ctrl extends mdl {

    // 📌 Auxiliar para mostrar cada valor (te, pr, ap, ps)
    private function getValor($manual, $original) {
        $isManual = isset($manual);

        if ($isManual && $manual != $original) {
            return [

                'html' => "
                    <span class='text-blue-600 fw-bold'>{$manual}</span>
                    <span class='text-gray-400 text-xs ms-1 line-through' title='Original: {$original}'>{$original}</span>
                ",

                'class' => 'text-center',
                'title' => 'Valor modificado manualmente'
            ];
        }

        return [
            'html'  => $original,
            'class' => 'text-gray-700 text-center',
            'title' => 'Valor original'
        ];
    }

    function list() {
        $instancia = 'calificacion';
        $__row     = [];

        $ls = $this->getEntities(['id_tabulation' => $_POST['id']    ]);

        foreach ($ls as $item) {
        $a   = [];
        $a[] = [
            'html'    => '<i class="icon-pencil"></i> Editar',
            'icon'    => 'icon-pencil',
            'onclick' => "{$instancia}.editTabulation({$item['id']})",
            'class'   => 'btn btn-sm btn-outline-primary me-1 w-24'
        ];

        // Valores numéricos
        $valores = ['te' => 'Trabajo en equipo', 'pr' => 'Profesionalismo', 'ap' => 'Actitud Positiva', 'ps' => 'Pasion Servicio'];
        $row = [
            'id'          => $item['id'],
            'Colaborador' => $item['name'],
            'No personas' => [
            'html' => $item['people_count'],
            'class'=> 'bg-gray-100  text-center',
        ],
        ];

        $suma = 0;
        foreach ($valores as $campo => $titulo) {
        $original      = $item["{$campo}_original"] ?? null;
        $actual        = $item[$campo];
        $suma         += $actual;
        $row[$titulo]  = ($original !== null && $original != $actual)
        ? [
            'html'  => "<span title='Calificación original: {$original}'
            class='text-green-600 font-bold text-center pointer'>{$actual}</span>" ,

        ]
        : $actual;
        }

        $calf = round($suma / 4, 2);
        $row['Calificación'] = isset($item['calf_original'])
        ? [
        'html'  => $item['calf_original'],
        'class' => 'text-success text-center font-semibold',
        'title' => 'Promedio modificado manualmente'
        ]
        : $calf;

        $row['a'] = $a;
        $__row[] = $row;
        }

        return [
        "row" => $__row,
        'ls'  => $ls,
        'frm_head' => '
        <div class="flex flex-col">
            <span class="text-lg font-bold">Lista de empleados</span>
            <span class="text-gray-600 text-sm">Calificaciones de los empleados en los 4 rubros de evaluación</span>
        </div>'
        ];
    }

    function lsTabulationMap(){

           $listEvaluateds  = $this->getEvaluated([$_POST['id_period']]);
           $valuesCorporate = $this->getValuesCorporate();
           $data_scores     = [];

            foreach ($listEvaluateds as $evaluated) {
                
                $employed = $this-> getEmployedByID([ $evaluated['id_evaluated']]);
                
                $campos = [
                    'id'           => $idTabulacion,
                    'id_evaluated' => $evaluated['id_evaluated'],
                    'name       '  => $employed['Nombres'],
                ];

                $values = [];
                $sum    = 0;
                $count  = 0;

                $evaluations = $this -> getCountEvaluators([
                              $_POST['id_period'],
                        $evaluated['id_evaluated'],
                ]);

                $values['people_count'] = $evaluations['total'];

                foreach ($valuesCorporate as $value) {

                    $valor = $this ->getEvaluatedScoresAll([
                        $_POST['id_period'],
                        $evaluated['id_evaluated'],
                        $value['id']
                    ]);

                    $evaluatedScore = $this ->getEvaluatedScores([
                        $_POST['id_period'],
                        $evaluated['id_evaluated'],
                        $value['id']
                    ]);


                    // Transformar el arreglo en texto plano
                    $text = '';
                    foreach ($valor as $v) {
                        $text .= "<span class='text-xs text-gray-500'>-  {$v['answered']} </span><br>\n";
                    }

                    $score    = floatval($evaluatedScore['total_respuestas']);
                    $promedio = floatval($evaluatedScore['promedio']);
                    $values[$value['value']] = $text.'<br> <span class="text-xs text-gray-500">'.$score.'</span> - '.$promedio;

                    if ($score > 0) {
                        $sum += $promedio;
                        $count++;
                    }
                }

                
                $values['calf']         = ($count > 0) ? round($sum / $count, 2) : 0;
                $values['opc']          = 0;


                $row = array_merge($campos, $values);
                $data_scores[] = $row;

                // $data_sql = $this->util->sql($row);
                // $this->insertEvaluatedScore($data_sql);
            }


            return[
                'row' => $data_scores,
                'status' => 200,
                'message' => $listEvaluateds
            ];

    }


    function getTabulation() {

        $get      = $this->getById([$_POST['id']]);
        $employed = $this->getEmployedByID([$get['id_evaluated']]);
        $dpto     = $this->getPuesto([$employed['Puesto_Empleado']]);
        return [
            'status'   => $get ? 200 : 500,
            'message'  => $get ? 'Datos obtenidos.' : 'Error al obtener datos.',
            'data'     => $get,
            'employed' => $employed['Nombres'],
            'dpto'     => $dpto['Nombre_Puesto']  
        ];
    }

    function edit() {
       // Obtener datos actuales del registro
        $current = $this->getById([$_POST['id']]);

        $valores = ['ap', 'ps', 'pr', 'te'];
        $getters = [];

        foreach ($valores as $campo) {
            $originalKey = $campo . '_original';
            // Registrar valor original si cambió y no se había guardado
            if (isset($_POST[$campo]) && $_POST[$campo] != $current[$campo] && empty($current[$originalKey])) {
                $_POST[$originalKey] = $current[$campo];
            } else {
                $_POST[$originalKey] = $current[$originalKey] ?? null;
            }
        }
        // Calcular promedio
        $total    = 0;
        $contador = 0;

        foreach ($valores as $campo) {
            if (isset($_POST[$campo]) && is_numeric($_POST[$campo])) {
                $total += floatval($_POST[$campo]);
                $contador++;
            }
        }

        $_POST['calf'] = $contador > 0 ? round($total / $contador, 2) : 0;

        // Preparar datos para actualización
        $_POST = array_merge(array_diff_key($_POST, ['id' => '']), ['id' => $_POST['id']]);
        $data = $this->util->sql($_POST, 1);

        $edit = $this->updateCalificacion($data);

        return [
            'status'  => $edit ? 200 : 500,
            'message' => $edit ? 'Actualizado correctamente.' : 'Error al actualizar.',
        ];
    }


    function close() {

        // obtener el id del periodo en base al id de la tabulacion 
        $period = $this-> getPeriodByTabulation( [  $_POST['id'] ]);

        // hacer update al status del periodo
        $status = $this-> updatePeriodStatus($this->util->sql([

            'status' => '2',
            'id'     => $period['period_id']

        ], 1));

        $update = $this->update($this->util->sql($_POST, 1));

        return [
            'status'  => $update ? 200 : 500,
            'message' => $update ? 'Tabulación finalizada.' : 'Error al finalizar.',
            
        ];
    }

    }

   function dropdown($id) {
    return [
        ['icon' => 'icon-pencil', 'text' => 'Editar', 'onclick' => "calificacion.edit($id)"]
    ];

}


$obj = new ctrl();
$fn = $_POST['opc'];
$encode = $obj->$fn();
echo json_encode($encode);
