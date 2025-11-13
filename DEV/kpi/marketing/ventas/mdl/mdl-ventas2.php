<?php
require_once '../../../../conf/_CRUD.php';
require_once '../../../../conf/_Utileria.php';


class mdl extends CRUD {
    protected $util;
    public $bd;

    public function __construct() {
        $this->util = new Utileria;
        $this->bd = "rfwsmqex_gvsl_finanzas.";
    }

    function listSales($array) {
        $query = "
         SELECT
            vb.idBV AS id,
            vb.Fecha_Venta AS fecha,
            DAYNAME(vb.Fecha_Venta) AS dia,
            vb.Cantidad AS cantidad,
            u.UDN AS udn_nombre,
            u.idUDN AS id_udn,
            v.Name_Venta AS categoria,
            v.idVenta AS id_venta,
            vu.Stado  AS estado
            FROM
                rfwsmqex_gvsl_finanzas.venta_bitacora AS vb
            INNER JOIN rfwsmqex_gvsl_finanzas.ventas_udn   AS vu ON vb.id_UV = vu.idUV
            INNER JOIN rfwsmqex_gvsl.udn                   AS u  ON vu.id_UDN = u.idUDN
            INNER JOIN rfwsmqex_gvsl_finanzas.ventas       AS v  ON vu.id_Venta = v.idVenta
            WHERE
                u.idUDN = ?
                AND YEAR(vb.Fecha_Venta)  = ?
                AND MONTH(vb.Fecha_Venta) = ?
            ORDER BY vb.Fecha_Venta ASC, v.Name_Venta ASC
        ";

        return $this->_Read($query, $array);
    }

    function getSaleById($id) {
        return $this->_Select([
            'table' => "{$this->bd}venta_bitacora",
            'values' => "*",
            'where' => 'idBV = ?',
            'data' => [$id]
        ])[0];
    }

    function createSale($array) {
        return $this->_Insert([
            'table' => "{$this->bd}venta_bitacora",
            'values' => $array['values'],
            'data' => $array['data']
        ]);
    }

    function updateSale($array) {
        return $this->_Update([
            'table' => "{$this->bd}venta_bitacora",
            'values' => $array['values'],
            'where' => 'idBV = ?',
            'data' => $array['data']
        ]);
    }


    function existsSaleByDate($array) {
        $query = "
            SELECT COUNT(*) as total
            FROM {$this->bd}venta_bitacora vb
            LEFT JOIN {$this->bd}ventas_udn vu ON vb.id_Folio = vu.idUV
            WHERE vu.id_UDN = ?
                AND vb.Fecha_Venta = ?
        ";

        $result = $this->_Read($query, $array);
        return $result[0]['total'];
    }

    function lsUDN() {
        return $this->_Select([
            'table' => "udn",
            'values' => "idUDN as id, UDN as valor",
            'where' => 'Stado = 1',
            'order' => ['ASC' => 'UDN']
        ]);
    }

    function lsVentas() {
        return $this->_Select([
            'table' => "{$this->bd}ventas",
            'values' => "idVenta as id, Name_Venta as valor",
            'order' => ['ASC' => 'Name_Venta']
        ]);
    }

    function createVentaUDN($array) {
        return $this->_Insert([
            'table' => "{$this->bd}ventas_udn",
            'values' => $array['values'],
            'data' => $array['data']
        ]);
    }

    function updateVentaUDN($array) {
        return $this->_Update([
            'table' => "{$this->bd}ventas_udn",
            'values' => $array['values'],
            'where' => 'idUV = ?',
            'data' => $array['data']
        ]);
    }

    function getVentaUDNByFolio($id_folio) {
        return $this->_Select([
            'table' => "{$this->bd}ventas_udn",
            'values' => "*",
            'where' => 'idUV = ?',
            'data' => [$id_folio]
        ])[0];
    }

    function getFolioByFechaUdn($array) {
        return $this->_Select([
            'table'  => "{$this->bd}soft_folio",
            'values' => "*",
            'where'  => 'fecha_folio = ? AND id_udn = ?',
            'data'   => $array
        ])[0] ?? null;
    }

    function getVentaByFolioId($folioId) {
        return $this->_Select([
            'table'  => "{$this->bd}soft_restaurant_ventas",
            'values' => "*",
            'where'  => 'soft_folio = ?',
            'data'   => [$folioId]
        ])[0] ?? null;
    }

    function createVenta($array) {
        return $this->_Insert([
            'table'  => "{$this->bd}soft_restaurant_ventas",
            'values' => $array['values'],
            'data'   => $array['data']
        ]);
    }

    function updateVenta($array) {
        return $this->_Update([
            'table'  => "{$this->bd}soft_restaurant_ventas",
            'values' => $array['values'],
            'where'  => 'id_venta = ?',
            'data'   => $array['data']
        ]);
    }

    function createFolio($array) {
        return $this->_Insert([
            'table'  => "{$this->bd}soft_folio",
            'values' => $array['values'],
            'data'   => $array['data']
        ]);
    }

    function getSuitesOcupadasByFecha($fecha) {
        $query = "
            SELECT SUM(t.suite) AS total_suites_unicas
            FROM {$this->bd}turno t
            WHERE t.fecha_turno = ?
        ";

        $result = $this->_Read($query, [$fecha]);
        return $result[0]['total_suites_unicas'] ?? 0;
    }
}
