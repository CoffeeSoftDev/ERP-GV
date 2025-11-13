<?php

session_start();
if (empty($_POST['opc'])) exit(0);

require_once '../mdl/mdl-tabulacion-calificaciones.php';

$encode = [];

class ctrl extends mdl {

    function list() {

        $instancia = 'calificacion';
        $__row     = [];

        $ls = $this->getEntities([
            'id_tabulation' => $_POST['id'],
        ]);


        foreach ($ls as $item) {

            $a   = [];
            $a[] = [
                'html'    => '<i class="icon-pencil"></i> Editar',
                'icon'    => 'icon-pencil',
                'onclick' => "{$instancia}.edit({$item['id']})",
                'class'   => 'btn btn-sm btn-outline-primary me-1 w-24'
            ];

            //  Promedios.
            $te = $item['te_manual'] ?? $item['te'];
            $pr = $item['pr_manual'] ?? $item['pr'];
            $ap = $item['ap_manual'] ?? $item['ap'];
            $ps = $item['ps_manual'] ?? $item['ps'];

            $calf = ($te + $pr + $ap + $ps) / 4;


            $__row[] = [
                'id'                => $item['id'],
                'Colaborador'       => $item['name'],
                'Num de evaluadores'       => [
                    'html' => $item['people_count'],
                    'class'=> 'bg-gray-100 text-center',
                ],
                'Trabajo en equipo' => $item['te'],
                'Profesionalismo'   => $item['pr'],
                'Actitud Positiva'  => $item['ap'],
                'Pasion Servicio'   => $item['ps'],

                'Promedio Obtenido' =>  [
                        'html'  => $calf,
                        'class' => 'bg-gray-100 text-center',
                    ]
                   ,

                'opc'          => 0
            ];
        }

        return ["row" => $__row, 'ls' => $ls ,'frm_head' => '
          <button class="my-2 btn btn-outline-dark " id="btnExit" type="button" onclick="app.init()">
            <i class=" icon-left-5"></i>
            Volver a tabulaciones
        </button>
        '];
    }

    function listConcentrado() {
        $instancia = 'calificacion';
        $__row     = [];

        $ls = $this->getEntities([
            'id_tabulation' => $_POST['id'],
        ]);

        foreach ($ls as $item) {
            $a = [];
            $a[] = [
                'html'    => '<i class="icon-pencil"></i> Editar',
                'icon'    => 'icon-pencil',
                'onclick' => "{$instancia}.edit({$item['id']})",
                'class'   => 'btn btn-sm btn-outline-primary me-1 w-24'
            ];

            // Promedios individuales.
            $te = $item['te'];
            $pr = $item['pr'];
            $ap = $item['ap'];
            $ps = $item['ps'];

            $calf = ($te + $pr + $ap + $ps) / 4;

            $__row[] = [
                'id'          => $item['id'],
                'Colaborador' => $item['name'],
                'No personas' => [
                    'html'  => $item['people_count'],
                    'class' => 'bg-gray-100 text-center',
                ],
                'total' => [
                    'val'   => round($calf, 2),
                    'html'  => number_format($calf, 2),
                    'class' => 'text-center'
                ],
                'opc' => 0
            ];
        }

        // Aplicar estilos de máximo y mínimo
        $__row = pintarValPromedios($__row, ['total']);

        return [
            "row" => $__row,
            'ls' => $ls,
            'frm_head' => '
                <button class="my-2 btn btn-outline-dark" id="btnExit" type="button" onclick="app.init()">
                    <i class="icon-left-5"></i> Volver a tabulaciones
                </button>'
        ];
    }


}

function dropdown($id) {
    return [
        ['icon' => 'icon-pencil', 'text' => 'Editar', 'onclick' => "calificacion.edit($id)"]
    ];
}

 function pintarValPromedios($row, $campos) {
    foreach ($campos as $campo) {
        foreach ($row as &$r) {
            $val = $r[$campo]['val'];

            if ($val >= 1 && $val <= 4.00) {
                $r[$campo]['class'] .= ' bg-red-300 text-red-800'; // BD
            } elseif ($val >= 4.01 && $val <= 4.11) {
                $r[$campo]['class'] .= ' bg-amber-400 text-black'; // DA
            } elseif ($val >= 4.12 && $val <= 4.50) {
                $r[$campo]['class'] .= ' bg-yellow-300 text-yellow-800'; // DE
            } elseif ($val >= 4.51 && $val <= 4.67) {
                $r[$campo]['class'] .= ' bg-blue-200 text-black'; // AD
            } elseif ($val >= 4.68 && $val <= 5.00) {
                $r[$campo]['class'] .= ' bg-green-400 text-green-800'; // DEX
            } else {
                $r[$campo]['class'] .= ' bg-gray-300 text-black';
            }
        }
    }

    return $row;
}


$obj    = new ctrl();
$fn     = $_POST['opc'];
$encode = $obj->$fn();

echo json_encode($encode);
