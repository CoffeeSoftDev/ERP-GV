<?php

require_once('../../../../conf/_CRUD.php');
require_once('../../../../conf/_Utileria.php');

class mdl extends CRUD {

    public $util;
    public $bd;

    function __construct() {
        $this->util = new Utileria();
        $this->bd = "rfwsmqex_marketing.";
    }

    function lsClientes($params) {
        $leftjoin = [
            'udn' => 'cliente.udn_id = udn.idUDN'
        ];

        $whereClause = "1=1";
        $data = [];

        if (isset($params[0]) && $params[0] !== '') {
            $whereClause .= " AND cliente.active = ?";
            $data[] = $params[0];
        }

        if (isset($params[1]) && $params[1] !== '' && $params[1] !== 'all') {
            $whereClause .= " AND cliente.udn_id = ?";
            $data[] = $params[1];
        }

        if (isset($params[2]) && $params[2] !== '' && $params[2] !== 'all') {
            $whereClause .= " AND cliente.vip = ?";
            $data[] = $params[2];
        }

        return $this->_Select([
            'table' => "{$this->bd}cliente",
            'values' => "
                cliente.id,
                cliente.nombre,
                cliente.apellido_paterno,
                cliente.apellido_materno,
                cliente.vip,
                cliente.telefono,
                cliente.correo,
                cliente.fecha_cumpleaños,
                DATE_FORMAT(cliente.fecha_creacion, '%d/%m/%Y %H:%i') as fecha_creacion,
                cliente.udn_id,
                cliente.active,
                udn.UDN as udn_nombre,
                udn.color,
                udn.Abreviatura as udn_abreviatura
            ",
            'leftjoin' => $leftjoin,
            'where' => $whereClause,
            'order' => ['DESC' => 'cliente.id'],
            'data' => $data
        ]);
    }

    function getClienteById($id) {
        $cliente = $this->_Select([
            'table' => "{$this->bd}cliente",
            'values' => '*',
            'where' => 'id = ?',
            'data' => [$id]
        ]);

        if (empty($cliente)) {
            return null;
        }

        $clienteData = $cliente[0];

        $domicilio = $this->_Select([
            'table' => "{$this->bd}domicilio_cliente",
            'values' => '*',
            'where' => 'cliente_id = ? AND es_principal = 1',
            'data' => [$id]
        ]);

        if (!empty($domicilio)) {
            $clienteData['domicilio'] = $domicilio[0];
        } else {
            $clienteData['domicilio'] = null;
        }

        return $clienteData;
    }

    function createCliente($data) {
        return $this->_Insert([
            'table' => "{$this->bd}cliente",
            'values' => $data['values'],
            'data' => $data['data']
        ]);
    }

    function createDomicilio($data) {
        return $this->_Insert([
            'table' => "{$this->bd}domicilio_cliente",
            'values' => $data['values'],
            'data' => $data['data']
        ]);
    }

    function updateCliente($data) {
        return $this->_Update([
            'table' => "{$this->bd}cliente",
            'values' => $data['values'],
            'where' => 'id = ?',
            'data' => $data['data']
        ]);
    }

    function updateDomicilio($data) {
        return $this->_Update([
            'table' => "{$this->bd}domicilio_cliente",
            'values' => $data['values'],
            'where' => 'cliente_id = ? AND es_principal = 1',
            'data' => $data['data']
        ]);
    }

    function existsClienteByPhone($telefono, $excludeId = null) {
        $query = "
            SELECT COUNT(*) as total
            FROM {$this->bd}cliente 
            WHERE telefono = ?
        ";
        
        $params = [$telefono];

        if ($excludeId !== null) {
            $query .= " AND id != ?";
            $params[] = $excludeId;
        }

        $result = $this->_Read($query, $params);
        return isset($result[0]['total']) ? (int)$result[0]['total'] : 0;
    }

    function getDomicilioPrincipal($clienteId) {
        $result = $this->_Select([
            'table' => "{$this->bd}domicilio_cliente",
            'values' => '*',
            'where' => 'cliente_id = ? AND es_principal = 1',
            'data' => [$clienteId]
        ]);

        return !empty($result) ? $result[0] : null;
    }

