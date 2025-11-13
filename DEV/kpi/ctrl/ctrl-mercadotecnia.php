
<?php

if(empty($_POST['opc'])) exit(0);

//

require_once ('../mdl/mdl-mercadotecnia.php');
$obj = new Mercadotecnia;

// require_once('../../../conf/_Utileria.php');
// $util = new Utileria;


// Variables globales :
$encode = [];
switch ($_POST['opc']) {
    
    case 'initComponents':
        $udn     = $obj->lsUDN();
        
        $encode    = [
            "udn"     => $udn
        ];
    break;

    case 'lsKPI':
        $encode = lsVentas($obj);
    break;
}

function rowGroup($data){

    $row = [];
    $col = [];

    $bgGroup = 'bg-alert  fw-bold';
    $style   = 'font-size:1.2rem;';


    for ($i = 1; $i <= $data['mes']; $i++) {
        $arg = getMes($i) . '-' . $data['anio'];

        $col[$arg] = ['class' => $bgGroup, 'style' => $style];
    }


    // columnas iniciales:
    $row = array(
        'id' => '0',
        'Area' => ['html' => $data['titulo'], 'class' => $bgGroup, 'style' => $style],
    );

    $col['opc'] = 0;

    // Unir columnas :
    return array_merge($row, $col);
}

function lsChequePromedio($obj){

    $__row = [];
    $mes   = $_POST['Mes'];
    $anio  = 2024;

    /*   --- [ CLIENTES ]  --- */

    $__row = rowGroup([
        'mes'    => $mes,
        'anio'   => $anio,
        'titulo' => 'CLIENTES'
    ]);


   return $__row;

}

function lsVentas($obj){
    $__row = [];

    $mes   = $_POST['Mes'];
    $anio  = 2024; 

    
    $lsOrdenKPI = $obj->lsTablero();


//    $__row[]     = rowGroup($obj);


 /*   --- [ CLIENTES ]  --- */

    $__row[] = rowGroup([
        'mes'    => $mes,
        'anio'   => $anio,
        'titulo' => 'CLIENTES'
    ]);


    foreach ($lsOrdenKPI as $kpi) {


            // Lista de categorias por venta:
        $list = $obj->lsVentas([$_POST['UDN'], $kpi['idTablero']]);

            foreach ($list as $key) {
                $row = [];
                $col = [];

                   // $totalVentas  = 0;

                for ($i = 1; $i <= $mes; $i++) { // obtener ventas por mes

                    $totalClientes           = $obj->lsClientes([$i, $anio, $key['id']]);
                    
                    $col[getMes($i) . '-' . $anio] = evaluar2($totalClientes);

                }

                $row = array(

                    'id'     => $key['id'],
                    'ventas' => $key['arearestaurant'],
                    
                    
                
                );

                $col['opc']  = 0; 

                // Unir columnas :
                $__row[] = array_merge($row,$col);

            
        
            }



             // columnas iniciales:
        $row = array(
            'id'   => $kpi['idTablero'],
            'Area' => "%",     
        );

        for ($i = 1; $i <= $mes; $i++) {
            $arg = getMes($i) . '-' . $anio;

            $col[$arg] = '';
        }


        $col['opc'] = 1;

  
        // Unir columnas :
        // if ($kpi['idTablero'] == 2)

        $__row[] = array_merge($row, $col);


    }

















    /*   --- [ VENTAS ]  --- */

    $__row[] = rowGroup([
        'mes'    => $mes,
        'anio'   => $anio,
        'titulo' => 'VENTAS'
    ]);

  
    foreach ($lsOrdenKPI as $kpi) {

        $totalMes         = [];
        $totalMesAnterior = [];

        // Lista de categorias por venta:
        $list = $obj->lsVentas([$_POST['UDN'], $kpi['idTablero']]);


        foreach ($list as $key) {
            $row = [];
            $col = [];

            // $totalVentas  = 0;

            for ($i = 1; $i <= $mes; $i++) { // obtener ventas por mes

                $totalVentas           = $obj->lsTotalVentas([$i, $anio, $key['id']]);
                $totalVentasAnteriores = ventasMensuales($obj,$i);
                
                $col[getMes($i) . '-' . $anio] = evaluar($totalVentas);


                $totalMes[getMes($i).'-'. $anio]         += $totalVentas;
                $totalMesAnterior[getMes($i).'-'. $anio] += $totalVentasAnteriores;

            }

            $row = array(
                'id'     => $key['id'],
                'ventas' => $key['arearestaurant'],                
            );

            $col['opc']  = 0; 

            // Unir columnas :
            $__row[] = array_merge($row,$col);


    
        }


        //  -- Total por KPI -- :

        $row = [];
        $col = [];

        $row2 =[];
        $col2 =[];


        for ($i = 1; $i <= $mes; $i++) {

            $arg           = getMes($i) . '-' . $anio;
            $total         = $totalMes[$arg];
            $totalAnterior = $totalMesAnterior[$arg];
            
            $porcentaje = ($total / $totalAnterior) -1;
            if($total == 0) $porcentaje = 0;

            $col[$arg] =  [
                'html'  => evaluar($porcentaje,1),
                'class' => 'text-end fw-bold bg-success-light',
                'style' => 'font-size:1rem;'
            ];


            $col2[$arg] = evaluar($totalMes[$arg]);
        }


        // columnas iniciales:
        $row = array(
            'id'   => $kpi['idTablero'],
            'Area' => "%",     
        );

        $row2 = array(
            'id' => $kpi['idTablero'],
            'Area' => "TOTAL DE SERVICIOS",
        );

        $col['opc'] = 1;

        $col2['opc'] = 2;

        // Unir columnas :
        if ($kpi['idTablero'] == 2)
        $__row[] = array_merge($row2, $col2);

        $__row[] = array_merge($row, $col);

     
        

    }


    // Total ventas :

    $row = [];
    $col = [];


    for ($i = 1; $i <= $mes; $i++) {
        $arg = getMes($i) . '-' . $anio;
    
        $col[$arg] = '';
    }


    // columnas iniciales:
    
    $row = array(
        'id'   => $kpi['idTablero'],
        'Area' => "TOTAL DE VENTAS",
    );

    $col['opc'] = 2;


    

    // Unir columnas :
    $__row[] = array_merge($row, $col);


   


























      
        // for($i=1; $i <= $mes; $i++){

        //     $totalVentas = $obj->lsClientes([$i, $anio, $key['id']]);
            
        //     // Calcular promedio si la categoria es comedor                
        //     $col[getMes($i).'-'. $anio] = $totalVentas;

        // }
      
              
   
        



    //     //--   SE AGREGA UNA FILA DE PORCENTAJE  DESPUES DEL ÁREA COMEDOR -- 

    //     if ($key['arearestaurant'] == 'COMEDOR'):

    //         $col = [];    

    //         $row = array(
    //             'id'           => 'Ventas',
    //             'Ventas'       => ['html' => '%', 'class' =>  'bg-success-light fw-bold'],
    //         );

    //         for($i=1; $i <= $mes; $i++){

    //             $totalVentas = $obj->lsClientes([$i, $anio, $key['id']]);

    //             $porcentajeVenta = $totalVentas/1240;

    //             $col[getMes($i) . '-' . $anio]  = ['html'=> evaluar($porcentajeVenta,1), 'class'=> 'bg-success-light text-end '];
    //         }

    //         $col['opc'] = 0;
    //         $__row[]    = array_merge($row, $col);
    //     endif;



    // }






     
    // Ventas:
    // $bg_ventas = 'bg-primary';

    // $__row[] = array(
    //     'id'           => 'Ventas',
    //     'Ventas'       => ['html'=>'VENTAS','class'=> $bg_ventas.' fw-bold'],
    //     'ENERO-2024'   => ['class'=> $bg_ventas],
    //     'FEBRERO-2024' => ['class'=> $bg_ventas],
    //     'MARZO-2024'   => ['class'=> $bg_ventas],
    //     'ABRIL-2024'   => ['class'=> $bg_ventas],

    //     'opc'=>1
    // );

    // foreach ($list as $key):

    //     $row = [];
    //     $col = [];

    //     for ($i = 1; $i <= $mes; $i++) {


    //         $totalVentas = $obj->lsTotalVentas([$i, $anio, $key['id']]);
    //         $col[getMes($i) . '-' . $anio] = evaluar($totalVentas);

    //     }

    //     $col['opc'] = 0;


    //     $row = array(
    //         'id' => $key['id'],
    //         'ventas' => $key['arearestaurant'],
    //     );

    //     // Unir columnas :

    //     $__row[] = array_merge($row, $col);


    // endforeach;    


    // encapsular datos:
    return [
        "thead" => '',
        "row" => $__row,
    ];

   
}


