<?php
if (empty($_POST['opc'])) {
    exit(0);
}

require_once ('../mdl/mdl-mercadotecnia.php');
require_once('../../conf/_Utileria.php');
require_once('../../conf/coffeSoft.php');

$encode = [];


class KPIS extends Kpismerca{

    public $util;


    public function __construct() {
        parent::__construct(); // Llama al constructor de la clase padre
        $this->util = new Utileria(); 
    }    

    function initComponents(){

        $lsUDN = $this->lsUDN();
        return [ 'udn' => $lsUDN ];

    }


    function ComparativaMensual(){
    
        # Variables
        $Mes          = $_POST['Mes'];
        $mesCompleto  = $_POST['mesCompleto'];
        $Anio         = $_POST['Anio'];
        $AnioAnterior = $Anio-1;
        $days         = listDays();
        $__row        = [];


        if($_POST['udn'] == 1){

            $consultas = array(
                'totalGeneral'   => 'totalGeneral',
                'totalHospedaje' => 'totalHospedaje',
                'totalAyB'       => 'AyB',
                'totalDiversos'  => 'totalDiversos',       
            );

        }else{

            $consultas = array(
                'totalAyB'       => 'AyB',
                'totalAlimentos' => 'total Alimentos',
                'totalBebidas'   => 'total Bebidas',
            );

        }

    
         $month = [
            'currentMonth'   => ['year'=> $Anio, 'month'=>$_POST['Mes']],
            'previousMonth'  => ['year'=> $AnioAnterior, 'month'=>$_POST['Mes']],        
         ];   

        
         
         $row   = [];
        foreach ($consultas as $key => $value) {

            $__row[] = array(

                'id'  => $key,
                'dayOfWeek'   => $value,
                'colgroup'    => true

            );


            foreach ($days as $noDias => $Days){

                // añadir columna mensual
                $meses = [];


                $campos = array(
                    'id'        => $noDias,
                    'dayOfWeek' => $Days,                 
                );
               foreach($month as $titulo => $_date): // list for years.

                
                 
                 $ingresoDiario  = $this -> ingresoPorDia([$_POST['UDN'],$_date['year'],$_date['month'], $noDias]);

                 $promedio =($ingresoDiario['totalDias'] != 0)  ? $ingresoDiario[$key] /$ingresoDiario['totalDias'] : $ingresoDiario[$key];

               
                 
                 $meses[$titulo] = [ 'text' =>  evaluar($promedio,''), 'val'  =>   $promedio];
                //   $meses[$titulo] =  $promedio;
               
                endforeach; 
               
                $meses['dif'] = evaluar($meses['currentMonth']['val'] - $meses['previousMonth']['val'],'');

                $meses['opc'] = 0;

                $__row[]       = array_merge($campos,$meses);

            

            }



       
            // $__row = $row;


        }

        // Encapsular arreglos
        return [

            'view'  => $data,
            "thead" => [ 
            
                'DIA ' , 
                $mesCompleto . ' / ' . $Anio,
                $mesCompleto . ' / ' . $AnioAnterior,
                'DIFERENCIA'
            ],

            "row" => $__row,
        ];
      
    }