    function lsUDN() {
        return $this->_Select([
            'table' => "udn",
            'values' => "idUDN as id, UDN as valor, Abreviatura",
            'where' => 'Stado = 1',
            'order' => ['ASC' => 'UDN']
        ]);
    }

    function getTotalClientesActivos($udnId = null) {
        $whereClause = "active = 1";
        $params = [];

        if ($udnId !== null) {
            $whereClause .= " AND udn_id = ?";
            $params[] = $udnId;
        }

        $query = "
            SELECT COUNT(*) as total
            FROM {$this->bd}cliente
            WHERE {$whereClause}
        ";

        $result = $this->_Read($query, $params);
        return isset($result[0]['total']) ? (int)$result[0]['total'] : 0;
    }

    function getTotalClientesVIP($udnId = null) {
        $whereClause = "active = 1 AND vip = 1";
        $params = [];

        if ($udnId !== null) {
            $whereClause .= " AND udn_id = ?";
            $params[] = $udnId;
        }

        $query = "
            SELECT COUNT(*) as total
            FROM {$this->bd}cliente
            WHERE {$whereClause}
        ";

        $result = $this->_Read($query, $params);
        return isset($result[0]['total']) ? (int)$result[0]['total'] : 0;
    }

    function getClientesCumpleañosMes($udnId = null) {
        $whereClause = "active = 1 AND MONTH(fecha_cumpleaños) = MONTH(CURDATE())";
        $params = [];

        if ($udnId !== null) {
            $whereClause .= " AND udn_id = ?";
            $params[] = $udnId;
        }

        return $this->_Select([
            'table' => "{$this->bd}cliente",
            'values' => "
                id,
                nombre,
                apellido_paterno,
                apellido_materno,
                telefono,
                correo,
                fecha_cumpleaños,
                DAY(fecha_cumpleaños) as dia_cumpleaños
            ",
            'where' => $whereClause,
            'order' => ['ASC' => 'DAY(fecha_cumpleaños)'],
            'data' => $params
        ]);
    }

    // ============================================
    // COMPORTAMIENTO DE CLIENTES
    // ============================================

    /**
     * Obtiene el comportamiento detallado de un cliente
     * @param int $clienteId ID del cliente
     * @return array Datos de comportamiento del cliente
     */
    function getComportamientoCliente($clienteId) {
        $query = "
            SELECT 
                c.id,
                c.nombre,
                c.apellido_paterno,
                c.apellido_materno,
                c.vip,
                c.telefono,
                c.correo,
                c.fecha_creacion,
                u.UDN as udn_nombre,
                COUNT(p.id) as total_pedidos,
                SUM(p.monto) as monto_total,
                AVG(p.monto) as ticket_promedio,
                MAX(p.fecha_pedido) as ultima_compra,
                MIN(p.fecha_pedido) as primera_compra,
                DATEDIFF(CURDATE(), MAX(p.fecha_pedido)) as dias_sin_comprar
            FROM {$this->bd}cliente c
            LEFT JOIN udn u ON c.udn_id = u.idUDN
            LEFT JOIN {$this->bd}pedido p ON c.id = p.cliente_id
            WHERE c.id = ?
            GROUP BY c.id
        ";

        $result = $this->_Read($query, [$clienteId]);
        return !empty($result) ? $result[0] : null;
    }

 
    function getHistorialPedidos($clienteId, $limit = 10) {
        $query = "
            SELECT 
                p.id,
                p.fecha_pedido,
                p.monto,
                p.envio_domicilio,
                c.nombre as canal_nombre,
                u.UDN as udn_nombre
            FROM {$this->bd}pedido p
            LEFT JOIN {$this->bd}canal c ON p.canal_id = c.id
            LEFT JOIN udn u ON p.udn_id = u.idUDN
            WHERE p.cliente_id = ?
            ORDER BY p.fecha_pedido DESC
            LIMIT 10
        ";

        return $this->_Read($query, [$clienteId]);
    }

    
    function getComportamientoClientes($params) {
        $whereClause = "c.active = ?";
        $data = [$params[0]];

        if (isset($params[1]) && $params[1] !== '' && $params[1] !== 'all') {
            $whereClause .= " AND c.udn_id = ?";
            $data[] = $params[1];
        }

        $query = "
            SELECT 
                c.id,
                c.nombre,
             
                c.vip,
                c.telefono,
                c.correo,
                u.UDN as udn_nombre,
                u.color,
                COUNT(p.id) as total_pedidos,
                SUM(p.monto) as monto_total,
                AVG(p.monto) as ticket_promedio,
                MAX(p.fecha_pedido) as ultima_compra,
                DATEDIFF(CURDATE(), MAX(p.fecha_pedido)) as dias_sin_comprar,
                CASE 
                    WHEN COUNT(p.id) = 0 THEN 'Sin pedidos'
                    WHEN DATEDIFF(CURDATE(), MAX(p.fecha_pedido)) <= 30 THEN 'Activo'
                    WHEN DATEDIFF(CURDATE(), MAX(p.fecha_pedido)) <= 90 THEN 'Regular'
                    ELSE 'Inactivo'
                END as frecuencia
            FROM {$this->bd}cliente c
            LEFT JOIN udn u ON c.udn_id = u.idUDN
            LEFT JOIN {$this->bd}pedido p ON c.id = p.cliente_id
            WHERE {$whereClause}
            GROUP BY c.id
            ORDER BY total_pedidos DESC, ultima_compra DESC
        ";

        return $this->_Read($query, $data);
    }

