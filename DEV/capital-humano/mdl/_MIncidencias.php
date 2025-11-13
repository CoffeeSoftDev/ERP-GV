<?php
require_once('mdl-ch.php');
class MIncidencias extends MCH {
function lsTerminologia(){
    return $this->_Select([
        'table'  => "{$this->bd_ch}terminologia",
        'values' => 'idTerminologia AS id,Terminologia AS terminologia,TermCorta AS valor,text_color AS color,bg_color AS bg'
    ]);
}
function calendario_incidencias($limit){
    return $this->_Select([
        'table'  => "{$this->bd_ch}calendario_incidencias",
        'values' => 'fecha_inicio AS inicio,fecha_fin AS fin',
        'where'  => 'fecha_inicio <= NOW()',
        'order'  => ['DESC' => 'idCalendario_Inc'],
        'limit'  => $limit
    ]);
}
function bitacora_incidencias($array){
    return $this->_Select([
        'table'     => "{$this->bd_ch}bitacora_incidencia",
        'values'    => 'id_Terminologia AS id',
        'where'     => 'id_Empleado,Fecha_Incidencia',
        'data'      => $array
    ])[0]['id'];
}
function horarioAperturaInc($array){
    return $this->_Select([
        'table'     => "{$this->bd_ch}apertura_mensual_incidencias",
        'values'    => 'horario',
        'where'     => 'mes',
        'data'      => $array
    ])[0]['horario'];
}
function aperturaIncidencia($array){
    return $this->_Select([
        'table'  => "{$this->bd_ch}apertura_incidencia",
        'values' => 'idBitacoraIncidencia AS id',
        'where'  => 'id_UDN,fecha,estado = 1',
        'data'   => $array
    ])[0]['id'];
}
function openIncidencias(){
    return $this->_Select([
        'table'  => "{$this->bd_ch}apertura_incidencia",
        'values' => 'COUNT(DISTINCT  id_UDN) AS udn',
    ])[0]['udn'];
}
function lsOpenIncidencias(){
    return $this->_Select([
        'table'  => "{$this->bd_ch}apertura_incidencia",
        'values' => 'idBitacoraIncidencia AS id,id_UDN AS udn,fecha,motivo',
        'where'  => 'estado = 1',
        'data'   => $array
    ]);
}
function lsMovAdicional($array){
    $values = [
        'idExtra AS id',
        'Fecha_Inc_Extra AS fecha',
        'horaExtra AS hraExtra',
        'Bono AS bono',
        'Complemento AS comp',
        'Observaciones AS obs',
    ];

    return $this->_Select([
        'table'     => "{$this->bd_ch}incidencia_extra",
        'values'    => $values,
        'where'     => 'id_Empleado,Fecha_Inc_Extra BETWEEN ? AND ?',
        'data'      => $array
    ]);
}
function sumIncExtra($array){
    $value = [
        "SUM(horaExtra) AS hraExtra",
        "SUM(Complemento) AS comp", 
        "SUM(Bono) as bono"
    ];

    return $this->_Select([
        'table'     => "{$this->bd_ch}incidencia_extra",
        'values'    => $value,
        'where'     => 'id_Empleado,Fecha_Inc_Extra BETWEEN ? AND ?',
        'data'      => $array
    ])[0];
}
function sumBitacoraInc($array){
    return $this->_Select([
        'table'     => "{$this->bd_ch}bitacora_incidencia",
        'values'    => 'id_Terminologia AS id,COUNT(*) AS cant',
        'where'     => 'id_Empleado,Fecha_Incidencia BETWEEN ? AND ?',
        'group'     => 'id_Terminologia',
        'order'     => ['ASC'=>'id_Terminologia'],
        'data'      => $array
    ]);
}
function sumInfonavit($array){
    return $this->_Select([
        'table'     => "{$this->bd_ch}bitacora_creditos",
        'values'    => 'SUM(monto) AS cant',
        'where'     => 'id_TipoCredito = 1 OR id_TipoCredito = 2 OR id_TipoCredito = 3,id_Empleado,Fecha_Incidencia BETWEEN ? AND ?',
        'data'      => $array
    ])[0]['cant'];
}
function sumFonacot($array){
    return $this->_Select([
        'table'     => "{$this->bd_ch}bitacora_creditos",
        'values'    => 'SUM(monto) AS cant',
        'where'     => 'id_TipoCredito = 4,id_Empleado,Fecha_Incidencia BETWEEN ? AND ?',
        'data'      => $array
    ])[0]['cant'];
}
function sumCPersonal($array){
    return $this->_Select([
        'table'     => "{$this->bd_ch}bitacora_creditos",
        'values'    => 'SUM(monto) AS cant',
        'where'     => 'id_TipoCredito = 5,id_Empleado,Fecha_Incidencia BETWEEN ? AND ?',
        'data'      => $array
    ])[0]['cant'];
}
}
?>