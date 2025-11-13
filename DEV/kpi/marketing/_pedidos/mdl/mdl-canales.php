<?php
require_once '../../../../conf/_CRUD.php';
require_once '../../../../conf/_Utileria.php';
session_start();

class mdl extends CRUD {
    protected $util;
    public $bd;

    public function __construct() {
        $this->util = new Utileria;
        $this->bd = "database_name.";
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

    function lsRedesSociales($array) {
        return $this->_Select([
            'table' => "{$this->bd}red_social",
            'values' => "id, nombre AS valor, icono, color",
            'where' => 'active = ?',
            'order' => ['ASC' => 'nombre'],
            'data' => $array
        ]);
    }

    function listCanales($array) {
        return $this->_Select([
            'table' => $this->bd . 'canal',
            'values' => "
                id,
                nombre,
                active,
                DATE_FORMAT(fecha_creacion, '%Y-%m-%d') AS fecha_creacion
            ",
            'where' => 'active = ?',
            'order' => ['DESC' => 'id'],
            'data' => $array
        ]);
    }

    function getCanalById($array) {
        $result = $this->_Select([
            'table' => $this->bd . 'canal',
            'values' => '*',
            'where' => 'id = ?',
            'data' => $array
        ]);
        return $result[0] ?? null;
    }

    function createCanal($array) {
        return $this->_Insert([
            'table' => $this->bd . 'canal',
            'values' => $array['values'],
            'data' => $array['data']
        ]);
    }

    function updateCanal($array) {
        return $this->_Update([
            'table' => $this->bd . 'canal',
            'values' => $array['values'],
            'where' => 'id = ?',
            'data' => $array['data']
        ]);
    }

    function existsCanalByName($array) {
        $query = "
            SELECT id
            FROM {$this->bd}canal
            WHERE LOWER(nombre) = LOWER(?)
            AND active = 1
        ";
        $exists = $this->_Read($query, $array);
        return count($exists) > 0;
    }

    function listCampanas($array) {
        $leftjoin = [
            $this->bd . 'red_social' => 'campana.red_social_id = red_social.id'
        ];

        return $this->_Select([
            'table' => $this->bd . 'campana',
            'values' => "
                campana.id,
                campana.nombre,
                campana.estrategia,
                campana.fecha_inicio,
                campana.fecha_fin,
                campana.presupuesto,
                campana.total_clics,
                campana.active,
                campana.fecha_creacion,
                red_social.nombre AS red_social_nombre,
                red_social.icono AS red_social_icono,
                red_social.color AS red_social_color
            ",
            'leftjoin' => $leftjoin,
            'where' => 'campana.active = ? AND campana.udn_id = ?',
            'order' => ['DESC' => 'campana.fecha_creacion'],
            'data' => $array
        ]);
    }

    function getCampanaById($array) {
        $result = $this->_Select([
            'table' => $this->bd . 'campana',
            'values' => '*',
            'where' => 'id = ?',
            'data' => $array
        ]);
        return $result[0] ?? null;
    }

    function createCampana($array) {
        return $this->_Insert([
            'table' => $this->bd . 'campana',
            'values' => $array['values'],
            'data' => $array['data']
        ]);
    }

    function updateCampana($array) {
        return $this->_Update([
            'table' => $this->bd . 'campana',
            'values' => $array['values'],
            'where' => 'id = ?',
            'data' => $array['data']
        ]);
    }

    function getCampanaPerformance($array) {
        $query = "
            SELECT 
                c.nombre AS campana_nombre,
                COUNT(p.id) AS pedidos_generados,
                SUM(p.monto) AS ingresos_generados,
                c.presupuesto,
                c.total_clics,
                CASE 
                    WHEN c.presupuesto > 0 
                    THEN (SUM(p.monto) / c.presupuesto) * 100
                    ELSE 0 
                END AS roi
            FROM {$this->bd}campana c
            LEFT JOIN {$this->bd}pedido p ON c.id = p.campana_id AND p.active = 1
            WHERE c.id = ?
            GROUP BY c.id, c.nombre, c.presupuesto, c.total_clics
        ";
        $result = $this->_Read($query, $array);
        return $result[0] ?? [
            'campana_nombre' => '',
            'pedidos_generados' => 0,
            'ingresos_generados' => 0,
            'presupuesto' => 0,
            'total_clics' => 0,
            'roi' => 0
        ];
    }
}
