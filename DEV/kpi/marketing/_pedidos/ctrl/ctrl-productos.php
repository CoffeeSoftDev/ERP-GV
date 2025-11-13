<?php

if (empty($_POST['opc'])) exit(0);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

require_once '../mdl/mdl-productos.php';

class ctrl extends mdl {

    function init() {
        return [
            'udn' => $this->lsUDN()
        ];
    }

    function ls() {
        $__row = [];
        $active = $_POST['active'];
        $udn = $_POST['udn'] ?? $_SESSION['SUB'];
        
        $ls = $this->listProductos([$active, $udn]);
        
        foreach ($ls as $key) {
            $a = [];
            
            if ($key['active'] == 1) {
                $a[] = [
                    'class' => 'btn btn-sm btn-primary me-1',
                    'html' => '<i class="icon-pencil"></i>',
                    'onclick' => 'productos.editProducto(' . $key['id'] . ')'
                ];
                
                $a[] = [
                    'class' => 'btn btn-sm btn-danger',
                    'html' => '<i class="icon-toggle-on"></i>',
                    'onclick' => 'productos.statusProducto(' . $key['id'] . ', ' . $key['active'] . ')'
                ];
            } else {
                $a[] = [
                    'class' => 'btn btn-sm btn-outline-danger',
                    'html' => '<i class="icon-toggle-off"></i>',
                    'onclick' => 'productos.statusProducto(' . $key['id'] . ', ' . $key['active'] . ')'
                ];
            }
            
            $__row[] = [
                'id' => $key['id'],
                'Nombre' => $key['nombre'],
                'Descripción' => $key['descripcion'],
                'Precio' => evaluar($key['precio']),
                'Tipo' => ucfirst($key['tipo']),
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

    function getProducto() {
        $status = 500;
        $message = 'Error al obtener producto';
        
        $producto = $this->getProductoById([$_POST['id']]);
        
        if ($producto) {
            $status = 200;
            $message = 'Producto obtenido correctamente';
        }
        
        return [
            'status' => $status,
            'message' => $message,
            'data' => $producto
        ];
    }

    function addProducto() {
        $status = 500;
        $message = 'Error al crear producto';
        
        $_POST['fecha_creacion'] = date('Y-m-d H:i:s');
        $_POST['udn_id'] = $_SESSION['SUB'];
        $_POST['active'] = 1;
        
        $exists = $this->existsProductoByName([$_POST['nombre'], $_SESSION['SUB']]);
        
        if ($exists) {
            return [
                'status' => 409,
                'message' => 'Ya existe un producto con ese nombre en esta unidad de negocio'
            ];
        }
        
        $create = $this->createProducto($this->util->sql($_POST));
        
        if ($create) {
            $status = 200;
            $message = 'Producto creado correctamente';
        }
        
        return [
            'status' => $status,
            'message' => $message
        ];
    }

    function editProducto() {
        $status = 500;
        $message = 'Error al editar producto';
        
        $edit = $this->updateProducto($this->util->sql($_POST, 1));
        
        if ($edit) {
            $status = 200;
            $message = 'Producto editado correctamente';
        }
        
        return [
            'status' => $status,
            'message' => $message
        ];
    }

    function statusProducto() {
        $status = 500;
        $message = 'Error al cambiar estado';
        
        $update = $this->updateProducto($this->util->sql($_POST, 1));
        
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
