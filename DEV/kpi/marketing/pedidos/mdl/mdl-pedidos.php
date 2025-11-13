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

    function lsUDN() {
        $query = "
            SELECT idUDN AS id, UDN AS valor
            FROM udn
            WHERE Stado = 1 AND idUDN NOT IN (8, 10, 7)
            ORDER BY UDN DESC
        ";
        return $this->_Read($query, null);
    }

    function lsCanales($array) {
        return $this->_Select([
            'table' => "{$this->bd}canal",
            'values' => "id, nombre AS valor",
            'where' => 'active = ?',
            'order' => ['ASC' => 'id'],
            'data' => $array
        ]);
    }

    function lsProductos($array) {
        return $this->_Select([
            'table' => "{$this->bd}producto",
            'values' => "id, nombre AS valor, udn_id",
            'where' => 'active = ?',
            'order' => ['ASC' => 'id'],
            'data' => $array
        ]);
    }

    function lsCampanas($array) {
        return $this->_Select([
            'table' => "{$this->bd}campaña",
            'values' => "id, nombre AS valor",
            'where' => 'active = ?',
            'order' => ['DESC' => 'fecha_creacion'],
            'data' => $array
        ]);
    }

    function lsSocialNetworks($array) {
        return $this->_Select([
            'table' => "{$this->bd}red_social",
            'values' => "id, nombre AS valor",
            'where' => 'active = ?',
            'order' => ['ASC' => 'nombre'],
            'data' => [1]
        ]);
    }

    function lsAnuncios() {
        $query = "
            SELECT 
                a.id,
                a.nombre AS valor,
                a.imagen,
                a.fecha_inicio,
                a.fecha_fin,
                c.nombre AS campana_nombre,
                c.udn_id
            FROM {$this->bd}anuncio a
            LEFT JOIN {$this->bd}campaña c ON a.campaña_id = c.id
            WHERE c.active = 1
            AND fecha_resultado IS NULL
            ORDER BY a.fecha_inicio DESC
        ";
        return $this->_Read($query, null);
    }

    // Clients.
    function getAllClients(){
        $query = "
            SELECT
                id,
                nombre as name,
                telefono as phone,
                correo as email,
                fecha_cumpleaños 
            FROM
            {$this->bd}cliente
            WHERE active = 1 ";

        return $this->_Read($query,null);
    }

    function searchClientes($array) {
        $query = "
            SELECT 
                id,
                CONCAT(nombre) AS text,
                nombre,
                telefono,
                correo,
                fecha_cumpleaños,
                udn_id
            FROM {$this->bd}cliente
            WHERE (nombre LIKE ? OR telefono LIKE ?)
            AND active = 1
            LIMIT 10
        ";
        return $this->_Read($query, $array);
    }

    function searchClientesByName($array) {
        $query = "
            SELECT 
                id,
                CONCAT(nombre) AS text,
                nombre,
                telefono,
                correo,
                fecha_cumpleaños,
                udn_id
            FROM {$this->bd}cliente
            WHERE (nombre LIKE ?)
            AND active = 1
            LIMIT 10
        ";
        return $this->_Read($query, $array);
    }

    function getClienteById($array) {
        $query = "
            SELECT 
                id,
                 CONCAT(nombre) AS text,
                nombre,
                telefono,
                correo,
                fecha_cumpleaños,
                udn_id
            FROM {$this->bd}cliente
            WHERE id = ?
        ";
        $result = $this->_Read($query, $array);
        return $result[0] ?? null;
    }

    // Pedidos
    function listPedidos($array) {
        $query = "
            SELECT 
                p.id,
                p.monto,
                p.fecha_pedido,
                p.fecha_creacion,
                p.envio_domicilio,
                p.pago_verificado,
                p.llego_establecimiento,
                p.active,
                cl.nombre AS cliente_nombre,
                cl.telefono AS cliente_telefono,
                c.nombre AS canal_nombre,
                rs.nombre AS red_social_nombre,
                rs.color AS red_social_color,
                rs.icono AS red_social_icono,
                p.udn_id,
                u.usser AS user_nombre,
                p.user_id,
                a.nombre AS anuncio_nombre
            FROM {$this->bd}pedido p
            LEFT JOIN {$this->bd}cliente cl ON p.cliente_id = cl.id
            LEFT JOIN {$this->bd}canal c ON p.canal_id = c.id
            LEFT JOIN {$this->bd}red_social rs ON p.red_social_id = rs.id
            LEFT JOIN {$this->bd}anuncio a ON p.anuncio_id = a.id
            LEFT JOIN usuarios u ON p.user_id = idUser 
            WHERE p.udn_id = ?
            AND p.active = 1
            AND p.fecha_pedido IS NOT NULL
            AND YEAR(p.fecha_pedido) = ?
            AND MONTH(p.fecha_pedido) = ?
            ORDER BY p.fecha_creacion DESC
        ";
        return $this->_Read($query, $array);
    }

    function createCliente($array) {
        return $this->_Insert([
            'table' => $this->bd . 'cliente',
            'values' => $array['values'],
            'data' => $array['data']
        ]);
    }

    function maxCliente() {
        $query = "
            SELECT MAX(id) AS max_id
            FROM {$this->bd}cliente
        ";
        $result = $this->_Read($query, null);
        return $result[0]['max_id'] ?? 0;
    }

    function getClienteByPhone($array) {
        $result = $this->_Select([
            'table' => $this->bd . 'cliente',
            'values' => '*',
            'where' => 'telefono = ?',
            'data' => $array
        ]);
        return $result[0] ?? null;
    }

    function updateCliente($array) {
        return $this->_Update([
            'table' => $this->bd . 'cliente',
            'values' => $array['values'],
            'where' => 'id = ?',
            'data' => $array['data']
        ]);
    }

    function createPedido($array) {
        return $this->_Insert([
            'table' => $this->bd . 'pedido',
            'values' => $array['values'],
            'data' => $array['data']
        ]);
    }

    function maxPedido() {
        $query = "
            SELECT MAX(id) AS max_id
            FROM {$this->bd}pedido
        ";
        $result = $this->_Read($query, null);
        return $result[0]['max_id'] ?? 0;
    }

    function createProductoPedido($array) {
        return $this->_Insert([
            'table' => $this->bd . 'producto_pedido',
            'values' => $array['values'],
            'data' => $array['data']
        ]);
    }

    function getPedidoById($array) {
        $query = "
            SELECT 
                p.*,
                cl.nombre AS cliente_nombre,
                cl.telefono AS cliente_telefono,
                cl.correo AS cliente_correo,
                cl.fecha_cumpleaños AS cliente_cumpleaños
            FROM {$this->bd}pedido p
            LEFT JOIN {$this->bd}cliente cl ON p.cliente_id = cl.id
            WHERE p.id = ?
        ";
        $result = $this->_Read($query, $array);
        return $result[0] ?? null;
    }

    function getProductosByPedido($array) {
        $query = "
            SELECT producto_id
            FROM {$this->bd}producto_pedido
            WHERE pedido_id = ?
        ";
        return $this->_Read($query, $array);
    }

    function updatePedido($array) {
        return $this->_Update([
            'table' => $this->bd . 'pedido',
            'values' => $array['values'],
            'where' => 'id = ?',
            'data' => $array['data']
        ]);
    }

    function removeProductsOrder($array){
        $query = "
            DELETE FROM {$this->bd}producto_pedido
            WHERE pedido_id = ?
        ";

        return $this->_CUD($query, $array);
    }




    // function deleteProductosPedido($array) {
    //     return $this->_Delete([
    //         'table' => "{$this->bd}producto_pedido",
    //         'where' => $array['where'],
    //         'data'  => $array['data']
    //     ]);
    // }

    function validatePedidoAge($array) {
        $query = "
            SELECT 
                id,
                fecha_creacion,
                DATEDIFF(NOW(), fecha_creacion) AS dias_transcurridos
            FROM {$this->bd}pedido
            WHERE id = ?
        ";
        $result = $this->_Read($query, $array);
        
        if (empty($result)) {
            return ['valid' => false, 'dias' => 999];
        }
        
        $dias = $result[0]['dias_transcurridos'];
        return [
            'valid' => $dias <= 7,
            'dias' => $dias
        ];
    }
}
