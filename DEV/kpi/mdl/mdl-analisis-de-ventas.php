<?php
require_once('../../conf/_CRUD.php');

class Analisisdeventas extends CRUD {
    private $bd;
// Constructor
public function __construct() {
    $this->bd = 'rfwsmqex_gvsl_finanzas.';
}
function lsYears(){
    return $this->_Select([
        "table"  => "{$this->bd}venta_bitacora",
        "values" => "YEAR(Fecha_Venta)-1 AS years",
        "group" => "years",
        "order"  => ["DESC" => "years"]
    ]);
}
function lsUDN(){
    return $this->_Select([
        "table"  => "udn",
        "values" => "idUDN,UDN,Abreviatura",
        "where"  => "Stado = 1,idUDN != 8,idUDN != 10",
        "order"  => ["ASC" => "Antiguedad"]
    ]);
}
function ultimoFechaIngreso($array){
    return $this->_Select([ 
        "table"     => "{$this->bd}venta_bitacora",
        "values"    => "Fecha_Venta AS date,DATE_FORMAT(Fecha_Venta,'%d-%m-%Y') AS fecha",
        "innerjoin" => ["{$this->bd}ventas_udn" => 'id_UV = idUV'],
        "where"     => "id_UDN",
        "order"     => ["DESC" => "Fecha_Venta"],
        "data"      => $array
    ])[0];
}
function cuentasVenta($array){
    return $this->_Read("SELECT idUV ,Name_Venta as venta
                        FROM {$this->bd}ventas,{$this->bd}ventas_udn
                        WHERE idVenta = id_Venta AND id_UDN = ? AND Stado = 1 
                        GROUP BY Name_Venta 
                        ORDER BY idUV ASC",$array);
}
function sumaVentas($array){
    return $this->_Read("SELECT SUM( Cantidad ) AS cantidad
                            FROM {$this->bd}venta_bitacora,{$this->bd}ventas_udn,{$this->bd}ventas
                            WHERE id_UV    = idUV
                                AND   id_Venta = idVenta
                                AND   Stado    = 1
                                AND   id_UDN   = ?
                                AND   id_UV    = ?
                                AND Fecha_Venta BETWEEN ? AND ?",$array)[0]['cantidad'];
}
}
?>