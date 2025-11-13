<?php 
// Obtener la informaci��n actual con la zona horaria correcta
date_default_timezone_set('America/Mexico_City');
class Incidencias {
    // INSTANCIAS
    private $c;
    private $obj;
    private $util;
    private $bd_ch;

    public function __construct($ch) {
        $this->c     = $ch;
        $this->obj   = $this->c->obj;
        $this->util  = $this->c->util;
        $this->bd_ch = $this->obj->getCH();
    }
public function terminologia() {
    $perfiles_permitidos = [5,8,9,10,19,28,15];
    $permiso             = in_array($_COOKIE['IDP'],$perfiles_permitidos);
    
    return array_merge([
        'open'         => $this->historyIncidencias(),
        'permiso'      => $permiso,
        'terminologia' => $this->obj->lsTerminologia()
    ], $this->c->initAdvance());
} 
function historyIncidencias(){
    return $this->obj->lsOpenIncidencias();
}
function aperturaIncidencias($fechaInc){
    $idE = $this->c->getVar('idE');
    // Obtener la fecha de hoy, mes actual y la hora actual del sistema. 
    $date       = new DateTime();
    $hoy        = $date->format('Y-m-d');
    $mesActual  = intval($date->format('m'));
    $horaActual = $date->format('H');

    // Obtener la fecha de ayer del sistema
    $date->modify('-1 day');
    $ayer = $date->format('Y-m-d');

    // Consultar la hora del cierre del mes actual de la BD
    $horarioCierre = $this->obj->horarioAperturaInc([$mesActual]);
    
    // Por defecto hoy esta abierto se retorna true
    $result = true;

    // Comparativo de fechas diferentes a hoy
    if ($fechaInc !== $hoy ):
        if ($fechaInc === $ayer && intval($horaActual) < intval($horarioCierre) ):
            $result = true;
        else:
            $id = $this->obj->aperturaIncidencia([$idE,$fechaInc]);
            if (!isset($id)) $result = false;
        endif;
    endif;

    return $result;
}
public function lsIncidencias(){
    $this->c->setVar('filtro',1);
    
    $date1  = $this->c->getVar('date1');
    $date2  = $this->c->getVar('date2');
    $filtro = $_POST['filtro2'];
    
    $result = [];
    $dates = $this->util->intervalDates($date1,$date2);

    $filtroIncidencias = null;
    if($_COOKIE['IDE'] == 8 AND $filtro == 1 AND $_COOKIE['IDP'] != 9 AND $_COOKIE['IDP'] != 9AND $_COOKIE['IDP'] != 28 AND $_COOKIE['IDP'] != 10 && $_COOKIE['IDP'] != 8)  {
        $filtroIncidencias = [
            'where' => 'Area_Empleado',
            'data' => $_COOKIE['IDA']
        ];
    }

    $sql = $this->c->lsColaboradores($filtroIncidencias);
    foreach ($sql as $item):
        $id = $item['id'];

        $incidencia_extra = $this->obj->lsMovAdicional([$id,$date1,$date2]);
        $exist_incExtra = empty($incidencia_extra) ? false : true;

        if($filtro == 1){
            $incidencias = [];
            foreach ($dates['dates'] as $date):
                $incidencias[] = [
                    'id'   => $this->obj->bitacora_incidencias([$id,$date]),
                    'open' => $this->aperturaIncidencias($date),
                    'date' => $date,
                ];
            endforeach;

            
            $result['colaboradores'][] = [
                'id'          => $id,
                'valor'       => $item['valor'],
                'area'        => $item['area'],
                'puesto'      => $item['puesto'],
                'sd'          => $item['sd'],
                'anticipo'    => $this->obj->acumulado([$id,$date1,$date2]),
                'incidencias' => $incidencias,
                'inc_extra'   => $exist_incExtra,
            ];
        } else {
            $extra = $this->obj->sumIncExtra([$id,$date1,$date2]);

            $result['colaboradores'][] = [
                'id'          => $id,
                'valor'       => $item['valor'],
                'area'        => $item['area'],
                'puesto'      => $item['puesto'],
                'sd'          => $item['sd'],
                'anticipo'    => $this->obj->acumulado([$id,$date1,$date2]),
                'hraExtra'    => [ $extra['hraExtra'], ( ( $item['sd'] / 8 ) * $extra['hraExtra'] ) ],
                'comp'        => $extra['comp'],
                'bono'        => $extra['bono'],
                'bitacora'    => $this->obj->sumBitacoraInc([$id,$date1,$date2]),
                'infonavit'   => $this->obj->sumInfonavit([$id,$date1,$date2]),
                'fonacot'     => $this->obj->sumFonacot([$id,$date1,$date2]),
                'prestamo'    => $this->obj->sumCPersonal([$id,$date1,$date2]),
                'incidencias' => $incidencias,
                'inc_extra'   => $exist_incExtra,
            ];

        }

    endforeach;

    $result['thDates'] = $dates['thsm'];
    // $result['filtro'] = $filtroIncidencias;

    return $result;
}
public function saveIncidencia(){
    $post = $_POST;
    unset($_POST['id_Terminologia']);
    $data  = $this->util->sql($_POST,2);
    $table = $this->obj->getCH().'bitacora_incidencia';

    if($this->obj->_Delete($data,$table))
    return $this->obj->_Insert($this->util->sql($post),$table);
}
public function bitacoraInc(){
    $table    = $this->obj->getCH()."apertura_incidencia";
    $idE      = $this->c->getVar('idE');
    $dates    = explode(' - ',str_replace('/','-',$_POST['dates']));
    $interval = $this->util->intervalDates($dates[0],$dates[1]);
    
    $data = [];
    foreach ($interval['dates'] as $date) $data[] = [$idE,$date,$_POST['motivo']]; 

    return $this->obj->_Insert([
        'table'  => $table,
        'values' => "id_UDN,fecha,motivo",
        'data'   => $data,
    ]);
    
}
public function closedInc(){
    $id    = $_POST['id'];
    $table = $this->obj->getCH()."apertura_incidencia";
    $array = ['table' => $table,'values' => 'estado'];    
    
    if ( $id == 0 ):
        $array['where']  = 'estado';
        $array['data']   = [0,1];
    elseif ($id < 100):
        $array['where']  = 'estado,id_UDN';
        $array['data']   = [0,1,$id];
    else:
        $array['where']  = 'estado,idBitacoraIncidencia';
        $array['data']   = [0,1,$id];
    endif;

    return $this->obj->_Update($array);
}
public function adicionalInc(){
    return $this->obj->_Insert($this->util->sql($_POST),$this->obj->getCH().'incidencia_extra');
}
public function deleteExtraInc(){
    return $this->obj->_Delete($this->util->sql($_POST,1),$this->obj->getCH().'incidencia_extra');
}
public function lsMovAdicional(){
    $date1 = $this->c->getVar('date1');
    $date2 = $this->c->getVar('date2');
    $idEmp = $_POST['id'];

    return $this->obj->lsMovAdicional([$idEmp,$date1,$date2]);
}
public function lsCreditosAdicional(){

}
}