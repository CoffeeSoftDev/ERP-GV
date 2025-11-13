<?php
if( empty($_POST['opc']) ) exit(0);

require_once('../mdl/mdl-colaboradores.php');
$obj = new Colaboradores;

require_once('../mdl/mdl-colaboradores.php');
$obj = new Colaboradores;

$encode = [];
switch ($_POST['opc']) {
    case 'lsUDN': 
            $encode = $obj->lsUDN();
        break;
    case 'tbColaboradores':
            // Variables de uso general
            $estado   = $_POST['estado'];
            $fechaNow = $obj->NOW();

            // Manipulacion de TH
            $thPeriodo  = ($estado == 1) ? "Crecimiento" : "Duración";
            
            $encode['table'] = ["id"=>"tbColaborador"];
            $encode['thead'] = [
                "Colaborador",
                "Cumpleaños",
                "Teléfono",
                "$thPeriodo",
                "S. Diario",
                "S. Fiscal",
                "Opciones"
            ];

            $tbody = [];
            $sql   = $obj->lsColaboradores([$_POST['idE'],$estado]);
            foreach ($sql as $tr) {
                $id = $tr['id'];
                // Alerta por defecto
                $alerta       = '<span class="text-danger icon-alert"></span>';
                $departamento = isset($tr['departamento']) ? $tr['departamento'].'/' : '';
                $puesto       = isset($tr['puesto']) ? $tr['puesto'] : '';

                $span = ($departamento != '' || $puesto != '') 
                        ? "<sub class='form-text fw-bold'>$departamento $puesto</sub><br>" 
                        : '';
                
                if ($estado == 0) $fechaNow = $tr['baja'];

                $fecha_alta  = isset($tr['alta']) ? $tr['alta'] : $alerta;
                $crecimiento = isset($tr['alta']) ? diferenciaFechas($fecha_alta,$fechaNow) : '-';

                $tbody[] = [
                    [ "html" => $span.$tr['nombre_completo']],
                    [ "html" => $tr['nacimiento'], "class" => "text-center" ],
                    [ "html" => isset($tr['telefono']) ? formatearTelefono($tr['telefono']) : $alerta,"class"=>"text-center" ],
                    [ "html" => $crecimiento['s'], "class" => "text-center","title" => "$fecha_alta / $fechaNow" ],
                    [ "html" => "$ ".number_format($tr["sd"],2,'.',','), "class" => "text-end" ],
                    [ "html" => "$ ".number_format($tr["sf"],2,'.',','), "class" => "text-end" ],
                    [
                        "elemento" => "button",
                        "button" => [
                            "click" => ["editarColaborador($id,'".$tr['nombre_completo']."')"]
                        ]
                    ]
                ];
            }
            
            $encode['tbody'] = $tbody;
        break;
}

echo json_encode($encode);

function formatearTelefono($numero) {
    // Eliminar cualquier caracter no numérico del número
    $numeroLimpio = preg_replace('/[^0-9]/', '', $numero);

    if (strlen($numeroLimpio) !== 10) return '<i class="text-danger icon-alert"></i>';

    // Aplicar un formato específico (por ejemplo, (123) 456-7890)
    $telefonoFormateado = sprintf("(%s) %s %s",
        substr($numeroLimpio, 0, 3),
        substr($numeroLimpio, 3, 3),
        substr($numeroLimpio, 6)
    );

    return $telefonoFormateado;
}
function diferenciaFechas($beforeDate,$afterDate) {
    $fechaActual = new DateTime($afterDate. " 00:00:00");
    $fechaNac = new DateTime($beforeDate. " 00:00:00");
    $anios = $fechaActual->format('Y') - $fechaNac->format('Y');
    $meses = $fechaActual->format('m') - $fechaNac->format('m');
    $dias = $fechaActual->format('d') - $fechaNac->format('d');

    if ($fechaActual->format('Y') === $fechaNac->format('Y')) {
        if ($fechaActual->format('m') === $fechaNac->format('m')) {
            if ($fechaActual->format('d') <= $fechaNac->format('d')) {
                $anios = 0;
                $meses = 0;
                $dias = 0;
            }
        }
    }

    // if ($fechaActual->format('m') < $fechaNac->format('m')) {
    //     $anios = 0;
    //     $meses = 0;
    //     $dias = 0;
    // }

    if ($meses < 0 || ($meses === 0 && $dias < 0)) {
        $anios--;
        $meses += 12;
    }

    if ($dias < 0) {
        $ultimoDiaMesAnterior = (new DateTime($fechaActual->format('Y-m-d')))->modify('last day of previous month')->format('d');
        $dias += $ultimoDiaMesAnterior;
        $meses--;
    }

    $datos = [
        'y' => $anios,
        'm' => $meses,
        'd' => $dias,
        's' => '',
    ];

    if ($datos['d'] == 0 || !is_numeric($datos['d'])) {
        $datos['s'] = 'Inválido';
    } else {
        if ($anios >= 1) {
            // Años
            $datos['s'] = $anios . ' año';
            if ($anios > 1) $datos['s'] .= 's';    
            // Meses
            if($meses >= 1) {
                $datos['s'] .= ' '.$meses . ' mes';
                if ($meses > 1) ' '.$datos['s'] .= 'es';
            }
        } else if ($meses >= 1) {
            // Meses
            $datos['s'] = $meses . ' mes';
            if ($meses > 1) $datos['s'] .= 'es';
            // Días
            $datos['s'] .= ' '.$dias . ' día';
            if ($dias > 1) ' '.$datos['s'] .= 's';
        } else {
            // Días
            $datos['s'] = $dias . ' día';
            if ($dias > 1) $datos['s'] .= 's';
        }
    }

    return $datos;
}
function cumple($fecha_nacimiento){    
    $fechaNac = new DateTime($fecha_nacimiento. " 00:00:00");
    
    $y = $fechaNac->format('Y');
    $m = $fechaNac->format('m');
    $d = $fechaNac->format('d');



}
?>