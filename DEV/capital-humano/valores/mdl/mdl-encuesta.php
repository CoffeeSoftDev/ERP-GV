<?php
require_once '../../../conf/_CRUD.php';
require_once('../../../conf/_Utileria.php');
class Encuesta extends CRUD{

    protected $bd;

    public function __construct(){
        $this->bd = "rfwsmqex_erp.";
        $this->util = new Utileria();
    }

    function lsUDN(){
        $query = "SELECT idUDN AS id, UDN AS valor
        FROM udn WHERE Stado = 1";
        return $this->_Read($query, null);
    }

    function listValues(){
        $query = "SELECT
            values_corporate.idValue as id,
            values_corporate.`value` as valor
        FROM
        values_corporate";
        return $this->_Read($query, null);
    }

    function listQuestions(){
        $query = "SELECT
            val_questions.idQuestion as id,
            val_questions.question   as text
        FROM
            {$this->bd}val_questions";
        return $this->_Read($query, null);
    }

    function listQuestionsByValues($array){

        $query = "SELECT
            val_questions.idQuestion as id,
            val_questions.question   as text
        FROM
            val_questions
        WHERE id_value = ? ";

        return $this->_Read($query, $array);
    }

    //  -- encuesta --

    function arrayMatrix($array) {

        $leftjoin = [
            "{$this->bd}val_matrix" => "val_matrix.idMatrix = val_evaluators.id_matrix",
            "rfwsmqex_gvsl_rrhh.empleados" => "idEmpleado = id_evaluated"
        ];

        return $this->_Select([
            'table' => "{$this->bd}val_evaluators",
            'values' => 'id_evaluated AS id, Nombres AS valor,Puesto_empleado as puesto,idMatrix',
            'where' => 'id_evaluator = ?
                        AND id_udn = ?
                        AND val_matrix.status = 1',
            'leftjoin' => $leftjoin,
            'data' => $array
        ]);
    }

    function addMatriz($array){

        return $this->_Insert([
            'table'  => "{$this->bd}val_matrix_evaluation",
            'values' => $array['values'],
            'data'   => $array['data']
        ]);
    }


     function getPuesto($array){

        $query = "

          SELECT
            rh_puestos.Nombre_Puesto,
            rh_puesto_area.idPuesto_Area
            FROM
            rh_puesto_area
            INNER JOIN rh_puestos ON rh_puesto_area.id_Puesto = rh_puestos.idPuesto
            WHERE idPuesto_Area = ?
        ";

        return $this->_Read($query, $array)[0];

    }

    function listEvaluadores($array){

        $query = "

            SELECT
                Nombres,
                FullName,
                id_matrix,
                id_evaluator
            FROM
            rfwsmqex_erp.val_evaluators
            INNER JOIN rfwsmqex_gvsl_rrhh.empleados
            ON rfwsmqex_erp.val_evaluators.id_evaluator = rfwsmqex_gvsl_rrhh.empleados.idEmpleado
            WHERE id_matrix = ?
        ";

        return $this->_Read($query, $array);

    }

    function getEvaluatorById($array){

        $query = "
            SELECT
                rfwsmqex_erp.val_evaluation.idEvaluation as id,
                rfwsmqex_gvsl_rrhh.empleados.Nombres as valor
            FROM
                rfwsmqex_erp.val_evaluation
            INNER JOIN rfwsmqex_gvsl_rrhh.empleados ON rfwsmqex_erp.val_evaluation.id_evaluator = rfwsmqex_gvsl_rrhh.empleados.idEmpleado
            WHERE id_evaluator = ?
        ";

        return $this->_Read($query, $array)[0];

    }

    function getEmployedByID($array){

        $query = "
        SELECT
            rfwsmqex_gvsl_rrhh.empleados.Nombres,
            rfwsmqex_gvsl_rrhh.empleados.FullName
            FROM
            rfwsmqex_gvsl_rrhh.empleados
            where idEmpleado = ?

        ";

        return $this->_Read($query, $array)[0];

    }

    function getListEvaluated($array){

        $query = "
            SELECT
                val_evaluators.id_matrix,
                val_matrix.id_evaluated,
                val_matrix.`status`,
                val_matrix.date_create,
                val_evaluators.id_evaluator,
                val_matrix.id_udn
            FROM
            {$this->bd}val_evaluators
            INNER JOIN {$this->bd}val_matrix ON val_evaluators.id_matrix = val_matrix.idMatrix
            WHERE id_evaluator = ? and status = 1 and id_udn = ?

            ORDER BY id_matrix DESC LIMIT 1
        ";

        return $this->_Read($query, $array);

    }

    function updateEvaluation($array){
        return $this->_Update([
            'table'  => "{$this->bd}val_evaluation",
            'values' => $array['values'],
            'where'  => $array['where'],
            'data'   => $array['data']
        ]);
    }


    // Surveys
    function createSurvey($array){
        return $this->_Insert([
            'table'  => "{$this->bd}val_survey",
            'values' => $array['values'],
            'data'   => $array['data'],
        ]);
    }

    function getSurvey($a){
        $query = "SELECT
            val_survey.idSurvey,
            val_survey.id_evaluated,
            val_survey.id_evaluation,
            val_survey.id_question,
            answered

        FROM {$this->bd}val_survey
        WHERE
        id_evaluated = ?
        and id_evaluation = ?
        and id_question = ?

        ";
        return $this->_Read($query, $a);
    }

    function getSurveyByEmployed($array){
        $query = "SELECT
            val_survey.idSurvey,
            val_survey.id_evaluated,
            val_survey.id_evaluation,
            val_survey.id_question,
            answered

        FROM val_survey
        WHERE
        id_evaluated = ?
        and id_evaluation = ?


        ";

        return $this->_Read($query, $array);
    }

    function updateSurvey($array){
        return $this->_Update([
            'table'  => "{$this->bd}val_survey",
            'values' => $array['values'],
            'where'  => $array['where'],
            'data'   => $array['data']
        ]);
    }






    // 📜 **Eliminar una encuesta**
    public function deleteEncuesta($idEncuesta){
        return $this->_Delete('encuestas', ["id" => $idEncuesta]);
    }
}
