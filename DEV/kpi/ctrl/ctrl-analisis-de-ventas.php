<?php
if(empty($_POST['opc'])) exit(0);

require_once('../mdl/mdl-analisis-de-ventas.php');
$obj = new Analisisdeventas;

require_once('../../conf/_Utileria.php');
$util = new Utileria;

$encode = [];
switch ($_POST['opc']) {

case 'listYears':   // Años disponibles 
        $years  = $obj->lsYears();
        $encode = array_map(function ($row) {
            return [
                "id"    => $row['years'],
                "valor" => $row['years'],
            ];
        }, $years);
break;

case 'tbVentas': 
        $dates1 = explode(",",$_POST['dates']);
        $year1  = date('Y',strtotime($dates1[1]));
        $dates2 = opposite_dates($dates1,$_POST['year']);
        $year2  = date('Y',strtotime($dates2[1]));
        
        if ($_POST['year'] == 0) {
            $dates2 = explode(',',$_POST['dates2']);
            $year2  = 'Fecha 1';
            $year1  = 'Fecha 2';
        } 

        $thead = [
            '<span class="d-block d-sm-none">UDN</span><span class="d-none d-sm-block">UNIDADES DE NEGOCIO</span>',
            '<span class="d-block d-sm-none"><i class="icon-calendar"></i></span><span class="d-none d-sm-block">REGISTROS</span>',
            "{$year2}",
            "{$year1}",
            'DIFERENCIA',
            '<span class="d-block d-sm-none"><i class="icon-percent"></i></span><span class="d-none d-sm-block">CRECIMIENTO</span>'
        ];
        
        $sumaTotal       = 0;
        $sumaTotalComp   = 0;
        $difTotal        = 0;
        $porcentajeTotal = 0;
        $lsUDN           = [];
        
        $array_udn = [];
        $sqlUDN    = $obj->lsUDN();
        foreach($sqlUDN as $udn) {
            $idE                 = $udn['idUDN'];
            $nameUDN             = $udn['UDN'];
            $abreviatura         = $udn['Abreviatura'];
            $ultimaFecha         = $obj->ultimoFechaIngreso([$idE]);
            $sumaTotal_UDN       = 0;
            $sumaTotalComp_UDN   = 0; //Comp = Comparativa
            $difTotal_UDN        = 0;
            $porcentajeTotal_UDN = 0;

              //Sumatoria especial Frances, Bizcocho,Bocadillos, Pastelería
            $sumaTotal_FBP       = 0;
            $sumaTotalComp_FBP   = 0;
            $difTotal_FBP        = 0;
            $porcentajeTotal_FBP = 0;

            $array_cuentas = [];
            $sqlCuentas    = $obj->cuentasVenta([$idE]);
            foreach ($sqlCuentas as $venta) {
                $id     = $venta['idUV'];
                $cuenta = ($id == 15) ? 'Pastelería' : $venta['venta'];
                if ( $id != 32 && $id != 7 && $id != 83 ) { //Evitamos la sumatoria de pastelería premium, bocadillos [FZ] y Otros ingresos [QT]
                    //Se obtiene la sumatoria de las ventas por cada cuenta de la udn
                    $sumaActual      = $obj->sumaVentas([$idE,$id,$dates1[0],$dates1[1]]);
                    $sumaComparativa = $obj->sumaVentas([$idE,$id,$dates2[0],$dates2[1]]);

                    // Sumatoria de casos especiales de Fogaza y Quinta Tabachines
                    if ( $idE == 6 || $idE == 1 ) {
                        $sumaAdicional    = sumatoria_casos_especiales($obj,$idE,$id,$dates1,$dates2);
                        $lsUDN[]          = $sumaAdicional;
                        $sumaActual      += $sumaAdicional[0];
                        $sumaComparativa += $sumaAdicional[1];
                    }
                    
                    $sumaTotal_UDN        += $sumaActual;
                    $sumaTotalComp_UDN    += $sumaComparativa;
                    $diferencia            = $sumaActual - $sumaComparativa;
                    $porcentajeDiferencia  = ( $sumaComparativa > 0 ) ? (($diferencia / $sumaComparativa) * 100) : 100;
                    $text                  = ($diferencia < 0) ? 'text-danger' : '';
                        
                    if ( $idE == 6  && ( $id == 13 || $id == 14 || $id == 15 ) ) {
                        // 13 => Frances
                        // 14 => Bizcocho
                        // 15 => Pastelería Normal
                        $sumaTotal_FBP     += $sumaActual;
                        $sumaTotalComp_FBP += $sumaComparativa;
                    }

                    if ( $id != 83 ) {
                        $array_cuentas[] = 
                        [
                            ['html' => $cuenta],
                            ['html' => ''],
                            ['html' => $util->format_number($sumaComparativa), 'class' => 'text-end'],
                            ['html' => $util->format_number($sumaActual), 'class' => 'text-end'],
                            ['html' => "<span class='{$text}'>".$util->format_number($diferencia).'</span>', 'class' => 'text-end'],
                            ['html' => "<span class='{$text}'>".$util->format_number($porcentajeDiferencia,'%').'</span>', 'class' => 'text-end'],
                            ['tr'=>['class'=>"cuenta{$idE} hide"]]
                        ];
                    }
                }
            }
            
            $sumaTotal           += $sumaTotal_UDN;
            $sumaTotalComp       += $sumaTotalComp_UDN;
            $difTotal_UDN         = $sumaTotal_UDN - $sumaTotalComp_UDN;
            $porcentajeTotal_UDN  = ( $sumaTotalComp_UDN > 0 ) ? (( $difTotal_UDN / $sumaTotalComp_UDN ) * 100) : 100;
            $text                 = ($difTotal_UDN < 0) ? 'text-danger fw-bold' : '';
            $icon                 = "<i class='icon-right-dir iconUDN{$idE}'></i>";
            $array_udn[]          = 
            [
                ['html' => '<span class="d-block d-sm-none fw-bold">'.$abreviatura.'</span><span class="d-none d-sm-block fw-bold">'.$icon.$nameUDN.'</span>', 'class'=>'pointer','onclick'=>"toggleCuenta({$idE})"],
                ['html' => '<span class="fw-bold"><i class="d-none d-sm-inline icon-right-hand"></i>'.$ultimaFecha['fecha'].'</span>', 'class'=>'text-center pointer',"onclick"=>"input_date_range('{$ultimaFecha['date']}')"],
                ['html' => $util->format_number($sumaTotalComp_UDN),'class' => 'text-end'],
                ['html' => $util->format_number($sumaTotal_UDN),'class' => 'text-end'],
                ['html' => $util->format_number($difTotal_UDN),'class' => "text-end {$text}"],
                ['html' => $util->format_number($porcentajeTotal_UDN,'%'),'class' => "text-end {$text}"],
            ];

            if ( $idE == 6 ) {
                $diferencia           = $sumaTotal_FBP - $sumaTotalComp_FBP;
                $porcentajeDiferencia = ( $sumaTotalComp_FBP > 0 ) ? (( $diferencia / $sumaTotalComp_FBP ) * 100) : 100;
                $text                 = ($diferencia < 0) ? 'text-danger fw-bold' : '';

                $array_udn[] = 
                [
                    ['html' => 'Sumatoría Frances, Bizcocho y Pastelería.', 'class'=>'pointer'],
                    ['html' => ''],
                    ['html' => $util->format_number($sumaTotalComp_FBP),'class' => 'text-end'],
                    ['html' => $util->format_number($sumaTotal_FBP),'class' => 'text-end'],
                    ['html' => $util->format_number($diferencia),'class' => 'text-end'],
                    ['html' => $util->format_number($porcentajeDiferencia,'%'),'class' => 'text-end'],
                    ['tr'=>['class'=>"cuenta{$idE} hide"]]
                ];
            }

            $array_udn = array_merge($array_udn,$array_cuentas);
        }
        
        
        $difTotal        = $sumaTotal - $sumaTotalComp;
        $porcentajeTotal = ( $sumaTotalComp > 0 ) ? (( $difTotal / $sumaTotalComp ) * 100 ) : 100;
        $text            = ($difTotal < 0) ? 'text-danger' : '';

        $array_udn[]     = 
        [
            ['html' => 'TOTAL', 'class'=> 'fw-bold' ],
            ['html' => '<span class="d-block d-sm-none">'.$util->format_number($sumaTotal).'</span>', 'class'=>'text-end fw-bold'],
            ['html' => $util->format_number($sumaTotalComp),'class' => 'text-end fw-bold'],
            ['html' => $util->format_number($sumaTotal),'class' => 'text-end fw-bold'],
            ['html' => $util->format_number($difTotal),'class' => "text-end fw-bold {$text}"],
            ['html' => $util->format_number($porcentajeTotal,'%'),'class' => "text-end fw-bold {$text}"],
        ];


        $tbody = $array_udn;

        $encode = ['table'=>['id'=>'tbIngresos'],'thead'=>$thead,'tbody'=>$tbody,0=>$lsUDN];
    break;
}
echo json_encode($encode);

