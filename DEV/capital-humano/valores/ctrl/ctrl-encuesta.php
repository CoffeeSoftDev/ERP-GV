<?php
if (empty($_POST['opc'])) exit(0);
// conf. api
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

setlocale(LC_TIME, 'es_ES.UTF-8');
date_default_timezone_set('America/Mexico_City');

// incluir tu modelo
require_once ('../mdl/mdl-encuesta.php');

// sustituir 'mdl' extends de acuerdo al nombre que tiene el modelo
class ctrl extends Encuesta {

    public function init(){
        return [
            'udn' => $this -> lsUDN()
        ];
    }

    function getGroup(){
       $listEvaluators = $this->arrayMatrix([$_POST['id_evaluator'], $_POST['id_udn']]);
    
       $__evaluators = [];

       $questions = $this->listQuestions();
     

       foreach ($listEvaluators as $evaluator) {
           
           $surveys   = $this->getSurveyByEmployed([$evaluator['id'], $_POST['idEvaluation']]);
           $puesto    = $this -> getPuesto([$evaluator['puesto']]);

           $__evaluators[] = [
                'id'     => $evaluator['id'],
                'valor'  => $evaluator['valor'].' ('.$puesto['Nombre_Puesto'].')',
                'puesto' => $puesto['Nombre_Puesto'],
                'items'  => count($questions),
                'result' => count($surveys)
           ]; 
       }


       return [
           'evaluators' => $__evaluators ,
           [$_POST['id_evaluator'], $_POST['id_udn']],
         
       ];
    }

    function getQuestionnaire(){

        $valores = $this->listValues();
        $__row = [];
        
        foreach ($valores as $valor) {
            
            $__data = [];
            $questions = $this->listQuestionsByValues([$valor['id']]);

            foreach ($questions as $question) {

                $getSurvey = $this->getSurvey([$_POST['id_evaluated'],$_POST['id_evaluation'],$question['id']]);


                $__data[] = [
                    'id'        => $question['id'],
                    'text'      => $question['text'],
                    'data'      => $getSurvey    
                ];
            }



            $__row[]   = [
                'id'        => $valor['id'],
                'title'     => $valor['valor'],
                'questions' => $__data
            ];
        }
        return $__row;
    }

    //  Survey
    function addSurvey() {
        $status = 500;
        $message = 'No se pudo insertar correctamente';

        $getSurvey = $this->getSurvey([$_POST['id_evaluated'],$_POST['id_evaluation'],$_POST['id_question']]);

        if($getSurvey): // edit survey
            $__values = [
                'id_evaluated'    => $_POST['id_evaluated'],
                'id_evaluation'   => $_POST['id_evaluation'],
                'id_question'     => $_POST['id_question'],
                'answered'        => $_POST['answered'],
                'idSurvey'        => $getSurvey[0]['idSurvey']  
            ];
            $message = 'Se actualizo correctamente';

            $create = $this->updateSurvey($this->util->sql($__values,1));

        else: // add survey
            $__values = [
                'id_evaluation'   => $_POST['id_evaluation'],
                'id_evaluated'    => $_POST['id_evaluated'],
                'id_question'     => $_POST['id_question'],
                'answered'        => $_POST['answered'],
            ];

            $create = $this->createSurvey($this->util->sql($__values));
            $message = 'Se agrego correctamente.';
        endif;    

     

        if ($create == true) {
            $status  = 200;
        }

        return [
            'status'   => $status,
            'message' => $message,
           $getSurvey
        ];
    }

    function endEvaluation(){
        $status = 500;
        $message = 'Error al finalizar encuesta';
        
        $edit = $this->updateEvaluation($this->util->sql([
            'id_status'    => $_POST['id_status'],
            'idEvaluation' => $_POST['idEvaluation']
        ], 1));

        $lsMatriz = $this -> arrayMatrix([
            $_POST['idEmployed'],
            $_POST['udn'],
          
        ]) ;

        foreach ($lsMatriz as $matriz) {
            $values = [
                'id_matrix'        => $matriz['idMatrix'],
                'id_evaluation'    =>  $_POST['idEvaluation']
            ];
            $asign[] = $this->addMatriz($this->util->sql($values));
        }
        
        if ($edit) {
            $status = 200;
            $message = 'Se finalizo con exito';
        }
        return [
            'status'  => $status,
            'message' => $message,
           $asign
        ];
    }


}

// Instancia del objeto

$obj = new ctrl();
$fn = $_POST['opc'];
$encode = $obj->$fn();

echo json_encode($encode);