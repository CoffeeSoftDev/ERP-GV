<?php

if (empty($_POST['opc'])) exit(0);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

require_once '../mdl/mdl-canales.php';

class ctrl extends mdl {

    function init() {
        return [
            'udn' => $this->lsUDN(),
            'redes_sociales' => $this->lsRedesSociales([1])
        ];
    }

    function lsCanales() {
        $__row = [];
        $active = $_POST['active'];
        
        $ls = $this->listCanales([$active]);
        
        foreach ($ls as $key) {
            $a = [];
            
            if ($key['active'] == 1) {
                $a[] = [
                    'class' => 'btn btn-sm btn-primary me-1',
                    'html' => '<i class="icon-pencil"></i>',
                    'onclick' => 'canales.editCanal(' . $key['id'] . ')'
                ];
                
                $a[] = [
                    'class' => 'btn btn-sm btn-danger',
                    'html' => '<i class="icon-toggle-on"></i>',
                    'onclick' => 'canales.statusCanal(' . $key['id'] . ', ' . $key['active'] . ')'
                ];
            } else {
                $a[] = [
                    'class' => 'btn btn-sm btn-outline-danger',
                    'html' => '<i class="icon-toggle-off"></i>',
                    'onclick' => 'canales.statusCanal(' . $key['id'] . ', ' . $key['active'] . ')'
                ];
            }
            
            $__row[] = [
                'id' => $key['id'],
                'Nombre' => $key['nombre'],
                'Estado' => renderStatus($key['active']),
                'Fecha' => formatSpanishDate($key['fecha_creacion']),
                'a' => $a
            ];
        }
        
        return [
            'row' => $__row,
            'ls' => $ls
        ];
    }

    function getCanal() {
        $status = 500;
        $message = 'Error al obtener canal';
        
        $canal = $this->getCanalById([$_POST['id']]);
        
        if ($canal) {
            $status = 200;
            $message = 'Canal obtenido correctamente';
        }
        
        return [
            'status' => $status,
            'message' => $message,
            'data' => $canal
        ];
    }

    function addCanal() {
        $status = 500;
        $message = 'Error al crear canal';
        
        $_POST['fecha_creacion'] = date('Y-m-d H:i:s');
        $_POST['active'] = 1;
        
        $exists = $this->existsCanalByName([$_POST['nombre']]);
        
        if ($exists) {
            return [
                'status' => 409,
                'message' => 'Ya existe un canal con ese nombre'
            ];
        }
        
        $create = $this->createCanal($this->util->sql($_POST));
        
        if ($create) {
            $status = 200;
            $message = 'Canal creado correctamente';
        }
        
        return [
            'status' => $status,
            'message' => $message
        ];
    }

    function editCanal() {
        $status = 500;
        $message = 'Error al editar canal';
        
        $edit = $this->updateCanal($this->util->sql($_POST, 1));
        
        if ($edit) {
            $status = 200;
            $message = 'Canal editado correctamente';
        }
        
        return [
            'status' => $status,
            'message' => $message
        ];
    }

    function statusCanal() {
        $status = 500;
        $message = 'Error al cambiar estado';
        
        $update = $this->updateCanal($this->util->sql($_POST, 1));
        
        if ($update) {
            $status = 200;
            $message = 'Estado actualizado correctamente';
        }
        
        return [
            'status' => $status,
            'message' => $message
        ];
    }

