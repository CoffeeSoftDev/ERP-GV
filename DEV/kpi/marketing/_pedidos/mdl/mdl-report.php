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

    // UDN and Filters

    function lsUDN() {
        $query = "
            SELECT idUDN AS id, UDN AS valor
            FROM udn
            WHERE Stado = 1 AND idUDN NOT IN (8, 10, 7)
            ORDER BY UDN DESC
        ";
        return $this->_Read($query, null);
    }

    function lsCanales() {
        return $this->_Select([
            'table'  => "{$this->bd}canal",
            'values' => 'id, nombre as valor',
            'where'  => 'active = ?',
            'order'  => ['ASC' => 'nombre'],
            'data'   => [1]
        ]);
    }

    function lsAños() {
        $currentYear = date('Y');
        $years = [];
        for ($i = 0; $i < 5; $i++) {
            $year = $currentYear - $i;
            $years[] = ['id' => $year, 'valor' => $year];
        }
        return $years;
    }

    // Pedidos Reports

    function listPedidosByCanal($filters) {
        $query = "
            SELECT 
                MONTH(p.fecha_pedido) as mes,
                MONTHNAME(p.fecha_pedido) as mes_nombre,
                c.nombre as valor,
                COUNT(*) as cantidad
            FROM {$this->bd}pedido p
            LEFT JOIN {$this->bd}canal c ON p.canal_id = c.id
            WHERE p.udn_id = ? 
            AND YEAR(p.fecha_pedido) = ?
            AND p.active = 1
            GROUP BY MONTH(p.fecha_pedido), c.nombre
            ORDER BY MONTH(p.fecha_pedido)
        ";
        
        return $this->_Read($query, $filters);
    }

    function getPedidosByUdnYearCanal($filters) {
        $query = "
            SELECT 
                MONTH(p.fecha_pedido) as mes,
                COUNT(*) as cantidad
            FROM {$this->bd}pedido p
            WHERE p.udn_id = ? 
            AND MONTH(p.fecha_pedido) = ?
            AND YEAR(p.fecha_pedido) = ?
            AND p.canal_id = ?
            AND p.active = 1
            GROUP BY MONTH(p.fecha_pedido)
            ORDER BY MONTH(p.fecha_pedido)
        ";
        
        $result = $this->_Read($query, $filters);
        return $result[0] ?? ['mes' => $filters[1], 'cantidad' => 0];
    }

    function listVentasByCanal($filters) {
        $query = "
            SELECT 
                MONTH(p.fecha_pedido) as mes,
                MONTHNAME(p.fecha_pedido) as mes_nombre,
                c.nombre as canal_comunicacion,
                SUM(p.monto) as monto_total,
                COUNT(*) as cantidad_pedidos
            FROM {$this->bd}pedido p
            LEFT JOIN {$this->bd}canal c ON p.canal_id = c.id
            WHERE p.udn_id = ? 
            AND YEAR(p.fecha_pedido) = ?
            AND p.active = 1
            GROUP BY MONTH(p.fecha_pedido), c.nombre
            ORDER BY MONTH(p.fecha_pedido)
        ";
        
        return $this->_Read($query, $filters);
    }

    function getVentasByUdnYearCanal($filters) {
        $query = "
            SELECT 
                SUM(p.monto) as monto_total
            FROM {$this->bd}pedido p
            WHERE p.udn_id = ? 
            AND MONTH(p.fecha_pedido) = ?
            AND YEAR(p.fecha_pedido) = ?
            AND p.canal_id = ?
            AND p.active = 1
        ";
        
        return $this->_Read($query, $filters)[0];
    }

    // Ingresos Diarios - Basado en pedidos reales agrupados por día

    function listIngresosDiarios($filters) {
        $query = "
            SELECT 
                DATE(p.fecha_pedido) as fecha,
                c.nombre as canal_comunicacion,
                SUM(p.monto) as monto,
                COUNT(*) as cantidad_pedidos,
                DATE_FORMAT(p.fecha_creacion, '%d/%m/%Y') as fecha_creacion
            FROM {$this->bd}pedido p
            LEFT JOIN {$this->bd}canal c ON p.canal_id = c.id
            WHERE p.udn_id = ? 
            AND YEAR(p.fecha_pedido) = ? 
            AND MONTH(p.fecha_pedido) = ? 
            AND p.active = 1
            GROUP BY DATE(p.fecha_pedido), c.nombre
            ORDER BY DATE(p.fecha_pedido) DESC, c.nombre
        ";
        
        return $this->_Read($query, $filters);
    }

   
    function existsIngresoByDateAndCanal($filters) {
        $query = "
            SELECT COUNT(*) as count
            FROM {$this->bd}pedido p
            LEFT JOIN {$this->bd}canal c ON p.canal_id = c.id
            WHERE DATE(p.fecha_pedido) = ? 
            AND c.nombre = ? 
            AND p.udn_id = ? 
            AND p.active = 1
        ";
        
        $result = $this->_Read($query, $filters);
        return $result[0]['count'] > 0;
    }

    // KPIs and Analytics

    function getKPIData($filters) {
        $query = "
            SELECT 
                COUNT(*) as total_pedidos,
                SUM(p.monto) as total_ingresos,
                AVG(p.monto) as cheque_promedio
            FROM {$this->bd}pedido p
            WHERE p.udn_id = ? 
            AND YEAR(p.fecha_pedido) = ?
            AND p.active = 1
        ";
        
        $kpis = $this->_Read($query, $filters)[0] ?? [];
        
        // Porcentaje por canal
        $queryCanales = "
            SELECT 
                c.nombre as canal_comunicacion,
                COUNT(*) as cantidad,
                SUM(p.monto) as monto,
                (COUNT(*) * 100.0 / (
                    SELECT COUNT(*) 
                    FROM {$this->bd}pedido 
                    WHERE udn_id = ? AND YEAR(fecha_pedido) = ? AND active = 1
                )) as porcentaje
            FROM {$this->bd}pedido p
            LEFT JOIN {$this->bd}canal c ON p.canal_id = c.id
            WHERE p.udn_id = ? 
            AND YEAR(p.fecha_pedido) = ?
            AND p.active = 1
            GROUP BY c.nombre
            ORDER BY cantidad DESC
        ";
        
        $canales = $this->_Read($queryCanales, array_merge($filters, $filters));
        
        return [
            'kpis' => $kpis,
            'canales' => $canales
        ];
    }

    function getComparativeData($filters) {
        $currentYear = $filters[1];
        $previousYear = $currentYear - 1;
        
        $query = "
            SELECT 
                YEAR(p.fecha_pedido) as año,
                c.nombre as canal_comunicacion,
                COUNT(*) as cantidad,
                SUM(p.monto) as monto
            FROM {$this->bd}pedido p
            LEFT JOIN {$this->bd}canal c ON p.canal_id = c.id
            WHERE p.udn_id = ? 
            AND YEAR(p.fecha_pedido) IN (?, ?)
            AND p.active = 1
            GROUP BY YEAR(p.fecha_pedido), c.nombre
            ORDER BY c.nombre, YEAR(p.fecha_pedido)
        ";
        
        return $this->_Read($query, [$filters[0], $currentYear, $previousYear]);
    }

    // Test Methods - Pruebas unitarias de consultas

    function testListPedidosByCanal() {
        echo "=== TEST: listPedidosByCanal ===\n";
        $filters = [1, 2024]; // UDN 1, Año 2024
        $result = $this->listPedidosByCanal($filters);
        
        echo "Parámetros: UDN=" . $filters[0] . ", Año=" . $filters[1] . "\n";
        echo "Resultados encontrados: " . count($result) . "\n";
        
        if (!empty($result)) {
            echo "Ejemplo de resultado:\n";
            print_r($result[0]);
        } else {
            echo "No se encontraron resultados\n";
        }
        echo "\n";
        
        return $result;
    }

    function testListVentasByCanal() {
        echo "=== TEST: listVentasByCanal ===\n";
        $filters = [1, 2024]; // UDN 1, Año 2024
        $result = $this->listVentasByCanal($filters);
        
        echo "Parámetros: UDN=" . $filters[0] . ", Año=" . $filters[1] . "\n";
        echo "Resultados encontrados: " . count($result) . "\n";
        
        if (!empty($result)) {
            echo "Ejemplo de resultado:\n";
            print_r($result[0]);
        } else {
            echo "No se encontraron resultados\n";
        }
        echo "\n";
        
        return $result;
    }

    function testGetKPIData() {
        echo "=== TEST: getKPIData ===\n";
        $filters = [1, 2024]; // UDN 1, Año 2024
        $result = $this->getKPIData($filters);
        
        echo "Parámetros: UDN=" . $filters[0] . ", Año=" . $filters[1] . "\n";
        echo "KPIs obtenidos:\n";
        print_r($result['kpis']);
        echo "Canales encontrados: " . count($result['canales']) . "\n";
        
        if (!empty($result['canales'])) {
            echo "Ejemplo de canal:\n";
            print_r($result['canales'][0]);
        }
        echo "\n";
        
        return $result;
    }

    function testListIngresosDiarios() {
        echo "=== TEST: listIngresosDiarios ===\n";
        $filters = [1, 2024, 12]; // UDN 1, Año 2024, Mes 12
        $result = $this->listIngresosDiarios($filters);
        
        echo "Parámetros: UDN=" . $filters[0] . ", Año=" . $filters[1] . ", Mes=" . $filters[2] . "\n";
        echo "Resultados encontrados: " . count($result) . "\n";
        
        if (!empty($result)) {
            echo "Ejemplo de resultado:\n";
            print_r($result[0]);
        } else {
            echo "No se encontraron resultados\n";
        }
        echo "\n";
        
        return $result;
    }

    function runAllTests() {
        echo "========================================\n";
        echo "EJECUTANDO PRUEBAS UNITARIAS DEL MODELO\n";
        echo "========================================\n\n";
        
        $this->testListPedidosByCanal();
        $this->testListVentasByCanal();
        $this->testGetKPIData();
        $this->testListIngresosDiarios();
        
        echo "========================================\n";
        echo "PRUEBAS COMPLETADAS\n";
        echo "========================================\n";
    }
}