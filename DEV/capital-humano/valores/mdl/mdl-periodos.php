<?php
require_once('../../../conf/_CRUD.php');
require_once('../../../conf/_Utileria.php');

class mdl extends CRUD {
    protected $bd;

    public function __construct() {
        $this->bd  = "rfwsmqex_erp."; // Cambia si el nombre real de la tabla es diferente
        $this->util = new Utileria();
    }

    function lsUDN(){
        $query = "SELECT idUDN AS id, UDN AS valor
        FROM udn WHERE Stado = 1 ORDER BY idUDN desc";
        return $this->_Read($query, null);
    }

    function getPeriods($data) {
        $startDate = $data['fi'] . ' 00:00:00';
        $endDate   = $data['ff'] . ' 23:59:59';
        $params    = [$startDate, $endDate];

        $query = "
            SELECT
                val_periods.id,
                DATE_FORMAT(val_periods.date_init, '%d-%m-%Y') AS start_date,
                DATE_FORMAT(val_periods.date_end, '%d-%m-%Y') AS end_date,
                val_periods.id_UDN,
                val_periods.name,
                udn.UDN,
                val_periods.status
            FROM
                {$this->bd}val_periods
            INNER JOIN
                {$this->bd}udn ON val_periods.id_UDN = udn.idUDN
            WHERE
                val_periods.date_init BETWEEN ? AND ?
        ";

        if (!empty($data['status'])) {
            $query .= " AND val_periods.status = ?";
            $params[] = $data['status'];
        }

        if (!empty($data['udn']) && $data['udn'] !== '0') {
            $query .= " AND val_periods.id_UDN = ?";
            $params[] = $data['udn'];
        }

        $query .= " ORDER BY val_periods.date_init ASC";

        return $this->_Read($query, $params);
    }

    // 📜 Obtener periodo por ID
    function getPeriodById($id) {
        return $this->_Select([
            "table" => "{$this->bd}val_periods",
            "values" => "id, date_init, date_end, id_UDN, name, status",
            "where" => "id = ?",
            "data"  => $id
        ]);
    }

    function createPeriod($data) {
        return $this->_Insert([
            "table" => "{$this->bd}val_periods",
            'values' => $data['values'],
            'data'   => $data['data'],
        ]);
    }

    function updatePeriod($data) {
        return $this->_Update([
            "table" => "{$this->bd}val_periods",
            'values' => $data['values'],
            'where'  => $data['where'],
            'data'   => $data['data']
        ]);
    }

    function destroyPeriod($data) {
        return $this->_Delete([
            "table" => "{$this->bd}val_periods",
            'values' => $data['values'],
            'where'  => $data['where'],
            'data'   => $data['data']
        ]);
    }

    function existPeriod($idPeriod) {
        $result = $this->_Select([
            'table'  => "{$this->bd}val_evaluation",
            'values' => 'COUNT(*) AS total',
            'where'  => 'id_period = ?',
            'data'   => [$idPeriod]
        ]);
        return $result[0]['total'] ?? 0;
    }

    public function existsActivePeriod($idUdn) {
        $result = $this->_Select([
            'table'  => "{$this->bd}val_periods",
            'values' => 'COUNT(*) AS total',
            'where'  => 'id_UDN = ? AND status = 1',
            'data'   => [$idUdn]
        ]);
        return $result[0]['total'] ?? 0;
    }



}