    function lsCampanas() {
        $__row = [];
        $active = $_POST['active'];
        $udn = $_POST['udn'] ?? $_SESSION['SUB'];
        
        $ls = $this->listCampanas([$active, $udn]);
        
        foreach ($ls as $key) {
            $a = [];
            
            if ($key['active'] == 1) {
                $a[] = [
                    'class' => 'btn btn-sm btn-primary me-1',
                    'html' => '<i class="icon-pencil"></i>',
                    'onclick' => 'canales.editCampana(' . $key['id'] . ')'
                ];
                
                $a[] = [
                    'class' => 'btn btn-sm btn-info me-1',
                    'html' => '<i class="icon-chart-bar"></i>',
                    'onclick' => 'canales.showCampanaPerformance(' . $key['id'] . ')'
                ];
                
                $a[] = [
                    'class' => 'btn btn-sm btn-danger',
                    'html' => '<i class="icon-toggle-on"></i>',
                    'onclick' => 'canales.statusCampana(' . $key['id'] . ', ' . $key['active'] . ')'
                ];
            } else {
                $a[] = [
                    'class' => 'btn btn-sm btn-outline-danger',
                    'html' => '<i class="icon-toggle-off"></i>',
                    'onclick' => 'canales.statusCampana(' . $key['id'] . ', ' . $key['active'] . ')'
                ];
            }
            
            $__row[] = [
                'id' => $key['id'],
                'Nombre' => $key['nombre'],
                'Red Social' => [
                    'html' => '<div class="flex items-center gap-2">
                                <i class="' . $key['red_social_icono'] . '" style="color:' . $key['red_social_color'] . '"></i>
                                <span>' . $key['red_social_nombre'] . '</span>
                              </div>',
                    'class' => 'text-left'
                ],
                'Presupuesto' => evaluar($key['presupuesto']),
                'Clics' => evaluar($key['total_clics']),
                'Fecha Inicio' => formatSpanishDate($key['fecha_inicio']),
                'Fecha Fin' => formatSpanishDate($key['fecha_fin']),
                'Estado' => renderStatus($key['active']),
                'a' => $a
            ];
        }
        
        return [
            'row' => $__row,
            'ls' => $ls
        ];
    }

    function getCampana() {
        $status = 500;
        $message = 'Error al obtener campaña';
        
        $campana = $this->getCampanaById([$_POST['id']]);
        
        if ($campana) {
            $status = 200;
            $message = 'Campaña obtenida correctamente';
        }
        
        return [
            'status' => $status,
            'message' => $message,
            'data' => $campana
        ];
    }

    function addCampana() {
        $status = 500;
        $message = 'Error al crear campaña';
        
        $_POST['fecha_creacion'] = date('Y-m-d H:i:s');
        $_POST['udn_id'] = $_SESSION['SUB'];
        $_POST['active'] = 1;
        
        if (strtotime($_POST['fecha_fin']) < strtotime($_POST['fecha_inicio'])) {
            return [
                'status' => 400,
                'message' => 'La fecha de fin debe ser posterior a la fecha de inicio'
            ];
        }
        
        $create = $this->createCampana($this->util->sql($_POST));
        
        if ($create) {
            $status = 200;
            $message = 'Campaña creada correctamente';
        }
        
        return [
            'status' => $status,
            'message' => $message
        ];
    }

    function editCampana() {
        $status = 500;
        $message = 'Error al editar campaña';
        
        if (strtotime($_POST['fecha_fin']) < strtotime($_POST['fecha_inicio'])) {
            return [
                'status' => 400,
                'message' => 'La fecha de fin debe ser posterior a la fecha de inicio'
            ];
        }
        
        $edit = $this->updateCampana($this->util->sql($_POST, 1));
        
        if ($edit) {
            $status = 200;
            $message = 'Campaña editada correctamente';
        }
        
        return [
            'status' => $status,
            'message' => $message
        ];
    }

    function statusCampana() {
        $status = 500;
        $message = 'Error al cambiar estado';
        
        $update = $this->updateCampana($this->util->sql($_POST, 1));
        
        if ($update) {
            $status = 200;
            $message = 'Estado actualizado correctamente';
        }
        
        return [
            'status' => $status,
            'message' => $message
        ];
    }

    function apiCampanaPerformance() {
        $performance = $this->getCampanaPerformance([$_POST['id']]);
        
        return [
            'status' => 200,
            'data' => [
                'nombre' => $performance['campana_nombre'],
                'pedidos' => evaluar($performance['pedidos_generados']),
                'ingresos' => evaluar($performance['ingresos_generados']),
                'presupuesto' => evaluar($performance['presupuesto']),
                'clics' => evaluar($performance['total_clics']),
                'roi' => number_format($performance['roi'], 2) . '%'
            ]
        ];
    }
}

function renderStatus($status) {
    switch ($status) {
        case 1:
            return '<span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                        Activo
                    </span>';
        case 0:
            return '<span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                        <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                        Inactivo
                    </span>';
        default:
            return '<span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                        <span class="w-2 h-2 bg-gray-500 rounded-full"></span>
                        Desconocido
                    </span>';
    }
}

$obj = new ctrl();
echo json_encode($obj->{$_POST['opc']}());