    function ComparativaMensualx(){
    
        # Variables
        $Mes          = $_POST['Mes'];
        $mesCompleto  = $_POST['mesCompleto'];
        $Anio         = $_POST['Anio'];
        $AnioAnterior = $Anio-1;

        $days         = listDays();
        
        $__row = [];

        $consultas = array(

            // 'Ingresos Totales Gral.' => 'totalGeneral',
            // 'Hospedaje'              => 'totalHospedaje',
            'totalAyB'                    => 'AyB',
            // 'Diversos'               => 'totalDiversos',
        
        );


         $month = [
            'currentMonth'   => ['year'=>2024, 'month'=>$_POST['Mes']],
            'previousMonth'  => ['year'=>2023, 'month'=>$_POST['Mes']],        
             
         ];   

        

        foreach ($consultas as $key => $value) {
            $row   = [];

            $row[] = array(

                'id'  => $key,
                'a'   => $value,

                'b'   => '',
                'c'   => '',

                'opc' => 1

            );


            foreach ($days as $noDias => $Days):

                // añadir columna mensual
                $meses = [];


                $campos = array(
                    'id'        => $noDias,
                    'dayOfWeek' => $Days,                 
                );

               foreach($month as $titulo => $getFecha): // list for years.

                  $meses[$titulo] = $titulo;
                
               endforeach; 
               
                $meses['opc'] = 0;

                $row[]        = array_merge($campos,$meses);

                //  $ingresoDiario       = $this -> ingresoPorDia([$Anio, $Mes, $noDias]);
                //  $ingresoAnioAnterior = $this -> ingresoPorDia([$AnioAnterior, $Mes, $noDias]);
                 
                //  $promedio            = $ingresoDiario[$value] /$ingresoDiario['totalDias'];
                //  $promedioAnterior    = $ingresoAnioAnterior[$value] /$ingresoAnioAnterior['totalDias'];
                                 
                //  $row[]       = array(

                //     'id'            => $value, // id = 1.
                //     'dayOfWeek'     => $Days,
                //     'currentMonth'  => ['text'=>evaluar($promedio),'val'=> $promedio],
                //     'previousMonth' => ['text'=> evaluar($promedioAnterior),'val'=> $promedioAnterior],
                //     'diferencia'        =>evaluar($promedio - $promedioAnterior),
                //     'opc'           => 0
                //  );

             endforeach;



            // $res = pintarValPromedios($row,['currentMonth','previousMonth']);
            // $__row = array_merge($__row,$res);
            //  temporal.
            $__row[] = $row;


        }

        // Encapsular arreglos
        return [

            'view'  => $data,
            "thead" => [ 
            
                'DIA ' , 
                $mesCompleto . ' / ' . $Anio,
                $mesCompleto . ' / ' . $AnioAnterior,
                'DIFERENCIA'
            ],

            "row" => $__row,
        ];
      
    }

    function ComparativaMensualPromedios(){
                # Variables
        $Mes          = $_POST['Mes'];
        $mesCompleto  = $_POST['mesCompleto'];
        $Anio         = $_POST['Anio'];
        $AnioAnterior = $Anio-1;
        $__row        = [];


        $days         = listDays();

      
      
         $month = [
            'currentMonth' =>['year'=>2024, 'month'=>$_POST['Mes']],
            'previousMonth'=>['year'=>2023, 'month'=>$_POST['Mes']],        
             
         ];      
           
        $consultas = array(
       
            'Cheque Prom. Hosp'         => 'chequePromHospedaje',
            // 'Cheque Prom. AyB'          => 'chequePromedioAyB',
            // 'Total cheque Prom.'        => 'totalChequePromedio',
            // 'Tarifa efectiva acomulada' => 'tarifaEfectiva'
        );

       
        // // acceder a la base de datos. 
        //             $ingresoDiario       = $this->ingresoPorDia([$getFecha['year'], $getFecha['month'], $noDias]);  

            

        foreach ($consultas as $key => $value) {
            $row = [];

            $row[] = array('id'=> 0,'dayOfWeek'=> $key,'colgroup'=> true);

  
            foreach ($days as $noDias => $Days):
                // añadir columna mensual
                $meses = [];


                $campos = array(
                    'id'        => $noDias,
                    'dayOfWeek' => $Days,                 
                );

               
                foreach($month as $titulo => $getFecha): // list for years.
                    
                    // Consultar desplazamientos promedios.
                    $lsPromedios = $this -> lsPromediosAcomulados(['Anio' => $getFecha['year'], 'Mes' => $getFecha['month']]);

                    if($value != 'totalChequePromedio'):
                    
                        $results     = getPromedioDia(($noDias-1),$lsPromedios,$value);
                        $val         = $results;
                    
                    else:

                        $promHospedaje     = getPromedioDia(($noDias-1),$lsPromedios,'chequePromHospedaje');
                        $promAyB           = getPromedioDia(($noDias-1),$lsPromedios,'chequePromedioAyB');

                        $results = $promHospedaje + $promAyB;
                        $val     = $results;
               
                    endif;

                    //agregar row.
                    //  $meses[$titulo] = '';
                    $meses[$titulo] =   [ 'text' =>  evaluar($results,''), 'val'  =>   $val];

                endforeach;
                
                $meses['dif'] = evaluar($meses['currentMonth']['val'] - $meses['previousMonth']['val'],'');
                $meses['dif'] = 0;
                $meses['opc'] = 0;

                $row[]        = array_merge($campos,$meses);
                // $__row[]        = array_merge($campos,$meses);
                
            endforeach;   
            
            
           
            
            $res   = pintarValPromedios($row,['currentMonth','previousMonth']);
            $__row = array_merge($__row,$res);
            // $__row[] = array_merge($__row,$res);
            
        }



       



        // Encapsular arreglos
        return [
            "thead" => [

                'DIA ',
                $mesCompleto . ' / ' . $Anio,
                $mesCompleto . ' / ' . $AnioAnterior,
                'DIFERENCIA'
            ],

            "row" => $__row,
            'data'=> $lsPromedios ,
            'ok'  => $ok
        ];


    }

