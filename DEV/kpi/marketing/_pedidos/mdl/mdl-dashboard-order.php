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

    function listOrderMetrics($array) {
        $query = "
            SELECT 
                COUNT(*) as total_pedidos,
                SUM(monto) as total_ingresos,
                AVG(monto) as promedio_pedido,
                DATE_FORMAT(fecha_creacion, '%Y-%m') as periodo
            FROM {$this->bd}pedido 
            WHERE udn_id = ? 
            AND YEAR(fecha_creacion) = ? 
            AND MONTH(fecha_creacion) = ?
            AND active = 1
        ";
        return $this->_Read($query, $array);
    }

    function getMonthlyOrderTrends($array) {
        $query = "
            SELECT 
                MONTH(fecha_pedido) as mes,
                MONTHNAME(fecha_pedido) as nombre_mes,
                COUNT(*) as total_pedidos,
                SUM(monto) as total_ventas,
                AVG(monto) as promedio_pedido
            FROM {$this->bd}pedido 
            WHERE udn_id = ? 
            AND YEAR(fecha_pedido) = ?
            AND active = 1
            GROUP BY MONTH(fecha_pedido), MONTHNAME(fecha_pedido)
            ORDER BY MONTH(fecha_pedido)
        ";
        return $this->_Read($query, $array);
    }

    function getChannelPerformance($array) {
        $query = "
            SELECT 
                c.nombre as canal,
                c.id as canal_id,
                COUNT(p.id) as total_pedidos,
                SUM(p.monto) as total_monto,
                AVG(p.monto) as promedio_monto,
                (SUM(p.monto) / (
                    SELECT SUM(monto) 
                    FROM {$this->bd}pedido 
                    WHERE udn_id = ? AND YEAR(fecha_pedido) = ? AND MONTH(fecha_pedido) = ? AND active = 1
                )) * 100 as porcentaje
            FROM {$this->bd}pedido p
            JOIN {$this->bd}canal c ON p.canal_id = c.id
            WHERE p.udn_id = ? 
            AND YEAR(p.fecha_pedido) = ? 
            AND MONTH(p.fecha_pedido) = ?
            AND p.active = 1
            AND c.active = 1
            GROUP BY c.id, c.nombre
            ORDER BY total_monto DESC
        ";
        return $this->_Read($query, array_merge($array, $array));
    }

    function getWeeklyStats($array) {
        $query = "
            SELECT 
                DAYNAME(fecha_creacion) as dia,
                DAYOFWEEK(fecha_creacion) as dia_numero,
                COUNT(*) as total_pedidos,
                SUM(monto) as total_ventas,
                AVG(monto) as promedio_dia,
                COUNT(DISTINCT cliente_id) as clientes_unicos
            FROM {$this->bd}pedido 
            WHERE udn_id = ? 
            AND YEAR(fecha_creacion) = ? 
            AND MONTH(fecha_creacion) = ?
            AND active = 1
            GROUP BY DAYNAME(fecha_creacion), DAYOFWEEK(fecha_creacion)
            ORDER BY promedio_dia DESC
        ";
        return $this->_Read($query, $array);
    }

    function getRevenueComparison($array) {
        $query = "
            SELECT 
                YEAR(fecha_creacion) as anio,
                MONTH(fecha_creacion) as mes,
                SUM(monto) as total_ventas,
                COUNT(*) as total_pedidos
            FROM {$this->bd}pedido 
            WHERE udn_id = ? 
            AND fecha_creacion >= DATE_SUB(CURDATE(), INTERVAL 24 MONTH)
            AND active = 1
            GROUP BY YEAR(fecha_creacion), MONTH(fecha_creacion)
            ORDER BY YEAR(fecha_creacion) DESC, MONTH(fecha_creacion) DESC
        ";
        return $this->_Read($query, $array);
    }

    function getDashboardCards($array) {
        $query = "
            SELECT 
                (SELECT SUM(monto) FROM {$this->bd}pedido 
                 WHERE udn_id = ? AND DATE(fecha_pedido) = CURDATE() - INTERVAL 1 DAY AND active = 1) as venta_dia,
                (SELECT SUM(monto) FROM {$this->bd}pedido 
                 WHERE udn_id = ? AND YEAR(fecha_pedido) = ? AND MONTH(fecha_pedido) = ? AND active = 1) as venta_mes,
                (SELECT COUNT(DISTINCT cliente_id) FROM {$this->bd}pedido 
                 WHERE udn_id = ? AND YEAR(fecha_pedido) = ? AND MONTH(fecha_pedido) = ? AND active = 1) as clientes,
                (SELECT AVG(monto) FROM {$this->bd}pedido 
                 WHERE udn_id = ? AND YEAR(fecha_pedido) = ? AND MONTH(fecha_pedido) = ? AND active = 1) as cheque_promedio
        ";
        return $this->_Read($query, $array);
    }

    function getChannelComparison($array) {
        $currentYear = $array[2];
        $previousYear = $currentYear - 1;
        
        $query = "
            SELECT 
                'A&B' as categoria,
                COALESCE((SELECT SUM(monto) FROM {$this->bd}pedido WHERE udn_id = ? AND YEAR(fecha_creacion) = ? AND active = 1), 0) as anio_actual,
                COALESCE((SELECT SUM(monto) FROM {$this->bd}pedido WHERE udn_id = ? AND YEAR(fecha_creacion) = ? AND active = 1), 0) as anio_anterior
            UNION ALL
            SELECT 
                'Alimentos' as categoria,
                COALESCE((SELECT SUM(p.monto) FROM {$this->bd}pedido p 
                         JOIN {$this->bd}producto_pedido pp ON p.id = pp.pedido_id 
                         JOIN {$this->bd}producto pr ON pp.producto_id = pr.id 
                         WHERE p.udn_id = ? AND YEAR(p.fecha_creacion) = ? AND pr.es_servicio = 0 AND p.active = 1), 0) as anio_actual,
                COALESCE((SELECT SUM(p.monto) FROM {$this->bd}pedido p 
                         JOIN {$this->bd}producto_pedido pp ON p.id = pp.pedido_id 
                         JOIN {$this->bd}producto pr ON pp.producto_id = pr.id 
                         WHERE p.udn_id = ? AND YEAR(p.fecha_creacion) = ? AND pr.es_servicio = 0 AND p.active = 1), 0) as anio_anterior
            UNION ALL
            SELECT 
                'Bebidas' as categoria,
                COALESCE((SELECT SUM(p.monto) FROM {$this->bd}pedido p 
                         JOIN {$this->bd}producto_pedido pp ON p.id = pp.pedido_id 
                         JOIN {$this->bd}producto pr ON pp.producto_id = pr.id 
                         WHERE p.udn_id = ? AND YEAR(p.fecha_creacion) = ? AND pr.es_servicio = 1 AND p.active = 1), 0) as anio_actual,
                COALESCE((SELECT SUM(p.monto) FROM {$this->bd}pedido p 
                         JOIN {$this->bd}producto_pedido pp ON p.id = pp.pedido_id 
                         JOIN {$this->bd}producto pr ON pp.producto_id = pr.id 
                         WHERE p.udn_id = ? AND YEAR(p.fecha_creacion) = ? AND pr.es_servicio = 1 AND p.active = 1), 0) as anio_anterior
        ";
        
        $params = [
            $array[0], $currentYear, $array[0], $previousYear,
            $array[0], $currentYear, $array[0], $previousYear,
            $array[0], $currentYear, $array[0], $previousYear
        ];
        
        return $this->_Read($query, $params);
    }

    function getChannelMonthlyData($array) {
        $query = "
            SELECT 
                c.nombre as canal,
                MONTH(p.fecha_pedido) as mes,
                SUM(p.monto) as total_monto,
                COUNT(p.id) as total_pedidos
            FROM {$this->bd}pedido p
            JOIN {$this->bd}canal c ON p.canal_id = c.id
            WHERE p.udn_id = ? 
            AND YEAR(p.fecha_pedido) = ?
            AND p.active = 1
            AND c.active = 1
            GROUP BY c.id, c.nombre, MONTH(p.fecha_pedido)
            ORDER BY c.nombre, MONTH(p.fecha_pedido)
        ";
        return $this->_Read($query, $array);
    }

    function getTotalYearOrders($array) {
        $query = "
            SELECT COUNT(*) as total_pedidos
            FROM {$this->bd}pedido 
            WHERE udn_id = ? 
            AND YEAR(fecha_creacion) = ?
            AND active = 1
        ";
        $result = $this->_Read($query, $array);
        return $result[0]['total_pedidos'] ?? 0;
    }

    function getDashboardCardsByYear($array) {
        $query = "
            SELECT 
                (SELECT SUM(monto) FROM {$this->bd}pedido 
                 WHERE udn_id = ? AND YEAR(fecha_creacion) = ? AND active = 1) as venta_anio,
                (SELECT COUNT(*) FROM {$this->bd}pedido 
                 WHERE udn_id = ? AND YEAR(fecha_creacion) = ? AND active = 1) as pedidos_anio,
                (SELECT COUNT(DISTINCT cliente_id) FROM {$this->bd}pedido 
                 WHERE udn_id = ? AND YEAR(fecha_creacion) = ? AND active = 1) as clientes,
                (SELECT AVG(monto) FROM {$this->bd}pedido 
                 WHERE udn_id = ? AND YEAR(fecha_creacion) = ? AND active = 1) as cheque_promedio,
                (SELECT c.nombre FROM {$this->bd}pedido p 
                 JOIN {$this->bd}canal c ON p.canal_id = c.id 
                 WHERE p.udn_id = ? AND YEAR(p.fecha_creacion) = ? AND p.active = 1 
                 GROUP BY c.id, c.nombre 
                 ORDER BY SUM(p.monto) DESC LIMIT 1) as canal_principal
        ";
        return $this->_Read($query, $array);
    }

    function getChannelYearlyData($array) {
        $query = "
            SELECT 
                c.nombre as canal,
                MONTH(p.fecha_creacion) as mes,
                SUM(p.monto) as total_monto,
                COUNT(p.id) as total_pedidos
            FROM {$this->bd}pedido p
            JOIN {$this->bd}canal c ON p.canal_id = c.id
            WHERE p.udn_id = ? 
            AND YEAR(p.fecha_creacion) = ?
            AND p.active = 1
            AND c.active = 1
            GROUP BY c.id, c.nombre, MONTH(p.fecha_creacion)
            ORDER BY c.nombre, MONTH(p.fecha_creacion)
        ";
        return $this->_Read($query, $array);
    }

    function getChannelPerformanceByYear($array) {
        $query = "
            SELECT 
                c.nombre as canal,
                c.id as canal_id,
                COUNT(p.id) as total_pedidos,
                SUM(p.monto) as total_monto,
                AVG(p.monto) as promedio_monto,
                (SUM(p.monto) / (
                    SELECT SUM(monto) 
                    FROM {$this->bd}pedido 
                    WHERE udn_id = ? AND YEAR(fecha_creacion) = ? AND active = 1
                )) * 100 as porcentaje
            FROM {$this->bd}pedido p
            JOIN {$this->bd}canal c ON p.canal_id = c.id
            WHERE p.udn_id = ? 
            AND YEAR(p.fecha_creacion) = ?
            AND p.active = 1
            AND c.active = 1
            GROUP BY c.id, c.nombre
            ORDER BY total_monto DESC
        ";
        return $this->_Read($query, array_merge($array, $array));
    }

    function getTotalMonthOrders($array) {
        $query = "
            SELECT COUNT(*) as total_pedidos
            FROM {$this->bd}pedido 
            WHERE udn_id = ? 
            AND MONTH(fecha_pedido) = ?
            AND YEAR(fecha_pedido) = ?
            AND active = 1
        ";
        $result = $this->_Read($query, $array);
        return $result ? intval($result[0]['total_pedidos']) : 0;
    }

    function getSalesByChannel($array) {
        $udn = $array[0];
        $mes = $array[1];
        $anio = $array[2];
        
        // Calcular mes anterior
        $mesAnterior = $mes ;
        $anioAnterior = $anio-1;
        // if ($mesAnterior < 1) {
        //     $mesAnterior = 12;
        //     $anioAnterior = $anio - 1;
        // }
        
        $query = "
            SELECT 
                c.nombre as canal,
                c.id as canal_id,
                -- Ventas del mes actual
                COALESCE((SELECT SUM(p.monto) 
                         FROM {$this->bd}pedido p 
                         WHERE p.canal_id = c.id 
                         AND p.udn_id = ? 
                         AND MONTH(p.fecha_pedido) = ? 
                         AND YEAR(p.fecha_pedido) = ? 
                         AND p.active = 1), 0) as venta_actual,
                -- Ventas del mes anterior
                COALESCE((SELECT SUM(p.monto) 
                         FROM {$this->bd}pedido p 
                         WHERE p.canal_id = c.id 
                         AND p.udn_id = ? 
                         AND MONTH(p.fecha_pedido) = ? 
                         AND YEAR(p.fecha_pedido) = ? 
                         AND p.active = 1), 0) as venta_anterior,
                -- Pedidos del mes actual
                COALESCE((SELECT COUNT(*) 
                         FROM {$this->bd}pedido p 
                         WHERE p.canal_id = c.id 
                         AND p.udn_id = ? 
                         AND MONTH(p.fecha_pedido) = ? 
                         AND YEAR(p.fecha_pedido) = ? 
                         AND p.active = 1), 0) as pedidos_actual,
                -- Pedidos del mes anterior
                COALESCE((SELECT COUNT(*) 
                         FROM {$this->bd}pedido p 
                         WHERE p.canal_id = c.id 
                         AND p.udn_id = ? 
                         AND MONTH(p.fecha_pedido) = ? 
                         AND YEAR(p.fecha_pedido) = ? 
                         AND p.active = 1), 0) as pedidos_anterior
            FROM {$this->bd}canal c
            WHERE c.active = 1
            ORDER BY c.nombre
        ";
        
        $params = [
            $udn, $mes, $anio,           // Mes actual
            $udn, $mesAnterior, $anioAnterior,  // Mes anterior
            $udn, $mes, $anio,           // Pedidos mes actual
            $udn, $mesAnterior, $anioAnterior   // Pedidos mes anterior
        ];
        
        return $this->_Read($query, $params);
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

}