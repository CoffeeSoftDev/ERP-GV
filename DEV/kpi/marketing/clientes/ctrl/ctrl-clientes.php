<?php
if (empty($_POST['opc'])) exit(0);

require_once '../mdl/mdl-clientes.php';

class ctrl extends mdl {

    function init() {
        return [
            'udn' => $this->lsUDN()
        ];
    }

    // Clientes.

    function listClientes() {
        $__row = [];
        
        $active = isset($_POST['active']) ? $_POST['active'] : 1;
        $udnId  = isset($_POST['udn_id']) && $_POST['udn_id'] !== 'all' ? $_POST['udn_id'] : null;
        $vip    = isset($_POST['vip']) && $_POST['vip']       !== 'all' ? $_POST['vip'] : null;
        
        $ls     = $this->lsClientes([$active, $udnId, $vip]);

        foreach ($ls as $key) {
            $a = [];

            $nombreCompleto = $key['nombre'];
            $correo = $key['correo'] ?? '';

            //  Filtro unicamente para tabachines
            if($key['udn_id'] == 1){

                $badgeVIP = $key['vip'] == 1
                    ? '<span class="px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-yellow-600"><i class="icon-star"></i> VIP</span>'
                    : '<span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">Regular</span>';
            }else{
                $badgeVIP = '<span class="vip-badge px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 ">Regular</span>';
            }


            if ($key['active'] == 1) {

                if($key['udn_id'] == 1){

                    $a[] = [
                        'class'   => 'btn btn-sm bg-orange-100  text-yellow-600 hover:bg-orange-300  hover:text-yellow-700 me-1',
                        'html'    => '<i class="icon-star"></i>',
                        'onclick' => 'clientes.updateClientVipStatus(' . $key['id'] . ', ' . $key['vip'] . ')'
                    ];
                }

                $a[] = [
                    'class'   => 'btn btn-sm btn-primary me-1',
                    'html'    => '<i class="icon-pencil"></i>',
                    'onclick' => 'clientes.editCliente(' . $key['id'] . ')'
                ];

             
                $a[] = [
                    'class'   => 'btn btn-sm btn-danger',
                    'html'    => '<i class="icon-toggle-on"></i>',
                    'onclick' => 'clientes.statusCliente(' . $key['id'] . ', ' . $key['active'] . ')'
                ];



            } else {
                $a[] = [
                    'class'   => 'btn btn-sm btn-warning me-1',
                    'html'    => '<i class="icon-star"></i>',
                    'onclick' => 'clientes.updateClientVipStatus(' . $key['id'] . ', ' . $key['vip'] . ')'
                ];

                $a[] = [
                    'class'   => 'btn btn-sm btn-outline-success',
                    'html'    => '<i class="icon-toggle-off"></i>',
                    'onclick' => 'clientes.statusCliente(' . $key['id'] . ', ' . $key['active'] . ')'
                ];
            }

            $__row[] = [
                'id'                  => $key['id'],
                'Cliente'             => ['html' => renderUserCard($nombreCompleto, $correo,$key['color']), 'class' => 'align-middle'],
                'Teléfono'            => $key['telefono'],
                'Unidad de Negocio'   => $key['udn_nombre'],
                'Fecha de cumpleaños' => formatSpanishDate($key['fecha_cumpleaños']),
                'Estatus'             => renderStatus($key['active']),
                'VIP'                 => ['html' => $badgeVIP, 'class' => 'text-center '],
                'a'                   => $a
            ];
        }

        return [
            'row' => $__row,
            'ls' => $ls
        ];
    }