    function lsPromediosAcomulados($array){

    
        $udn = $_POST['UDN'];
        # -- variables para fechas
        $fi = new DateTime($array['Anio'] . '-' . $array['Mes'] . '-01');

        $hoy = clone $fi;

        $hoy->modify('last day of this month');
        $__row = [];


        while ($fi <= $hoy) {
            $idRow++;
            $fecha = $fi->format('Y-m-d');

            $softVentas = $this->getsoft_ventas([$udn,$fecha]);
            $opc        = ($softVentas['noHabitaciones']) ? 0 : 1;
            
            
            $noHabitaciones       += $softVentas['noHabitaciones'];
            $total                += $softVentas['Hospedaje'] + $softVentas['AyB'] + $softVentas['Diversos'];
            
            $hospedaje         += $softVentas['Hospedaje'];
            $PromedioHospedaje  = $hospedaje / $noHabitaciones;
            
            $AyB               += $softVentas['AyB'];
            $PromedioAyB        = $AyB / $noHabitaciones;
            
            $tarifaEfectiva     = ($idRow ==1 ) ? ($total/12) : (($total/12)/ ($idRow-1)) ;
            
            $ingresosDiversos  += $softVentas['Diversos'];
            $PromedioDiversos   = $ingresosDiversos / $noHabitaciones;


            $tarifaEfectivaDiaria  = $total / 12;
            $porcentajeOcupacion   = evaluar($noHabitaciones / 12, '%');

            $__row[] = array(

                'id'                    => $idRow,
                'fecha'                 => $fecha,
                'dia'                   => formatSpanishNoDay($fecha),
               
                'Hospedaje'              => $hospedaje,
                'chequePromHospedaje'    => $PromedioHospedaje,
                'chequePromedioAyB'      => $PromedioAyB,
                'chequePromedioDiversos' => $PromedioDiversos,
                
                'tarifaEfectiva'         => $tarifaEfectiva,

                // 'Costo de amenididad'      => '0.00',
                // 'Costo de AyB '            => '0.00',
                // 'Costo Diversos diario'    => '0.00',


             
            );

        //     // endif;

            $fi->modify('+1 day');
        }


        #encapsular datos
        return $__row;


        // return [

        //     "row" => $__row,
        //     "thead" => ''
        // ];


    }

