<?php
require_once('../../conf/_CRUD.php');
class Reclutamiento extends CRUD {
    private $bd_ch;
    public function __construct() {
        $this->bd_ch = "rfwsmqex_gvsl_rrhh.";
    }
function searchEmail($array){
    $sql = $this->_Select([
        'table'  => "{$this->bd_ch}empleados",
        'values' => 'Email',
        'where'  => 'Email',
        'data'   => $array
    ])[0]['Email'];

    return isset($sql) ? true : null;
}
function searchCURP($array){
    $sql = $this->_Select([
        'table'  => "{$this->bd_ch}empleados",
        'values' => 'CURP',
        'where'  => 'CURP',
        'data'   => $array
    ])[0]['CURP'];
    
    return isset($sql) ? true : null;
}
function searchPhone($array){
    $sql = $this->_Select([
        'table'  => "{$this->bd_ch}empleados",
        'values' => 'Telefono_Movil',
        'where'  => 'Telefono_Movil',
        'data'   => $array
    ])[0]['Telefono_Movil'];
    return isset($sql) ? true : null;
}
function new_id(){
    return $this->_Select([
        'table'  => "{$this->bd_ch}empleados",
        'values' => 'MAX(idEmpleado)+1 as max'
    ])[0]['max'];
}
function new_employed($array){
    $values = [
        'idEmpleado',
        'Nombres',
        'FullName',
        'APaterno',
        'AMaterno',
        'Email',
        'Telefono_Movil',
        'CURP',
        'Fecha_Nacimiento',
        'Sexo',
        'Estudios_Empleado',
        'Carrera_Empleado',
        'Nacimiento_Empleado',
        'CP',
        'Direccion',
        'id_Patron',
        'UDN_Empleado',
        'Area_Empleado',
        'Puesto_Empleado',
        'fecha_alta',
        'Sueldo_Diario',
        'Sueldo_Fiscal',
        'Porcentaje_Anticipo',
        'IMMS_Alta',
        'NSS',
        'RFC',
        'id_banco',
        'cuentaBancaria',
        'opiniones',
        'FotoEmpleado',
        'Code_Empleado'
    ];

    $sql =  $this->_Insert([
        'table'  => "{$this->bd_ch}empleados",
        'values' => $values,
        'data'   => $array
    ]);

    if($sql == true) 
        return $this->_Select([
            'table'  => "{$this->bd_ch}empleados",
            'values' => 'MAX(idEmpleado) AS id',
        ])[0]['id'];
    else return false;

}
function bitacora_empleado($array){
    return $this->_Insert([
        'table'  => "{$this->bd_ch}bitacora_ab",
        'values' => 'UDN_AB,Estado_ab,Fecha_ab,Observacion_ab,AB_Empleados',
        'data'   => $array
    ]);
}
}
?>