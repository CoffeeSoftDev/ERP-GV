<?php

if (empty($_POST['opc'])) exit(0);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

require_once '../mdl/mdl-pedidos.php';

class ctrl extends mdl {

    function init() {
        return [
            'udn'            => $this-> lsUDN(),
            'canales'        => $this-> lsCanales([1]),
            'productos'      => $this-> lsProductos([1]),
            'campanas'       => $this-> lsCampanas([1]),
            'redes_sociales' => $this-> lsSocialNetworks([]),
            'anuncios'       => $this-> lsAnuncios()
        ];
    }

    function apiSearchClientes() {
        $search = '%' . $_POST['search'] . '%';
        
        $clientes = $this->searchClientes([$search, $search]); 
        
        return [
            'results' => $clientes
        ];
    }

    function getCliente() {
        $cliente = $this->getClienteById([$_POST['id']]);
        
        return [
            'status' => $cliente ? 200 : 404,
            'data' => $cliente
        ];
    }

    function lsPedido() {
        $__row = [];
        $udn = $_POST['udn'];
        $anio = $_POST['anio'];
        $mes = $_POST['mes'];
        
        $ls = $this->listPedidos([$udn, $anio, $mes]);
        
        foreach ($ls as $key) {
            $diasTranscurridos = (strtotime(date('Y-m-d')) - strtotime($key['fecha_creacion'])) / 86400;
            $puedeEditar       = $diasTranscurridos <= 7;

            $row = [
                'id'       => $key['id'],
                'Fecha pedido'    => date('d/m/Y', strtotime($key['fecha_pedido'])),
                'Creado el'  => date('d/m/Y H:i', strtotime($key['fecha_creacion'])),
                'Cliente'  => $key['cliente_nombre'],
                'Teléfono' => $key['cliente_telefono'],
                'Canal'    => $key['canal_nombre'] ?? 'N/A',
                'Anuncio'    => [
                    'html' => '<div class="flex items-center gap-2">'
                        . canalSVG($key['red_social_nombre']) .
                        '<span>' . ($key['anuncio_nombre'] ?? '<span class="text-gray-500">Sin anuncio</span>') . '</span>' .
                    '</div>',
                    'class' => 'text-left'
                ],
                'Monto'    => evaluar($key['monto']),
                'Envío'    => renderEnvio($key['envio_domicilio']),
                'Pago'   => renderStatus($key['pago_verificado']),
                'Capturado por' => $key['user_nombre'] ?? '',
                'dropdown' => dropdown($key['id'], $key['active'], $puedeEditar, $key['pago_verificado'], $key['udn_id'], $key['llego_establecimiento'])
            ];
            // Solo agregar llegada si udn_id == 1
            if ($key['udn_id'] == 1) {
                $row['Llegada'] = renderArrivalStatus($key['llego_establecimiento'], $key['udn_id']);
            }
            $__row[] = $row;
        }
        
        return [
            'row' => $__row,
            'ls' => $ls,
        ];
    }

