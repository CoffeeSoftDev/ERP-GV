<?php
setlocale(LC_TIME, 'es_ES.UTF-8');
date_default_timezone_set('America/Mexico_City');
class Anticipos {
    // INSTANCIAS
    private $c;
    private $obj;
    private $util;
    private $permisos;

    public function __construct($ch) {
        $this->c    = $ch;
        $this->obj  = $this->c->obj;
        $this->util = $this->c->util;
    }

private function habilitado() {
    // Fechas permitidas para anticipos
    $diaActual = intval(date('d'));
    if ( ( $diaActual >= 5 && $diaActual <= 11 ) || ( $diaActual >= 20 && $diaActual <= 25 )) return true;
}
private function buttonNewAdvance() {
    $button = [[
        'lbl'      => '<i class="icon-plus"></i> Anticipo',
        'elemento' => "button",
    ]];

    $this->permisos = ['7','9','12','17','18','20','23','25','28','36'];

    if ( $this->habilitado() == true && in_array($_COOKIE['IDP'],$this->permisos) ) :
        $button[0]['onclick'] = "advance()";
    elseif ( in_array($_COOKIE['IDP'],$this->permisos) ): 
        $button[0]['class'] = "btn btn-warning col-12";
        $button[0]['onclick'] = "noData()";
    else :
        $button = [];
    endif;

    return $button;
}

public function  initAdvance() {

    $limite_incidencias = 12;
    return [
        'udn'        => $this->c->listUDN(),
        'calendario' => array_reverse($this->obj->calendario_incidencias($limite_incidencias)),
        'habil'      => $this->buttonNewAdvance(),
        'permisos'   => $this->permisos
    ];
}
public function tbAnticipos() {
    $idE   = $this->c->getVar('idE');
    $date1 = $this->c->getVar('date1');
    $date2 = $this->c->getVar('date2');

    $result = [];
    $sql    = $this->obj->colaboradoresAnticipos([$idE,$date1,$date2]);
    foreach ($sql as $item):
        $id = $item['id'];

        $anticipos = [];
        $acumulado = 0;
        $sql2 = $this->obj->anticipos([$id,$date1,$date2]);
        foreach ($sql2 as $item2):
            $solicitado   = $item2['solicitado'];
            $acumulado   += $solicitado;
            
            $anticipos[]  = [
                'id'    => $item2['id'],
                'fecha' => $item2['fecha'],
                'saldo' => $solicitado
            ];
        endforeach;

        $result[] = [
            'id'        => $id,
            'name'      => $item['nombre'],
            'acumulado' => $acumulado,
            'anticipos' => $anticipos
        ];
    endforeach;

    return $result;
}
public function lsColaboradoresxAnticipo() {
    $idE   = $this->c->getVar('idE');
    $date1 = $this->c->getVar('date1');
    $date2 = $this->c->getVar('date2');

    $result = [];
    $sql = $this->obj->lsColaboradores([1,$idE]);
    foreach ($sql as $val):
        $id = $val['id'];

        $result[] = [
            'id'        => $id,
            'valor'     => $val['valor'],
            'sd'        => $val['sd'],
            'pa'        => $val['pa'],
            'alta'      => $val['alta'],
            'acumulado' => $this->obj->acumulado([$id,$date1,$date2])
        ];
    endforeach;
    
    return $result;
}
public function newAdvance(){
    // if ( $this->habilitado() == true && $_COOKIE['IDP'] != 10 ) :
        $table = $this->obj->getCH().'anticipos';
        $array = $this->util->sql($_POST);
        return $this->obj->_Insert($array,$table);
    // endif;
    
    // return 'false';
}
public function printAdvance(){
    $encode                      = $this->obj->data_anticipo([$_POST['idAdvance']]);

    $encode['folio']             = $_POST['idAdvance'];
    $porcentaje_sueldo           = ($encode['salario_diario'] * 15 ) * ($encode['porcentaje'] / 100 );
    $encode['telefono']          = $this->util->format_phone($encode['telefono']);
    $encode['porcentaje_sueldo'] = $this->util->format_number($porcentaje_sueldo);
    $encode['porcentaje']        = number_format($encode['porcentaje'],0).' %';
    $acumulado                   = $encode['acumulado'];
    $solicitado                  = $encode['solicitado'];
    $encode['solicitado']        = '+ '.$this->util->format_number($solicitado);
    $encode['anterior']          = $this->util->format_number($acumulado - $solicitado);
    $encode['acumulado']         = $this->util->format_number($acumulado);
    $encode['acumulado2']        = '- '.$this->util->format_number($acumulado);
    $encode['salario_diario']    = $this->util->format_number($encode['salario_diario']);
    $encode['disponible']        = $this->util->format_number($porcentaje_sueldo - $acumulado);

    return $encode;
}
}
?>