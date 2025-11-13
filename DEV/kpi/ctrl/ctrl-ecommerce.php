<?php
if (empty($_POST['opc']))
    exit(0);


require_once ('../mdl/mdl-ecommerce.php');
$obj = new Ecommerce;

class RptEcommerce extends Ecommerce
{
    function lsEcommerce()
    {

        # Declarar variables
        $__row = [];

        // #Consultar a la base de datos
        $ls = $this->lsPedidosSonoras($_POST['fi'], $_POST['ff']);

        foreach ($ls as $key) {
            // Obtener fecha de compra
            $fechaCompra = new DateTime($key['nuevaCompra']);

            $compra = $key['nuevaCompra'];
            $enterado = $key['enterado'];
            $confirmado = $key['confirmado'];
            $preparando = $key['preparando'];
            $enviado = $key['enviado'];
            $cancelado = $key['cancelado'];

            $__row[] = [

                'id' => $key['idBitacoraEnvios'],
                'orden' => '#' . $key['id_Orden'],
                'fecha' => $fechaCompra->format('d-m-Y'),
                'compra' => $fechaCompra->format('H:i:s'),
                'enterado' => espera($compra, $enterado) . '<br>' . tiempo($compra, $enterado),
                'preparando' => espera($enterado, $preparando) . '<br>' . tiempo($enterado, $preparando),
                'enviado' => espera($preparando, $enviado) . '<br>' . tiempo($preparando, $enviado),
                'cancelado' => espera($enviado, $cancelado) . '<br>' . tiempo($enviado, $cancelado),
                'opc' => 0
            ];
        }

        #encapsular datos
        return [

            "thead" => '',
            "row" => $__row,
        ];

    }
    function lsEcommerceFz()
    {

        # Declarar variables
        $__row = [];

        // #Consultar a la base de datos
        $ls = $this->lsPedidosFogaza($_POST['fi'], $_POST['ff']);

        foreach ($ls as $key) {
            // Obtener fecha de compra
            $fechaCompra = new DateTime($key['nuevaCompra']);

            $compra = $key['nuevaCompra'];
            $enterado = $key['enterado'];
            $confirmado = $key['confirmado'];
            $preparando = $key['preparando'];
            $enviado = $key['enviado'];
            $cancelado = $key['cancelado'];

            $__row[] = [

                'id' => $key['idBitacoraEnvios'],
                'orden' => '#' . $key['id_Orden'],
                'fecha' => $fechaCompra->format('d-m-Y'),
                'compra' => $fechaCompra->format('H:i:s'),
                'enterado' => espera($compra, $enterado) . '<br>' . tiempo($compra, $enterado),
                'preparando' => espera($enterado, $preparando) . '<br>' . tiempo($enterado, $preparando),
                'enviado' => espera($preparando, $enviado) . '<br>' . tiempo($preparando, $enviado),
                'cancelado' => espera($enviado, $cancelado) . '<br>' . tiempo($enviado, $cancelado),
                'opc' => 0
            ];
        }

        #encapsular datos
        return [

            "thead" => '',
            "row" => $__row,
        ];

    }

    function lsCCVTQT()
    {

        # Declarar variables
        $__row = [];

        // #Consultar a la base de datos
        $ls = $this->lsBitacoraSuiteQT($_POST['fi'], $_POST['ff']);
        foreach ($ls as $key) {
            $a = [];

            $file = $key['rutaBitacora'] . $key['nameBitacora'];
            $a[] = [
                'id' => 1,
                'class' => 'btn btn-sm btn-success',
                'html' => '<i class="icon-download"></i>',
                'href' => '../../ERP/' . $file . '?t=' . time(),
                'target' => '_blank',
            ];


           
            $date = formato_titulo($key['fechaSuite']);
            $__row[] = [
                'id' => $key['idSuite'],
                'fecha' => $date,
                'suites' => $key['cantidad'],
                // 'file' => '<a href="../'.$file.'?t='.time().'" target="_blank" class="btn btn-sm btn-success" ><i class="icon-download"></i></a>',
                'a' => $a,
            ];
        }

        #encapsular datos
        return [
            "thead" => '',
            "row" => $__row,
        ];

    }
}

// Instancia del objeto

$obj = new RptEcommerce();
$fn = $_POST['opc'];
$encode = $obj->$fn();

function espera($fecha1, $fecha2)
{
    $respuesta = '';

    if ($fecha2 != '') {
        $espera = '';
        $horaInicio = new DateTime($fecha1);
        $horaTermino = new DateTime($fecha2);
        $interval = $horaInicio->diff($horaTermino);

        $dias = intval($interval->format('%d'));
        $horas = intval($interval->format('%H'));
        $minutos = intval($interval->format('%i'));
        $segundos = intval($interval->format('%s'));

        if ($dias > 0) {
            $espera .= $interval->format('%d día ');
        }

        if ($horas > 0) {
            $espera .= $interval->h . ' hr ';
        }

        if ($minutos > 0) {
            $espera .= $interval->i . ' min ';
        }

        if ($segundos > 0) {
            $espera .= $interval->format('%s seg.');
        }

        if ($minutos > 4) {
            $respuesta = '<i class="text-danger">' . $espera . '</i>';
        } else {
            $respuesta = $espera;
        }
    }

    return $respuesta;

}

function tiempo($fecha1, $fecha2)
{
    $response = '';
    if ($fecha2 != '') {
        $inicio = new DateTime($fecha1);
        $termino = new DateTime($fecha2);

        $date1 = $inicio->format('d-m-Y');
        $date2 = $termino->format('d-m-Y');

        if ($date1 == $date2) {
            $response = $termino->format('H:i:s');
        } else {
            $d = $termino->format('d');
            $m = $termino->format('m');
            $y = $termino->format('Y');

            $response = $d . '-' . mes_letra($m) . '-' . $y . ' ' . $termino->format('H:i:s');
        }
    }

    return $response;
}
function mes_letra($mes)
{
    $mes = intval($mes);
    switch ($mes) {
        case 1:
            $mes = 'ene';
            break;
        case 2:
            $mes = 'feb';
            break;
        case 3:
            $mes = 'mar';
            break;
        case 4:
            $mes = 'abr';
            break;
        case 5:
            $mes = 'may';
            break;
        case 6:
            $mes = 'jun';
            break;
        case 7:
            $mes = 'jul';
            break;
        case 8:
            $mes = 'ago';
            break;
        case 9:
            $mes = 'sep';
            break;
        case 10:
            $mes = 'oct';
            break;
        case 11:
            $mes = 'nov';
            break;
        case 12:
            $mes = 'dic';
            break;
    }

    return $mes;
}
function diaSemana($week)
{
    switch ($week) {
        case 1:
            $week = 'Lun';
            break;
        case 2:
            $week = 'Mar';
            break;
        case 3:
            $week = 'Mie';
            break;
        case 4:
            $week = 'Jue';
            break;
        case 5:
            $week = 'Vie';
            break;
        case 6:
            $week = 'Sab';
            break;
        default:
            $week = 'Dom';
            break;
    }

    return $week;
}
function formato_titulo($dateString)
{
    $date = new DateTime($dateString);

    $w = $date->format('w');
    $d = $date->format('d');
    $m = $date->format('m');
    $y = $date->format('Y');

    $titulo = diaSemana($w) . ': ' . $d . '-' . mes_letra($m) . '-' . $y;

    return $titulo;
}
// Print JSON :
echo json_encode($encode);
?>