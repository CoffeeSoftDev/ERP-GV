<?php
require_once '../../../../../conf/_CRUD2.php';
require_once '../../../../../conf/_Utileria.php';

class mdl extends CRUD {
    protected $util;
    public $bd;

    public function __construct() {
        $this->util = new Utileria;
        $this->bd = "rfwsmqex_gvsl_finanzas.";
    }

    function listProductos($array = []) {
        $query = "
            SELECT 
                sp.id_Producto,
                sp.clave_producto_soft as clave_producto,
                sp.descripcion,
                sp.id_grupoc as id_grupo_productos,
                sp.id_udn,
                sp.costo,
                u.UDN as udn_nombre,
                COALESCE(g.grupo, 'Sin grupo') as grupo_nombre,
                COALESCE(SUM(spv.cantidad), 0) as cantidad_vendida,
                COALESCE(spv.precioventa, 0) as precio_venta,
                COALESCE(spv.precioventacatalogo, 0) as precio_licencia
            FROM {$this->bd}soft_productos AS sp
            INNER JOIN rfwsmqex_gvsl.udn AS u 
                ON sp.id_udn = u.idUDN
            LEFT JOIN {$this->bd}soft_grupoc AS g
                ON sp.id_grupoc = g.idgrupo
            LEFT JOIN {$this->bd}soft_productosvendidos AS spv
                ON sp.id_Producto = spv.id_productos
            LEFT JOIN {$this->bd}soft_folio AS sf
                ON spv.idFolioRestaurant = sf.id_folio
            WHERE 1=1
        ";

        $params = [];

        // Filtro por UDN
        if (!empty($array['udn']) && $array['udn'] !== 'all') {
            $query .= " AND u.idUDN = ?";
            $params[] = $array['udn'];
        }

        // Filtro por Grupo
        if (!empty($array['grupo']) && $array['grupo'] !== 'all') {
            $query .= " AND sp.id_grupoc = ?";
            $params[] = $array['grupo'];
        }

        // Filtro por Año
        if (!empty($array['anio'])) {
            $query .= " AND YEAR(sf.fecha_folio) = ?";
            $params[] = $array['anio'];
        }

        // Filtro por Mes
        if (!empty($array['mes'])) {
            $query .= " AND MONTH(sf.fecha_folio) = ?";
            $params[] = $array['mes'];
        }

        $query .= " GROUP BY sp.id_Producto ORDER BY sp.descripcion ASC";

        return $this->_Read($query, empty($params) ? null : $params);
    }

    function getProductoById($id) {
        $result = $this->_Select([
            'table' => "{$this->bd}soft_productos",
            'values' => "*",
            'where' => 'id_Producto = ?',
            'data' => [$id]
        ]);

        return !empty($result) ? $result[0] : null;
    }

    function lsUDN() {
        return $this->_Select([
            'table' => "rfwsmqex_gvsl.udn",
            'values' => "idUDN as id, UDN as valor",
            'where' => 'Stado = 1',
            'order' => ['ASC' => 'UDN']
        ]);
    }

    function lsGrupos() {
        return $this->_Select([
            'table' => "{$this->bd}soft_grupoc",
            'values' => "idgrupo as id, grupo as valor",
            'order' => ['ASC' => 'grupo']
        ]);
    }

    function listConcentrado($array = []) {
        $query = "
            SELECT 
                sp.id_Producto,
                sp.clave_producto_soft as clave_producto,
                sp.descripcion,
                sp.id_grupoc as id_grupo_productos,
                sp.id_udn,
                u.UDN as udn_nombre,
                COALESCE(g.grupo, 'Sin grupo') as grupo_nombre,
                
                COALESCE(SUM(CASE 
                    WHEN MONTH(sf.fecha_folio) IN (1, 2, 3) 
                    THEN spv.cantidad 
                    ELSE 0 
                END), 0) as cantidad_3_meses,
                
                COALESCE(SUM(CASE 
                    WHEN MONTH(sf.fecha_folio) IN (4, 5, 6) 
                    THEN spv.cantidad 
                    ELSE 0 
                END), 0) as cantidad_6_meses,
                
                COALESCE(SUM(CASE 
                    WHEN MONTH(sf.fecha_folio) IN (7, 8, 9) 
                    THEN spv.cantidad 
                    ELSE 0 
                END), 0) as cantidad_9_meses,
                
                COALESCE(SUM(CASE 
                    WHEN MONTH(sf.fecha_folio) IN (10, 11, 12) 
                    THEN spv.cantidad 
                    ELSE 0 
                END), 0) as cantidad_12_meses,
                
                COALESCE(SUM(spv.cantidad), 0) as cantidad_total
                
            FROM {$this->bd}soft_productos AS sp
            INNER JOIN rfwsmqex_gvsl.udn AS u 
                ON sp.id_udn = u.idUDN
            LEFT JOIN {$this->bd}soft_grupoc AS g
                ON sp.id_grupoc = g.idgrupo
            LEFT JOIN {$this->bd}soft_productosvendidos AS spv
                ON sp.id_Producto = spv.id_productos
            LEFT JOIN {$this->bd}soft_folio AS sf
                ON spv.idFolioRestaurant = sf.id_folio
            WHERE 1=1
        ";

        $params = [];

        if (!empty($array['udn']) && $array['udn'] !== 'all') {
            $query .= " AND u.idUDN = ?";
            $params[] = $array['udn'];
        }

        if (!empty($array['grupo']) && $array['grupo'] !== 'all') {
            $query .= " AND sp.id_grupoc = ?";
            $params[] = $array['grupo'];
        }

        if (!empty($array['anio'])) {
            $query .= " AND YEAR(sf.fecha_folio) = ?";
            $params[] = $array['anio'];
        }

        $query .= " GROUP BY sp.id_Producto 
                    HAVING cantidad_total > 0
                    ORDER BY sp.clave_producto ASC";

        return $this->_Read($query, empty($params) ? null : $params);
    }
}
