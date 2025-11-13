<?php
session_start();
if (empty($_POST['opc'])) exit(0);

require_once '../mdl/mdl-ingresos.php';

class ctrl extends mdl {

    public $util;

    function __construct() {
        parent::__construct();
        // $this->util = new Utileria();
    }

     function lsIngresosCaptura() {
        $fi  = new DateTime($_POST['anio'].'-' . $_POST['mes'] . '-01');
        $hoy = clone $fi;
        $hoy->modify('last day of this month');

        $__row = [];

        while ($fi <= $hoy) {
            $idRow++;
            $fecha = $fi->format('Y-m-d');
            // $softVentas = $this->getsoftVentas([$_POST['UDN'], $fecha]);
            // $idVentas = $softVentas['id_venta'];

            $row = [
                'id'     => $idRow,
                'fecha'  => $fecha,
            //     'dia'    => formatSpanishDay($fecha),
            //     'Estado' => $softVentas['id_venta']
            //         ? '<i class="icon-ok-circled-2 text-success"></i>'
            //         : '<i class="icon-info-circled-3 text-warning"></i>',
            ];

            // if ($_POST['UDN'] == 1) {
            //     $total = $softVentas['Hospedaje'] + $softVentas['AyB'] + $softVentas['Diversos'];
            //     $grupo = $this->createdGroups(['noHabitaciones', 'Hospedaje', 'AyB', 'Diversos'], $softVentas, $idVentas);
            //     $grupo['total'] = evaluar($total);
            //     $grupo['opc'] = 0;
            // } elseif ($_POST['UDN'] == 5) {
            //     $total = $softVentas['alimentos'] + $softVentas['bebidas'] + $softVentas['guarniciones'] + $softVentas['sales'] + $softVentas['domicilio'];
            //     $grupo = $this->createdGroups(['noHabitaciones', 'alimentos', 'bebidas', 'guarniciones', 'sales', 'domicilio'], $softVentas, $idVentas);
            //     $grupo['total'] = evaluar($total);
            //     $grupo['opc'] = 0;
            // } else {
                $grupo = [
            //         'alimentos' => createElement('input', [
            //             'name' => 'alimentos',
            //             'value' => number_format($softVentas['alimentos'], 2, '.', ''),
            //             'onkeyup' => "ingresosDiarios.setVentas(event, $idVentas)"
            //         ]),
            //         'bebidas' => createElement('input', [
            //             'name' => 'bebidas',
            //             'value' => number_format($softVentas['bebidas'], 2, '.', ''),
            //             'onkeyup' => "ingresosDiarios.setVentas(event, $idVentas)"
            //         ]),
            //         'No habitaciones' => createElement('input', [
            //             'name' => 'noHabitaciones',
            //             'value' => $softVentas['noHabitaciones'],
            //             'onkeyup' => "ingresosDiarios.setVentas(event, $idVentas)"
            //         ]),
            //         'Total' => evaluar($softVentas['bebidas'] + $softVentas['alimentos']),
                    'opc' => 0
                ];
            // }

            $__row[] = array_merge($row, $grupo);
            $fi->modify('+1 day');
        }

        return [
            "row" => $__row,
            "thead" => '',
            "frm_head" => "<strong>Conectado a: </strong> {$this->bd}"
        ];
    }




}    


// ✅ Instancia final del controlador
$ctrl = new ctrl();
echo json_encode($ctrl->{$_POST['opc']}());