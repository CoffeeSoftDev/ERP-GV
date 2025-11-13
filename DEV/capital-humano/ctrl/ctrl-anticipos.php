<?php
if(empty($_POST['opc'])) exit(0);
date_default_timezone_set('America/Mexico_City');

require_once('../mdl/mdl-anticipos.php');   $obj  = new MAnticipos;
require_once('../mdl/mdl-incidencias.php'); $inc  = new Incidencias;
require_once('../../conf/_Utileria.php');   $util = new Utileria;

$encode = [];
switch ($_POST['opc']) {
case "init_components":
        $encode['udn']        = $inc->lsUDN();
        $encode['calendario'] = $inc->calendario_incidencias();
        $encode['habil']      = habilitado();
    break;
case "lsColaboradores":
        $sql = $obj->lsColaboradores([$_POST['udn']]);
        $colaboradores = [];
        foreach ($sql as $value) {
            $acumulado       = [
                    'acumulado' => number_format($obj->anticipo_acumulado([$value['id'],$_POST['date1'],$_POST['date2']]),2,'.',','),
                    'dias'      => dias_transcurridos($value['fecha_alta']),
                    'permitido' => (($value['sueldo'] * 15) * ($value['porcentaje'] / 100)),
                ];
            $colaboradores[] = array_merge($value,$acumulado);
        }
        $encode = $colaboradores;
    break;
case "tbAnticipos":
        $table = ['id'=>'tbAnticipos'];
        $thead = 'Fecha,Colaborador,Solicitado,Saldo Acumulado,imprimir';

        $sql = $obj->tbAnticipo($util->sql($_POST,3)['data']);
        $tbody = [];
        foreach ($sql as $value) {
            $tbody[] = [
                ['html' => $value['fecha'],'class'=>'text-center'],
                ['html' => $value['colaborador']],
                ['html' => '$ '.number_format($value['solicitado'],2,'.',','),'class'=>'text-end'],
                ['html' => '$ '.number_format($value['acumulado'],2,'.',','),'class'=>'text-end'],
                ['html' => '<button class="btn btn-info" onClick="formato_ancitipos('.$value['id'].');"><i class="icon-print"></i></button>','class'=>'text-center'],
            ];
        }
        
        $encode = [
            'table' => $table,
            'thead' => $thead,
            'tbody' => $tbody
        ];
    break;
case 'save':
        $habil = habilitado();
        if($habil){
            $_POST['Folio']          = $obj->folio();
            $_POST['Fecha_Anticipo'] = date('Y-m-d H:i:s');
            $encode                  = $obj->newAnticipo($util->sql($_POST));
        } else {
            $encode = $habil;
        }
    break;
case 'print':
        $encode                      = $obj->data_anticipo([$_POST['id']]);
        $porcentaje_sueldo           = ($encode['salario_diario'] * 15 ) * ($encode['porcentaje'] / 100 );
        $encode['telefono']          = $util->format_phone($encode['telefono']);
        $encode['porcentaje_sueldo'] = $util->format_number($porcentaje_sueldo);
        $encode['porcentaje']        = number_format($encode['porcentaje'],0).' %';
        $acumulado                   = $encode['acumulado'];
        $solicitado                  = $encode['solicitado'];
        $encode['solicitado']        = '+ '.$util->format_number($solicitado);
        $encode['anterior']          = $util->format_number($acumulado - $solicitado);
        $encode['acumulado']         = $util->format_number($acumulado);
        $encode['acumulado2']        = '- '.$util->format_number($acumulado);
        $encode['salario_diario']    = $util->format_number($encode['salario_diario']);
        $encode['disponible']        = $util->format_number($porcentaje_sueldo - $acumulado);
    break;
}

function dias_transcurridos($fecha){
    // Convertir la fecha ingresada en un timestamp
    $timestamp_fecha = strtotime($fecha);

    // Obtener el timestamp de hoy
    $hoy = time();

    // Calcular la diferencia en segundos entre la fecha ingresada y hoy
    $diferencia_segundos = $hoy - $timestamp_fecha;

    // Calcular el número de días de diferencia
    $dias_diferencia = intval($diferencia_segundos / (60 * 60 * 24));

    return $dias_diferencia;
}

function habilitado(){
    // Obtener la fecha actual
    $fechaActual = date('Y-m-d');
    // Obtener solo el día de la fecha actual
    $diaActual = date('d', strtotime($fechaActual));

    // Verificar si el día está entre el 5 y el 9, o entre el 20 y el 25
    if (($diaActual >= 5 && $diaActual <= 9) || ($diaActual >= 20 && $diaActual <= 25)) return true;

    return false;
}

echo json_encode($encode);


?>