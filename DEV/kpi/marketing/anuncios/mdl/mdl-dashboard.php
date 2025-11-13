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
    

    function getDashboardData($array) {
        $query = "
            SELECT 
                SUM(a.total_monto) as inversion_total,
                SUM(a.total_clics) as total_clics,
                COUNT(DISTINCT a.campaña_id) as total_campañas,
                COUNT(a.id) as total_anuncios,
                CASE 
                    WHEN SUM(a.total_clics) > 0 
                    THEN SUM(a.total_monto) / SUM(a.total_clics)
                    ELSE 0 
                END as cpc_promedio
            FROM {$this->bd}anuncio a
            INNER JOIN {$this->bd}campaña c ON a.campaña_id = c.id
            WHERE c.udn_id = ?
            AND c.red_social_id = ?
            AND YEAR(a.fecha_inicio) = ?
            AND MONTH(a.fecha_inicio) = ?
            AND fecha_resultado IS NULL
        ";
        
        return $this->_Read($query, $array)[0];
    }

    function getMonthlyTrends($array) {
        $query = "
            SELECT 
                DAY(a.fecha_inicio) as dia,
                SUM(a.total_monto) as inversion,
                SUM(a.total_clics) as clics
            FROM {$this->bd}anuncio a
            INNER JOIN {$this->bd}campaña c ON a.campaña_id = c.id
            WHERE c.udn_id = ?
            AND c.red_social_id = ?
            AND YEAR(a.fecha_inicio) = ?
            AND MONTH(a.fecha_inicio) = ?
            GROUP BY DAY(a.fecha_inicio)
            ORDER BY dia ASC
        ";
        
        return $this->_Read($query, $array);
    }

    function getComparativeData($array) {
        $query = "
            SELECT 
                YEAR(a.fecha_inicio) as año,
                SUM(a.total_monto) as inversion_total,
                SUM(a.total_clics) as total_clics,
                CASE 
                    WHEN SUM(a.total_clics) > 0 
                    THEN SUM(a.total_monto) / SUM(a.total_clics)
                    ELSE 0 
                END as cpc_promedio
            FROM {$this->bd}anuncio a
            INNER JOIN {$this->bd}campaña c ON a.campaña_id = c.id
            WHERE c.udn_id = ?
            AND c.red_social_id = ?
            AND MONTH(a.fecha_inicio) = ?
            AND YEAR(a.fecha_inicio) IN (?, ?)
            GROUP BY YEAR(a.fecha_inicio)
            ORDER BY año DESC
        ";
        
        return $this->_Read($query, $array);
    }

    function getTopCampaigns($array) {
        $query = "
            SELECT 
                c.nombre AS campaña,
                a.nombre AS anuncio,
                c.estrategia,
                a.total_monto AS inversion,
                a.total_clics AS clics,
                CASE 
                    WHEN a.total_clics > 0 
                    THEN a.total_monto / a.total_clics
                    ELSE 0 
                END AS cpc
            FROM {$this->bd}campaña c
            INNER JOIN {$this->bd}anuncio a ON c.id = a.campaña_id
            WHERE c.udn_id = ?
            AND c.red_social_id = ?
            AND YEAR(a.fecha_inicio) = ?
            AND MONTH(a.fecha_inicio) = ?
            ORDER BY cpc ASC, a.total_clics DESC
            LIMIT 5
        ";
        
        return $this->_Read($query, $array);
    }

    function getAnnouncementsByType($array) {
        $query = "
            SELECT 
                t.nombre as tipo,
                COUNT(a.id) as cantidad,
                SUM(a.total_monto) as inversion,
                SUM(a.total_clics) as clics
            FROM {$this->bd}anuncio a
            INNER JOIN {$this->bd}campaña c ON a.campaña_id = c.id
            INNER JOIN {$this->bd}tipo_anuncio t ON a.tipo_id = t.id
            WHERE c.udn_id = ?
            AND c.red_social_id = ?
            AND YEAR(a.fecha_inicio) = ?
            AND MONTH(a.fecha_inicio) = ?
            GROUP BY t.id, t.nombre
            ORDER BY inversion DESC
        ";
        
        return $this->_Read($query, $array);
    }
}
