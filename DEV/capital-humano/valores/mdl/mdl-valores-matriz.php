<?php
require_once('../../../conf/_CRUD.php');
class MValoresmatriz extends CRUD{
    protected $bd;
    public $ch;
  
    public function __construct() {
        $this->ch = "rfwsmqex_erp.";   
        $this-> $bd_ch = "rfwsmqex_gvsl_rrhh.";
        $this->util = new Utileria(); 
    }
    
    function lsUDN() {
        return $this->_Select([
                'table'  => 'udn',
                'values' => 'idUDN AS id, UDN AS valor',
                'where'  => 'Stado = 1',
                'order' => ['ASC'=>'Antiguedad']
        ]);
    }

    function lsStatus() {
        $sql = $this->_Select([
            'table'  => "{$this->bd}status_process",
            'values' => 'idStatus AS id,UPPER(status) AS valor',
        ]);
        
        return array_merge([["id"=>0,"valor"=>"TODOS LOS ESTADOS"]],$sql);
    }

    function lsMatrix($array) {
        $values = [
            'val_matrix.idMatrix AS id',
            'empleados.Nombres',
            'udn.UDN',
            'val_matrix.status as status',
            'val_matrix.id_evaluated',
            "DATE_FORMAT(val_matrix.date_create, '%Y-%m-%d') AS date",
            "DATE_FORMAT(val_matrix.date_create, '%H:%i') AS hours"
        ];

        $innerjoin = [
            "rfwsmqex_gvsl_rrhh.empleados" => "val_matrix.id_evaluated = empleados.idEmpleado",
            "rfwsmqex_erp.udn"             => "val_matrix.id_udn = udn.idUDN"
        ];

        // Manejo de fechas
        $startDate = $array['fi'] . ' 00:00:00';
        $endDate   = $array['ff'] . ' 23:59:59';

        $where = ['val_matrix.date_create BETWEEN ? AND ?', 'val_matrix.status = 1'];

        // Reasignar fechas al array
        $array['fi'] = $startDate;
        $array['ff'] = $endDate;

        // Filtro por UDN
        if (isset($array['udn']) && $array['udn'] !== '0') {
            $where[] = 'val_matrix.id_udn = ?';
        } else {
            unset($array['udn']);
        }

        // Filtro por empleado
        if (isset($array['employee']) && $array['employee'] !== '0') {
            $where[] = 'val_matrix.id_evaluated = ?';
        } else {
            unset($array['employee']);
        }
        return $this->_Select([
            'table'      => 'rfwsmqex_erp.val_matrix AS val_matrix',
            'values'     => $values,
            'innerjoin'  => $innerjoin,
            'where'      => $where,
            'order'      => ['DESC' => 'val_matrix.date_create'],
            'data'       => array_values($array)
        ]);
    }
    
    function isMatrixUsed($idMatrix) {
        $result = $this->_Select([
            'table'  => "{$this->bd}val_matrix_evaluation",
            'values' => 'COUNT(*) AS total',
            'where'  => 'id_matrix = ? ',
            'data'   => [$idMatrix]
        ]);

        
        return $result[0]['total'] ?? 0;
    }

    function listEvaluadores($array) {
      $query = "
        SELECT
            Nombres,
            FullName,
            id_matrix,
            id_evaluator,
            UDN
        FROM
        rfwsmqex_erp.val_evaluators
        INNER JOIN rfwsmqex_gvsl_rrhh.empleados ON rfwsmqex_erp.val_evaluators.id_evaluator = rfwsmqex_gvsl_rrhh.empleados.idEmpleado
        LEFT JOIN rfwsmqex_erp.udn ON rfwsmqex_gvsl_rrhh.empleados.UDN_Empleado = rfwsmqex_erp.udn.idUDN
        WHERE id_matrix = ? 
        ";
      return $this->_Read($query, $array);
    }

    function lsEmployedForUdn($array) {
        return $this->_Select([
            'table'     => "rfwsmqex_gvsl_rrhh.empleados",
            'values'    => 'idEmpleado AS id,CONCAT("[",Abreviatura,"] ",Nombres) AS valor',
            'where'     => 'Estado = 1, UDN_Empleado = ?',
            'innerjoin' => ["udn" => "UDN_Empleado = idUDN"],
            'order'     => ['ASC'=>'idUDN,Nombres'],
            'data'      => $array
        ]);
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

    // Matriz de evaluación
    function existMatrix($array) {
        return $this->_Select([
            'table' => "rfwsmqex_erp.val_matrix",
            'values' => 'idMatrix',
            'where' => 'id_udn = ? AND id_evaluated = ? AND status = 1 AND MONTH(date_create) = MONTH(NOW())',
            'data' => $array
        ])[0]['idMatrix'];
    }

    function getMatrixById($array) {
        // Obtener el evaluador
        $values = [
            'rfwsmqex_erp.val_matrix.idMatrix AS id',
            'rfwsmqex_erp.val_matrix.id_udn',
            'rfwsmqex_erp.val_matrix.id_evaluated',
            'rfwsmqex_erp.val_matrix.status',
            'rfwsmqex_erp.val_matrix.date_create',
            'rfwsmqex_gvsl_rrhh.empleados.Nombres AS nombre',
        ];

        $innerjoin = [
            "rfwsmqex_gvsl_rrhh.empleados" => "rfwsmqex_erp.val_matrix.id_evaluated = rfwsmqex_gvsl_rrhh.empleados.idEmpleado"
        ];
        return $this->_Select([
            'table' => "rfwsmqex_erp.val_matrix",
            'values' => $values,
            'innerjoin' => $innerjoin,
            'where' => 'idMatrix = ?',
            'data' => $array
        ]);
    }

    function createMatrix($array) {
        return $this->_Insert([
            'table' => "rfwsmqex_erp.val_matrix",
            'values' => $array['values'],
            'data' => $array['data']
        ]);
    }

    function updateMatrix($data) {
        return $this->_Update([
            'table'  => "rfwsmqex_erp.val_matrix",
            'values' => $data['values'],
            'where'  => $data['where'],
            'data'   => $data['data']
        ]);
    }

    function maxMatrix() {
        return $this->_Select([
            'table' => "rfwsmqex_erp.val_matrix",
            'values' => 'MAX(idMatrix) AS id'
        ])[0]['id'];
    }

    // Evaluadores
    function getEvaluator($array) {
        $query = "
        SELECT
            Nombres,
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

    function createEvaluators($array) {
        return $this->_Insert([
            'table' => "rfwsmqex_erp.val_evaluators",
            'values' => $array['values'],
            'data' => $array['data']
        ]);
    }

    function deleteEvaluators($data) {
        return $this->_Delete([
            'table'  => "{$this->ch}val_evaluators",
            'values' => $data['values'],
            'where'  => $data['where'],
            'data'   => $data['data']
        ]);
    }
}
?>
