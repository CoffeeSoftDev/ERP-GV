<?php
if (empty($_POST['opc']))
    exit(0);

// incluir tu modelo
require_once ('../mdl/mdl-mercadotecnia.php');
require_once ('../../conf/_Utileria.php');

// sustituir 'mdl' extends de acuerdo al nombre que tiene el modelo
class ctrl extends Kpismerca
{
    public $util;

    public function __construct() {
        parent::__construct(); // Llama al constructor de la clase padre
        $this->util = new Utileria();
    }


    function lsPromediosAcomulados(){
        $udn = $_POST['UDN'];
        # -- variables para fechas
        $fi = new DateTime($_POST['Anio'] . '-' . $_POST['Mes'] . '-01');

        $hoy = clone $fi;

        $hoy->modify('last day of this month');
        $__row = [];


        while ($fi <= $hoy) {
            $idRow++;
            $fecha = $fi->format('Y-m-d');

            $softVentas = $this->getsoft_ventas([$udn,$fecha]);
            $opc        = ($softVentas['noHabitaciones']) ? 0 : 1;
            
            
            $noHabitaciones    += $softVentas['noHabitaciones'];
       
            
        //     $PromedioDiversos = $softVentas['Diversos'] / $noHabitaciones;

        //     $tarifaEfectivaDiaria = $total / 12;
        //     $porcentajeOcupacion = evaluar($noHabitaciones / 12, '%');


           if($udn == 1):

                $total             += $softVentas['Hospedaje'] + $softVentas['AyB'] + $softVentas['Diversos'];
                $hospedaje         += $softVentas['Hospedaje'];
                $PromedioHospedaje  = $hospedaje / $noHabitaciones;
                $AyB               += $softVentas['AyB'];
                $PromedioAyB        = $AyB / $noHabitaciones;
                $tarifaEfectiva     = ($idRow ==1 ) ? evaluar($total/12) : evaluar(($total/12)/ ($idRow-1)) ;
                $ingresosDiversos  += $softVentas['Diversos'];
                $PromedioDiversos   = $ingresosDiversos / $noHabitaciones;


                $__row[] = array(

                    'id'                    => $idRow,
                    'fecha'                 => $fecha,
                    'dia'                   => formatSpanishDate($fecha),
                    'Habitaciones'          => $noHabitaciones,
                    'Suma de ingresos'      => $total,
                    'Hospedaje'             => $hospedaje,
                    'chequePromHospedaje'   => ['text'=>evaluar($PromedioHospedaje),'value'=>$PromedioHospedaje],

                    'Tarifa efectiva acum.' => $tarifaEfectiva ,

                    'Ingreso AyB'              => evaluar($AyB),
                    'Cheque Promedio AyB'      => evaluar($PromedioAyB),
                    'Ingreso Diversos'         => evaluar($ingresosDiversos),
                    'Cheque Promedio Diversos' => evaluar($PromedioDiversos),
                    // 'Costo de amenididad'      => '0.00',
                    // 'Costo de AyB '            => '0.00',
                    // 'Costo Diversos diario'    => '0.00',


                    'opc' => $opc
                );

            else:
                  // Calculo.
                  $total           += $softVentas['totalAyB'];
                  $ventasAlimentos += $softVentas['alimentos'];
                  $ventasBebidas   += $softVentas['bebidas'];




                  $__row[] = array(

                    'id'                    => $idRow,
                    'fecha'                 => $fecha,
                    'dia'                   => formatSpanishDate($fecha),
                    'Clientes'              => $noHabitaciones,

                    'Ventas AyB'            => evaluar($total),
                    'Ventas Alimentos'      => evaluar($ventasAlimentos),
                    'Cheque Prom Alimentos' => evaluar(0),

                    'Ventas Bebidas'      => evaluar($ventasBebidas),
                    'Cheque Prom Bebidas' => '',


                  
                    // 'Costo de amenididad'      => '0.00',
                    // 'Costo de AyB '            => '0.00',
                    // 'Costo Diversos diario'    => '0.00',


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

    function lsPromediosDia() {

        $__row = [];

        $days = array(2 => 'Lunes', 3 => 'Martes', 4 => 'Miercoles', 5 => 'Jueves', 6 => 'Viernes', 7 => 'Sabado', 1 => 'Domingo');

        foreach ($days as $noDias => $Days):


            $lsDays = $this->getIngresosDayOfWeek([$_POST['UDN'], $_POST['Anio'], $_POST['Mes'], $noDias]);

            foreach ($lsDays as $key) {
                $__row[] = [

                    'id' => $noDias,

                    'fecha' => $key['fecha'],
                    'Dia de la semana' => $Days,
                    'Ingresos Totales' => evaluar($key['total']),


                    'Hospedaje' => evaluar($key['Hospedaje'] / $key['noHabitaciones']),
                    'AyB' => evaluar($key['AyB'] / $key['noHabitaciones']),
                    'Diversos' => evaluar($key['Diversos'] / $key['noHabitaciones']),
                    'Cheq Prom.' => '',
                    'Total Prom.' => evaluar($key['total'] / $key['noHabitaciones']),
                    '% Ocupacion' => evaluar($key['noHabitaciones'] / 12, '%'),
                    'costoamenidades' => '0.00',
                    'costoAyB' => '0.00',
                    'costoDiversos' => '0.00',


                    'opc' => 0
                ];
            }

            $__row[] = [
                'id' => 'x',
                'fecha' => '',

                'colgroup' => 1
            ];

        endforeach;

        #encapsular datos
        return [

            "row" => $__row,
            "thead" => [
                'fecha',
                'Día',
                'Ingresos Diarios',
                'Cheque Prom. Hospedaje',
                'Cheque Prom. AyB'
                ,
                'Cheque Prom. Diversos',
                'Cheque Prom. ',
                'Tarifa Efectiva Diaria',
                '% de Ocupacion',
                'Costo de amenididad',
                'Costo de AyB ',
                'Costo Diversos diario'
            ]
        ];


    }


}


// Instancia del objeto

$obj    = new ctrl();
$fn     = $_POST['opc'];
$encode = $obj->$fn();

echo json_encode($encode);

function evaluar($val, $sign = '$'){

    if ($sign == '$') {

        return $val ? '$ ' . number_format($val, 2, '.', ',') : '-';
    } else if ($sign == '%') {

        return $val ? number_format($val, 2, '.', ', ') . ' %' : '-';
    } else {

        return $val ? number_format($val, 2, '.', ',') : '-';
    }

}


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