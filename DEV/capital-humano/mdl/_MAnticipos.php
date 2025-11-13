<?php
require_once('mdl-ch.php');
class MAnticipos extends MCH {
public function acumulado($array){
    return $this->_Select([
        'table'  => "{$this->bd_ch}anticipos",
        'values' => 'IFNULL(SUM(Saldo),0) AS acumulado',
        'where'  => "Empleado_Anticipo,DATE_FORMAT(Fecha_Anticipo,'%Y-%m-%d') BETWEEN ? AND ?",
        'data'   => $array
    ])[0]['acumulado'];
}
function colaboradoresAnticipos($array){
    return $this->_Select([
        'table'     => "{$this->bd_ch}anticipos",
        'values'    => 'idEmpleado AS id,Nombres AS nombre',
        'innerjoin' => ["{$this->bd_ch}empleados" => 'Empleado_Anticipo = idEmpleado'],
        'where'     => "id_UDN,DATE_FORMAT( Fecha_Anticipo, '%Y-%m-%d' ) BETWEEN ? AND ?",
        'group'     => 'idEmpleado',
        'order'     => ['DESC'=>'id'],
        'data'      => $array
    ]);
}
function anticipos($array){
    $values = [
        'idAnticipo AS id',
        'Folio AS folio',
        "DATE_FORMAT( Fecha_Anticipo, '%Y-%m-%d' ) AS fecha",
        'Saldo AS solicitado',
        'Saldo_Acumulado AS acumulado'
    ];

    return $this->_Select([
        'table'     => "{$this->bd_ch}anticipos",
        'values'    => $values,
        'where'     => "Empleado_Anticipo,DATE_FORMAT( Fecha_Anticipo, '%Y-%m-%d' ) BETWEEN ? AND ?",
        'order'     => ['ASC'=>'fecha'],
        'data'      => $array
    ]);
}
function data_anticipo($array){
    $values = [
        "DATE_FORMAT( Fecha_Anticipo, '%Y-%m-%d' ) AS fecha",
        "DATE_FORMAT( Fecha_Anticipo, '%H:%m:%s' ) AS hora",
        'Saldo AS solicitado',
        'Saldo_Acumulado AS acumulado',
        'Empleado_Anticipo AS id',
        'CURP AS curp',
        'RFC AS rfc',
        'nss AS nss',
        'Nombres AS colaborador',
        'Porcentaje_Anticipo AS porcentaje',
        'Sueldo_Diario AS salario_diario',
        'Telefono_Movil AS telefono',
        'Area AS departamento',
        'Nombre_Puesto AS puesto',
        'UDN AS udn'
    ];

    $leftjoin = [
        "rh_area_udn"    => 'Area_Empleado = idAreaUDN',
        "rh_area"        => 'idArea = id_Area',
        "rh_puesto_area" => 'Puesto_Empleado = idPuesto_Area',
        "rh_puestos"     => 'idPuesto = id_Puesto',
        "udn"            => 'UDN_Empleado = idUDN'
    ];

    return $this->_Select([
        'table'     => "{$this->bd_ch}anticipos",
        'values'    => $values,
        'where'     => 'idAnticipo',
        'innerjoin' => ["{$this->bd_ch}empleados" => 'Empleado_Anticipo = idEmpleado'],
        'leftjoin'  => $leftjoin,
        'data'      => $array
    ])[0];
}
}
?>