    // Pestañas:
    function PromediosDiariosx() {

        # Declarar variables
        $__row = [];
        $mesCompleto = $_POST['mesCompleto'];
        $Anio = $_POST['Anio'];
        $AnioAnterior = $Anio - 1;

    
        // --
        $ventas         = $this->ingresosMensuales([$_POST['Anio'], $_POST['Mes']]);
        $ventasAnterior = $this->ingresosMensuales([$AnioAnterior, $_POST['Mes']]);

        $chequePromedioHospedaje = $ventas['totalHospedaje'] / $ventas['totalHabitaciones'];
        $chequePromedioAyB       = $ventas['totalAyB'] / $ventas['totalHabitaciones'];
        $chequeDiversos          = $ventas['totalDiversos'] / $ventas['totalHabitaciones'];
        $chequePromedio          = $ventas['totalGeneral'] / $ventas['totalHabitaciones'];

        $chequePromedioHospedajeAnterior = $ventasAnterior['totalHospedaje'] / $ventasAnterior['totalHabitaciones'];
        $chequePromedioAyBAnterior       = $ventasAnterior['totalAyB'] / $ventasAnterior['totalHabitaciones'];
        $chequeDiversosAnterior          = $ventasAnterior['totalDiversos'] / $ventasAnterior['totalHabitaciones'];
        $chequePromedioAnterior          = $ventasAnterior['totalGeneral'] / $ventasAnterior['totalHabitaciones'];


        $diferenciaIngreso   = ($ventas['totalGeneral'] - $ventasAnterior['totalGeneral']);
        $diferenciaHospedaje = ($ventas['totalHospedaje'] - $ventasAnterior['totalHospedaje']);
        $diferenciaAyB       = ($ventas['totalAyB'] - $ventasAnterior['totalAyB']);
        $diferenciaDiversos  = ($ventas['totalDiversos'] - $ventasAnterior['totalDiversos']);


        $porcIngreso   = $ventasAnterior['totalGeneral'] / $diferenciaIngreso;
        $porcHospedaje = $ventasAnterior['totalHospedaje'] / $diferenciaHospedaje;
        $porcAyB       = $ventasAnterior['totalAyB'] / $diferenciaAyB;
        $porcDiversos  = $ventasAnterior['totalDiversos'] / $diferenciaDiversos;


        // $porcHabitaciones = 


         $ocupacion            = evaluar($ventas['totalHabitaciones'] / 31 / 12, '%');
         $ocupacionAnterior    = evaluar($ventasAnterior['totalHabitaciones'] / 31 / 12, '%');

         $tarifaEfectiva       = ($ventas['totalGeneral'] /12)/30;
         $tarifaEfectivaAnterior       = ($ventasAnterior['totalGeneral'] /12)/30;




        $__row = [
            array(
                'id'         => 0,
                'Concepto'   => 'Suma de Ingresos',
                'ABR 24'     => evaluar($ventas['totalGeneral']),
                'ABR 23'     => evaluar($ventasAnterior['totalGeneral']),
                'Diferencia' => evaluar( $diferenciaIngreso ),
                '%'          => evaluar($porcIngreso,'%'),
                'opc'        => 0
            ),

            array(

                'id'         => 0,
                'Concepto'   => 'Ingresos de Hospedaje',
                'ABR 24'     => evaluar($ventas['totalHospedaje']),
                'ABR 23'     => evaluar($ventasAnterior['totalHospedaje']),
                'Diferencia' => evaluar($diferenciaHospedaje),
                '%'          => evaluar($porcHospedaje,'%'),
                'opc'        => 0

            ),

            array(

                'id'         => 0,
                'Concepto'   => 'Ingresos AyB',
                'ABR 24'     => evaluar($ventas['totalAyB']),
                'ABR 23'     => evaluar($ventasAnterior['totalAyB']),
                'Diferencia' => evaluar($diferenciaAyB),
                '%'          => evaluar($porcAyB),

                'opc' => 0
            ),

            array(

                'id'         => 0,
                'Concepto'   => 'Ingresos Diversos',
                'ABR 24'     => evaluar($ventas['totalDiversos']),
                'ABR 23'     => evaluar($ventasAnterior['totalDiversos']),
                'Diferencia' => evaluar($diferenciaDiversos),
                '%'          => evaluar($porcDiversos),

                'opc' => 0
            ),


            

            array(

                'id' => 0,
                'Concepto' => '',
                'colgroup' => true
            ),

            array(

                'id'         => 0,
                'Concepto'   => 'Habitaciones',
                'ABR 24'     => $ventas['totalHabitaciones'],
                'ABR 23'     => $ventasAnterior['totalHabitaciones'],
                'Diferencia' => evaluar($ventas['totalHabitaciones']- $ventasAnterior['totalHabitaciones'],''),
                '%'          => evaluar(0),

                'opc' => 0
            ),

            array(

                'id'         => 0,
                'Concepto'   => '% Ocupación',
                'ABR 24'     => $ocupacion,
                'ABR 23'     => $ocupacionAnterior,
                'Diferencia' => evaluar($ocupacion - $ocupacionAnterior,'%'),
                '%'          => evaluar(0),
                'opc'        => 0
            ),

            array(

                'id'         => 0,
                'Concepto'   => 'Tarifa Efectiva Acomulada',
                'ABR 24'     => evaluar($tarifaEfectiva),
                'ABR 23'     => evaluar($tarifaEfectiva),
                'Diferencia' => evaluar($tarifaEfectiva - $tarifaEfectivaAnterior ),
                '%'          => evaluar(0),
                'opc'        => 0
            ),

            array(
                'id' => 0,
                'Concepto' => '',
                'colgroup' => true
            ),

            array(

                'id'         => 0,
                'Concepto'   => 'Cheque Promedio',
                'ABR 24'     => evaluar($chequePromedio),
                'ABR 23'     => evaluar($chequePromedioAnterior),
                'Diferencia' => evaluar($chequePromedio - $chequePromedioAnterior),
                '%'          => evaluar(0),
                'opc'        => 0
            ),

            array(

                'id'         => 0,
                'Concepto'   => 'Cheque Promedio Hospedaje',
                
                'ABR 24'     => evaluar($chequePromedioHospedaje),
                'ABR 23'     => evaluar($chequePromedioHospedajeAnterior),
                
                'Diferencia' => evaluar($chequePromedioHospedaje - $chequePromedioHospedajeAnterior),
                '%'          => evaluar(0),
                'opc'        => 0

            ),

            array(

                'id'         => 0,
                'Concepto'   => 'Cheque Promedio AyB',
                'ABR 24'     => evaluar($chequePromedioAyB),
                'ABR 23'     => evaluar($chequePromedioAyBAnterior),
                'Diferencia' => evaluar($chequePromedioAyB - $chequePromedioAyBAnterior),
                '%'          => evaluar(0),
                'opc'        => 0
            ),

            array(

                'id'         => 0,
                'Concepto'   => 'Cheque Promedio Diversos',
                'ABR 24'     => evaluar($chequeDiversos),
                'ABR 23'     => evaluar($chequeDiversosAnterior),
                'Diferencia' => evaluar($chequeDiversos - $chequeDiversosAnterior),
                '%'          => evaluar(0),
                'opc'        => 0
            ),

            array(
                'id' => 0,
                'Concepto' => '',
                'colgroup' => true
            ),

            array(

                'id'         => 0,
                'Concepto'   => '% Costo amenidades',
                'ABR 24'     => '-',
                'ABR 23'     => '-',
                'Diferencia' => evaluar($diferencia),
                '%'          => evaluar(0),
                'opc'        => 0
            ),

            array(

                'id'         => 0,
                'Concepto'   => '% Costo AyB',
                'ABR 24'     => '-',
                'ABR 23'     => '-',
                'Diferencia' => evaluar($diferencia),
                '%'          => evaluar(0),
                'opc'        => 0
            ),

          


        ];







        #encapsular datos

        return [

            "thead" => ['Concepto',
                $mesCompleto . ' / ' . $Anio,
                $mesCompleto . ' / ' . $AnioAnterior,
              'Diferencia', '%'],
            "row" => $__row,
        ];


    }

