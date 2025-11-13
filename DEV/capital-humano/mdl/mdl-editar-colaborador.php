<?php
require_once('../../conf/_CRUD2.php');

class Editarcolaborador extends CRUD{
    private $bd_ch;
    public function __construct(Type $var = null) {
        $this->bd_ch = "rfwsmqex_gvsl_rrhh.";
    }
function searchEmail($array){
    $sql = $this->_Select([
        'table'  => "{$this->bd_ch}empleados",
        'values' => 'Email',
        'where'  => 'Email,idEmpleado != ?',
        'data'   => $array
    ])[0]['Email'];

    return isset($sql) ? true : null;
}
function searchCURP($array){
    $sql = $this->_Select([
        'table'  => "{$this->bd_ch}empleados",
        'values' => 'CURP',
        'where'  => 'CURP,idEmpleado != ?',
        'data'   => $array
    ])[0]['CURP'];
    
    return isset($sql) ? true : null;
}
function searchPhone($array){
    $sql = $this->_Select([
        'table'  => "{$this->bd_ch}empleados",
        'values' => 'Telefono_Movil',
        'where'  => 'Telefono_Movil,idEmpleado != ?',
        'data'   => $array
    ])[0]['Telefono_Movil'];
    return isset($sql) ? true : null;
}
function employed($array){
    $values = [
        'FullName AS nombre',
        'APaterno AS apaterno',
        'AMaterno AS amaterno',
        'Email AS email',
        'Telefono_Movil AS telefono',
        'CURP AS curp',
        'Estudios_Empleado AS grado_estudio',
        'Carrera_Empleado AS carrera',
        'Nacimiento_Empleado AS lugar_nacimiento',
        'CP AS codigo_postal',
        'Direccion AS direccion',
        'id_Patron AS patron',
        'UDN_Empleado AS udn',
        'Area_Empleado AS departamento',
        'Puesto_Empleado AS puesto',
        'fecha_alta AS fecha_ingreso',
        'Sueldo_Diario AS salario_diario',
        'Sueldo_Fiscal AS salario_fiscal',
        'Porcentaje_Anticipo AS anticipo',
        'IMMS_Alta AS fecha_imss',
        'NSS AS nss',
        'RFC AS rfc',
        'id_banco AS banco',
        'cuentaBancaria AS cuenta',
        'opiniones AS opiniones',
        'FotoEmpleado AS photoColaborador',
        'Fecha_Nacimiento AS fecha_nacimiento',
        'Sexo AS genero',
        'Estado AS estado',
        'Telefono_Empresa'
    ];

    return $this->_Select([
        'table'  => "{$this->bd_ch}empleados",
        'values' => $values,
        'where'  => 'idEmpleado',
        'data'   => $array
    ])[0];
}
function edit_employed($array){
    $values = [
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
        'Telefono_Empresa'
    ];

    return $this->_update([
        'table'  => "{$this->bd_ch}empleados",
        'values' => $values,
        'where'  => 'idEmpleado',
        'data'   => $array
    ]);
}
function bitacora_empleado($array){
    return $this->_Insert([
        'table'  => "{$this->bd_ch}bitacora_ab",
        'values' => 'Fecha_ab,UDN_AB,Estado_ab,Observacion_ab,AB_Empleados',
        'data'   => $array
    ]); 
}
}
?>