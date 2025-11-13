<?php
require_once('../../../conf/_CRUD.php');
class MConcentrado extends CRUD{
    private $bd_ch = null;

    public function __construct() {
        $this->bd_ch = "rfwsmqex_gvsl_rrhh.";
    }
function lsUDN(){
    $sql = $this->_Select([
            'table'  => 'udn',
            'values' => 'idUDN AS id, UDN AS valor',
            'where'  => 'Stado = 1',
            'order' => ['DESC'=>'Antiguedad']
        ]);

        return array_merge([['id' => 0, "valor" => "SELECCIONAR UDN"]], $sql);
}
function listPeriodos($array){
    $query = "SELECT
                val_periods.id AS id,
                val_periods.`name` AS valor
            FROM
                val_periods
            WHERE
                val_periods.id_UDN = ? ORDER BY id DESC LIMIT 2";
    return $this->_Read($query, $array);
}
function lsColaboradores($array){
    $query = "SELECT
                    rfwsmqex_erp.val_matrix.id_evaluated AS id,
                    rfwsmqex_gvsl_rrhh.empleados.Nombres AS valor
                FROM
                    rfwsmqex_erp.val_evaluation
                    INNER JOIN rfwsmqex_erp.val_matrix_evaluation ON val_evaluation.idEvaluation = val_matrix_evaluation.id_evaluation
                    INNER JOIN rfwsmqex_erp.val_matrix ON val_matrix_evaluation.id_matrix = val_matrix.idMatrix
                    INNER JOIN rfwsmqex_gvsl_rrhh.empleados ON rfwsmqex_erp.val_matrix.id_evaluated = rfwsmqex_gvsl_rrhh.empleados.idEmpleado
                WHERE
                    val_evaluation.id_period = ?
                GROUP BY id_evaluated";
    return $this->_Read($query,$array);
}
public function getValuesCorporate() {
    $sql = "SELECT idValue as id, value as valor FROM {$this->bd}values_corporate";
    return $this->_Read($sql, null);
}
public function countAllQuestions(){
    $result = $this->_Read("SELECT COUNT(*) AS count FROM `val_questions` WHERE enabled = 1", null);
    return isset($result) ? $result[0]['count'] : 0;
}
public function getQuestionsByValue($array) {
    return $this->_Select([
        'table'     => "{$this->bd}val_questions",
        'values'    => 'idQuestion AS id, question AS valor, enabled as status',
        'where'     => 'id_value',
        'data'      => $array
    ]);
}
public function getEvaluators($array){
    $query = "SELECT
                    val_evaluation.id_evaluator,
                    val_evaluation.idEvaluation AS idEvaltion
                FROM
                    val_survey
                    INNER JOIN val_evaluation ON val_survey.id_evaluation = val_evaluation.idEvaluation
                WHERE
                    val_evaluation.id_udn = ?
                    AND val_evaluation.id_period = ?
                    AND val_survey.id_evaluated = ?
                GROUP BY
                    id_evaluator";
    return $this->_Read($query, $array);
}
public function getAnswered($array){
    $query = "SELECT
                    answered
                FROM
                    val_survey
                WHERE
                    val_survey.id_evaluated = ?
                    AND val_survey.id_question = ?
                    AND val_survey.id_evaluation = ?";
    $sql = $this->_Read($query,$array);
    return isset($sql) ? $sql[0]['answered'] : 0;
}
public function totalQuestions(){
    $sql = $this->_Read("SELECT COUNT(*) AS cont FROM val_questions WHERE val_questions.enabled = 1", null);
    return isset($sql) ? $sql[0]['cont'] : 0;
}
public function getListEvaluators($array){
    $query = "SELECT
                rfwsmqex_gvsl_rrhh.empleados.Nombres AS valor,
                rfwsmqex_gvsl_rrhh.empleados.idEmpleado AS id
            FROM
                rfwsmqex_erp.val_matrix
                INNER JOIN rfwsmqex_gvsl_rrhh.empleados ON rfwsmqex_erp.val_matrix.id_evaluated = rfwsmqex_gvsl_rrhh.empleados.idEmpleado
            WHERE
                rfwsmqex_erp.val_matrix.`status` = 1";
    return $this->_Read($query, $array );
}
public function resultTabulacion($array){
    $result = $this->_Select([
        'table'     => "val_tabulation",
        'values'    => 'ap,ps,pr,te,calf',
        'innerjoin' => ["val_tabulation_calification" => 'val_tabulation.id = id_tabulation'],
        'where'     => 'id_UDN,id_period,id_evaluated',
        'data'      => $array
    ]);
    // Si no hay resultados, retornar null
    return isset($result) ? $result[0] : null;
}
}
?>
