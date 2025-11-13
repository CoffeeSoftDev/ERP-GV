<?php
function evaluar($val, $simbol = '$'){

    $value = is_nan( $val ) ? 0 : $val;

    if($simbol == ''){
        
        if($value < 0):
            
            $valor =  number_format($value, 2, '.', ',');
            return $value ?  "<span class='text-danger'>  $valor </span>"     : '-';

        
        else:
            return number_format($value, 2, '.', ', ');
        
        endif;    
        
        
    }if($simbol == '%'){
        
        if($value < 0):
            
            $valor =  number_format($value, 2, '.', ',');
            return $value ?  "<span class='text-danger'>$  $valor </span>"     : '-';
       
        else:
            return $value ?  number_format($value, 2, '.', ',').' % ' : '-';
        endif; 
        
        
        
    }else {
     
       if($value < 0):
        $valor =  number_format($value, 2, '.', ',');

        return $value ?  "<span class='text-danger'>$  $valor </span>"     : '-';

       else:
            return $value ? '$ ' . number_format($value, 2, '.', ',') : '-';
       endif; 

    }


    
}

function formatSpanishDateAll($fecha = null) {
    setlocale(LC_TIME, 'es_ES.UTF-8'); // Establecer la localización a español

    if ($fecha === null) {
        $fecha = date('Y-m-d'); // Utilizar la fecha actual si no se proporciona una fecha específica
    }

    // Convertir la cadena de fecha a una marca de tiempo
    $marcaTiempo = strtotime($fecha);

    $formatoFecha = "%A, %d de %B del %Y"; // Formato de fecha en español
    $fechaFormateada = strftime($formatoFecha, $marcaTiempo);

    return $fechaFormateada;
}

function formatSpanishDate($fecha = null) {
    setlocale(LC_TIME, 'es_ES.UTF-8'); // Establecer la localización a español

    if ($fecha === null) {
        $fecha = date('Y-m-d'); // Utilizar la fecha actual si no se proporciona una fecha específica
    }

    // Convertir la cadena de fecha a una marca de tiempo
    $marcaTiempo = strtotime($fecha);

    $formatoFecha = "%d/%B/%Y"; // Formato de fecha en español
    $fechaFormateada = strftime($formatoFecha, $marcaTiempo);

    return $fechaFormateada;
}

function formatSpanishNoDay($fecha = null){
    setlocale(LC_TIME, 'es_ES.UTF-8'); // Establecer la localización a español

   // Si no se proporciona una fecha específica, usar la fecha actual
    if ($fecha === null) {
        $fecha = date('Y-m-d');
    }

    // Convertir la cadena de fecha a una marca de tiempo
    $marcaTiempo = strtotime($fecha);

    // Obtener el número del día de la semana (0 para domingo, 6 para sábado)
    $numeroDia = date('w', $marcaTiempo);

    return $numeroDia;
}



function formatSpanishDay($fecha = null){
    setlocale(LC_TIME, 'es_ES.UTF-8'); // Establecer la localización a español

    if ($fecha === null) {
        $fecha = date('Y-m-d'); // Utilizar la fecha actual si no se proporciona una fecha específica
    }

    // Convertir la cadena de fecha a una marca de tiempo
    $marcaTiempo = strtotime($fecha);

    $formatoFecha = "%A"; // Formato de fecha en español
    $fechaFormateada = strftime($formatoFecha, $marcaTiempo);

    return $fechaFormateada;
}

function listDays(){
    
    return [
        2 => 'Lunes',
        3 => 'Martes',
        4 => 'Miercoles',
        5 => 'Jueves',
        6 => 'Viernes',
        7 => 'Sabado',
        1 => 'Domingo'
        
    ];
}

