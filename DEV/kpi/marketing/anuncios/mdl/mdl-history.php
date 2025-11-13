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

    function getCPCHistory($array) {
        $query = "
            SELECT 
                MONTH(a.fecha_inicio) as mes,
                MONTHNAME(a.fecha_inicio) as mes_nombre,
                SUM(a.total_monto) as inversion_total,
                SUM(a.total_clics) as total_clics,
                CASE 
                    WHEN SUM(a.total_clics) > 0 
                    THEN (SUM(a.total_monto) / SUM(a.total_clics))
                    ELSE 0 
                END as cpc_promedio
            FROM {$this->bd}anuncio a
            INNER JOIN {$this->bd}campaña c ON a.campaña_id = c.id
            WHERE YEAR(a.fecha_inicio) = ?
            AND c.udn_id = ?
            AND c.red_social_id = ?
            AND a.total_clics > 0
            GROUP BY MONTH(a.fecha_inicio), MONTHNAME(a.fecha_inicio)
            ORDER BY mes ASC
        ";
        
        return $this->_Read($query, $array);
    }

    function getCACHistory($array) {
        $query = "
            SELECT 
                MONTH(a.fecha_inicio) as mes,
                MONTHNAME(a.fecha_inicio) as mes_nombre,
                SUM(a.total_monto) as inversion_total,
                COUNT(DISTINCT p.cliente_id) as numero_clientes,
                CASE 
                    WHEN COUNT(DISTINCT p.cliente_id) > 0 
                    THEN SUM(a.total_monto) / COUNT(DISTINCT p.cliente_id)
                    ELSE 0 
                END as cac
            FROM {$this->bd}anuncio a
            INNER JOIN {$this->bd}campaña c ON a.campaña_id = c.id
            LEFT JOIN {$this->bd}pedido p ON p.anuncio_id = a.id
            WHERE YEAR(a.fecha_inicio) = ?
            AND c.udn_id = ?
            AND c.red_social_id = ?
            GROUP BY MONTH(a.fecha_inicio), MONTHNAME(a.fecha_inicio)
            ORDER BY mes ASC
        ";
        
        return $this->_Read($query, $array);
    }

    function getMonthsArray() {
        return [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
    }
}
