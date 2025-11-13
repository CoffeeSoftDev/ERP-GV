<?php
require_once('../../conf/_CRUD.php');

class Incidenciascalendar extends CRUD{
    private $bd;
    public function __construct(Type $var = null) {
        $this->bd = 'rfwsmqex_gvsl_rrhh.';
    }
function tbIncidenciasCalendar(){
    return $this->_Select([
        'table'  => "{$this->bd}calendario_incidencias",
        'values' => "idCalendario_Inc AS id,DATE_FORMAT(fecha_inicio,'%d-%m-%Y') AS inicio, DATE_FORMAT(fecha_fin,'%d-%m-%Y') AS fin",
        'order'  => ['DESC'=>'idCalendario_Inc'],
        'limit' => '24'
    ]);
}
function existencia($array){
    $dato =  $this->_Select([
        'table'  => "{$this->bd}calendario_incidencias",
        'values' => 'idCalendario_Inc',
        'where'  => 'fecha_inicio,fecha_fin',
        'data'   => $array
    ])[0]['idCalendario_Inc'];
    
    return isset($dato) ? false : true;
}
function new_period($array){
    return $this->_Insert([
        'table'  => "{$this->bd}calendario_incidencias",
        'values' => 'fecha_inicio,fecha_fin,year',
        'data'   => $array
    ]);
}
function delete_periodo($array){
    return $this->_Delete([
        'table'  => "{$this->bd}calendario_incidencias",
        'where'  => 'idCalendario_Inc',
        'data'   => $array
    ]);
}
}
?>