    /**
     * Obtiene clientes por frecuencia de compra
     * @param string $frecuencia Tipo de frecuencia (activo, regular, inactivo)
     * @param int|null $udnId Filtrar por unidad de negocio
     * @return array Lista de clientes
     */
    function getClientesPorFrecuencia($frecuencia, $udnId = null) {
        $whereClause = "c.active = 1";
        $params = [];

        if ($udnId !== null && $udnId !== 'all') {
            $whereClause .= " AND c.udn_id = ?";
            $params[] = $udnId;
        }

        $havingClause = "";
        switch ($frecuencia) {
            case 'activo':
                $havingClause = "HAVING dias_sin_comprar <= 30";
                break;
            case 'regular':
                $havingClause = "HAVING dias_sin_comprar > 30 AND dias_sin_comprar <= 90";
                break;
            case 'inactivo':
                $havingClause = "HAVING dias_sin_comprar > 90 OR dias_sin_comprar IS NULL";
                break;
        }

        $query = "
            SELECT 
                c.id,
                c.nombre,
                c.apellido_paterno,
                c.apellido_materno,
                c.telefono,
                u.UDN as udn_nombre,
                COUNT(p.id) as total_pedidos,
                MAX(p.fecha_pedido) as ultima_compra,
                DATEDIFF(CURDATE(), MAX(p.fecha_pedido)) as dias_sin_comprar
            FROM {$this->bd}cliente c
            LEFT JOIN udn u ON c.udn_id = u.idUDN
            LEFT JOIN {$this->bd}pedido p ON c.id = p.cliente_id
            WHERE {$whereClause}
            GROUP BY c.id
            {$havingClause}
            ORDER BY dias_sin_comprar ASC
        ";

        return $this->_Read($query, $params);
    }

    function getTopClient( $udnId = null) {
        $whereClause = " c.active = 1 ";
        $params = [];

        if ($udnId !== null && $udnId !== 'all') {
            $whereClause .= " AND c.udn_id = ? ";
            $params[] = $udnId;
        }

        
        $query = "
            SELECT 
                c.id,
                c.nombre,
                c.apellido_paterno,
                c.apellido_materno,
                c.vip,
                c.telefono,
                u.UDN as udn_nombre,
                COUNT(p.id) as total_pedidos,
                SUM(p.monto) as monto_total,
                AVG(p.monto) as ticket_promedio,
                MAX(p.fecha_pedido) as ultima_compra
            FROM {$this->bd}cliente c
            LEFT JOIN udn u ON c.udn_id = u.idUDN
            LEFT JOIN {$this->bd}pedido p ON c.id = p.cliente_id
            WHERE {$whereClause}
            GROUP BY c.id
            HAVING total_pedidos > 0
            ORDER BY monto_total DESC
            LIMIT 10
        ";

        return $this->_Read($query, $params);
    }

    function updateClientVipStatus($array) {
        return $this->_Update([
            'table' => "{$this->bd}cliente",
            'values' => $array['values'],
            'where' => 'id = ?',
            'data' => $array['data']
        ]);
    }
}