function sumatoria_casos_especiales($obj,$idE,$idUV,$dates1,$dates2){
    $sumaActual = 0;
    $sumaComparativa = 0;
    switch ($idUV) {
        case 6://DIVERSOS DE TABACHINES #6 / OTROS INGRESOS DE TABACHINES #7
                $otrosIngresosQT = 7;
                $sumaActual      = $obj->sumaVentas([$idE,$otrosIngresosQT,$dates1[0],$dates1[1]]);
                $sumaComparativa = $obj->sumaVentas([$idE,$otrosIngresosQT,$dates2[0],$dates2[1]]);
            break;
        case 13://FRANCES DE FOGAZA #13 / FRANCHES DE CHACOYS #33
                $idE             = 10;
                $francesChacoys  = 33;
                $sumaActual      = $obj->sumaVentas([$idE,$francesChacoys,$dates1[0],$dates1[1]]);
                $sumaComparativa = $obj->sumaVentas([$idE,$francesChacoys,$dates2[0],$dates2[1]]);
            break;
        case 14://BIZCOCHO DE FOGAZA #14 / BIZCOCHO DE CHACOYS #34 / BOCADILLOS DE FOGAZA #83
                $idE             = 10;
                $bizcochoChacoys = 34;
                $sumaActual      = $obj->sumaVentas([$idE,$bizcochoChacoys,$dates1[0],$dates1[1]]);
                $sumaComparativa = $obj->sumaVentas([$idE,$bizcochoChacoys,$dates2[0],$dates2[1]]);
                
                $idE              = 6;
                $bocadillos       = 83;
                $sumaActual      += $obj->sumaVentas([$idE,$bocadillos,$dates1[0],$dates1[1]]);
                $sumaComparativa += $obj->sumaVentas([$idE,$bocadillos,$dates2[0],$dates2[1]]);
            break;
        case 15://PASTELERÍA DE FOGAZA #15 / PASTELERÍA DE CHACOYS #35 / PASTELERÍA PREMIUM FOGAZA #32
                $idE               = 10;
                $pasteleriaChacoys = 35;
                $sumaActual        = $obj->sumaVentas([$idE,$pasteleriaChacoys,$dates1[0],$dates1[1]]);
                $sumaComparativa   = $obj->sumaVentas([$idE,$pasteleriaChacoys,$dates2[0],$dates2[1]]);

                $idE                = 6;
                $pasteleriaPremium  = 32;
                $sumaActual        += $obj->sumaVentas([$idE,$pasteleriaPremium,$dates1[0],$dates1[1]]);
                $sumaComparativa   += $obj->sumaVentas([$idE,$pasteleriaPremium,$dates2[0],$dates2[1]]);
            break;
        case 17://RESFRESCOS DE FOGAZA #17 / REFRESCOS DE CHACOYS #36
                $idE = 10;
                $pasteleriaChacoys = 36;
                $sumaActual = $obj->sumaVentas([$idE,$pasteleriaChacoys,$dates1[0],$dates1[1]]);
                $sumaComparativa = $obj->sumaVentas([$idE,$pasteleriaChacoys,$dates2[0],$dates2[1]]);
            break;
        default:
                $sumaActual = 0;
                $sumaComparativa = 0;
            break;
    }

    return array($sumaActual,$sumaComparativa);
}
function opposite_dates($original_dates,$year){
    $result_dates = [];
    
    $m1 = date('m',strtotime($original_dates[0]));
    $d1 = date('d',strtotime($original_dates[0]));

    $m2 = date('m',strtotime($original_dates[1]));
    $d2 = date('d',strtotime($original_dates[1]));

    $year_extra     = ($m1 > $m2) ? $year - 1 : $year;
    
    $result_dates[] = "{$year_extra}-{$m1}-{$d1}";

    $result_dates[] = "{$year}-{$m2}-{$d2}";
    
    return $result_dates;
}

?>