<?php
require_once '../../../../conf/_CRUD.php';
require_once '../../../../conf/_Utileria.php';
session_start();

class mdl extends CRUD {
    protected $util;
    public $bd;

    public function __construct() {
        $this->util = new Utileria;
        $this->bd = "database_name.";
    }

    function lsUDN() {
        $query = "
            SELECT idUDN AS id, UDN AS valor
            FROM udn
            WHERE Stado = 1 AND idUDN NOT IN (8, 10, 7)
            ORDER BY UDN DESC
        ";
        return $this->_Read($query, null);
    }

    function listProductos($array) {
        return $this->_Select([
            'table' => $this->bd . 'producto',
            'values' => "
                id,
                nombre,
                descripcion,
                precio,
                tipo,
                udn_id,
                active,
                DATE_FORMAT(fecha_creacion, '%Y-%m-%d') AS fecha_creacion
            ",
            'where' => 'active = ? AND udn_id = ?',
            'order' => ['DESC' => 'id'],
            'data' => $array
        ]);
    }

    function getProductoById($array) {
        $result = $this->_Select([
            'table' => $this->bd . 'producto',
            'values' => '*',
            'where' => 'id = ?',
            'data' => $array
        ]);
        return $result[0] ?? null;
    }

    function createProducto($array) {
        return $this->_Insert([
            'table' => $this->bd . 'producto',
            'values' => $array['values'],
            'data' => $array['data']
        ]);
    }

    function updateProducto($array) {
        return $this->_Update([
            'table' => $this->bd . 'producto',
            'values' => $array['values'],
            'where' => 'id = ?',
            'data' => $array['data']
        ]);
    }

    function existsProductoByName($array) {
        $query = "
            SELECT id
            FROM {$this->bd}producto
            WHERE LOWER(nombre) = LOWER(?)
            AND udn_id = ?
            AND active = 1
        ";
        $exists = $this->_Read($query, $array);
        return count($exists) > 0;
    }

    function getProductoUsageStats($array) {
        $query = "
            SELECT 
                COUNT(pp.id) AS total_pedidos,
                SUM(p.monto) AS total_ingresos
            FROM {$this->bd}producto_pedido pp
            LEFT JOIN {$this->bd}pedido p ON pp.pedido_id = p.id
            WHERE pp.producto_id = ?
            AND p.active = 1
        ";
        $result = $this->_Read($query, $array);
        return $result[0] ?? ['total_pedidos' => 0, 'total_ingresos' => 0];
    }
}
