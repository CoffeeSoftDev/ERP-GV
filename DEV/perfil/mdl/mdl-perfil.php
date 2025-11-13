<?php
require_once('../../conf/_CRUD.php');

class Perfil extends CRUD{
    private $bd_ch;
    public function __construct() {
        $this->bd_ch = 'rfwsmqex_gvsl_rrhh.';
    }
/** SELECT */ 
function datos_usuario($array){
    return $this->_Select([
        'table'     => 'usuarios',
        'values'    => 'usser,UDN AS udn,user_photo AS foto_perfil,usr_empleado',
        'innerjoin' => ['udn' => 'usr_udn = idUDN'],
        'where'     => 'idUser',
        'data'      => $array
    ])[0];
}
function datos_empleado($array){
    $values = [
        "DATE_FORMAT(fecha_alta,'%d-%m-%Y') AS fecha_ingreso",
        'Telefono_Movil AS telefono',
        'Email AS correo',
        'Nombres AS nombres',
        'APaterno AS apaterno',
        'Nombres AS nombre_completo',
        'Nombre_Puesto AS puesto',
        'Area AS departamento'
    ];

    $innerjoin = [
        'rh_puesto_area' => 'Puesto_Empleado = idPuesto_Area',
        'rh_puestos'     => 'id_Puesto = idPuesto',
        'rh_area_udn'    => 'Area_Empleado = idAreaUDN',
        'rh_area'        => 'id_Area = idArea'
    ];

    return $this->_Select([
        'table'     => "{$this->bd_ch}empleados",
        'values'    => $values,
        'innerjoin' => $innerjoin,
        'where'     => 'idEmpleado',
        'data'      => $array
    ])[0];

}
function contact_user($array){
    return $this->_Select([
        'table'     => 'usuarios',
        'values'    => 'Telefono_Movil AS telefono,Email AS correo,Nombres AS nombres,APaterno AS apaterno',
        'innerjoin' => ["{$this->bd_ch}empleados" => 'usr_empleado = idEmpleado'],
        'where'     => 'idUser',
        'data'      => $array,
    ])[0];
}
function idEmpleado($array){
    return $this->_Select([
        'table'     => 'usuarios',
        'values'    => 'idEmpleado AS id',
        'innerjoin' => ["{$this->bd_ch}empleados" => 'usr_empleado = idEmpleado'],
        'where'     => 'idUser',
        'data'      => $array
    ])[0]['id'];
}
function validar_codigo($array){
    return $this->_Select([
        "table"  => "usuarios",
        "values" => "idUser",
        "where"  => "usr_codigo",
        "data"   => $array
    ])[0]['idUser'];
}
/** UPDATE */ 
function update_picture($array){
    return $this->_Update([
        'table'  => 'usuarios',
        'values' => 'user_photo',
        'where'  => 'idUser',
        'data'   => $array
    ]);
}
function update_keey($array){
    return $this->_Update([
        'table'  => 'usuarios',
        'values' => 'keey = MD5(?), keey2 = null',
        'where'  => 'idUser',
        'data'   => $array
    ]);
}
function update_contact($values,$array){
    return $this->_Update([
        'table'  => "{$this->bd_ch}empleados",
        'values' => $values,
        'where'  => 'idEmpleado',
        'data'   => $array
    ]);
}
function update_nameUser($array){
    return $this->_update([
        'table'  => 'usuarios',
        'values' => 'usser',
        'where'  => 'idUser',
        'data'   => $array
    ]);
}
function update_code($array){
    return $this->_Update([
        'table'  => 'usuarios',
        'values' => 'usr_codigo',
        'where'  => 'idUser',
        'data'   => $array
    ]);
}
function clean_code($array){
    return $this->_Update([
        'table'  => 'usuarios',
        'values' => 'usr_codigo = null',
        'where'  => 'idUser',
        'data'   => $array
    ]);
}
}
?>