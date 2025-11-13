<?php
require_once('../../conf/_CRUD.php');

class Departamentos extends CRUD{
    private $bd;
    public function __construct() {
        $this->bd = 'rfwsmqex_gvsl_rrhh.';
    }
function tbDepartamentos($array){    
    return $this->_Select([
        'table'     => 'rh_area_udn',
        'values'    => 'idAreaUDN AS id,Area AS valor',
        'innerjoin' => ['rh_area' => 'idArea = id_Area'],
        'where'     => 'id_UDN,rh_area_udn.stado = 1',
        'data'      => $array
    ]);
}
function existencia_departamento($array){
    return $this->_Select([
        'table'  => 'rh_area',
        'values' => 'idArea AS id',
        'where'  => 'UPPER(Area) = UPPER(?)',
        'data'   => $array
    ])[0]['id'];    
}
function max_departamento(){
    return $this->_Select([
        'table'  => 'rh_area',
        'values' => 'MAX(idArea) AS id',
    ])[0]['id'];
}
function nuevo_departamento($array){
    $id = $this->existencia_departamento($array);
    if(!isset($id)) {
        $result = $this->_Insert([
            'table'  => 'rh_area',
            'values' => 'Area',
            'data'   => $array
        ]);
        
        if($result == true) return $this->max_departamento();
    }

    return $id;
}
function relacion_udn_departamento($array){
    return $this->_Insert([
        'table'  => 'rh_area_udn',
        'values' => 'id_Area,id_UDN',
        'data'   => $array
    ]);
}
function cant_empleados($array){
    return $this->_Select([
        'table'  => "{$this->bd}empleados",
        'values' => 'count(idEmpleado) AS cant',
        'where'  => 'Area_Empleado',
        'data'   => $array
    ])[0]['cant'];
}
function eliminar_departamento_udn($array){
    return $this->_Delete([
        'table'  => 'rh_area_udn',
        'where'  => 'idAreaUDN',
        'data'   => $array
    ]);
}
}
?>