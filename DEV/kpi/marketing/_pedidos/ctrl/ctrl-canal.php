<?php

if (empty($_POST['opc'])) exit(0);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

require_once '../mdl/mdl-canal.php';

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
                    'class'   => 'btn btn-sm btn-primary me-1',
                    'html'    => '<i class="icon-pencil"></i>',
                    'onclick' => 'admin.editCanal(' . $key['id'] . ')'
                ];
                
                $a[] = [
                    'class' => 'btn btn-sm btn-danger',
                    'html' => '<i class="icon-toggle-on"></i>',
                    'onclick' => 'admin.statusCanal(' . $key['id'] . ', ' . $key['active'] . ')'
                ];
            } else {
                $a[] = [
                    'class' => 'btn btn-sm btn-outline-danger',
                    'html' => '<i class="icon-toggle-off"></i>',
                    'onclick' => 'admin.statusCanal(' . $key['id'] . ', ' . $key['active'] . ')'
                ];
            }
            
            $__row[] = [
                'id'     => $key['id'],
                'Nombre' => $key['nombre'],
                'Estado' => renderStatus($key['active']),
                'a'      => $a
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

?>