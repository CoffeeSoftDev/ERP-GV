<?php
if (empty($_POST['opc']))
    exit(0);

// incluir tu modelo
require_once ('../mdl/mdl-mercadotecnia.php');
require_once ('../../conf/_Utileria.php');

// sustituir 'mdl' extends de acuerdo al nombre que tiene el modelo
class ctrl extends Kpismerca{

    function lsPromedios(){

        # -- variables para fechas
        $fi = new DateTime($_POST['Anio'] . '-' . $_POST['Mes'] . '-01');

        $hoy = clone $fi;

        $hoy->modify('last day of this month');
        $__row = [];


        while ($fi <= $hoy) {
            $idRow++;

            $fecha = $fi->format('Y-m-d');
            $softVentas = $this->getsoft_ventas([$_POST['UDN'],$fecha]);

            if($_POST['UDN']== 1):


                        $total = $softVentas['Hospedaje'] + $softVentas['AyB'] + $softVentas['Diversos'];
                        $noHabitaciones = $softVentas['noHabitaciones'];

                        $opc = $noHabitaciones ? 0 : 1;


                        $PromedioHospedaje = $softVentas['Hospedaje'] / $noHabitaciones;
                        $PromedioAyB       = $softVentas['AyB'] / $noHabitaciones;
                        $PromedioDiversos  = $softVentas['Diversos'] / $noHabitaciones;

                        $tarifaEfectivaDiaria = $total / 12;
                        $porcentajeOcupacion  = evaluar($noHabitaciones / 12, '%');


                    $__row[] = array(

                        'id'    => $idRow,
                        'fecha'            => ['text'=>$fecha],
                        'dia'              => formatSpanishDate($fecha),
                        'Ingresos Diarios' => evaluar($total),


                        'Cheque Promedio Hospedaje' => evaluar($PromedioHospedaje),
                        'Cheque Promedio AyB'       => evaluar($PromedioAyB),
                        'Cheque Promedio Diversos'  => evaluar($PromedioDiversos),
                        // 'Cheque Promedio'           => evaluar(0),
                        'Tarifa Efectiva Diaria'    => evaluar($tarifaEfectivaDiaria),
                         '% de Ocupacion'            => evaluar($softVentas['porcOcupacion'],'%'),
                   

                        // 'Costo de amenididad'   => '0.00',
                        // 'Costo de AyB '         => '0.00',
                        // 'Costo Diversos diario' => '0.00',

                        'opc' => 0
                    );

            else:

                $total = $softVentas['alimentos'] + $softVentas['bebidas'];

                $__row[] = array(

                    'id'                        => $idRow,
                    'fecha'                     => $fecha,
                    'dia'                       => formatSpanishDate($fecha),
                    'Ingresos Diarios'          => evaluar($softVentas['totalAyB']),
                    'Cheque Promedio alimentos' => evaluar($softVentas['promedio_alimentos']),
                    'Cheque Promedio bebidas'   => evaluar($softVentas['promedio_bebidas']),
                    'Cheque Promedio'           => evaluar($softVentas['promedio_total_ayb']),
                    
                    // 'Costo de amenididad'   => '0.00',
                    // 'Costo de AyB '         => '0.00',
                    // 'Costo Diversos diario' => '0.00',
                    'opc' => 0
                );


            endif;

            $fi->modify('+1 day');
        }


        #encapsular datos
        return [

            "row" => $__row,
            "thead" => ''
        ];


    }

    function lsPromediosDia(){

        $__row = [];

        $days = array(2 => 'Lunes', 3 => 'Martes', 4 => 'Miercoles', 5 => 'Jueves', 6 => 'Viernes', 7 => 'Sabado', 1 => 'Domingo');

        foreach ($days as $noDias => $Days):


            $lsDays = $this->getIngresosDayOfWeek([$_POST['UDN'], $_POST['Anio'], $_POST['Mes'], $noDias]);

            foreach ($lsDays as $key) {

                if($_POST['UDN']== 1):

                    $__row[] = [

                        'id' => $noDias,

                        'fecha'            => $key['fecha'],
                        'Dia de la semana' => $Days,
                        'Ingresos Totales' => evaluar($key['total']),
                        'Hospedaje'        => evaluar($key['Hospedaje']/$key['noHabitaciones']),
                        'AyB'              => evaluar($key['AyB'] / $key['noHabitaciones']),
                        'Diversos'         => evaluar($key['Diversos'] / $key['noHabitaciones']),
                        
                        'Cheq Prom.'       => '',
                        
                        'Total Prom.'      => evaluar($key['total'] / $key['noHabitaciones']),
                        '% Ocupacion'      => evaluar( $key['noHabitaciones']/12,'%'),

                        'costoamenidades'  => '0.00',
                        'costoAyB'         => '0.00',
                        'costoDiversos'    => '0.00',


                        'opc' => 0
                    ];

                else:


                    $total = $key['noHabitaciones'] != 0  ?  $key['totalGral'] /  $key['noHabitaciones'] :0; 

                    

                        $__row[] = [

                        'id' => $noDias,

                        'fecha'            => $key['fecha'],
                        'Dia de la semana' => ['text'=>$Days],
                        'Ingresos Totales' => evaluar($key['totalGral']),
                        'chequePromAlim'   => evaluar($key['promedio_alimentos']),
                        'chequePromBeb'    => evaluar($key['promedio_bebidas']),
                        'Cheq Promedio'    => evaluar($total),

                        // 'costoamenidades'      => '0.00',
                        // 'costoAyB'             => '0.00',
                        // 'costoDiversos'        => '0.00', 
                        'opc'              => 0    
                    ];
                    
                    

                endif;
            }

            $__row[] = [
                'id' => 0,
                'fecha' => '',
     
                'colgroup' => 1
            ];

        endforeach;

        #encapsular datos
        return [

            "row" => $__row,
            "thead" => $this->getColumnName()
        ];


    }


    function getColumnName(){


        switch($_POST['UDN']){

            case '1':
            return ['fecha','Día','Ingresos Diarios','Cheque Prom. Hospedaje','Cheque Prom. AyB' ,'Cheque Prom. Diversos','Cheque Prom. ',
            'Tarifa Efectiva Diaria','% de Ocupacion','Costo de amenididad','Costo de AyB ','Costo Diversos diario'
            ];

            default:

            return ['Fecha','Dia','Ingresos','Cheque promedio alimentos','Cheque promedio Bebidas','Cheque promedio'];

        }



    }


}


// Instancia del objeto

$obj = new ctrl();
$fn = $_POST['opc'];
$encode = $obj->$fn();

echo json_encode($encode);

function formatSpanishDate($fecha = null)
{
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

function evaluar($val, $sign = '$')
{

    if ($sign == '$') {

        return $val ? '$ ' . number_format($val, 2, '.', ',') : '-';
    } else if ($sign == '%') {

        return $val ? number_format($val, 2, '.', ', ') . ' %' : '-';
    } else {

        return $val ? number_format($val, 2, '.', ',') : '-';
    }

}
