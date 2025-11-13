<?php
if(empty($_POST['opc'])) exit(0);

require_once('../mdl/mdl-incidencias.php'); $obj  = new Incidencias;
require_once('../../conf/_Utileria.php');   $util = new Utileria;

$encode = [];

switch ($_POST['opc']) {
case 'init_components': 
        $encode['udn']        = $obj->lsUDN();
        $encode['simbolos']   = $obj->lsTerminologia();
        $encode['calendario'] = array_reverse($obj->calendario_incidencias());
    break;
case 'tbIncidencias': 
        $dates = explode(',',$_POST['dates']);
        $sqlColaboradores = $obj->lsColaboradores([$_POST['idE']]);
        $interval_date = interval_date($dates);

        foreach ($sqlColaboradores as $tr) {
            $incidencia = [];

              // Tratamiento nombre y puesto
            $span = '<sub class="fw-bold">'.$tr['nombre'].'</sub>';

            $incidencia[] = ['html'=>$span];
            $incidencia[] = ['html'=>'<sub class="form-text">'.$tr['departamento'].'</sub><br><sub>'.$tr['puesto'].'</sub>'];
            $incidencia[] = ['html'=>$util->format_number($tr['salario']),'class'=>'text-end'];
            $valor[]      = $obj->bitacora_incidencias(array_merge([$tr['id']],$dates));

            foreach ($interval_date as $fecha) {
                $valor = $obj->bitacora_incidencias([$tr['id'],$fecha]);
                $incidencia[] = [
                        'html' => '<input 
                            type      = "text"
                            class     = "cell-inc text-center text-uppercase"
                            maxlength = "2"
                            valor     = "'.$valor['valor'].'"
                            value     = "'.$valor['valor'].'"
                            style     = "color:#'.$valor['text_color'].';"
                            id        = "cell_'.$tr['id'].'_'.$fecha.'"
                            fecha     = "'.$fecha.'"
                            ident     = "'.$tr['id'].'"
                            tipo      = "texto"
                            onBlur    = "actualizar_incidencia('.$tr['id'].', \''.$fecha.'\');"
                            />',
                            "style" => 'background:#'.$valor['bg_color'].';'
                        ];
            }
              // Anticipos
            $anticipo     = $util->format_number($obj->anticipos([$dates[0],$dates[1],$tr['id']]));
            $incidencia[] = ['html'=>$anticipo,'class'=>'text-center'];
              // Complementos
            $complemento  = $obj->incidencias_extras([$dates[0],$dates[1],$tr['id']],'Complemento');
            $incidencia[] = ['html'=>$complemento,'class'=>'text-center'];
              // Bonos
            $bono         = $obj->incidencias_extras([$dates[0],$dates[1],$tr['id']],'Complemento');
            $incidencia[] = ['html'=>$bono,'class'=>'text-center'];
              // Adicionales
            $incidencia[] = ['html'=>'<button class="btn btn-sm btn-info" onClick="adicionales('.$tr['id'].',\''.$tr['nombre'].'\');"><i class="icon-pencil"></i></button>','class'=>'text-center'];

            $encode['tbody'][] = $incidencia;
            $encode['valor'] = $valor;
        }
    break;
case 'tbIncidencias2': 
        $dates = explode(',',$_POST['dates']);

        $encode['table']  = ['id'=>'tbIncidencias'];
        $interval_date    = interval_date($dates);
        $treatment        = month_day_format($interval_date);
        $encode['thead']  = array_merge(['Colaborador','Departamento','Salario Diario'],$treatment,['Anticipos','Complementos','Bonos','ADICIONALES']);
        $sqlColaboradores = $obj->lsColaboradores([$_POST['idE']]);
        foreach ($sqlColaboradores as $tr) {
            $incidencia = [];

              // Tratamiento nombre y puesto
            $span = '<sub class="fw-bold">'.$tr['nombre'].'</sub>';

            $incidencia[] = ['html'=>$span,'class'=>'col-2'];
            $incidencia[] = ['html'=>'<sub class="form-text">'.$tr['departamento'].'</sub><br><sub>'.$tr['puesto'].'</sub>','class'=>'col-2'];
            $incidencia[] = ['html'=>$util->format_number($tr['salario']),'class'=>'col-2 text-end'];

            foreach ($interval_date as $fecha) {
                $valor = $obj->bitacora_incidencias([$tr['id'],$fecha]);

                $incidencia[] = [
                        'html' => '<input 
                            type      = "text"
                            class     = "cell-inc text-center text-uppercase"
                            maxlength = "2"
                            valor     = "'.$valor['valor'].'"
                            value     = "'.$valor['valor'].'"
                            style     = "color:#'.$valor['text_color'].';"
                            id        = "cell_'.$tr['id'].'_'.$fecha.'"
                            fecha     = "'.$fecha.'"
                            ident     = "'.$tr['id'].'"
                            tipo      = "texto"
                            onBlur    = "actualizar_incidencia('.$tr['id'].', \''.$fecha.'\');"
                            />',
                            "style" => 'background:#'.$valor['bg_color'].';'
                        ];
            }
              // Anticipos
            $anticipo     = $util->format_number($obj->anticipos([$dates[0],$dates[1],$tr['id']]));
            $incidencia[] = ['html'=>$anticipo,'class'=>'text-center'];
              // Complementos
            $complemento  = $obj->incidencias_extras([$dates[0],$dates[1],$tr['id']],'Complemento');
            $incidencia[] = ['html'=>$complemento,'class'=>'text-center'];
              // Bonos
            $bono         = $obj->incidencias_extras([$dates[0],$dates[1],$tr['id']],'Complemento');
            $incidencia[] = ['html'=>$bono,'class'=>'text-center'];
              // Observaciones
            $incidencia[] = ['html'=>'<button class="btn btn-sm btn-info" onClick="adicionales('.$tr['id'].',\''.$tr['nombre'].'\');"><i class="icon-pencil"></i></button>','class'=>'text-center'];

            $encode['tbody'][] = $incidencia;
        }
    break;
case 'incidencias': 
        $valor                    = $_POST['valor'];
        $array['id_Terminologia'] = $obj->idTerminologia([$valor]);  //Obtenemos el id_Terminologia con el valor
        unset($_POST['valor']);  //Eliminamos el asociativo 'valor'

        $_POST      = array_merge($array,$_POST);                         //Agregamos al principio el nuevo asociativo 'id_Terminologia'
        $idBitacora = $obj->existencia_incidencia($util->sql($_POST,2));  //Obtenemos el idBitacora para poder hacer actualizaciones

        #Declaramos valores por defecto, preparados para un insert
        $funcion = 'nueva_incidencia';
        $array   = $util->sql($_POST);  // Le damos tratamiento al post

        if ( isset($idBitacora) ) { // En caso que idBitacora no este vacío se preparan las variables para un update
              // if ( $valor === '') {
              //     $funcion = 'delete_incidencia';
              //     $array   = $util->sql($_POST,2);  // Le damos tratamiento al post
              // } else {
                $funcion = 'update_incidencia';
                $array   = $util->sql($_POST,2);  // Le damos tratamiento al post
              // }
        }

        $encode[$funcion] = $obj->$funcion($array);
    break;
case 'incidencia_extra': 
        $encode = $util->sql($_POST);
    break;
case 'tb_incidencia_extra': 
        $array  = array_merge([$_POST['id']],explode(",",$_POST['dates']));
        $encode = $obj->incidencias_adicionales($array);
    break;
case 'save_incidencias_adicionales': 
        unset($_POST['opc']);
        $array      = ['where'=>['Fecha_Incidencia','id_Empleado'],'data'=>['',$_POST['Fecha_Incidencia'],$_POST['id_Empleado']]];
        $idBitacora = $obj->existencia_incidencia($array);
        
        $array = [];
        if ( isset($idBitacora) ) { // En caso que idBitacora no este vacío se preparan las variables para un update
                $funcion = 'update_incidencia';
                
                $array = ['hora_extra'=>$_POST['hora_extra'],'observaciones'=>$_POST['observaciones'],'idBitacoraIncidencia'=>$idBitacora];

                if($_POST['hora_extra'] == '' || $_POST['hora_extra'] == 'undefined' ) unset($array['hora_extra']);
                if($_POST['observaciones'] == '' || $_POST['observaciones'] == 'undefined' ) unset($array['observaciones']);

                $array = $util->sql($array,1);

        } else {
              // Elemento que deseamos mover
            $elemento = $_POST['Fecha_Incidencia'];
              // Eliminar el elemento de la posición 0
            unset($_POST['Fecha_Incidencia']);
            // Insertar el elemento en la posición 2
            $_POST = array_slice($_POST, 0, 2, true) +
            array('Fecha_Incidencia' => $elemento) +
            array_slice($_POST, 2, null, true);

            
            $funcion = 'nueva_incidencia';
            $array = $util->sql($_POST);
        }

        // $encode[] = $funcion;
        // $encode[] = $array;
        $encode[] = [$funcion => $obj->$funcion($array)];
    break;
case 'delete_incidencias_adicionales':
        $datos = array_merge([
            'hora_extra' => null,
            'observaciones' => null
        ],$_POST);
        $encode = $obj->update_incidencia($util->sql($datos,1));
        // $encode = $util->sql($datos,1);
        // $encode = $datos;
    break;
}
echo json_encode($encode);