    function PromediosDiarios(){

         # Declarar variables
        $__row        = [];
        $mesCompleto  = $_POST['mesCompleto'];
        $Anio         = $_POST['Anio'];
        $AnioAnterior = $Anio ;
        $udn          = $_POST['UDN'];

        $__row        = [];
        $days         = listDays();

    
        $month = [
            'currentMonth'  => ['year'=> $Anio, 'month'=>$_POST['Mes']],
            'previousMonth' => ['year'=> $AnioAnterior, 'month'=>$_POST['Mes']],

        ];

         
        // consultas individuales. 

        if($udn == 1):
           
            $consultas = array(
        
                'totalGeneral'      => 'Suma de ingresos',
                'totalHospedaje'    => 'ingreso de Hospedaje',
                'totalAyB'          => 'ingreso AyB',
                'totalDiversos'     => 'ingreso Diversos',
                'totalHabitaciones' => 'Habitaciones',
                
                'group'             => '',
                
                'porcAgrupacion'          => '% Ocupacion',
                'tarifaEfectiva'          => 'Tarifa efectiva acumulada',
            
                'chequePromedio'          => 'Cheque Promedio',
                'chequePromedioHospedaje' => 'chequePromedioHospedaje',
                'chequePromedioAyB'       => 'cheque Promedio AyB',
                'chequePromedioDiversos'  => 'cheque Promedio Diversos',

            );
        else:
              $consultas = array(

                  
                  'totalHabitaciones' => 'Clientes',
                //   'totalAyB'          => 'Ventas AyB',
                  'totalGralAyB'          => 'Ventas AyB',
                  'totalAlimentos'    => 'Ventas alimentos',
                  'totalBebidas'      => 'Ventas bebidas',
                  'group'             => '',

                  'chequePromedioAyB'       => 'Cheque Promedio AyB',
                  'chequePromedioAlimentos' => 'Cheque Promedio Alimentos',
                  'chequePromedioBebidas'   => 'Cheque Promedio Bebidas',
                  'group'                   => '',

                  'CostoA' => '% Costo Alimento',
                  'CostoB' => '% Costo Bebidas',


  

              );
        endif;



     
        foreach ($consultas as $key => $titulo) {

            $row   = [];
            $meses = [];


            if($key != 'group'):

                $initialData = array( 'id'  => $key, 'concepto'  => $titulo );



                foreach($month as $_key => $fecha): // list for years.
                
                    $total_days = cal_days_in_month(CAL_GREGORIAN, $fecha['month'], $fecha['year']);
                    $ventas     = $this->ingresosMensuales([$udn,$fecha['year'], $fecha['month']]);
                    
                    //add info 
                    $value        = $this -> getCalculoPorConcepto($key, $ventas, $total_days);
                    $meses[$_key] = ['val'=>$value,'text'=> evaluar($value,'')];
                
                endforeach;    


                $meses['dif'] = evaluar($meses['currentMonth']['val'] - $meses['previousMonth']['val'],'');
                $meses['opc'] = 0;

                // combinas columnas.
                $row[]   = array_merge($initialData,$meses); 
                $__row   = array_merge($__row,$row); 

            else:
            
                $__row[]  =  array( 'id' => 0,'Concepto' => '', 'colgroup' => true);
        
            endif; 



        
        }   
        
        

        // Encapsular arreglos
        return [
            "thead" => [

                'DIA ',
                $mesCompleto . ' / ' . $Anio,
                $mesCompleto . ' / ' . $AnioAnterior,
                'DIFERENCIA'
            ],

            "row" => $__row,
            'data'=> $lsPromedios ,
            'ok'  => $ok
        ];



    }


