<?php
require_once '../../../conf/_CRUD.php';
require_once '../../../conf/_Utileria.php';

class mdl extends CRUD {
    protected $util;
    protected $bd;

    public function __construct() {
        $this->util = new Utileria;
        $this->bd = '';
    }

    function lsUDN(){
        $query = "SELECT idUDN AS id, UDN AS valor
        FROM udn WHERE Stado = 1";
        $sql = $this->_Read($query, null);

        return array_merge([["id"=>0,"valor"=>"TODAS LAS UDN"]],$sql);
    }

    // Periodos

    function getPeriods(){
        $query = "SELECT
            	val_periods.id AS id,
            	val_periods.`name` AS valor,
            	val_periods.date_init,
            	val_periods.date_end,
            	val_periods.id_UDN AS idE
            FROM
            	val_periods
            WHERE
            	val_periods.`status` = 1";
        return $this->_Read($query, null);
    }

    function getPeriodsByID($array){
        $query = "SELECT
            	val_periods.id AS id,
            	val_periods.`name` AS valor,
            	val_periods.date_init,
            	val_periods.date_end,
            	val_periods.id_UDN AS idE
            FROM
            	val_periods
            WHERE
            	val_periods.`status` = 1
                AND id_UDN = ?

                ";
        return $this->_Read($query, $array);
    }


    function getEntities($array) {
        $values = [
            "val_tabulation.id",
            "val_periods.date_init",
            "val_tabulation.id_period",
            "val_tabulation.stado",
            "val_tabulation.id_UDN",
            "udn.UDN as udn",
            "val_periods.name as period_name",
        ];

        $innerjoin = [
            "val_tabulation"    =>  "val_tabulation.id_period = val_periods.id",
            "udn"               =>  "val_periods.id_UDN = udn.idUDN AND val_tabulation.id_UDN = udn.idUDN",
        ];

        $where = ["val_periods.date_init BETWEEN ? AND ? "];
        $data = [$array['date_init'],$array['date_end']];

        if($array['stado'] != 0){
            $where[] = 'val_tabulation.stado';
            $data[]  = $array['stado'];
        }

        if($array['udn'] != 0){
            $where[] = 'val_periods.id_UDN';
            $data[] = $array['udn'];
        }

        return $this->_Select([
            'table'     => "val_periods",
            'values'    => $values,
            'innerjoin' => $innerjoin,
            'where'     => $where,
            'order'     => ['DESC' => 'val_tabulation.id'],

            'data'      => $data
        ]);
    }

    function getEvaluateds($array){
      $values = [
            'val_tabulation_calification.id ',
            'val_tabulation_calification.te',
            'val_tabulation_calification.pr',
            'val_tabulation_calification.ap',
            'val_tabulation_calification.ps',
            'val_tabulation_calification.calf',
            'val_tabulation_calification.calf_manual',
            'val_tabulation.stado',
            'val_tabulation_calification.people_count',
            'empleados.Nombres as name'
        ];

        $innerjoin = [
            'val_tabulation' => 'val_tabulation.id = val_tabulation_calification.id_tabulation',
            'rfwsmqex_gvsl_rrhh.empleados' => 'val_tabulation_calification.id_evaluated = empleados.idEmpleado'
        ];

        $where = [

            'id_tabulation = ?'
        ];

        return $this->_Select([
            'table'     => 'val_tabulation_calification',
            'values'    => $values,
            'innerjoin' => $innerjoin,
            'where'     => $where,
            'order'     => ['DESC' => 'empleados.Nombres'],
            'data'      => array_values($array),
        ]);
    }

    function getById($array) {
        return $this->_Select([
            'table' => 'val_tabulation',
            'values' => '*',
            'where' => 'id = ?',
            'data' => $array
        ])[0];
    }

    // Tabulacion.
    function existsTabulation($array) {
    $result = $this->_Select([
        'table'  => 'val_tabulation',
        'values' => 'id',
        'where'  => 'id_period = ? AND id_UDN = ?',
        'data'   =>  $array
    ]);

    return !empty($result); // true si ya existe
}

    function createTabulation($array) {
        return $this->_Insert([
            'table'  => 'val_tabulation',
            'values' => $array['values'],
            'data'   => $array['data'],
        ]);
    }

    function maxTabulation($array) {

        return $this->_Select([
            'table'  => "val_tabulation",
            'values' => "MAX(id) AS id",
            'where'  => "id_UDN = ?",
            'data'   => $array
        ])[0]['id'];
    }

    // Score

    function getEvaluated($array) {
        $query = "

        SELECT DISTINCT val_survey.id_evaluated
        FROM val_survey
        INNER JOIN val_evaluation
            ON val_survey.id_evaluation = val_evaluation.idEvaluation
        WHERE val_evaluation.id_period = ?

        ";
        return $this->_Read($query, $array);
    }

    function getValuesCorporate() {
        $query = "
            SELECT
                idValue as id,
                value,
                shortName
            FROM values_corporate
            WHERE status = 1 ";
        return $this->_Read($query, null);
    }

    function getEvaluatedScores($array) {
        $query = "
            SELECT
            SUM(val_survey.answered) AS total_respuestas,
            AVG(val_survey.answered) AS promedio
            FROM val_survey
            INNER JOIN val_evaluation
            ON val_survey.id_evaluation = val_evaluation.idEvaluation
            INNER JOIN val_questions
            ON val_survey.id_question = val_questions.idQuestion
            WHERE val_evaluation.id_period = ?
            AND val_survey.id_evaluated = ?
            AND val_questions.id_value = ?
        ";
        return $this->_Read($query, $array)[0];
    }

    function getCountEvaluators($array){
         $query = "
           SELECT COUNT(DISTINCT val_evaluation.idEvaluation) AS total
            FROM val_survey
            INNER JOIN val_evaluation ON val_survey.id_evaluation = val_evaluation.idEvaluation
            WHERE val_evaluation.id_period = ?
            AND val_survey.id_evaluated = ?
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

    function insertEvaluatedScore($array) {
        return $this->_Insert([
            'table'  => 'val_tabulation_calification',
            'values' => $array['values'],
            'data'   => $array['data'],
        ]);
    }

    function update($array) {
        return $this->_Update([
            'table' => 'val_tabulation',
            'values' => $array['values'],
            'where' => $array['where'],
            'data' => $array['data'],
        ]);
    }
}
