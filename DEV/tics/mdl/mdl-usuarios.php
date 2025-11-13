<?php
require_once('../../conf/_CRUD.php');
class Usuarios extends CRUD {
    private $bd_ch;
    public function __construct() {
        $this->bd_ch = 'rfwsmqex_gvsl_rrhh.';
    }
  // SELECT
function listUDN(){
    return $this->_Select([
        "table"  => "udn",
        "values" => "idUDN AS id, UDN as valor",
        "where"  => "Stado = 1",
        "order"  => ["ASC" => "Antiguedad"]
    ]);
}
function listUDNUSR(){
    return $this->_Select([
        "table"     => "udn",
        "values"    => "idUDN AS id,UDN AS valor",
        "innerjoin" => ["usuarios"=>"idUDN = usr_udn"],
        "where"     => "usr_estado, Stado",
        "group"     => "usr_udn",
        "order"     => ["ASC" => "Antiguedad"],
        "data"      => [1,1]
    ]);
}
function lsColaboradores($array){
    return $this->_Select([
        "table"    => "{$this->bd_ch}empleados",
        "values"   => "idEmpleado AS id, Nombres AS valor",
        "leftjoin" => ["usuarios" => "idEmpleado = usr_empleado"],
        "where"    => "usr_empleado IS NULL, Estado = 1, UDN_Empleado",
        "order"    => ["ASC" => "valor"],
        "data"     => $array
    ]);
}
function lsPerfiles(){
    return $this->_Select([
        "table"  => "perfiles",
        "values" => "idPerfil AS id,perfil AS valor",
        "where"  => "perfil_estado = 1"
    ]);
}
function userList($array) {
    $values = [
        'idUser as id',
        'usser as valor',
        'usr_estado as `status`',
        'idPerfil as idP',
        'perfil as perfil',
        'idUDN as idE',
        'UDN as udn',
        'idEmpleado as idEmp',
        'Nombres as nombres',
        'Telefono_Movil as mobil',
        'Email as correo',
    ];

    $innerjoin = [
        "perfiles"                => "usr_perfil = idPerfil",
        "udn"                     => "usr_udn = idUDN",
        "{$this->bd_ch}empleados" => "usr_empleado = idEmpleado"
    ];

    return $this->_Select([
        'table'     => "usuarios",
        'values'    => $values,
        'innerjoin' => $innerjoin,
        'where'     => $where,
        'order'     => ['DESC'=>'idUser,usr_estado'],
        'data'      => $array
    ]);
}
function datosEmpleado($array){
    return $this->_Select([
        "table"  => "{$this->bd_ch}empleados",
        "values" => "FullName as nombre, APaterno AS aPaterno,Telefono_Movil AS telefono,Email as correo",
        "where"  => "idEmpleado",
        "data"   => $array
    ])[0];
}
function datosEmpleadoUser($array){
    return $this->_Select([
        "table"  => "usuarios",
        "values" => "FullName as nombre, APaterno AS aPaterno,Telefono_Movil AS telefono,Email as correo",
        "innerjoin" => ["{$this->bd_ch}empleados" => "usr_empleado = idEmpleado"],
        "where"  => "idUser",
        "data"   => $array
    ])[0];
}
// INSERT
function newUser($array){
    return $this->_CUD("INSERT INTO usuarios (
                        usr_udn,
                        usr_empleado,
                        usr_perfil,
                        usser,
                        keey2
                    ) VALUE (?,?,?,?,md5(?))",$array);
}
// UPDATE
function userToggle($array){
    // $query = "UPDATE usuarios SET usr_estado = ? WHERE idUser = ?";
    return $this->_Update([
        'table'  => "usuarios",
        'values' => 'usr_estado',
        'where'  => 'idUser',
        'data'   => $array
    ]);
    // return $this->_CUD($query,$array); 
}
function updateUser($array){
    $campo = '';

    if(count($array) > 3) $campo = ', keey = null, keey2 = MD5(?)';
    
    $query = "UPDATE usuarios SET usser = ? $campo, usr_perfil = ? WHERE idUser = ?";
    // return $array;
    return $this->_CUD($query,$array);
}
}
?>