    function addPedido() {
        $status = 500;
        $message = 'Error al crear pedido';
        
        try {
            // Configuración inicial del pedido
            $fecha_creacion = date('Y-m-d H:i:s');
            $udn_id = $_POST['udn_id'] ?? 4;
            $idUser = isset($_COOKIE['IDU']) ? $_COOKIE['IDU'] : null;

            
            // Determinar el cliente_id
            $clienteId = null;
            $clienteData = [];
            // Si viene cliente_id, es un cliente existente
            if (!empty($_POST['cliente_id'])) {
                $clienteId = $_POST['cliente_id'];
            } else if (!empty($_POST['cliente_nombre'])) {
                    // Si no viene cliente_id, crear un nuevo cliente
                    // Verificar que no exista un cliente con el mismo nombre y teléfono
                    $clienteExistente = $this->searchClientesByName([$_POST['cliente_nombre']]);
                    if (!empty($clienteExistente)) {
                        return [
                            'status' => 400,
                            'message' => 'Ya existe un cliente con ese nombre o teléfono'
                        ];
                    }

                    // 🧩 Limpiar valores y convertir 'null' o vacío a NULL real
                    $clienteData = [
                        'nombre'           => $this->sanitize($_POST['cliente_nombre'] ?? null),
                        'telefono'         => $this->sanitize($_POST['cliente_telefono'] ?? null),
                        'correo'           => $this->sanitize($_POST['cliente_correo'] ?? null),
                        'fecha_cumpleaños' => $this->sanitize($_POST['cliente_cumpleaños'] ?? null),
                        'fecha_creacion'   => date('Y-m-d H:i:s'),
                        'udn_id'           => $_POST['udn_id']
                    ];

                    $clientecito = $this->createCliente($this->util->sql($clienteData));
                    $clienteId = $this->maxCliente();
            } else {
                return [
                    'status' => 400,
                    'message' => 'Debe proporcionar un cliente existente o los datos para crear uno nuevo'
                ];
            }

            // Crear el pedido
            $pedidoData = [
                'monto'            => $_POST['monto'],
                'envio_domicilio'  => $_POST['envio_domicilio'],
                'fecha_pedido'     => $_POST['fecha_pedido'],
                'fecha_creacion'   => $fecha_creacion,
                'canal_id'         => $_POST['canal_id'],
                'cliente_id'       => $clienteId,
                'user_id'          => $idUser,
                'udn_id'           => $udn_id,
                'red_social_id'    => $this->sanitize($_POST['red_social_id'] ?? null),
                'anuncio_id'       => $this->sanitize($_POST['anuncio_id'] ?? null),
                'pago_verificado'  => 0,
                'active'           => 1
            ];
    
            
            $values = $this->util->sql($pedidoData);
            $create = $this->createPedido($values);

            if ($create) {
                $pedidoId = $this->maxPedido();
                
                // 📜 Guardar productos si vienen
                if (!empty($_POST['producto_id'])) {

                    // 🔵 Asegurar que siempre sea un array
                    $productos = is_array($_POST['producto_id'])
                        ? $_POST['producto_id']
                        : [$_POST['producto_id']];

                    // 📌 Recorrer y guardar cada producto asociado al pedido
                    foreach ($productos as $productoId) {
                        if (!empty($productoId)) {
                            $this->createProductoPedido($this->util->sql([
                                'producto_id' => $productoId,
                                'pedido_id'   => $pedidoId
                            ]));
                        }
                    }
                }

                $status = 200;
                $message = 'Pedido creado correctamente';
            }
            
        } catch (Exception $e) {
            $message = 'Error al crear pedido';
        }
       
        return [
            'status' => $status,
            'message' =>$message,
           
        ];
        
    }


    function getPedido() {
        $pedido = $this->getPedidoById([$_POST['id']]);
        
        if ($pedido) {
            // Obtener productos del pedido
            $productos = $this->getProductosByPedido([$_POST['id']]);
            $pedido['productos'] = array_column($productos, 'producto_id');
        }
        
        return [
            'status' => $pedido ? 200 : 404,
            'message' => $pedido ? 'Pedido obtenido' : 'Pedido no encontrado',
            'data' => $pedido
        ];
    }

    function editPedido() {
        $status = 500;
        $message = 'Error al editar pedido';
        
        try {
            // Validar antigüedad del pedido (máximo 7 días)
            $validation = $this->validatePedidoAge([$_POST['id']]);
            
            if (!$validation['valid']) {
                return [
                    'status' => 403,
                    'message' => 'No se puede editar pedidos con más de 7 días de antigüedad. Este pedido tiene ' . $validation['dias'] . ' días.'
                ];
            }
            
            // Actualizar datos del pedido
            $pedidoData = [
                'monto' => $_POST['monto'],
                'envio_domicilio' => $_POST['envio_domicilio'],
                'fecha_pedido' => $_POST['fecha_pedido'],
                'canal_id' => $_POST['canal_id'],
                'red_social_id' => $_POST['red_social_id'],
                'anuncio_id' => $_POST['anuncio_id'] ?? null
            ];
            
            // Actualizar cliente si viene un nuevo cliente_id
            if (!empty($_POST['cliente_id'])) {
                $pedidoData['cliente_id'] = $_POST['cliente_id'];
            }

            $pedidoData['id'] = $_POST['id'];

            $update = $this->updatePedido($this->util->sql($pedidoData, 1));
            
            if ($update) {
                // Actualizar productos si vienen
                if (isset($_POST['producto_id']) && is_array($_POST['producto_id'])) {
                    // Eliminar productos anteriores
                    $this->deleteProductosPedido([$_POST['id']]);
                    
                    // Insertar nuevos productos
                    foreach ($_POST['producto_id'] as $productoId) {
                        $this->createProductoPedido($this->util->sql([
                            'producto_id' => $productoId,
                            'pedido_id' => $_POST['id']
                        ]));
                    }
                }
                
                $status = 200;
                $message = 'Pedido editado correctamente';
            }
            
        } catch (Exception $e) {
            $message = 'Error al editar pedido: ' . $e->getMessage();
        }
        
        return [
            'status' => $status,
            'message' => $message
        ];
    }