function getMes($numeroMes){
    $meses = [
        1 => "Enero",
        2 => "Febrero",
        3 => "Marzo",
        4 => "Abril",
        5 => "Mayo",
        6 => "Junio",
        7 => "Julio",
        8 => "Agosto",
        9 => "Septiembre",
        10 => "Octubre",
        11 => "Noviembre",
        12 => "Diciembre"
    ];

    if (isset($meses[$numeroMes])) {
        return $meses[$numeroMes];
    } else {
        return "Número de mes inválido";
    }
}


function ventasMensuales($obj,$mes){
    $anio  = 2024-1; 

      # -- variables para fechas
    $fi = new DateTime($anio.'-'. $mes. '-01');
    
    $ff = clone $fi;


    $txt = '';
    // Modificar la fecha clonada para obtener el último día del mes
    $ff->modify('last day of this month');
    $idRow = 0;
    $total = 0;

     while ($fi <= $ff) {
    //     $idRow++;
        
        $fecha   = $fi->format('Y-m-d');
        $lsFolio = $obj->existe_folio([$fecha, $_POST['UDN']]);

        

         foreach ($lsFolio as $key) {
            $total +=   $obj->getVentas([$key['id_folio']]);
             
            // $txt .= $key['id_folio'].':'.$total.' /';

             
    //     //      // Calcular suma de las ventas del dia:
  
        }







     
      $fi->modify('+1 day');
     }
     

     return $total;


}


echo json_encode($encode);

// Complementos :
function evaluar($val,$opc = 0){
    $simbolo = '$ ';

    if($opc == 1)
    $simbolo = ' %';
    
    if($opc ==0){

        return $val ? $simbolo . number_format($val, 2, '.', ',') : '-';
    }else{
        return $val ?   number_format($val, 2, '.', ',').$simbolo : '-';

    }
}


function evaluar2($val,$opc = 0){
        return $val ?  number_format($val, 2, '.', ',') : '-';


}
