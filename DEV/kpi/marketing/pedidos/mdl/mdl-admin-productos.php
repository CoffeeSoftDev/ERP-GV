<?php
require_once '../../../../conf/_CRUD.php';
require_once '../../../../conf/_Utileria.php';
session_start();

class mdl extends CRUD {
    protected $util;
    public $bd;

    public function __construct() {
        $this->util = new Utileria;
        $this->bd = "rfwsmqex_marketing.";
    }

    function listProductos($array) {
        $where = 'producto.active = ?';
        $data = [$array[0]];

        if ($array[1] !== null && $array[1] != '') {
            $where .= ' AND producto.udn_id = ?';
            $data[] = $array[1];
        }

        $query = "
            SELECT 
                producto.id,
                producto.nombre,
                producto.descripcion,
                producto.udn_id,
                udn.UDN as udn_nombre,
                producto.active
            FROM {$this->bd}producto
            LEFT JOIN udn ON producto.udn_id = udn.idUDN
            WHERE {$where}
            ORDER BY producto.id DESC
        ";

        return $this->_Read($query, $data);
    }

    function getProductoById($id) {
        return $this->_Select([
            'table'  => $this->bd . 'producto',
            'values' => '*',
            'where'  => 'id = ?',
            'data'   => [$id]
        ])[0];
    }

    function createProducto($array) {
        return $this->_Insert([
            'table'  => $this->bd . 'producto',
            'values' => $array['values'],
            'data'   => $array['data']
        ]);
    }

    function updateProducto($array) {
        return $this->_Update([
            'table'  => $this->bd . 'producto',
            'values' => $array['values'],
            'where'  => $array['where'],
            'data'   => $array['data']
        ]);
    }

    function existsProductoByName($array) {
        $query = "
            SELECT id
            FROM {$this->bd}producto
            WHERE LOWER(nombre) = LOWER(?)
            AND active = 1
            AND udn_id = ?
        ";

        $exists = $this->_Read($query, $array);
        return count($exists) > 0;
    }

    function lsUDN() {
        return $this->_Select([
            'table'  => $this->bd . 'udn',
            'values' => 'idUDN as id, UDN as valor',
            'where'  => 'Stado = 1',
            'order'  => ['ASC' => 'UDN']
        ]);
    }
}
