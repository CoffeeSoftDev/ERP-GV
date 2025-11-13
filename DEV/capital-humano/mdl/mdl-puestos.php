<?php
require_once('../../conf/_CRUD.php');

class Puestos extends CRUD{
    private $bd;
    public function __construct() {
        $this->bd = "rfwsmqex_gvsl_rrhh.";
    }
function lsDepartamentos($array){
    return $this->_Select([
        'table'     => 'rh_area_udn',
        'values'    => 'idAreaUDN AS id,Area AS valor',
        'innerjoin' => ['rh_area' => 'idArea = id_Area'],
        'where'     => 'id_UDN,rh_area_udn.stado = 1',
        'data'      => $array
    ]);
}
function lsPuestos($array){
    return $this->_Select([
        'table'     => 'rh_puesto_area',
        'values'    => 'idPuesto_Area AS id,Nombre_Puesto AS valor,Area',
        'innerjoin' => ['rh_puestos' => 'idPuesto = id_Puesto','rh_area_udn'=>'id_AreaUDN = idAreaUDN','rh_area'=>'idArea = id_Area'],
        'where'     => 'id_UDN,rh_puesto_area.stado = 1',
        'data'      => $array
    ]);
}
function cant_empleados($array){
    return $this->_Select([
        'table'  => "{$this->bd}empleados",
        'values' => 'COUNT(idEmpleado) AS cant',
        'innerjoin' => ['rh_puesto_area' => 'idPuesto_Area = Puesto_Empleado'],
        'where'  => 'Puesto_Empleado',
        'data'   => $array
    ])[0]['cant'];
}
function consultar_existencia_puesto($array){
    return $this->_Select([
        'table'  => 'rh_puestos',
        'values' => 'idPuesto AS id',
        'where'  => 'UPPER(Nombre_Puesto) = UPPER(?)',
        'data'   => $array
    ])[0]['id'];
}
function max(){
    return $this->_Select([
        'table'  => 'rh_puestos',
        'values' => 'MAX(idPuesto) as max',
    ])[0]['max'];
}
function insert_puesto($array){
    $id = $this->consultar_existencia_puesto($array);
    if(!isset($id)) {
        $exito = $this->_Insert([
            'table'  => 'rh_puestos',
            'values' => 'Nombre_Puesto',
            'data'   => $array
        ]);
        
        if($exito == true) return $this->max();
    } 
    return $id;
}
function insert_puesto_udn($array){
    return $this->_Insert([
        'table'  => 'rh_puesto_area',
        'values' => 'id_Puesto,id_AreaUDN',
        'data'   => $array
    ]);
}
function delete_puesto($array){
    return $this->_Delete([
        'table'  => 'rh_puesto_area',
        'where'  => 'idPuesto_Area',
        'data'   => $array
    ]);
}
}
?>