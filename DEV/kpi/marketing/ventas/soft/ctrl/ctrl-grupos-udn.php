<?php

if (empty($_POST['opc'])) exit(0);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

require_once '../mdl/mdl-grupos-udn.php';
require_once '../../../../../conf/coffeSoft.php';

class ctrl extends mdlGruposUdn {

    function init() {
        return [
            'udn' => $this->lsUDN()
        ];
    }

    function lsGroups() {
        $__row = [];
        
        $udn = $_POST['udn'];
        
        if ($udn !== 'all' && !is_numeric($udn)) {
            return [
                'status' => 400,
                'message' => 'UDN inválida',
                'grupos' => []
            ];
        }
        
        $params = [];
        if ($udn !== 'all') {
            $params['udn'] = $udn;
        }
        
        $ls = $this->listGrupos($params);
        
        foreach ($ls as $key) {
            $__row[] = [
                'id' => $key['id'],
                'valor' => $key['grupoproductos'],
                'cantidad_productos' => intval($key['cantidad_productos'])
            ];
        }
        
        return [
            'status' => 200,
            'grupos' => $__row,
            'total' => count($__row)
        ];
    }

    function lsProductos() {
        $__row = [];
        
        $udn = $_POST['udn'];
        $grupo = $_POST['grupo'];

        if (!$grupo) {
            return [
                'status' => 400,
                'row' => [],
                'message' => 'Grupo no especificado'
            ];
        }

        $params = ['grupo' => $grupo];
        if ($udn !== 'all') {
            $params['udn'] = $udn;
        }

        $ls = $this->listProductos($params);

        foreach ($ls as $key) {
            $nombre_costsys = $this->select_homologar([$key['id']]);
            $totalEnlaces = count($nombre_costsys);
            $enlaces = [];

            foreach ($nombre_costsys as $costsys) {
                $enlaces[] = $costsys['idhomologado'];
            }

            $linkHtml = renderEstadoHomologacion($key['id'], $enlaces, $totalEnlaces);

            $__row[] = [
                'id' => $key['id'],
                'Descripción' => htmlspecialchars($key['descripcion'] ?? '', ENT_QUOTES, 'UTF-8'),
                'Grupo' => htmlspecialchars($key['grupoproductos'] ?? 'Sin grupo', ENT_QUOTES, 'UTF-8'),
                'Homologación' => [
                    'html' => $linkHtml,
                    'class' => 'text-center'
                ],
                'Costo' => [
                    'html' => evaluar($key['costo'] ?? 0),
                    'class' => 'text-end'
                ],
                'Precio Venta' => [
                    'html' => evaluar($key['precio_venta'] ?? 0),
                    'class' => 'text-end'
                ]
            ];
        }

        return [
            'status' => 200,
            'row' => $__row,
            'ls' => $ls
        ];
    }
}

// Complements

function renderEstadoHomologacion($idProducto, $enlaces, $totalEnlaces) {
    if ($totalEnlaces === 0) {
        return '
            <div class="flex items-center justify-center gap-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-200 text-gray-700">
                    <i class="icon-link-off mr-1"></i>
                    Sin homologar
                </span>
            </div>
        ';
    }

    if ($totalEnlaces === 1) {
        return '
            <div class="flex items-center justify-center gap-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                    <i class="icon-check-circle mr-1"></i>
                    Homologado
                </span>
            </div>
        ';
    }

    $enlacesTexto = implode(', ', $enlaces);
    return '
        <div class="flex items-center justify-center gap-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700 cursor-pointer" 
                  onclick="mostrarAlertaMultiplesEnlaces(' . $idProducto . ', \'' . $enlacesTexto . '\', ' . $totalEnlaces . ')">
                <i class="icon-alert-triangle mr-1"></i>
                ' . $totalEnlaces . ' Enlaces
            </span>
        </div>
    ';
}

$obj = new ctrl();
echo json_encode($obj->{$_POST['opc']}());
