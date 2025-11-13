<?php
if(empty($_POST['opc'])) exit(0);

setlocale(LC_TIME, 'es_ES.UTF-8');
date_default_timezone_set('America/Mexico_City');

require_once('../mdl/mdl-valores.php');
require_once('../../../conf/_Utileria.php');
require_once('../../../conf/coffeSoft.php');

class Valores extends MValores {

    function init(){
        
        $lsUDN     = $this-> lsUDN();
        $lsEstatus = $this-> lsStatus();

        $udnZero = ["id" => "0", "valor" => "TODAS LAS UDNS"];
        array_unshift($lsUDN, $udnZero);

        return[
            'udn'      => $lsUDN,
            'estados'   => $lsEstatus,
            'temporada' => ["id" => 0, "valor" => "TODAS LAS TEMPORADAS"]
        ];
    }

     function getSeason(){
        return $this -> lsTemporada([
            $_POST['id_udn']
        ]);
    }

    function lsEvaluation(){
        # Declarar variables
        $__row = [];
        $ls = $this->listEvaluation2([
            'fi'     => $_POST['fi'],
            'ff'     => $_POST['ff'],
            'udn'    => $_POST['udn'],
            'estado' => $_POST['estado'],
            'season' => $_POST['id_period']
         ]);

        foreach ($ls as $key) {
            $evaluador = "";
            $matriz = $this->arrayMatrix([$key['id_evaluator'], $key['id_udn']]);
            
            foreach ($matriz as $evaluados) {
              $evaluador .= $evaluados['valor'].', <br>';
            }
            
            $btn   = [];
            if($key['id_status'] == 1){
                $btn[] = [
                    'class'   => 'btn me-1 btn-sm p-2 btn-outline-info',
                    'html'    => '<i class="icon-play"></i>',
                    "onclick" => "encuesta.onShowQuestionnaire(" . $key['id_evaluator'] . ",".$key['id_udn'].",".$key['id'].")",
                ];

                  $btn[] = [
                    'class'   => 'btn me-1 btn-sm p-2 btn-outline-danger',
                    'html'    => '<i class="icon-cancel"></i>',
                    "onclick" => "encuesta.delete(".$key['id'].")",
                ];
            }else{
                 $btn[] = [
                    'class'   => '',
                ];
            }
                
            $__row[] = [
                'id'          => $key['id'],
                'clave'       => $key['id'],
                'temporada'   => $key['season'],
                'evaluador'   => $key['Nombres'],
                'fecha'       => formatSpanishDate($key['date']),
                'hora'        => $key['hour'].' hrs',
                'UDN'         => $key['UDN'],
                'Estado'      => getEstatus($key['id_status']),
                'Evaluados'   => $evaluador,
                'a'           => $btn
            ];
        }
    
        # encapsular datos
        return [ 
            "row"    => $__row,
            'ls'     => $ls,
            "post"   => $_POST,
            'fi'     => $_POST['fi'],
            'ff'     => $_POST['ff'],
            'udn'    => $_POST['udn'],
            'estado' => $_POST['estado'],
            'season' => $_POST['id_period']
        ];
    }

    function getEvaluation(){
        $this -> arrayMatrix([
            $_POST['id_evaluator'],
            $_POST['id_udn']
        ]);
    }

    function getEvaluators(){
        return  $this->lsEmployed();
    }
    function getPeriod(){
        return $this->lsPeriod();
    }

    function addEvaluation(){
        $data   = [];
        $matriz = [];
        
        // Validar si no existe matrices con esa unidad no se puede evaluar.
        $matriz = $this->arrayMatrix([$_POST['id_evaluator'], $_POST['id_udn']]);
        
        $__evaluators = [];
        foreach ($matriz as $evaluator) {
             $__evaluators[] = [
                'id'     => $evaluator['id'],
                'valor'  => $evaluator['valor'].'',
                'puesto' => '',
                'items'  => 10,
                'result' => 0
           ]; 
        }

        // Si no hay matices de la UDN, no se puede crear la encuesta
        if ($matriz == null) {
            return [
                'status'  => 400,
                'message' => 'El empleado seleccionado no tiene evaluados en esta UDN. '. `<br>` . 'Porfavor, verifica la selección e intenta nuevamente. 😊 ',
            ];	
        }

        // Preguntar si la evaluación ya existe
        // 1. Obtener la encuesta por empleado, UDN y mes actual del año actual.
        $getSurvey = $this->getSurvey([
            $_POST['id_evaluator'],
            $_POST['id_udn'],
            2,
            date('m'),  // 🔵 mes actual
            date('Y')   // 🔵 año actual 
        ]); 

          // Crear encuesta
        $status                 = 500;
        $message                = 'Error al crear la Encuesta.';
        $_POST['date_creation'] = date('Y-m-d H:i:s');

          //  Si no hay resultados (null o arreglo vacío), se puede crear la encuesta
        if (empty($getSurvey)) { 
            $status  = 200;
            $message = 'Encuesta creada correctamente.';
            $create  = $this->createSurvey($this->util->sql($_POST));

             if ($create == true) {
                $max = $this->maxSurvey();
                $data = [
                    'id'         => $max,
                    'idEmployed' => $_POST['id_evaluator'],
                    'evaluators' => $__evaluators,
                    'udn'        => $_POST['id_udn']
                ];
            }
        } else {
             return [
                'status'  => 400,
                'message' => 'Ya existe una evaluación para este empleado en el mes de '. ucfirst(strftime('%B')).', ultima evaluación el '.$getSurvey[0]['date_creation'],
            ];  
        }

        return [
            'status'    => $status,
            'message'   => $message,
            'data'      => $data,
            'getSurvey' => $getSurvey
        ];
    }

    function deleteEvaluation(){
        $status = 500;
        $message = 'Error al eliminar registro.';
        $delete = $this->deleteEval($this->util->sql($_POST, 1));

        if ($delete == true) {
            $status  = 200;
            $message = 'Se ha eliminado correctamente';
        }

        return [
            'status'   => $status,
            'message' => $message,
            $delete
        ];
     }
 }

$opc = $_POST['opc'];
unset($_POST['opc']);

// Complements
function getEstatus($idstatus) {
    // 🔵 Definimos los estados con sus respectivos emojis y etiquetas
    $estados = [
        1 => '⏳  EN PROCESO',
        2 => '✅ FINALIZADO',
        3 => '❌ CANCELADO'
    ];

    // 📌 Verificamos si el estado existe en la lista, de lo contrario, asignamos un valor por defecto
    return $estados[$idstatus] ?? '❓ DESCONOCIDO';
}

$obj = new Valores();
$encode = $obj->$opc();
echo json_encode($encode);
?>