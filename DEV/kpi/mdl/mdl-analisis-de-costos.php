<?php
require_once('../../conf/_CRUD.php');
require_once('../../conf/_Utileria.php');

class MCostos extends CRUD {
    private $bd;
    private $bdP;
    public $util;
    
// Constructor
public function __construct() {
    $this->bd   = 'rfwsmqex_gvsl_finanzas.';
    $this->bdP  = 'rfwsmqex_gvsl_produccion.';
    $this->util = new Utileria;
}
function lsYears(){
    return $this->_Select([
        "table"  => "{$this->bd}venta_bitacora",
        "values" => "YEAR(Fecha_Venta)-1 AS years",
        "group" => "years",
        "order"  => ["DESC" => "years"]
    ]);
}
function lsUDN() {
    return $this->_Select([
        "table"  => "udn",
        "values" => "idUDN,UDN,Abreviatura",
        "where"  => "Stado = 1,idUDN != 8,idUDN != 10",
        "order"  => ["ASC" => "Antiguedad"]
    ]);
}
function ultimoFechaIngreso($array) {
    return $this->_Select([ 
        "table"     => "{$this->bd}venta_bitacora",
        "values"    => "Fecha_Venta AS date,DATE_FORMAT(Fecha_Venta,'%d-%m-%Y') AS fecha",
        "innerjoin" => ["{$this->bd}ventas_udn" => 'id_UV = idUV'],
        "where"     => "id_UDN",
        "order"     => ["DESC" => "Fecha_Venta"],
        "data"      => $array
    ])[0];
}
function cuentasVenta($array) {
    return $this->_Read("SELECT idUV ,Name_Venta as venta
                        FROM {$this->bd}ventas,{$this->bd}ventas_udn
                        WHERE idVenta = id_Venta AND id_UDN = ? AND Stado = 1 
                        GROUP BY Name_Venta 
                        ORDER BY idUV ASC",$array);
}
function sumaVentas($array) {
    $result = $this->_Read("SELECT SUM( Cantidad ) AS cantidad
                    FROM {$this->bd}venta_bitacora,{$this->bd}ventas_udn,{$this->bd}ventas
                    WHERE id_UV    = idUV
                        AND   id_Venta = idVenta
                        AND   Stado    = 1
                        AND   id_UDN   = ?
                        AND   id_UV    = ?
                        AND Fecha_Venta BETWEEN ? AND ?",$array);
    return isset($result) ? $result[0]['cantidad'] : 0;
}
function sumCost($array,$movimiento){
    $where = [
        "{$this->bd}insumos_udn.Stado = 1",
        "{$this->bd}compras.id_UDN IS NOT NULL",
        "id_UI",
        "Fecha_Compras BETWEEN ? AND ?"
    ];

    return $this->_Select([
        'table'     => "{$this->bd}compras",
        'values'    => "IFNULL(SUM({$movimiento}),0) AS cantidad",
        'innerjoin' => ["{$this->bd}insumos_udn" => 'idUI = id_UI'],
        'where'     => $where,
        'data'      => $array
    ])[0]['cantidad'];
}
function searchidUI($idE,$name){
    $success = $this->_Read("SELECT
                        idUI AS id
                    FROM
                        {$this->bd}insumos_clase
                        INNER JOIN {$this->bd}insumos_udn ON idIC = id_IC
                    WHERE
                        Stado = 1 
                        AND id_UDN = {$idE} 
                        AND Name_IC LIKE 'Costo%' 
                        AND Name_IC LIKE '%{$name}%'",null);

    return isset($success) ? $success[0]['id'] : 0;
}
function destajo($array){
    $success = $this->_Read("SELECT (
                        SUM(IFNULL(pagodestajo,0)) - 
                        SUM(IFNULL(Infonavit,0)) - 
                        SUM(IFNULL(Fonacot,0)) -
                        SUM(IFNULL(DiasExtras,0)) -
                        SUM(IFNULL(perdidaMaterial,0)) -
                        SUM(IFNULL(prestamoPersonal,0))
                    ) AS destajo_total
                FROM
                    {$this->bdP}destajo
                    INNER JOIN {$this->bdP}colaborador ON id_Colaborador = idEmpleado
                    INNER JOIN {$this->bdP}formatopago ON id_Pago = idPago 
                WHERE
                    id_area = ? 
                    AND DATE_FORMAT( FechaPago, '%Y-%m-%d' ) BETWEEN ? AND ?",$array);
    
    return isset($success) ? $success[0]['destajo_total'] : 0;
}
function ventaTotalUDN($array){
    $where = [
        "Stado = 1",
        "id_UDN",
        "Fecha_Venta BETWEEN ? AND ?"
    ];

    $success = $this->_Select([
        'table'  => "{$this->bd}venta_bitacora",
        'values' => "ROUND( SUM( IFNULL( Cantidad, 0 )), 2 ) AS cantidad",
        'innerjoin' => ["{$this->bd}ventas_udn" => "id_UV = idUV"],
        'where'  => $where,
        'data'   => $array
    ]);

    return isset($success) ? $success[0]['cantidad'] : 0;
}
function ventaQT($array){
    $success = $this->_Select([
        'table'     => "{$this->bd}ventas_udn",
        'values'    => "ROUND( SUM( IFNULL( Cantidad, 0 )), 2 ) AS venta",
        'innerjoin' => ["{$this->bd}venta_bitacora" => "idUV = id_UV"],
        'where'     => "id_UDN = 1,idUV != 6,Stado = 1,Fecha_Venta BETWEEN ? AND ?",
        'data'      => $array
    ]);

    return isset($success) ? $success[0]['venta'] : 0;
}
function costoQT($array){
    $innerjoin = [
        "{$this->bd}insumos_udn" => "id_UI = idUI",
        "{$this->bd}insumos_clase" => "id_IC = idIC"
    ];

    $where = [
        "{$this->bd}compras.id_UDN = 1",
        "Stado = 1",
        "Name_IC LIKE 'Costo%'",
        "Name_IC NOT LIKE '%Indirectos%'",
        "Name_IC NOT LIKE '%Blancos%'",
        "Fecha_Compras BETWEEN ? AND ?"
    ];
    
    $success = $this->_Select([
        'table'     => "{$this->bd}compras",
        'values'    => "ROUND( SUM( IFNULL( compras.Gasto, 0 )) + SUM( IFNULL( compras.Pago, 0 )), 2 ) AS costo",
        'innerjoin' => $innerjoin,
        'where'     => $where,
        'data'      => $array
    ]);

    return isset($success) ? $success[0]['costo'] : 0;
}
function ventasSM($array){
    $success = $this->_Select([
        'table'     => "{$this->bd}ventas_udn",
        'values'    => "ROUND( SUM( IFNULL( Cantidad, 0 )), 2 ) AS venta",
        'innerjoin' => ["{$this->bd}venta_bitacora" => "idUV = id_UV"],
        'where'     => "id_UDN = 5,idUV != 40,Stado = 1,Fecha_Venta BETWEEN ? AND ?",
        'data'      => $array
    ]);

    return isset($success) ? $success[0]['venta'] : 0;
}
function costoSM($array){

    $innerjoin = [
        "{$this->bd}insumos_udn" => "id_UI = idUI",
        "{$this->bd}insumos_clase" => "id_IC = idIC"
    ];

    $where = [
        "{$this->bd}compras.id_UDN = 5",
        "Stado = 1",
        "Name_IC LIKE 'Costo%'",
        "Name_IC NOT LIKE '%Indirectos%'",
        "Name_IC NOT LIKE '%Desechables%'",
        "Fecha_Compras BETWEEN ? AND ?"
    ];

    $success = $this->_Select([
        'table'     => "{$this->bd}compras",
        'values'    => "ROUND( SUM( IFNULL( compras.Gasto, 0 )) + SUM( IFNULL( compras.Pago, 0 )), 2 ) AS costo",
        'innerjoin' => $innerjoin,
        'where'     => $where,
        'data'      => $array
    ]);

    return isset($success) ? $success[0]['costo'] : 0;
}
function costoMateriaPrimaFogaza($array){
    $success = $this->_Read("SELECT
                                ROUND(SUM(IFNULL( compras.Pago, 0 )),2) AS costo
                            FROM
                                {$this->bd}compras
                                INNER JOIN {$this->bd}insumos_udn ON id_UI = idUI
                                INNER JOIN {$this->bd}insumos_clase ON id_IC = idIC 
                            WHERE
                                Fecha_Compras BETWEEN ? AND ? 
                                AND {$this->bd}compras.id_UDN = 6 
                                AND id_UI IS NOT NULL 
                                AND Stado = 1 
                                AND Name_IC LIKE '%Costo%' 
                                AND Name_IC NOT LIKE '%Indirecto%' 
                                AND Name_IC NOT LIKE '%empaque%'",$array);

    return isset($success) ? $success[0]['costo'] : 0;
}
function costoManoObraFogaza($array){
    $result = $this->_Read("SELECT
                                ROUND( 
                                    SUM(IFNULL(pagodestajo,0)) -
                                    SUM(IFNULL(Infonavit,0)) -
                                    SUM(IFNULL(Fonacot,0)) -
                                    SUM(IFNULL(DiasExtras,0)) -
                                    SUM(IFNULL(perdidaMaterial,0)) -
                                    SUM(IFNULL(prestamoPersonal,0)),2 
                                ) AS costo
                            FROM
                                {$this->bdP}formatopago
                                INNER JOIN {$this->bdP}destajo ON idPago = id_Pago
                            WHERE
                                DATE_FORMAT(FechaPago,'%Y-%m-%d') BETWEEN ? AND ?", $array);
    return isset($result) ? $result[0]['costo'] : 0;
}
function totalCostoUDN($array){
    $success = $this->_Read("SELECT
                                ROUND(SUM(IFNULL( compras.Gasto, 0 )) + SUM(IFNULL( compras.Pago, 0 )),2) AS costo
                            FROM
                                {$this->bd}compras
                                INNER JOIN {$this->bd}insumos_udn ON id_UI = idUI
                                INNER JOIN {$this->bd}insumos_clase ON id_IC = idIC 
                            WHERE
                                {$this->bd}compras.id_UDN = ? 
                                AND Fecha_Compras BETWEEN ? AND ? 
                                AND id_UI IS NOT NULL 
                                AND Stado = 1
                                AND Name_IC LIKE '%Costo%' 
                                AND Name_IC NOT LIKE '%Indirecto%'",$array);

    return isset($success) ? $success[0]['costo'] : 0;
}
}
?>