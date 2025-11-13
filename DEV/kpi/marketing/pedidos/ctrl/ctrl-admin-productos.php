<?php

if (empty($_POST['opc'])) exit(0);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

require_once '../mdl/mdl-admin-productos.php';

class ctrl extends mdl {

    function init() {
        return [
            'udn' => $this->lsUDN(),
            'status' => [
                ['id' => 1, 'valor' => 'Disponibles'],
                ['id' => 0, 'valor' => 'No disponibles']
            ]
        ];
    }

    function lsProductos() {
        $active = isset($_POST['estado-productos']) ? $_POST['estado-productos'] : 1;
        $udn = isset($_POST['udn']) ? $_POST['udn'] : null;
        $data = $this->listProductos([$active, $udn]);
        $rows = [];

        foreach ($data as $item) {
            $a = [];

            if ($active == 1) {
                $a[] = [
                    'class'   => 'btn btn-sm btn-primary me-1',
                    'html'    => '<i class="icon-pencil"></i>',
                    'onclick' => 'product.editProducto(' . $item['id'] . ')'
                ];

                $a[] = [
                    'class'   => 'btn btn-sm btn-danger',
                    'html'    => '<i class="icon-toggle-on"></i>',
                    'onclick' => 'product.statusProducto(' . $item['id'] . ', ' . $item['active'] . ')'
                ];
            } else {
                $a[] = [
                    'class'   => 'btn btn-sm btn-outline-danger',
                    'html'    => '<i class="icon-toggle-off"></i>',
                    'onclick' => 'product.statusProducto(' . $item['id'] . ', ' . $item['active'] . ')'
                ];
            }

            $rows[] = [
                'id'          => $item['id'],
                'Nombre'      => $item['nombre'],
                'Descripción' => $item['descripcion'],
                'UDN'         => $item['udn_nombre'],
                'Estado'      => renderStatus($item['active']),
                'a'           => $a
            ];
        }

        return [
            'row' => $rows,
            'ls'  => $data,
        ];
    }

    function getProducto() {
        $id = $_POST['id'];
        $status = 404;
        $message = 'Producto no encontrado';
        $data = null;

        $producto = $this->getProductoById($id);

        if ($producto) {
            $status  = 200;
            $message = 'Producto encontrado';
            $data    = $producto;
        }

        return [
            'status'  => $status,
            'message' => $message,
            'data'    => $data
        ];
    }

    function addProducto() {
        $status = 500;
        $message = 'No se pudo agregar el producto';

        $exists = $this->existsProductoByName([$_POST['nombre'], $_POST['udn_id']]);

        if (!$exists) {
            $_POST['active'] = 1;
            $create = $this->createProducto($this->util->sql($_POST));
            if ($create) {
                $status = 200;
                $message = 'Producto agregado correctamente';
            }
        } else {
            $status = 409;
            $message = 'Ya existe un producto con ese nombre en esta UDN.';
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }

    function editProducto() {
        $id = $_POST['id'];
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
        $message = 'No se pudo actualizar el estado del producto';

        $update = $this->updateProducto($this->util->sql($_POST, 1));

        if ($update) {
            $status = 200;
            $message = 'El estado del producto se actualizó correctamente';
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
