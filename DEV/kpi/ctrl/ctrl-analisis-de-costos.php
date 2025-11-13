<?php
if(empty($_POST['opc'])) exit(0);

require_once('../mdl/mdl-analisis-de-costos.php');

class Costos extends MCostos {
    public function getDates() {
        $dates1 = explode(',', $_POST['dates']);
        $yearParam = isset($_POST['year']) ? intval($_POST['year']) : 0;
    
        // Si year es 0, usar fechas proporcionadas directamente
        if ($yearParam === 0) {
            $dates2 = explode(',', $_POST['dates2']);
            $year1 = 'Fecha 2';
            $year2 = 'Fecha 1';
        } else {
            $m1 = date('m', strtotime($dates1[0]));
            $d1 = date('d', strtotime($dates1[0]));
    
            $m2 = date('m', strtotime($dates1[1]));
            $d2 = date('d', strtotime($dates1[1]));
    
            // Ajustar año si el primer mes es mayor que el segundo
            $yearExtra = ($m1 > $m2) ? $yearParam - 1 : $yearParam;
    
            $dates2 = [
                "{$yearExtra}-{$m1}-{$d1}",
                "{$yearParam}-{$m2}-{$d2}"
            ];
    
            $year1 = date('Y', strtotime($dates1[1]));
            $year2 = date('Y', strtotime($dates2[1]));
        }
    
        return [
            'dates1' => $dates1,
            'dates2' => $dates2,
            'year1'  => $year1,
            'year2'  => $year2
        ];
    }
    public function renderthead($year1,$year2){
        return [
            '<span class="d-block d-sm-none">UDN</span><span class="d-none d-sm-block">UNIDADES DE NEGOCIO</span>',
            "{$year2}",
            "{$year1}",
            '<span class="d-block d-sm-none"><i class="icon-percent"></i></span><span class="d-none d-sm-block">DIFERENCIA</span>'
        ];
    }
    public function renderRow($idE,$concepto,$ventaActual,$ventaComparativa,$costoActual,$costoComparativo,$title = false,$tr = false){

        $diferenciaVenta       = $ventaActual - $ventaComparativa;
        $diferenciaCosto       = $costoActual - $costoComparativo;
        $porcentajeTotal       = $diferenciaVenta != 0 ? ($diferenciaCosto / $diferenciaVenta) * 100 : 0;
        $porcentajeActual      = $ventaActual != 0 ? ($costoActual / $ventaActual) * 100 : 0;
        $porcentajeComparativo = $ventaComparativa != 0 ? ($costoComparativo / $ventaComparativa) * 100 : 0;
        $diferenciaPorcentaje  = $porcentajeActual - $porcentajeComparativo;

        $textDanger            = ($diferenciaPorcentaje < 0) ? 'text-danger' : '';
        $ventaComparativa      = $this->util->format_number($ventaComparativa);
        $ventaActual           = $this->util->format_number($ventaActual);
        $costoComparativo      = $this->util->format_number($costoComparativo);
        $costoActual           = $this->util->format_number($costoActual);
        $diferenciaVenta       = $this->util->format_number($diferenciaVenta);
        $diferenciaCosto       = $this->util->format_number($diferenciaCosto);
        $porcentajeTotal       = $this->util->format_number($porcentajeTotal,"%");
        $porcentajeActual      = $this->util->format_number($porcentajeActual,"%");
        $diferenciaPorcentaje  = $this->util->format_number($diferenciaPorcentaje,"%");
        $porcentajeComparativo = $this->util->format_number($porcentajeComparativo,"%");
        $printComparativa      = $costoComparativo != '-' ? "<i class='fs-5'>{$porcentajeComparativo}</i> <span class='fw-bold' style='font-size:12px'>({$costoComparativo})</span>" : $costoComparativo;
        $princActual           = $costoActual      != '-' ? "<i class='fs-5'>{$porcentajeActual}</i> <span class='fw-bold' style='font-size:12px'>({$costoActual})</span>" : $costoActual;
        $printDiferencia       = "<i class='fs-5' >{$diferenciaPorcentaje}</i>";

        $bg = '';
        $color = '';
        if($title === true){
            $bg         = 'bg-info';
            $color      = 'text-white fw-bold';
            $textDanger = '';
        }

        $result = [
            [
                'html'  => $concepto,
                'class' => $color
            ],
            [
                'html'  => $printComparativa,
                'title' => "Costo: {$costoComparativo} / Venta: {$ventaComparativa} = {$porcentajeComparativo}",
                'class' => "text-end {$color}"
            ],
            [
                'html'  => $princActual,
                'title' => "Costo: {$costoActual} / Venta: {$ventaActual} = {$porcentajeActual}",
                'class' => "text-end {$color}"
            ],
            [
                'html'  => $printDiferencia,
                'class' => "text-end {$textDanger} {$color}"
            ]
        ];

        if ($tr === true) {
            $result[] = ['tr' => ['class'=>'pointer','onclick'=>"toggleCosto({$idE})"]];
        } else {
            $result[] = ['tr' => ['class' => "costo{$idE} {$bg} hide"]];
        }

        return $result;
        
    }
    public function tbCostos(){
        $fechas = $this->getDates();
        $dates1 = $fechas['dates1'];
        $dates2 = $fechas['dates2'];
        $year1  = $fechas['year1'];
        $year2  = $fechas['year2'];

        $thead = $this->renderthead($year1,$year2);

        $sumaTotal       = 0;
        $sumaTotalComp   = 0;
        $difTotal        = 0;
        $porcentajeTotal = 0;
        $lsUDN           = [];
        
        $array_udn = [];
        $sqlUDN    = $this->lsUDN();
        foreach ($sqlUDN as $udn) {
            $idE                 = $udn['idUDN'];
            $nameUDN             = $udn['UDN'];
            $abreviatura         = $udn['Abreviatura'];

            $array_cuentas = [];
            $sqlCuentas    = $this->cuentasVenta([$idE]);

            if( $idE == 6 ) { //INFORMACION DE FOGAZA
                $array_cuentas[] = $this->totalMateriaPrima($dates1,$dates2);
                
                foreach ($sqlCuentas as $venta) {
                    $id               = $venta['idUV'];
                    $sumaActual       = $this->sumaVentas([$idE,$id,$dates1[0],$dates1[1]]);
                    $sumaComparativa  = $this->sumaVentas([$idE,$id,$dates2[0],$dates2[1]]);

                    switch ($venta['venta']) {
                        case 'Frances':
                                $idUI             = 36; //idUI = Costo Frances
                                $costoActual      = $this->sumCost([$idUI,$dates1[0],$dates1[1]],"Pago");
                                $costoComparativo = $this->sumCost([$idUI,$dates2[0],$dates2[1]],"Pago");
                                $array_cuentas[]  = $this->renderRow(6,"Frances",$sumaActual,$sumaComparativa,$costoActual,$costoComparativo);
                            break;
                        case 'Bizcocho':
                                $idCostoBizcocho      = 37; //idUI = Costo Bizcocho
                                $idCostoBizcochoDomos = 111; //idUI = Costo Domos Bizcocho

                                $array_cuentas[] = $this->costoBizcocho("Bizcocho (Matería prima)",$sumaActual,$sumaComparativa,$dates1,$dates2,$idCostoBizcocho);
                                $array_cuentas[] = $this->costoBizcocho("Bizcocho (Domos)",$sumaActual,$sumaComparativa,$dates1,$dates2,$idCostoBizcochoDomos);
                            break;
                        case 'Pastelería Normal':
                                $idCostoPasteleria = 38; //idUI = Costo Pastelería
                                $idDomosPasteleria = 96; //idUI = Costo Domos Pastelería

                                $array_cuentas[] = $this->costoPasteleria("Pastelería (Materia prima)",$sumaActual,$sumaComparativa,$dates1,$dates2,$idCostoPasteleria);
                                $array_cuentas[] = $this->costoPasteleria("Pastelería (Domos)",$sumaActual,$sumaComparativa,$dates1,$dates2,$idDomosPasteleria);
                            break;
                        case 'Souvenirs':
                                $array_cuentas[] = $this->souvenirs($venta,$sumaActual,$sumaComparativa,$dates1,$dates2);
                            break;
                        case 'Pastelería Premium':case 'Bocadillos':break;
                        default:
                                $idUI = $this->searchidUI($idE,$venta['venta']);
                                
                                $costoComparativo  = $this->sumCost([$idUI,$dates2[0],$dates2[1]],"Gasto");
                                $costoComparativo += $this->sumCost([$idUI,$dates2[0],$dates2[1]],"Pago");
                                $costoActual  = $this->sumCost([$idUI,$dates1[0],$dates1[1]],"Gasto");
                                $costoActual += $this->sumCost([$idUI,$dates1[0],$dates1[1]],"Pago");

                                $array_cuentas[] = $this->renderRow($idE,$venta['venta'],$sumaActual,$sumaComparativa,$costoActual,$costoComparativo);
                            break;
                    }
                }

                $array_cuentas[] = $this->totalManoObra($dates1,$dates2);

                foreach ($sqlCuentas as $venta) {
                    $id                    = $venta['idUV'];
                    $sumaActual            = $this->sumaVentas([$idE,$id,$dates1[0],$dates1[1]]);
                    $sumaComparativa       = $this->sumaVentas([$idE,$id,$dates2[0],$dates2[1]]);
                    // $diferencia            = $sumaActual - $sumaComparativa;
                    // $porcentajeDiferencia  = ( $sumaComparativa > 0 ) ? (($diferencia / $sumaComparativa) * 100) : 100;
                    // $text                  = ($diferencia < 0) ? 'text-danger' : '';
                    
                    switch ($venta['venta']) {
                        case 'Frances':
                                $array_cuentas[] = $this->costoDetajo($venta['venta'],$sumaActual,$sumaComparativa,$dates1,$dates2,1);
                            break;
                        case 'Bizcocho':
                                $array_cuentas[] = $this->costoDetajo($venta['venta'],$sumaActual,$sumaComparativa,$dates1,$dates2,4);
                            break;
                        case 'Pastelería Normal':
                                $array_cuentas[] = $this->costoDetajo($venta['venta'],$sumaActual,$sumaComparativa,$dates1,$dates2,2);
                            break;
                    }    
                }
            } 
            else if($idE == 1){ //INFORMACION DE QUINTA TABACHINES
                foreach ($sqlCuentas as $venta) {
                    $id                    = $venta['idUV'];
                    $sumaActual            = $this->sumaVentas([$idE,$id,$dates1[0],$dates1[1]]);
                    $sumaComparativa       = $this->sumaVentas([$idE,$id,$dates2[0],$dates2[1]]);

                    switch ($venta['venta']) {
                        case 'Hospedaje':
                                $idUI = 9; //ID COSTO AMENIDADES
                                    
                                $costoComparativo  = $this->sumCost([$idUI,$dates2[0],$dates2[1]],"Gasto");
                                $costoComparativo += $this->sumCost([$idUI,$dates2[0],$dates2[1]],"Pago");
                                $costoActual       = $this->sumCost([$idUI,$dates1[0],$dates1[1]],"Gasto");
                                $costoActual      += $this->sumCost([$idUI,$dates1[0],$dates1[1]],"Pago");

                                $array_cuentas[] = $this->renderRow($idE,"Amenidades",$sumaActual,$sumaComparativa,$costoActual,$costoComparativo);
                            break;
                        case'Otros Ingresos':    
                                $idUI = 122; //ID COSTO DECORACIONES
                                        
                                $costoComparativo  = $this->sumCost([$idUI,$dates2[0],$dates2[1]],"Gasto");
                                $costoComparativo += $this->sumCost([$idUI,$dates2[0],$dates2[1]],"Pago");
                                $costoActual       = $this->sumCost([$idUI,$dates1[0],$dates1[1]],"Gasto");
                                $costoActual      += $this->sumCost([$idUI,$dates1[0],$dates1[1]],"Pago");

                                $array_cuentas[] = $this->renderRow($idE,"Decoraciones",$sumaActual,$sumaComparativa,$costoActual,$costoComparativo);
                            break;
                        case'Diversos':break;
                        default:
                                $array_cuentas[] = $this->costosNormales($venta['venta'],$sumaActual,$sumaComparativa,$dates1,$dates2,$idE);
                            break;
                    }
                }
            } else { // Las demas UDN
                foreach ($sqlCuentas as $venta) {
                    if($venta['venta'] != 'Descorche' && $venta['venta'] != 'Servicio a domicilio' && $venta['venta'] != 'Ventas al 0%' && $venta['venta'] != 'Ventas al 16%'){
                        $id              = $venta['idUV'];
                        $sumaActual      = $this->sumaVentas([$idE,$id,$dates1[0],$dates1[1]]);
                        $sumaComparativa = $this->sumaVentas([$idE,$id,$dates2[0],$dates2[1]]);
                        $array_cuentas[] = $this->costosNormales($venta['venta'],$sumaActual,$sumaComparativa,$dates1,$dates2,$idE);
                    }
                }
            }

            $array_udn[] = $this->totalUDN($udn,$dates1,$dates2);
            $array_udn   = array_merge($array_udn,$array_cuentas);
        }

        $array_udn[]     = $this->totalGeneral($dates1,$dates2);

        return [
            'table' => ['id' => 'tbCostos'],
            'thead' => $thead,
            'tbody' => $array_udn,
            'ls'    => $lsUDN
        ];
    }
    function sumatoria_casos_especiales($idE,$idUV,$dates1,$dates2){
        $sumaActual      = 0;
        $sumaComparativa = 0;
        switch ($idUV) {
            case 6: //DIVERSOS DE TABACHINES #6 / OTROS INGRESOS DE TABACHINES #7
                    $otrosIngresosQT = 7;
                    $sumaActual      = $this->sumaVentas([$idE,$otrosIngresosQT,$dates1[0],$dates1[1]]);
                    $sumaComparativa = $this->sumaVentas([$idE,$otrosIngresosQT,$dates2[0],$dates2[1]]);
                break;
            case 13: //FRANCES DE FOGAZA #13 / FRANCHES DE CHACOYS #33
                    $idE             = 10;
                    $francesChacoys  = 33;
                    $sumaActual      = $this->sumaVentas([$idE,$francesChacoys,$dates1[0],$dates1[1]]);
                    $sumaComparativa = $this->sumaVentas([$idE,$francesChacoys,$dates2[0],$dates2[1]]);
                break;
            case 14: //BIZCOCHO DE FOGAZA #14 / BIZCOCHO DE CHACOYS #34 / BOCADILLOS DE FOGAZA #83
                    $idE             = 10;
                    $bizcochoChacoys = 34;
                    $sumaActual      = $this->sumaVentas([$idE,$bizcochoChacoys,$dates1[0],$dates1[1]]);
                    $sumaComparativa = $this->sumaVentas([$idE,$bizcochoChacoys,$dates2[0],$dates2[1]]);
                    
                    $idE              = 6;
                    $bocadillos       = 83;
                    $sumaActual      += $this->sumaVentas([$idE,$bocadillos,$dates1[0],$dates1[1]]);
                    $sumaComparativa += $this->sumaVentas([$idE,$bocadillos,$dates2[0],$dates2[1]]);
                break;
            case 15: //PASTELERÍA DE FOGAZA #15 / PASTELERÍA DE CHACOYS #35 / PASTELERÍA PREMIUM FOGAZA #32
                    $idE               = 10;
                    $pasteleriaChacoys = 35;
                    $sumaActual        = $this->sumaVentas([$idE,$pasteleriaChacoys,$dates1[0],$dates1[1]]);
                    $sumaComparativa   = $this->sumaVentas([$idE,$pasteleriaChacoys,$dates2[0],$dates2[1]]);
    
                    $idE                = 6;
                    $pasteleriaPremium  = 32;
                    $sumaActual        += $this->sumaVentas([$idE,$pasteleriaPremium,$dates1[0],$dates1[1]]);
                    $sumaComparativa   += $this->sumaVentas([$idE,$pasteleriaPremium,$dates2[0],$dates2[1]]);
                break;
            case 17: //RESFRESCOS DE FOGAZA #17 / REFRESCOS DE CHACOYS #36
                    $idE               = 10;
                    $pasteleriaChacoys = 36;
                    $sumaActual        = $this->sumaVentas([$idE,$pasteleriaChacoys,$dates1[0],$dates1[1]]);
                    $sumaComparativa   = $this->sumaVentas([$idE,$pasteleriaChacoys,$dates2[0],$dates2[1]]);
                break;
            default: 
                    $sumaActual      = 0;
                    $sumaComparativa = 0;
                break;
        }
    
        return array($sumaActual,$sumaComparativa);
    }
    function costoBizcocho($concepto,$sumaActual,$sumaComparativa,$dates1,$dates2,$idUI) {
        $idE                = 6;
        $idVentaBocadillos  = 83;
        $sumaActual        += $this->sumaVentas([$idE,$idVentaBocadillos,$dates1[0],$dates1[1]]);
        $sumaComparativa   += $this->sumaVentas([$idE,$idVentaBocadillos,$dates2[0],$dates2[1]]);
        $costoComparativo   = $this->sumCost([$idUI,$dates2[0],$dates2[1]],"Pago");
        $costoActual        = $this->sumCost([$idUI,$dates1[0],$dates1[1]],"Pago");

        return $this->renderRow(6,$concepto,$sumaActual,$sumaComparativa,$costoActual,$costoComparativo);
    }
    function costoPasteleria($concepto,$sumaActual,$sumaComparativa,$dates1,$dates2,$idUI){
        $idE                  = 6;
        $idPasteleriaPremium  = 32;

        $sumaActual       += $this->sumaVentas([$idE,$idPasteleriaPremium,$dates1[0],$dates1[1]]);
        $sumaComparativa  += $this->sumaVentas([$idE,$idPasteleriaPremium,$dates2[0],$dates2[1]]);
        $costoComparativo  = $this->sumCost([$idUI,$dates2[0],$dates2[1]],"Pago");
        $costoActual       = $this->sumCost([$idUI,$dates1[0],$dates1[1]],"Pago");

        return $this->renderRow($idE,$concepto,$sumaActual,$sumaComparativa,$costoActual,$costoComparativo);
    }
    function souvenirs($venta,$sumaActual,$sumaComparativa,$dates1,$dates2){
        $idE           = 6;
        $idCostoTazas  = 149;//idUI = Costo Tazas
        $idCostoMantas = 148;//idUI = Costo Bolsas de mantas
        
        // Comparativa
        $costoComparativo  = $this->sumCost([$idCostoTazas,$dates2[0],$dates2[1]],"Gasto");
        $costoComparativo += $this->sumCost([$idCostoTazas,$dates2[0],$dates2[1]],"Pago");
        $costoComparativo += $this->sumCost([$idCostoMantas,$dates2[0],$dates2[1]],"Gasto");
        $costoComparativo += $this->sumCost([$idCostoMantas,$dates2[0],$dates2[1]],"Pago");
        
        // Actual
        $costoActual  = $this->sumCost([$idCostoTazas,$dates1[0],$dates1[1]],"Gasto");
        $costoActual += $this->sumCost([$idCostoMantas,$dates1[0],$dates1[1]],"Gasto");
        $costoActual += $this->sumCost([$idCostoTazas,$dates1[0],$dates1[1]],"Pago");
        $costoActual += $this->sumCost([$idCostoMantas,$dates1[0],$dates1[1]],"Pago");


        return $this->renderRow(6,"Souvenirs (Tazas y Bolsos de Manta)",$sumaActual,$sumaComparativa,$costoActual,$costoComparativo);
    }
    function costosDiversos($concepto,$sumaActual,$sumaComparativa,$dates1,$dates2){
        $idE                     = 1;
        $idCostoAmenidades       = 9;
        $idCostoBlancos          = 91;
        $idCostoDecoraciones     = 102;
        $idUI                    = [$idCostoAmenidades,$idCostoBlancos,$idCostoDecoraciones];

        // Agregar ventas de otros ingresos.
        $sumaComparativa += $this->sumaVentas([$idE,6,$dates2[0],$dates2[1]]);
        $sumaActual      += $this->sumaVentas([$idE,6,$dates1[0],$dates1[1]]);

        for ($i=0; $i < count($idUI); $i++) { 
            // Comparativa
            $costoComparativo += $this->sumCost([$idUI[$i],$dates2[0],$dates2[1]],"Gasto");
            $costoComparativo += $this->sumCost([$idUI[$i],$dates2[0],$dates2[1]],"Pago");
            
            // Actual
            $costoActual += $this->sumCost([$idUI[$i],$dates1[0],$dates1[1]],"Gasto");
            $costoActual += $this->sumCost([$idUI[$i],$dates1[0],$dates1[1]],"Pago");
        }
        
        return $this->renderRow($idE,$concepto,$sumaActual,$sumaComparativa,$costoActual,$costoComparativo);
    }
    function costosNormales($concepto,$sumaActual,$sumaComparativa,$dates1,$dates2,$idE){
        $idUI = $this->searchidUI($idE,$concepto);
        
        $costoComparativo  = $this->sumCost([$idUI,$dates2[0],$dates2[1]],"Gasto");
        $costoComparativo += $this->sumCost([$idUI,$dates2[0],$dates2[1]],"Pago");
        $costoActual  = $this->sumCost([$idUI,$dates1[0],$dates1[1]],"Gasto");
        $costoActual += $this->sumCost([$idUI,$dates1[0],$dates1[1]],"Pago");

        
        return $this->renderRow($idE,$concepto,$sumaActual,$sumaComparativa,$costoActual,$costoComparativo);
    }
    function costoDetajo($concepto,$sumaActual,$sumaComparativa,$dates1,$dates2,$area){
        $idE = 6;

        if ( $concepto == 'Pastelería Normal' ) {
            $idPasteleriaPremium  = 32;
            $concepto             = 'Pastelería';
            $sumaActual          += $this->sumaVentas([$idE,$idPasteleriaPremium,$dates1[0],$dates1[1]]);
            $sumaComparativa     += $this->sumaVentas([$idE,$idPasteleriaPremium,$dates2[0],$dates2[1]]);
        }
        
        // Comparativa
        $costoComparativo = $this->destajo([$area,$dates2[0],$dates2[1]]);
        $costoActual      = $this->destajo([$area,$dates1[0],$dates1[1]]);

        return $this->renderRow($idE,$concepto,$sumaActual,$sumaComparativa,$costoActual,$costoComparativo);
    }
    function totalMateriaPrima($dates1,$dates2){
        $idE         = 6;

        $ventaActual           = $this->ventaTotalUDN([$idE,$dates1[0],$dates1[1]]);
        $ventaComparativa      = $this->ventaTotalUDN([$idE,$dates2[0],$dates2[1]]);

        $costoComparativo    = $this->costoMateriaPrimaFogaza([$dates2[0],$dates2[1]]);
        $costoActual         = $this->costoMateriaPrimaFogaza([$dates1[0],$dates1[1]]);
        
        return $this->renderRow($idE,"MATERIA PRIMA",$ventaActual,$ventaComparativa,$costoActual,$costoComparativo,true);
    }
    function totalManoObra($dates1,$dates2){
        $idE         = 6;

        $ventaActual           = $this->ventaTotalUDN([$idE,$dates1[0],$dates1[1]]);
        $ventaComparativa      = $this->ventaTotalUDN([$idE,$dates2[0],$dates2[1]]);

        $costoComparativo    = $this->costoManoObraFogaza([$dates2[0],$dates2[1]]);
        $costoActual         = $this->costoManoObraFogaza([$dates1[0],$dates1[1]]);

        return $this->renderRow($idE,"MANO DE OBRA",$ventaActual,$ventaComparativa,$costoActual,$costoComparativo,true);
    }
    function totalUDN($udn,$dates1,$dates2) {
        $idE         = $udn['idUDN'];
        $nameUDN     = $udn['UDN'];
        $abreviatura = $udn['Abreviatura'];
        $icon        = "<i class='icon-right-dir iconUDN{$idE}'></i>";

        $ventaComparativa = $this->ventaTotalUDN([$idE,$dates2[0],$dates2[1]]);
        $ventaActual      = $this->ventaTotalUDN([$idE,$dates1[0],$dates1[1]]);
        $costoComparativo = 0;
        $costoActual      = 0;

        if( $idE == 6 ) {
            $costoComparativo  = $this->costoMateriaPrimaFogaza([$dates2[0],$dates2[1]]);
            $costoActual       = $this->costoMateriaPrimaFogaza([$dates1[0],$dates1[1]]);
            $costoComparativo += $this->costoManoObraFogaza([$dates2[0],$dates2[1]]);
            $costoActual      += $this->costoManoObraFogaza([$dates1[0],$dates1[1]]);

        } elseif( $idE == 1 ) {
            $ventaComparativa = $this->ventaQT([$dates2[0],$dates2[1]]);
            $ventaActual      = $this->ventaQT([$dates1[0],$dates1[1]]);
            $costoComparativo = $this->costoQT([$dates2[0],$dates2[1]]);
            $costoActual      = $this->costoQT([$dates1[0],$dates1[1]]);
        } elseif( $idE == 5 ) {
            $ventaComparativa = $this->ventasSM([$dates2[0],$dates2[1]]);
            $ventaActual      = $this->ventasSM([$dates1[0],$dates1[1]]);
            $costoComparativo = $this->costoSM([$dates2[0],$dates2[1]]);
            $costoActual      = $this->costoSM([$dates1[0],$dates1[1]]);
        } else {
            $costoComparativo = $this->totalCostoUDN([$idE,$dates2[0],$dates2[1]]);
            $costoActual      = $this->totalCostoUDN([$idE,$dates1[0],$dates1[1]]);
        }

        $concepto = '<span class="d-block d-sm-none fw-bold">'.$abreviatura.'</span><span class="d-none d-sm-block fw-bold">'.$icon.$nameUDN.'</span>';
        
        return $this->renderRow($idE,$concepto,$ventaActual,$ventaComparativa,$costoActual,$costoComparativo,false,true);
    }
    function totalGeneral($dates1,$dates2){
        $udn = [1,4,5,6,7];

        $ventaActual      = 0;
        $ventaComparativa = 0;
        $costoActual      = 0;
        $costoComparativo = 0;

        for ($i=0; $i < count($udn); $i++) {
            $idE = $udn[$i];
            switch ($idE) {
                case "1":
                        $ventaComparativa += $this->ventaQT([$dates2[0],$dates2[1]]);
                        $ventaActual      += $this->ventaQT([$dates1[0],$dates1[1]]);
                        $costoComparativo += $this->costoQT([$dates2[0],$dates2[1]]);
                        $costoActual      += $this->costoQT([$dates1[0],$dates1[1]]);
                    break;
                case "4":
                        $ventaComparativa += $this->ventaTotalUDN([$idE,$dates2[0],$dates2[1]]);
                        $ventaActual      += $this->ventaTotalUDN([$idE,$dates1[0],$dates1[1]]);
                        $costoComparativo += $this->totalCostoUDN([$idE,$dates2[0],$dates2[1]]);
                        $costoActual      += $this->totalCostoUDN([$idE,$dates1[0],$dates1[1]]);
                    break;
                case "5":
                        $ventaComparativa += $this->ventasSM([$dates2[0],$dates2[1]]);
                        $ventaActual      += $this->ventasSM([$dates1[0],$dates1[1]]);
                        $costoComparativo += $this->costoSM([$dates2[0],$dates2[1]]);
                        $costoActual      += $this->costoSM([$dates1[0],$dates1[1]]);
                    break;
                case "7":
                        $ventaComparativa += $this->ventaTotalUDN([$idE,$dates2[0],$dates2[1]]);
                        $ventaActual      += $this->ventaTotalUDN([$idE,$dates1[0],$dates1[1]]);
                        $costoComparativo += $this->totalCostoUDN([$idE,$dates2[0],$dates2[1]]);
                        $costoActual      += $this->totalCostoUDN([$idE,$dates1[0],$dates1[1]]);
                    break;
                case "6":
                        $ventaComparativa += $this->ventaTotalUDN([$idE,$dates2[0],$dates2[1]]);
                        $ventaActual      += $this->ventaTotalUDN([$idE,$dates1[0],$dates1[1]]);
                        $costoComparativo += $this->costoMateriaPrimaFogaza([$dates2[0],$dates2[1]]) + $this->costoManoObraFogaza([$dates2[0],$dates2[1]]);
                        $costoActual      += $this->costoMateriaPrimaFogaza([$dates1[0],$dates1[1]]) + $this->costoManoObraFogaza([$dates1[0],$dates1[1]]);
                    break;
            }
        }


        $diferenciaVenta       = $ventaActual - $ventaComparativa;
        $diferenciaCosto       = $costoActual - $costoComparativo;
        $porcentaje            = $diferenciaVenta  != 0 ? ($diferenciaCosto / $diferenciaVenta) * 100 : 0;
        $porcentajeActual      = $ventaActual      != 0 ? ($costoActual / $ventaActual) * 100 : 0;
        $porcentajeComparativo = $ventaComparativa != 0 ? ($costoComparativo / $ventaComparativa) * 100 : 0;
        $diferenciaPorcentaje  = $porcentajeActual- $porcentajeComparativo;
        $text                  = ($diferenciaPorcentaje < 0) ? 'text-danger fw-bold' : '';

        $ventaActualTotal           = $this->util->format_number($ventaActual);
        $ventaComparativoTotal      = $this->util->format_number($ventaComparativa);
        $costoComparativoTotal      = $this->util->format_number($costoComparativo);
        $costoActualTotal           = $this->util->format_number($costoActual);
        $porcentajeActualTotal      = $this->util->format_number($porcentajeActual,"%");
        $porcentajeComparativoTotal = $this->util->format_number($porcentajeComparativo,"%");
        $diferenciaPorcentaje       = $this->util->format_number($diferenciaPorcentaje,"%");

        $titleComparativo = "Costo: {$costoComparativoTotal} / Venta: {$ventaComparativoTotal} = {$porcentajeComparativoTotal}";
        $titleActual      = "Costo: {$costoActualTotal} / Venta: {$ventaActualTotal} = {$porcentajeActualTotal}";
        $printComparativo = $costoComparativoTotal != '-' ? "<i class='fs-5'>{$porcentajeComparativoTotal}</i> <span class='fw-bold' style='font-size:14px'>({$costoComparativoTotal})</span>" : '-';
        $printActual      = $costoActualTotal      != '-' ? "<i class='fs-5'>{$porcentajeActualTotal}</i> <span class='fw-bold' style='font-size:14px'>({$costoActualTotal})</span>" : '-';
        $printDiferencia  = "<i class='fs-5'>{$diferenciaPorcentaje}</i>";
        
        return [
            ['html' => 'TOTAL', 'class'=> 'fw-bold' ],
            ['html' => $printComparativo,'title'=>$titleComparativo,'class' => 'text-end fw-bold'],
            ['html' => $printActual,"title"=>$titleActual,'class' => 'text-end fw-bold'],
            ['html' => $printDiferencia, 'class' => "text-end fw-bold {$text}"],
        ];
    }
}

$obj = new Costos();
echo json_encode($obj->{$_POST['opc']}());
?>