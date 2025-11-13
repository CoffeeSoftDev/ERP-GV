<?php

if (empty($_POST['opc'])) exit(0);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

require_once '../mdl/mdl-report.php';
require_once '../../../../conf/coffeSoft.php';

class ctrl extends mdl {

    function init() {
        return [
            'udn' => $this->lsUDN(),
            'canales' => $this->lsCanales(),
            'años' => $this->lsAños()
        ];
    }

    function lsResumenPedidos() {
        $udn =  $_POST['udn'];
        $year =  $_POST['year'];
        
        $canales = $this->lsCanales();
        
        // Organizar datos por mes
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        
        $rows = [];
        
        foreach ($meses as $numMes => $nombreMes) {
            $row = [
                'Mes' => $nombreMes
            ];
            
            $totalMes = 0;
            
            foreach ($canales as $canal) {
                // Consulta optimizada por canal específico
                $pedido                = $this->getPedidosByUdnYearCanal([$udn,$numMes, $year, $canal['id']]);
                $cantidad              = formatNumber($pedido['cantidad']);
                $row[$canal['valor']]  = $cantidad;
                $totalMes             += $pedido['cantidad'];
            
            }

            $row['Total'] = [
                'html'  => '<strong>' . evaluar($totalMes,'') . '</strong>',
                'class' => 'text-center  '
            ];
            // Resaltar mes actual
            $row['opc'] =0;
            
            if ($numMes == date('n')) {
                $row['opc'] = 2;
            }
            
            $rows[] = $row;
        }
        
        return [
            'row' => $rows,
            // 'data' => $data
        ];
    }

    function lsResumenVentas() {
        $udn = $_POST['udn'];
        $year = $_POST['year'];
        
        $canales = $this->lsCanales();
        
        // Organizar datos por mes
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        
        $rows = [];
        
        foreach ($meses as $numMes => $nombreMes) {
            $row = [
                'Mes' => $nombreMes
            ];
            
            $totalMes = 0;
            
            foreach ($canales as $canal) {
                // Consulta optimizada por canal específico
                $venta = $this->getVentasByUdnYearCanal([$udn, $numMes, $year, $canal['id']]);
                $monto = $venta['monto_total'] ?? 0;
                $row[$canal['valor']] = evaluar($monto);
                $totalMes += $monto;
            }

            $row['Total'] = [
                'html' => '<strong>' . evaluar($totalMes) . '</strong>',
                'class' => 'text-end '
            ];
            
            // Resaltar mes actual
            $row['opc'] = 0;
            
            if ($numMes == date('n')) {
                $row['opc'] = 2;
            }
            
            $rows[] = $row;
        }
        
        return [
            'row' => $rows,
            'data' => []
        ];
    }

    function lsBitacoraIngresos() {
        $udn = $_POST['filterUDN'] ?? $_POST['udn'];
        $año = $_POST['filterAño'] ?? $_POST['año'];
        $mes = $_POST['filterMes'] ?? $_POST['mes'] ?? date('n');
        
        $data = $this->listIngresosDiarios([$udn, $año, $mes]);
        $rows = [];
        
        foreach ($data as $item) {
            $rows[] = [
                'Fecha' => formatSpanishDate($item['fecha']),
                'Canal' => ucfirst($item['canal_comunicacion']),
                'Monto' => evaluar($item['monto']),
                'Pedidos' => $item['cantidad_pedidos'],
                'Registrado' => $item['fecha_creacion']
            ];
        }
        
        return [
            'row' => $rows,
            'data' => $data
        ];
    }

  

    function getKPIDashboard() {
        $udn = $_POST['filterUDN'] ?? $_POST['udn'];
        $año = $_POST['filterAño'] ?? $_POST['año'];
        
        $kpiData = $this->getKPIData([$udn, $año]);
        $comparativeData = $this->getComparativeData([$udn, $año]);
        
        return [
            'status' => 200,
            'message' => 'Datos obtenidos correctamente',
            'data' => [
                'kpis' => $kpiData['kpis'],
                'canales' => $kpiData['canales'],
                'comparative' => $comparativeData
            ]
        ];
    }

    // Método temporal de debug - REMOVER EN PRODUCCIÓN
    function debugReportes() {
        $udn = $_POST['filterUDN'] ?? $_POST['udn'] ?? 1;
        $año = $_POST['filterAño'] ?? $_POST['año'] ?? 2024;
        
        $data = $this->listPedidosByCanal([$udn, $año]);
        $canales = $this->lsCanales();
        
        return [
            'status' => 200,
            'message' => 'Debug data',
            'debug' => [
                'udn' => $udn,
                'año' => $año,
                'data_count' => count($data),
                'canales_count' => count($canales),
                'sample_data' => array_slice($data, 0, 3),
                'sample_canales' => array_slice($canales, 0, 3),
                'data_structure' => !empty($data) ? array_keys($data[0]) : [],
                'canales_structure' => !empty($canales) ? array_keys($canales[0]) : []
            ]
        ];
    }
}



// Complements

function formatNumber($value) {
    // Validar si el valor es null, vacío, 0 o '0'
    if ($value === null || $value === '' || $value === 0 || $value === '0') {
        return '-';
    }
    
    // Si es un número válido, formatearlo con separadores de miles
    if (is_numeric($value)) {
        return number_format($value);
    }
    
    // Si no es numérico, devolver el valor original o '-'
    return $value ?: '-';
}

$obj = new ctrl();
echo json_encode($obj->{$_POST['opc']}());