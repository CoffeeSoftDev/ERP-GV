<?php
require_once '../../../../../conf/_CRUD.php';
require_once '../../../../../conf/_Utileria.php';

class mdlConcentradoPeriodos extends CRUD {
    protected $util;
    public $bd;
    public $bd_costsys;

    public function __construct() {
        $this->util = new Utileria;
        $this->bd = "rfwsmqex_gvsl_finanzas.";
         $this->bd_costsys = 'rfwsmqex_gvsl_costsys.';
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

    
    function listMenu($array){
        
        $query = "SELECT
        costsys_menu.idmenu as id,
        costsys_menu.categoria as nombre,
        status,
        costsys_menu.id_udn,
        costsys_menu.id_clasificacion,
        clasificacion.Clasificacion
        FROM
        {$this->bd_costsys}costsys_menu
        INNER JOIN {$this->bd_costsys}clasificacion ON costsys_menu.id_clasificacion = clasificacion.idClasificacion
        WHERE costsys_menu.id_udn = 4 and id_clasificacion = ?
        order by orderMenu DESC
        ";


        $sql = $this->_Read($query, $array);
        return $sql;
    }
}