    function getCalculoPorConcepto($key, $ventas, $days) {

        $chequePromedioHospedaje = ($ventas['totalHabitaciones'] != 0) ? $ventas['totalHospedaje'] / $ventas['totalHabitaciones'] : 0;
        $chequePromedioAyB       = ($ventas['totalHabitaciones'] != 0) ? $ventas['totalAyB'] / $ventas['totalHabitaciones'] : 0;
        $chequeDiversos          = ($ventas['totalHabitaciones'] != 0) ? $ventas['totalDiversos'] / $ventas['totalHabitaciones'] : 0;
        $chequePromedio          = ($ventas['totalHabitaciones'] != 0) ? $ventas['totalGeneral'] / $ventas['totalHabitaciones'] : 0;

        switch ($key) {

            case 'porcAgrupacion':
                return ($days != 0) ? ($ventas['totalHabitaciones'] / $days / 12) * 100 : 0;

            case 'chequeDiversos':  
                return $chequeDiversos;  
            
            case 'chequePromedioHospedaje':
                return $chequePromedioHospedaje;

            case 'chequePromedioAyB':  
                return $chequePromedioAyB;

            case 'chequePromedioAlimentos':
                return ($ventas['totalHabitaciones'] != 0) ? $ventas['totalAlimentos'] / $ventas['totalHabitaciones'] : 0;
            
            case 'chequePromedioBebidas': 
                return ($ventas['totalHabitaciones'] != 0) ? $ventas['totalBebidas'] / $ventas['totalHabitaciones'] : 0;  

            case 'chequePromedio':
                return $chequePromedio;
            
            case 'tarifaEfectiva':
                return ($days != 0) ? ($ventas['totalGeneral'] / 12) / $days : 0;

            default:
                return isset($ventas[$key]) ? $ventas[$key] : null;
        }
    }


   



}



