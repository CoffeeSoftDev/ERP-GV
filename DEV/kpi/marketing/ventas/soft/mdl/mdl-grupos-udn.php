<?php
require_once '../../../../../conf/_CRUD.php';
require_once '../../../../../conf/_Utileria.php';

class mdlGruposUdn extends CRUD {
    protected $util;
    public $bd;

    public function __construct() {
        $this->util = new Utileria;
        $this->bd = "rfwsmqex_gvsl_finanzas.";
    }

    function lsUDN() {
        $query = "
            SELECT 
                idUDN as id, 
                UDN as valor
            FROM rfwsmqex_gvsl.udn
            WHERE Stado = 1
            ORDER BY UDN ASC
        ";

        return $this->_Read($query, null);
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

        $query .= " GROUP BY sp.id_Producto ORDER BY grupoproductos ASC";

        return $this->_Read($query, empty($params) ? null : $params);
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
}