function interval_date($dates){
      // Fecha inicial
    $fechaInicial = new DateTime($dates[0]);

      // Fecha final
    $fechaFinal = new DateTime($dates[1]);
    $fechaFinal->modify('+1 day');

      // Intervalo de un día
    $intervalo = new DateInterval('P1D');

      // Generar el intervalo de fechas
    $periodo = new DatePeriod($fechaInicial, $intervalo, $fechaFinal);

      // Recorrer e imprimir las fechas
            $recorrido                        = [];
    foreach ($periodo as $fecha) $recorrido[] = $fecha->format('Y-m-d');
    

    return $recorrido;
}

function month_day_format($fechas){
    
    $fechasTratadas = [];
      // Array para mapear nombres de meses en inglés a español
    $mesesEspañol = [
        'Jan' => 'Ene',
        'Feb' => 'Feb',
        'Mar' => 'Mar',
        'Apr' => 'Abr',
        'May' => 'May',
        'Jun' => 'Jun',
        'Jul' => 'Jul',
        'Aug' => 'Ago',
        'Sep' => 'Sep',
        'Oct' => 'Oct',
        'Nov' => 'Nov',
        'Dec' => 'Dic',
    ];

    
    $diasSemanaEspañol = [
        'Mon' => 'Lun',
        'Tue' => 'Mar',
        'Wed' => 'Mie',
        'Thu' => 'Jue',
        'Fri' => 'Vie',
        'Sat' => 'Sáb',
        'Sun' => 'Dom',
    ];

    foreach ($fechas as $fecha) {
          // Convertir el string de fecha a timestamp
        $timestamp = strtotime($fecha);

          // Formatear el timestamp para obtener el día y el mes en español
        $dia               = date('d', $timestamp);
        $mesEnIngles       = date('M', $timestamp);
        $diaSemanaEnIngles = date('D', $timestamp);

        $mesEnEspañol = isset($mesesEspañol[$mesEnIngles]) ? $mesesEspañol[$mesEnIngles] : $mesEnIngles;

          // Combinar el día y el mes en un solo string y agregar al nuevo array
        $fechaTratada     = $diasSemanaEspañol[$diaSemanaEnIngles].' '.$dia . '/' . $mesEnEspañol;
        $fechasTratadas[] = $fechaTratada;
    }

    return $fechasTratadas;
}
?>