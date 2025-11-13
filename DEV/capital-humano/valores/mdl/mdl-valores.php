<?php
require_once('../../../conf/_CRUD.php');
class MValores extends CRUD{

    protected $bd;

    public function __construct() {

        $this->bd = "rfwsmqex_erp.";
        $this->util = new Utileria();
    }

    function lsUDN(){
        return $this->_Select([
                'table'  => 'udn',
                'values' => 'idUDN AS id, UDN AS valor',
                'where'  => 'Stado = 1',
                'order' => ['ASC'=>'Antiguedad']
            ]);
    }

    function lsStatus(){
        $sql = $this->_Select([
            'table'  => "{$this->bd}status_process",
            'values' => 'idStatus AS id,UPPER(status) AS valor',
        ]);

        return array_merge([["id"=>0,"valor"=>"TODOS LOS ESTADOS"]],$sql);
    }

    function lsTemporada($array){
         $query = "
            SELECT
            val_periods.id AS id,
            val_periods.`name` as valor,
            val_periods.id_UDN
            FROM
            val_periods
            Where id_UDN = ? and status = 1
      ";
      return $this->_Read($query, $array);
    }



    function listEvaluation(){
      $query = "

      SELECT
            rfwsmqex_erp.val_evaluation.idEvaluation AS id,
            DATE_FORMAT(val_evaluation.date_creation, '%d-%m-%Y') AS date,
            DATE_FORMAT(val_evaluation.date_creation, '%H : %i') AS `hour`,
            rfwsmqex_erp.val_evaluation.idEvaluation,
            rfwsmqex_erp.val_evaluation.id_udn,
            rfwsmqex_erp.val_evaluation.id_status,
            rfwsmqex_erp.val_evaluation.date_creation,
            rfwsmqex_erp.val_evaluation.id_evaluator,
            rfwsmqex_gvsl_rrhh.empleados.idEmpleado,
            rfwsmqex_gvsl_rrhh.empleados.Puesto_Empleado,
            rfwsmqex_gvsl_rrhh.empleados.Nombres,
            rfwsmqex_erp.udn.UDN
        FROM
        rfwsmqex_erp.val_evaluation
        INNER JOIN rfwsmqex_gvsl_rrhh.empleados ON rfwsmqex_erp.val_evaluation.id_evaluator = rfwsmqex_gvsl_rrhh.empleados.idEmpleado
        INNER JOIN rfwsmqex_erp.udn ON rfwsmqex_erp.val_evaluation.id_udn = rfwsmqex_erp.udn.idUDN



      ";

      return $this->_Read($query, null);
    }

   function listEvaluation2($array) {
        $query = "
            SELECT
                val_evaluation.idEvaluation AS id,
                DATE_FORMAT(val_evaluation.date_creation, '%d-%m-%Y') AS date,
                DATE_FORMAT(val_evaluation.date_creation, '%H:%i') AS hour,
                val_evaluation.id_udn,
                val_evaluation.id_status,
                val_evaluation.date_creation,
                val_evaluation.id_evaluator,
                empleados.idEmpleado,
                empleados.Puesto_Empleado,
                empleados.Nombres,
                udn.UDN,
                val_periods.name AS season,
                val_periods.id AS idPeriod
            FROM rfwsmqex_erp.val_evaluation
            INNER JOIN rfwsmqex_gvsl_rrhh.empleados
                ON val_evaluation.id_evaluator = empleados.idEmpleado
            INNER JOIN rfwsmqex_erp.udn
                ON val_evaluation.id_udn = udn.idUDN
            INNER JOIN rfwsmqex_erp.val_periods
                ON val_periods.id_UDN = udn.idUDN
                AND val_evaluation.id_period = val_periods.id
             WHERE val_evaluation.date_creation BETWEEN ? AND ?
        ";

        // Condicionales dinámicos
        $params = [$array['fi'] . ' 00:00:00', $array['ff'] . ' 23:59:59'];

        if (isset($array['udn']) && $array['udn'] !== '0') {
            $query .= " AND val_evaluation.id_udn = ?";
            $params[] = $array['udn'];
        }

        if (isset($array['estado']) && $array['estado'] !== '0') {
            $query .= " AND val_evaluation.id_status = ?";
            $params[] = $array['estado'];
        }

        if (isset($array['season']) && $array['season'] !== '0') {
            $query .= " AND val_evaluation.id_period = ?";
            $params[] = $array['season'];
        }

        $query .= " ORDER BY val_evaluation.date_creation ASC";

        return $this->_Read($query, $params);
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

     function deleteEval($array) {

        return $this->_Delete([
            'table'  => "{$this->bd}val_evaluation",
            'where'  => $array['where'],
            'data'   => $array['data']
        ]);
    }

    function getEvaluatorById($array){

      $query = "
        SELECT
            rfwsmqex_erp.val_evaluation.idEvaluation,
            rfwsmqex_erp.val_evaluation.date_creation,
            rfwsmqex_gvsl_rrhh.empleados.FullName,
            rfwsmqex_gvsl_rrhh.empleados.Nombres
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

    function lsEmployed() {
        return $this->_Select([
            'table'     => "rfwsmqex_gvsl_rrhh.empleados",
            'values'    => 'idEmpleado AS id,CONCAT("[",Abreviatura,"] ",Nombres) AS valor',
            'where'     => 'Estado = 1',
            'innerjoin' => ["udn" => "UDN_Empleado = idUDN"],
            'order'     => ['ASC'=>'idUDN,Nombres']
        ]);
    }

    function lsPeriod() {
        return $this->_Select([
            'table'  => "{$this->bd}val_periods",
            'values' => 'id, name AS valor',
            'where'  => 'status = 1',
            'order'  => ['ASC'=>'name']
        ]);
    }
    // Encuesta
    function arrayMatrix($array) {

        $leftjoin = [
            "{$this->bd}val_matrix" => "val_matrix.idMatrix = val_evaluators.id_matrix",
            "rfwsmqex_gvsl_rrhh.empleados" => "idEmpleado = id_evaluated"
        ];

        return $this->_Select([
            'table' => "{$this->bd}val_evaluators",
            'values' => 'id_evaluated AS id, Nombres AS valor',
            'where' => 'id_evaluator = ?
                        AND id_udn = ?
                        AND val_matrix.status = 1',
            'leftjoin' => $leftjoin,
            'data' => $array
        ]);
    }

    function createSurvey($array) {
        $array['table'] = "{$this->bd}val_evaluation";
        return $this->_Insert($array);
    }

    function existSurvey($array) {
        $success = $this->_Select([
            'table'     => "{$this->bd}val_evaluation",
            'values'    => 'idEvaluation',
            'where'     => 'id_evaluator,id_status NOT IN (2,3)',
            'data'      => $array
        ]);

        return isset($success) ? true : false;
    }

    function getSurvey($array) {
        $query = "
            SELECT idEvaluation,date_creation
            FROM {$this->bd}val_evaluation
            WHERE id_evaluator = ?
            AND id_udn = ?
            AND id_status = ?
            AND MONTH(date_creation) = ?
            AND YEAR(date_creation) = ?
        ";

        return $this->_Read($query, $array);
    }

    function maxSurvey() {
        return $this->_Select([
            'table' => "{$this->bd}val_evaluation",
            'values' => 'MAX(idEvaluation) AS id'
        ])[0]['id'];
    }
}
?>
