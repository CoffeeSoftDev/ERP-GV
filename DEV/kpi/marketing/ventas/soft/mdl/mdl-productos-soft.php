<?php
require_once '../../../../../conf/_CRUD.php';
require_once '../../../../../conf/_Utileria.php';

class mdlProductosSoft extends CRUD {
    protected $util;
    public $bd;

    public function __construct() {
        $this->util = new Utileria;
        $this->bd = "rfwsmqex_gvsl_finanzas.";
    }

    function listProductos($array = []) {
        $query = "
            SELECT 
                sp.id_Producto as id,
                sp.clave_producto_soft as clave_producto,
                sp.descripcion,
                sp.id_grupoc,
                DATE_FORMAT(sp.fecha, '%Y-%m-%d') AS fecha,
                sp.costo,
                sp.id_grupo_productos,
                sp.activo_soft,
                sp.id_udn,
                u.UDN as udn_nombre,
                gp.idgrupo,
                gp.grupoproductos,
                gc.grupoc as grupo_nombre,
                COALESCE(SUM(spv.cantidad), 0) as cantidad_vendida,
                COALESCE(spv.precioventa, 0) as precio_venta,
                COALESCE(spv.precioventacatalogo, 0) as precio_licencia
            FROM {$this->bd}soft_productos AS sp
            INNER JOIN rfwsmqex_gvsl.udn AS u 
                ON sp.id_udn = u.idUDN
            INNER JOIN {$this->bd}soft_grupoproductos AS gp
                ON sp.id_grupo_productos = gp.idgrupo
            LEFT JOIN {$this->bd}soft_grupoc AS gc
                ON sp.id_grupoc = gc.idgrupo
            LEFT JOIN {$this->bd}soft_productosvendidos AS spv
                ON sp.id_Producto = spv.id_productos
            LEFT JOIN {$this->bd}soft_folio AS sf
                ON spv.idFolioRestaurant = sf.id_folio
            WHERE 1=1
        ";

        $params = [];

        if (!empty($array['udn']) && $array['udn'] !== 'all') {
            $query .= " AND sp.id_udn = ?";
            $params[] = $array['udn'];
        }

        if (!empty($array['grupo']) && $array['grupo'] !== 'all') {
            $query .= " AND sp.id_grupo_productos = ?";
            $params[] = $array['grupo'];
        }

        if (!empty($array['anio'])) {
            $query .= " AND YEAR(sf.fecha_folio) = ?";
            $params[] = $array['anio'];
        }

        if (!empty($array['mes'])) {
            $query .= " AND MONTH(sf.fecha_folio) = ?";
            $params[] = $array['mes'];
        }

        $query .= " GROUP BY sp.id_Producto ORDER BY grupoproductos asc";

        return $this->_Read($query, empty($params) ? null : $params);
    }

