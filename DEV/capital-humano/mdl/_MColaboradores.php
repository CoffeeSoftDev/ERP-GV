<?php 
require_once('mdl-ch.php');
class MColaboradores extends MCH {
function lsColaboradores($array,$filtroIncidencias = null){
    $leftjoin = [
        'rh_area_udn'    => 'Area_Empleado = idAreaUDN',
        'rh_area'        => 'id_Area = idArea',
        'rh_puesto_area' => 'Puesto_Empleado = idPuesto_Area',
        'rh_puestos'     => 'id_Puesto = idPuesto'
    ];

    $values = [
        'idEmpleado AS id',
        'Nombres AS valor',
        'Area AS area',
        'Nombre_Puesto AS puesto',
        'Sueldo_Diario AS sd',
        'Sueldo_Fiscal AS sf',
        'Telefono_Movil AS telefono',
        "Porcentaje_Anticipo AS pa",
        "fecha_alta AS alta"
    ];

    $where = ['Estado','UDN_Empleado'];

    if ( $filtroIncidencias !== null ){
        $where2 = explode(',', $filtroIncidencias['where']);
        $data2  = explode(',', $filtroIncidencias['data']);

        $where = array_merge($where,$where2);
        $array = array_merge($array,$data2);
    }

    return $this->_Select([
        'table'    => "{$this->bd_ch}empleados",
        'values'   => $values,
        'leftjoin' => $leftjoin,
        'where'    => $where,
        'data'     => $array
    ]);
}
function lsBirthday($array){
    return $this->_Select([
        'table'     => "{$this->bd_ch}empleados",
        'values'    => 'Abreviatura AS udn,UDN_Empleado AS udn2,Nombres AS colaborador,Fecha_Nacimiento AS fecha',
        'innerjoin' => ["udn"=>'idUDN = UDN_Empleado'],
        'where'     => 'Estado = 1,MONTH ( Fecha_Nacimiento )',
        'order'     => ['ASC' => 'DAY(Fecha_Nacimiento)'],
        'data'      => $array
    ]);
}
}
?>