// Instancia del objeto

$obj    = new KPIS();
$fn     = $_POST['opc'];
$encode = $obj->$fn();


// Print JSON :
echo json_encode($encode);
Utilerias:


function getPromedioDia($numeroDia,$data,$valor) {


    $filteredData = array_filter($data, function($item) use ($numeroDia) {
        // Convertir la fecha al formato de marca de tiempo
        $marcaTiempo = strtotime($item['fecha']);
        // Obtener el número del día de la semana (0 para domingo, 6 para sábado)
        $diaSemana = date('w', $marcaTiempo);
        // Comparar con el número del día deseado
        return $diaSemana == $numeroDia;
    });

   // Obtener solo los promedios de los datos filtrados
    $promedios = array_map(function($item) use ($valor) {
        // if ($valor == 'chequePromedioAyB') {
            return $item[$valor];
        // } else {
        //     return $item['chequePromHospedaje'];
        // }
    }, $filteredData);


    $dimension = count($promedios);

    // Sumar los promedios filtrados
    $sumaPromedios = array_sum($promedios)/$dimension;

    // return $promedios;

    return $sumaPromedios;
}





function pintarValPromedios($row, $campos){
    foreach ($campos as $campo) {
        // Pintar valores promedios.
        $currents = array_column($row, $campo);
        $currentsVals = array_column($currents, 'val');

        $maxCurrentMonth = max($currentsVals);
        $minCurrentMonth = min($currentsVals);
        $promCurrentMonth = array_sum($currentsVals) / count($currentsVals);

        $data[$campo] = [
            'max' => $maxCurrentMonth,
            'min' => $minCurrentMonth,
            'prom' => $promCurrentMonth
        ];

        // Encontrar los dos números menores al mayor
        sort($currentsVals);
        $secondMax = $currentsVals[count($currentsVals) - 2];
        $thirdMax = $currentsVals[count($currentsVals) - 3];

        $data['second_max'] = $secondMax;
        $data['third_max'] = $thirdMax;

        $degrado = 7;

        foreach ($row as &$rows) {

            if ($rows[$campo]['val'] == $maxCurrentMonth) {
                $rows[$campo]['class'] = 'bg-primary8 text-end';  // Indicar el mayor
            } elseif ($rows[$campo]['val'] == $secondMax) {
                $rows[$campo]['class'] = 'bg-primary6 text-end';  // Indicar el segundo mayor
            } elseif ($rows[$campo]['val'] == $thirdMax) {
                $rows[$campo]['class'] = 'bg-primary4 text-end';  // Indicar el tercer mayor
            }

        }

    }

    return $row;
}

function pintarValoresPromedios($row, $campo){

    // Pintar valores promedios.
    $currents     = array_column($row, $campo);
    $currentsVals = array_column($currents, 'val');

    $maxCurrentMonth  = max($currentsVals);
    $minCurrentMonth  = min($currentsVals);
    $promCurrentMonth = array_sum($currentsVals) / count($currentsVals);

    $data = [
        'max' => $maxCurrentMonth,
        'min' => $minCurrentMonth,
        'prom' => $promCurrentMonth
    ];

    // Encontrar los dos números menores al mayor
    sort($currentsVals);
    $secondMax = $currentsVals[count($currentsVals) - 2];
    $thirdMax = $currentsVals[count($currentsVals) - 3];

    $data['second_max'] = $secondMax;
    $data['third_max'] = $thirdMax;

    $degrado = 7;

    foreach ($row as &$rows) {

        if ($rows[$campo]['val'] == $maxCurrentMonth) {
            $rows[$campo]['class'] = 'bg-primary8 text-end';  // Indicar el mayor
        } elseif ($rows[$campo]['val'] == $secondMax) {
            $rows[$campo]['class'] = 'bg-primary6 text-end';  // Indicar el segundo mayor
        } elseif ($rows[$campo]['val'] == $thirdMax) {
            $rows[$campo]['class'] = 'bg-primary4 text-end';  // Indicar el tercer mayor
        }
    
    }

    return [ 
    
        'row'=> $row, 
        'data' => $data
    
    ];
}