    function select_homologar2($array){
        $query = "
        SELECT
        *
        FROM
        rfwsmqex_gvsl_finanzas.soft_costsys
        WHERE id_soft_productos = ? ";

        return $this->_Read($query, $array);
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

    function getEstadisticasHomologacionGrupo2($idGrupo, $udn = 'all') {
        $query = "
            SELECT 
                COUNT(DISTINCT sp.id_Producto) as total_productos,
                COUNT(DISTINCT CASE WHEN h.idhomologado IS NOT NULL THEN sp.id_Producto END) as productos_homologados
            FROM {$this->bd}soft_productos AS sp
            WHERE sp.id_grupo_productos = ?
        ";

        $params = [$idGrupo];

        if ($udn !== 'all') {
            $query .= " AND sp.id_udn = ?";
            $params[] = $udn;
        }

        $result = $this->_Read($query, $params);

        $total = $result[0]['total_productos'] ?? 0;
        $homologados = $result[0]['productos_homologados'] ?? 0;

        return [
            'total' => $total,
            'homologados' => $homologados,
            'sin_homologar' => $total - $homologados
        ];
    }

    function lsUDN() {
        return $this->_Select([
            'table' => "rfwsmqex_gvsl.udn",
            'values' => "idUDN as id, UDN as valor",
            'where' => 'Stado = 1',
            'order' => ['ASC' => 'UDN']
        ]);
    }

    function lsGrupos($array = []) {
        $query = "
            SELECT 
                idgrupo as id, 
                grupoproductos as valor,
                clavegrupo,
                id_udn
            FROM {$this->bd}soft_grupoproductos
            WHERE 1=1
        ";

        $params = [];

        if (!empty($array['udn']) && $array['udn'] !== 'all') {
            $query .= " AND id_udn = ?";
            $params[] = $array['udn'];
        }

        $query .= " ORDER BY grupoproductos ASC";

        return $this->_Read($query, empty($params) ? null : $params);
    }

    function listGrupos($array = []) {
        $query = "
            SELECT 
                g.idgrupo as id,
                g.grupoproductos,
                g.id_udn,
                COUNT(p.id_Producto) as cantidad_productos
            FROM {$this->bd}soft_grupoproductos g
            LEFT JOIN {$this->bd}soft_productos p ON g.idgrupo = p.id_grupo_productos
            WHERE 1=1
        ";

        $params = [];

        if (!empty($array['udn']) && $array['udn'] !== 'all') {
            $query .= " AND g.id_udn = ?";
            $params[] = $array['udn'];
        }

        $query .= " GROUP BY g.idgrupo, g.grupoproductos, g.id_udn
                    ORDER BY g.grupoproductos ASC";

        return $this->_Read($query, empty($params) ? null : $params);
    }

    function listConcentrado($array = []) {
        $query = "
            SELECT 
                sp.id_Producto,
                sp.clave_producto_soft as clave_producto,
                sp.descripcion,
                sp.id_grupoc,
                sp.id_grupo_productos,
                sp.id_udn,
                u.UDN as udn_nombre,
                gp.grupoproductos,
                COALESCE(gc.grupoc, 'Sin grupo') as grupo_nombre,
                
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
            INNER JOIN {$this->bd}soft_grupoproductos AS gp
                ON sp.id_grupo_productos = gp.idgrupo
            LEFT JOIN {$this->bd}soft_grupoc AS gc
                ON sp.id_grupoc = gc.idgrupo
            LEFT JOIN {$this->bd}soft_productosvendidos AS spv
                ON sp.id_Producto = spv.id_productos
            LEFT JOIN {$this->bd}soft_folio AS sf
                ON spv.idFolioRestaurant = sf.id_folio
            WHERE 1=1
        ";

        $params = [];

        if (!empty($array['udn']) && $array['udn'] !== 'all') {
            $query .= " AND sp.id_udn = ?";
            $params[] = $array['udn'];
        }

        if (!empty($array['grupo']) && $array['grupo'] !== 'all') {
            $query .= " AND sp.id_grupo_productos = ?";
            $params[] = $array['grupo'];
        }

        if (!empty($array['anio'])) {
            $query .= " AND YEAR(sf.fecha_folio) = ?";
            $params[] = $array['anio'];
        }

        $query .= " GROUP BY sp.id_Producto 
                    HAVING cantidad_total > 0
                    ORDER BY gp.grupoproductos ASC, sp.clave_producto ASC";

        return $this->_Read($query, empty($params) ? null : $params);
    }

    function getEstadisticasHomologacionGrupo($idGrupo, $udn = 'all') {
        // $query = "
        //     SELECT 
        //         COUNT(DISTINCT sp.id_Producto) as total_productos,
        //         COUNT(DISTINCT CASE WHEN h.idhomologado IS NOT NULL THEN sp.id_Producto END) as productos_homologados
        //     FROM {$this->bd}soft_productos AS sp
        //     LEFT JOIN {$this->bd}soft_homologacion AS h
        //         ON sp.id_Producto = h.id_soft_productos
        //     WHERE sp.id_grupo_productos = ?
        // ";

        // $params = [$idGrupo];

        // if ($udn !== 'all') {
        //     $query .= " AND sp.id_udn = ?";
        //     $params[] = $udn;
        // }

        // $result = $this->_Read($query, $params);

        // $total = $result[0]['total_productos'] ?? 0;
        // $homologados = $result[0]['productos_homologados'] ?? 0;

        // return [
        //     'total' => $total,
        //     'homologados' => $homologados,
        //     'sin_homologar' => $total - $homologados
        // ];
    }

    function select_homologar($array) {

        $query = "
            SELECT 
                idhomologado,
                id_costsys_recetas,
                id_soft_productos
            FROM {$this->bd}soft_costsys
            WHERE id_soft_productos = ?
        ";

        return $this->_Read($query, $array);
    }

    function listGruposConEstadisticas($array = []) {
        // $query = "
        //     SELECT 
        //         gp.idgrupo,
        //         gp.grupoproductos,
        //         gp.clavegrupo,
        //         COUNT(DISTINCT sp.id_Producto) as total_productos,
        //         COUNT(DISTINCT CASE WHEN h.idhomologado IS NOT NULL THEN sp.id_Producto END) as productos_homologados,
        //         COUNT(DISTINCT CASE WHEN h.idhomologado IS NULL THEN sp.id_Producto END) as productos_sin_homologar
        //     FROM {$this->bd}soft_grupoproductos AS gp
        //     INNER JOIN {$this->bd}soft_productos AS sp
        //         ON gp.idgrupo = sp.id_grupo_productos
        //     LEFT JOIN {$this->bd}soft_homologacion AS h
        //         ON sp.id_Producto = h.id_soft_productos
        //     WHERE 1=1
        // ";

        // $params = [];

        // if (!empty($array['udn']) && $array['udn'] !== 'all') {
        //     $query .= " AND gp.id_udn = ?";
        //     $params[] = $array['udn'];
        // }

        // $query .= " GROUP BY gp.idgrupo, gp.grupoproductos, gp.clavegrupo
        //             ORDER BY gp.grupoproductos ASC";

        // return $this->_Read($query, empty($params) ? null : $params);
    }


}


  


  
