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

    function getCampaignSummary($array) {
        $query = "
            SELECT 
                ca.id AS campaña_id,
                ca.nombre AS campaña,
                ca.estrategia,
                an.fecha_inicio,
                an.fecha_fin,
                an.total_monto,
                an.total_clics,
                an.nombre AS anuncio,
                tipo.nombre AS tipo,
                clasificacion.nombre AS clasificacion
            FROM {$this->bd}anuncio an
            INNER JOIN {$this->bd}campaña ca ON ca.id = an.campaña_id
            INNER JOIN {$this->bd}tipo_anuncio tipo ON an.tipo_id = tipo.id
            INNER JOIN {$this->bd}clasificacion_anuncio clasificacion ON an.clasificacion_id = clasificacion.id
            WHERE ca.udn_id = ? AND ca.red_social_id = ? AND YEAR(an.fecha_inicio) = ? AND MONTH(an.fecha_inicio) = ?
        ";
        return $this->_Read($query, $array);
    }

    function getCampaignTotals($array) {
        $query = "
            SELECT 
                SUM(an.total_monto) AS total_monto,
                SUM(an.total_clics) AS total_clics
            FROM {$this->bd}anuncio an
            INNER JOIN {$this->bd}campaña ca ON ca.id = an.campaña_id
            WHERE ca.udn_id = ? AND ca.red_social_id = ? AND YEAR(an.fecha_inicio) = ? AND MONTH(an.fecha_inicio) = ?
        ";
        return $this->_Read($query, $array);
    }

    function getMonthlySummary($array) {
        $query = "
            SELECT
                COUNT(DISTINCT ca.id) AS total_campañas,
                COUNT(an.id) AS total_anuncios
            FROM {$this->bd}anuncio an
            INNER JOIN {$this->bd}campaña ca ON ca.id = an.campaña_id
            WHERE ca.udn_id = ? AND ca.red_social_id = ? AND YEAR(an.fecha_inicio) = ? AND MONTH(an.fecha_inicio) = ?
        ";
        return $this->_Read($query, $array);
    }
}
