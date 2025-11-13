<?php
require_once('../../conf/_CRUD.php');

class Gral extends CRUD {
    private $bd_ch;
    public function __construct() {
        $this->bd_ch = "rfwsmqex_gvsl_rrhh.";
    }
function lsUDN(){
    return $this->_Select([
        "table"  => "udn",
        "values" => "idUDN AS id,UDN AS valor",
        "where"  => "Stado = 1, idUDN != 10",
        "order"  => ['ASC'=>'Antiguedad']
    ]);
}
function lsPatron(){
    return $this->_Select([
        'table'  => 'rh_patron',
        'values' => 'idPatron AS id,patron AS valor',
    ]);
}
function lsGradoEstudio(){
    return $this->_Select([
        'table'  => "{$this->bd_ch}gradoestudio",
        'values' => 'idGrado AS id,Ultimo_Grado AS valor',
    ]);
}
function lsCarrera(){
    return $this->_Select([
        'table'  => "{$this->bd_ch}carrera",
        'values' => 'idCarrera AS id, Nombre_carrera AS valor',
        'order'  => ['ASC' => 'Nombre_carrera']
    ]);
}
function lsLugarNacimiento(){
    return $this->_Select([
        'table'  => "{$this->bd_ch}lugarnacimiento",
        'values' => 'idNacimiento AS id,Lugar_Nacimiento AS valor',
        'order'  => ['ASC' => 'valor'],
    ]);
}
function lsBancos(){
    return $this->_Select([
        'table'  => 'rh_bancos',
        'values' => 'idBanco AS id,nombre_banco AS valor',
        'order'  => ['ASC' => 'valor']
    ]);
}
function lsDepartamentos(){
    return $this->_Select([
        'table'     => 'rh_area_udn',
        'values'    => 'id_UDN AS udn,idAreaUDN AS id,Area AS valor',
        'innerjoin' => ['rh_area' => 'id_Area = idArea'],
        'where'     => 'stado = 1',
        'order'     => ['ASC' => 'udn,valor']
    ]);
}
function lsPuestos(){
    return $this->_Select([
        'table'     => 'rh_puesto_area',
        'values'    => 'id_AreaUDN AS dpto,idPuesto_Area AS id,Nombre_Puesto AS valor',
        'innerjoin' => ['rh_puestos' => 'id_Puesto = idPuesto'],
        'where'     => 'stado = 1',
        'order'     => ['ASC'=>'valor']
    ]);
}
}
?>