    function verifyTransfer() {
        $status = 500;
        $message = 'Error al verificar pago';
        
        try {
            $pedidoData = [
                'pago_verificado' => 1,
                'fecha_pagado' => date('Y-m-d H:i:s'),
                'user_id' => $_SESSION['USER_ID'] ?? 0,
                'id' => $_POST['id']
            ];
            
            $update = $this->updatePedido($this->util->sql($pedidoData, 1));
            
            if ($update) {
                $status = 200;
                $message = 'Pago verificado correctamente';
            }
            
        } catch (Exception $e) {
            $message = 'Error al verificar pago: ' . $e->getMessage();
        }
        
        return [
            'status' => $status,
            'message' => $message
        ];
    }

    function registerArrival() {
        $status = 500;
        $message = 'Error al registrar llegada';
        
        try {
            $pedidoData = [
                'llego_establecimiento' => $_POST['arrived'],
                'id' => $_POST['id']
            ];
            
            $update = $this->updatePedido($this->util->sql($pedidoData, 1));
            
            if ($update) {
                $status = 200;
                $message = $_POST['arrived'] == 1 ? 'Cliente llegó al establecimiento' : 'Cliente no llegó al establecimiento';
            }
            
        } catch (Exception $e) {
            $message = 'Error al registrar llegada: ' . $e->getMessage();
        }
        
        return [
            'status' => $status,
            'message' => $message
        ];
    }

    function cancelPedido() {
        $status = 500;
        $message = 'Error al cancelar pedido';
        
        try {
            $pedidoData = [
                'active' => '0',
                'id' => $_POST['id']
            ];
            
            $update = $this->updatePedido($this->util->sql($pedidoData, 1));
            
            if ($update) {
                $status = 200;
                $message = 'Pedido cancelado correctamente';
            }
            
        } catch (Exception $e) {
            $message = 'Error al cancelar pedido: ' . $e->getMessage();
        }
        
        return [
            'status' => $status,
            'message' => $message
        ];
    }

    function sanitize($value) {
        return (isset($value) && $value !== '' && strtolower(trim($value)) !== 'null')
            ? $value
            : null;
    }
}

function dropdown($id, $active, $puedeEditar, $pagoVerificado, $udn, $llego){
    $options = [];
    
    if ($puedeEditar && $active == 1) {
        $options[] = [
            'icon' => 'icon-pencil',
            'text' => 'Editar',
            'onclick' => "pedidos.editPedido($id)"
        ];
    }
    
    if (!$pagoVerificado && $active == 1) {
        $options[] = [
            'icon' => 'icon-dollar',
            'text' => 'Verificar Pago',
            'onclick' => "pedidos.verifyTransfer($id)"
        ];
    }
    if ($udn == 1 && $llego != 1 && $llego != null) {
        $options[] = [
            'icon' => 'icon-map-pin',
            'text' => 'Registrar Llegada',
            'onclick' => "pedidos.registerArrival($id)"
        ];
    }
    
    if ($active == 1) {
        $options[] = [
            'icon' => 'icon-trash',
            'text' => 'Cancelar',
            'onclick' => "pedidos.cancelPedido($id)"
        ];
    }

    return $options;
}

function renderStatus($status) {
    switch ($status) {
        case 1:
            return '<span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                        Pagado
                    </span>';
        case 0:
            return '<span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                        <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                        No pagado
                    </span>';
        default:
            return '<span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                        <span class="w-2 h-2 bg-gray-500 rounded-full"></span>
                        Desconocido
                    </span>';
    }
}

function renderArrivalStatus($arrived, $udn) {
    if ( $udn == 1) {
        if ($arrived != null && $arrived != 0) {
            return '<span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                    <i class="icon-check"></i>
                    Llegó
                </span>';
           
        } else {
            return '<span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                    <i class="icon-times"></i>
                    No llegó
                </span>';
        }
    } else {
        return '<span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                    No aplica
                </span>';
    }
}

function renderEnvio($envio_domicilio) {
    if ($envio_domicilio == 1) {
        return '<span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                    <i class="icon-truck"></i>
                    Domicilio
                </span>';
    } else {
        return '<span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                    <i class="icon-shopping-bag"></i>
                    Recoger
                </span>';
    }
}

function evaluar($value) {
    return '$' . number_format($value, 2, '.', ',');
}

function canalSVG($canalNombre) {
    if (!$canalNombre) return '';
    $nombre = strtolower(trim($canalNombre));
    $nombre = preg_replace('/[^a-z0-9_-]/', '', $nombre); // solo letras, números, guion y guion bajo
    $svgPath = '../../marketing/img/' .  $nombre . '.svg';
   
        return '<img src="' . $svgPath . '" class="w-6 h-6 object-contain">';
 
}

$obj = new ctrl();
echo json_encode($obj->{$_POST['opc']}());