    function getCliente() {
        $status = 500;
        $message = 'Error al obtener los datos del cliente';
        $data = null;

        if (empty($_POST['id'])) {
            return [
                'status' => 400,
                'message' => 'ID de cliente no proporcionado',
                'data' => null
            ];
        }

        $cliente = $this->getClienteById($_POST['id']);

        if ($cliente) {
            $status = 200;
            $message = 'Cliente obtenido correctamente';
            $data = $cliente;
        } else {
            $status = 404;
            $message = 'Cliente no encontrado';
        }

        return [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
    }

    function addCliente() {
        $status = 500;
        $message = 'No se pudo registrar el cliente';

       

        $_POST['fecha_creacion'] = date('Y-m-d H:i:s');
        $_POST['active']         = 1;
        $_POST['vip']            = isset($_POST['vip']) && $_POST['vip'] == 1 ? 1 : 0;



        $clienteId = $this->createCliente($this->util->sql($_POST));
         if ($clienteId) {
           
            $status = 200;
            $message = 'Cliente agregado correctamente';

        }
      
        return [
            'status' => $status,
            'message' => $message
        ];
    }

    function editCliente() {
        $status = 500;
        $message = 'Error al actualizar el cliente';



        // $exists = $this->existsClienteByPhone($_POST['telefono'], $_POST['id']);
        // if ($exists > 0) {
        //     return [
        //         'status' => 409,
        //         'message' => 'Ya existe otro cliente registrado con ese número de teléfono'
        //     ];
        // }

        // $_POST['vip'] = isset($_POST['vip']) && $_POST['vip'] == 1 ? 1 : 0;

        $values  = $this->util->sql($_POST, 1);
        $updated = $this->updateCliente($values);

        if ($updated) {
        
            $status = 200;
            $message = 'Cliente actualizado correctamente';
        }

        return [
            'status' => $status,
            'message' => $message,
            $values
        ];
    }

    function statusCliente() {
        $status = 500;
        $message = 'No se pudo actualizar el estatus del cliente';

        if (empty($_POST['id'])) {
            return [
                'status' => 400,
                'message' => 'ID de cliente no proporcionado'
            ];
        }

        $updated = $this->updateCliente($this->util->sql($_POST, 1));

        if ($updated) {
            $status = 200;
            $message = "guardado correctamente";
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }

    function getEstadisticas() {
        $udnId = isset($_POST['udn_id']) && $_POST['udn_id'] !== 'all' ? $_POST['udn_id'] : null;

        $totalActivos = $this->getTotalClientesActivos($udnId);
        $totalVIP = $this->getTotalClientesVIP($udnId);
        $cumpleañosMes = $this->getClientesCumpleañosMes($udnId);

        return [
            'status' => 200,
            'data' => [
                'total_activos' => $totalActivos,
                'total_vip' => $totalVIP,
                'cumpleaños_mes' => count($cumpleañosMes),
                'lista_cumpleaños' => $cumpleañosMes
            ]
        ];
    }

    // Comportamient.
    function listComportamiento() {
        $__row = [];
        
        $active = isset($_POST['active']) ? $_POST['active'] : 1;
        $udnId = isset($_POST['udn_id']) && $_POST['udn_id'] !== 'all' ? $_POST['udn_id'] : null;

        $ls = $this->getComportamientoClientes([$active, $udnId]);

        foreach ($ls as $key) {
            $a = [];

            $nombreCompleto = $key['nombre'] ;
            $correo = $key['correo'] ?? '';

            $badgeVIP = $this->renderVipBadge($key['id'], $key['vip']);

            $userCard = renderUserCard($nombreCompleto, $correo,$key['color'] );

            $badgeFrecuencia = '';
            switch ($key['frecuencia']) {
                case 'Activo':
                    $badgeFrecuencia = '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-200 text-green-800 w-20 text-center inline-block">Activo</span>';
                    break;
                case 'Regular':
                    $badgeFrecuencia = '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-200 text-yellow-600 w-20 text-center inline-block">Regular</span>';
                    break;
                case 'Inactivo':
                    $badgeFrecuencia = '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-200 text-red-600 w-20 text-center inline-block">Inactivo</span>';
                    break;
                case 'Sin pedidos':
                    $badgeFrecuencia = '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-200 text-gray-600 w-20 text-center inline-block">Sin pedidos</span>';
                    break;
            }

            $a[] = [
                'class' => 'btn btn-sm btn-info',
                'html' => '<i class="icon-chart-line"></i>',
                'onclick' => 'analitycs.verDetalle(' . $key['id'] . ')'
            ];

            $__row[] = [
                'id'               => $key['id'],
                'Cliente'          => ['html' => $userCard, 'class' => 'align-middle'],
                'UDN'              => $key['udn_nombre'],
                'Total Pedidos'    => ['html' => '<strong>' . number_format($key['total_pedidos']) . '</strong>', 'class' => 'text-center '],
                'Monto Total'      => ['html' => '$' . number_format($key['monto_total'], 2), 'class' => 'text-end '],
                'Ticket Promedio'  => ['html' => '$' . number_format($key['ticket_promedio'], 2), 'class' => 'text-end '],
                'Última Compra'    => $key['ultima_compra'] ? formatSpanishDate($key['ultima_compra']) : '-',
                'Días sin Comprar' => ['html' => $key['dias_sin_comprar'] ?? '-', 'class' => 'text-center '],
                'Frecuencia'       => ['html' => $badgeFrecuencia, 'class' => 'text-center '],
                'a'                => $a
            ];
        }

        return [
            'row' => $__row,
            'ls' => $ls
        ];
    }

    function listPorFrecuencia() {
        $frecuencia = $_POST['frecuencia'] ?? 'activo';
        $udnId = isset($_POST['udn_id']) && $_POST['udn_id'] !== 'all' ? $_POST['udn_id'] : null;

        $ls = $this->getClientesPorFrecuencia($frecuencia, $udnId);

        return [
            'status' => 200,
            'data' => $ls
        ];
    }

    function getTopClientes() {
        $limit = $_POST['limit'] ?? 10;
        $udnId = isset($_POST['udn_id']) && $_POST['udn_id'] !== 'all' ? $_POST['udn_id'] : null;

        $ls = $this->getTopClient( $udnId);

        return [
            'status' => 200,
            'data' => $ls
        ];
    }

    function getComportamiento() {
        $status = 500;
        $message = 'Error al obtener comportamiento del cliente';
        $data = null;

        if (empty($_POST['id'])) {
            return [
                'status' => 400,
                'message' => 'ID de cliente no proporcionado'
            ];
        }

        $comportamiento = $this->getComportamientoCliente($_POST['id']);
        $historial = $this->getHistorialPedidos($_POST['id'], 10);

        if ($comportamiento) {
            $status = 200;
            $message = 'Comportamiento obtenido correctamente';
            $data = [
                'cliente' => $comportamiento,
                'historial' => $historial
            ];
        }

        return [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
    }

    function updateClientStatus() {
        $status = 500;
        $message = 'No se pudo actualizar el estado VIP del cliente';

        if (empty($_POST['id'])) {
            return [
                'status' => 400,
                'message' => 'ID de cliente no proporcionado'
            ];
        }

        if (!isset($_POST['vip'])) {
            return [
                'status' => 400,
                'message' => 'Estado VIP no proporcionado'
            ];
        }

        $vipStatus = $_POST['vip'] == 1 ? 1 : 0;
        $clientId = $_POST['id'];

        // Preparar datos para actualización
        $updateData = [
            'values' => 'vip = ?',
            'data' => [$vipStatus, $clientId]
        ];

        $updated = $this->updateClientVipStatus($updateData);

        if ($updated) {
            $status = 200;
            $statusText = $vipStatus == 1 ? 'VIP' : 'Regular';
            $message = "Cliente actualizado a {$statusText} correctamente";
        }

        return [
            'status' => $status,
            'message' => $message,
            'new_status' => $vipStatus
        ];
    }

    private function renderVipBadge($clientId, $vipStatus) {
        if ($vipStatus == 1) {
            return '<span class="vip-badge px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-yellow-600 cursor-pointer hover:bg-orange-200 transition-colors" 
                        data-client-id="' . $clientId . '" 
                        data-vip-status="1" 
                        title="Clic para cambiar a Regular">
                        <i class="icon-star"></i> VIP
                    </span>';
        } else {
            return '<span class="vip-badge px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 cursor-pointer hover:bg-gray-200 transition-colors" 
                        data-client-id="' . $clientId . '" 
                        data-vip-status="0" 
                        title="Clic para cambiar a VIP">
                        Regular
                    </span>';
        }
    }


}

function renderUserCard($name, $email = '', $color = '#2563EB') {
    $initials = '';
    $nameParts = explode(' ', trim($name));
    
    if (count($nameParts) >= 2) {
        $initials = strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1));
    } else {
        $initials = strtoupper(substr($name, 0, 2));
    }
    
    return '
        <div class="flex items-center gap-3">
            <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-sm" 
                 style="background-color: ' . $color . ';">
                ' . $initials . '
            </div>
            <div class="flex flex-col">
                <span class="text-xs font-semibold text-gray-800">' . htmlspecialchars($name) . '</span>
                ' . (!empty($email) ? '<span class="text-xs text-gray-500">' . htmlspecialchars($email) . '</span>' : '') . '
            </div>
        </div>
    ';
}

function renderStatus($status) {
    switch ($status) {
        case 1:
            return '<span class="inline-flex items-center gap-2 px-4 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                        Activo
                    </span>';
        case 0:
            return '<span class="inline-flex items-center gap-2 px-4 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                        <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                        Inactivo
                    </span>';
        default:
            return '<span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                        <span class="w-2 h-2 bg-gray-500 rounded-full"></span>
                        Desconocido
                    </span>';
    }
}

function formatSpanishDate($date) {
    if (empty($date)) return '-';
    $timestamp = strtotime($date);
    $months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    return date('d', $timestamp) . ' ' . $months[date('n', $timestamp) - 1] . ' ' . date('Y', $timestamp);
}

$obj = new ctrl();
echo json_encode($obj->{$_POST['opc']}());


   

