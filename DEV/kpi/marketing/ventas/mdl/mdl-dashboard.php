<?php
require_once('../../../../conf/_CRUD.php');
require_once('../../../../conf/_Utileria.php');


class mdl extends CRUD {
    public $bd;
    public $bd2;
    public $util;

    function __construct() {
        $this->bd  = "rfwsmqex_gvsl_finanzas.";
        $this->bd2 = "rfwsmqex_gvsl_finanzas.";

        $this->util = new Utileria();
    }

    function lsUDN() {
        $query = "
            SELECT idUDN AS id, UDN AS valor
            FROM udn
            WHERE Stado = 1 AND idUDN NOT IN (8, 10, 7)
            ORDER BY UDN DESC
        ";
        return $this->_Read($query, null);
    }

    function salesUDN() {
        $query = "
            SELECT idUDN AS id, UDN AS valor
            FROM udn
            WHERE Stado = 1 AND idUDN NOT IN (1,6,8, 10, 7)
            ORDER BY UDN asc
        ";
        return $this->_Read($query, null);
    }

     function lsClasificacion(){

        $query= $this->_Select([
            "table" => "rfwsmqex_gvsl_costsys.clasificacion",
            "values" => "idClasificacion AS id,Clasificacion AS valor,id_UDN as udn",
            "where" => "idClasificacion != 7 AND idClasificacion != 9",
        ],true);

        return $this->_Read($query, null);
    }

    // Dashboard - Ingresos
    public function getVentasDelDia($array) {
        $query = "
            SELECT
                FORMAT(SUM(F.monto_productos_vendidos), 2, 'es_MX') AS total
            FROM {$this->bd}soft_restaurant_ventas V
            JOIN {$this->bd}soft_folio F ON V.soft_folio = F.id_folio
            WHERE F.fecha_folio = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
            AND F.id_udn = ?
        ";
        $result = $this->_Read($query, $array);
        return !empty($result) ? $result[0]['total'] : '0.00';
    }

    public function getComparativaChequePromedio($array) {
        $query = "
            SELECT
            SUM(V.AyB) as AyB,
            SUM(V.alimentos) as Alimentos,
            SUM(V.bebidas) as Bebidas
            FROM {$this->bd}soft_restaurant_ventas V
            JOIN {$this->bd}soft_folio F ON V.soft_folio = F.id_folio
            WHERE MONTH(F.fecha_folio) = ?
            AND YEAR(F.fecha_folio) = ?
            AND F.id_udn = ?
        ";
        $result = $this->_Read($query, $array);
        return !empty($result) ? $result[0] : ['AyB' => 0, 'Alimentos' => 0, 'Bebidas' => 0];
    }



    function getsoftVentas($array) {
        $query = "
            SELECT
                soft_folio.id_folio,
                soft_folio.fecha_folio,
                soft_folio.id_udn,
                alimentos, bebidas, guarniciones, domicilio, sales,
                id_venta, noHabitaciones, Hospedaje, AyB, Diversos
            FROM
                {$this->bd2}soft_folio
            INNER JOIN {$this->bd2}soft_restaurant_ventas
                ON soft_restaurant_ventas.soft_folio = soft_folio.id_folio
            WHERE id_udn = ?
                AND DATE_FORMAT(fecha_folio,'%Y-%m-%d') = ?
        ";
        $sql = $this->_Read($query, $array);
        return !empty($sql) ? $sql[0] : null;
    }

    function getIngresosDayOfWeek($array){

        $query = "
            SELECT
                Hospedaje,
                AyB,
                Diversos,
                noHabitaciones,
                alimentos,
                bebidas,
                guarniciones,
                sales,
                domicilio,
                 (alimentos + bebidas) as totalGral,

                 CASE
                    WHEN noHabitaciones != 0 THEN alimentos / noHabitaciones
                    ELSE 0

                END AS promedio_alimentos,

                CASE
                    WHEN noHabitaciones != 0 THEN bebidas / noHabitaciones
                    ELSE 0

                END AS promedio_bebidas,



                (Hospedaje + AyB + Diversos) as total,

                DATE_FORMAT(fecha_folio,'%Y-%m-%d') as fecha,
                soft_folio.fecha_folio
            FROM
            {$this->bd}soft_folio
            INNER JOIN {$this->bd}soft_restaurant_ventas ON soft_restaurant_ventas.soft_folio = soft_folio.id_folio
            WHERE id_udn = ?
            AND YEAR(fecha_folio) = ?
            AND MONTH(fecha_folio) = ?
            AND DAYOFWEEK(fecha_folio) = ?
            ORDER BY fecha_folio asc


        ";

       return $this->_Read($query, $array);


    }

    function ingresosMensuales($array){

        $query = "
            SELECT
                SUM(Hospedaje) as totalHospedaje,
                SUM(AyB) as totalAyB,
                SUM(alimentos) as totalAlimentos,
                SUM(bebidas) as totalBebidas,
                SUM(Diversos) as totalDiversos,
                (SUM(Hospedaje) + SUM(AyB) + SUM(Diversos)) AS totalGeneral,
                (SUM(alimentos) + SUM(bebidas) ) AS totalGralAyB,
                SUM(noHabitaciones) as totalHabitaciones
            FROM
            {$this->bd}soft_folio
            INNER JOIN {$this->bd}soft_restaurant_ventas ON soft_restaurant_ventas.soft_folio = soft_folio.id_folio
            WHERE id_udn = ?
            AND YEAR(fecha_folio) = ?
            AND MONTH(fecha_folio) = ?
            ORDER BY fecha_folio asc
        ";

        $sql = $this->_Read($query, $array);

        return !empty($sql) ? $sql[0] : [
            'totalHospedaje' => 0,
            'totalAyB' => 0,
            'totalAlimentos' => 0,
            'totalBebidas' => 0,
            'totalDiversos' => 0,
            'totalGeneral' => 0,
            'totalGralAyB' => 0,
            'totalHabitaciones' => 0
        ];
    }


    function ingresoPorDia($array){

        $query = "
            SELECT
                SUM(Hospedaje) as totalHospedaje,
                SUM(alimentos) as totalAlimentos,
                SUM(bebidas) as totalBebidas,
                SUM(AyB) as totalAyB,
                SUM(Diversos) as totalDiversos,
                (SUM(Hospedaje) + SUM(AyB) + SUM(Diversos)) AS totalGeneral,
                SUM(noHabitaciones) as totalHabitaciones,
                COUNT(DISTINCT fecha_folio) as totalDias

            FROM
            {$this->bd}soft_folio
            INNER JOIN {$this->bd}soft_restaurant_ventas ON soft_restaurant_ventas.soft_folio = soft_folio.id_folio
            WHERE id_udn = ?


            AND YEAR(fecha_folio)      = ?
            AND MONTH(fecha_folio)     = ?
            AND DAYOFWEEK(fecha_folio) = ?

            ORDER BY fecha_folio asc
        ";

        $sql = $this->_Read($query, $array);

        return !empty($sql) ? $sql[0] : [
            'totalHospedaje' => 0,
            'totalAlimentos' => 0,
            'totalBebidas' => 0,
            'totalAyB' => 0,
            'totalDiversos' => 0,
            'totalGeneral' => 0,
            'totalHabitaciones' => 0,
            'totalDias' => 0
        ];
    }

    function getsoft_ventas($array){

        $query = "
            SELECT
                soft_folio.id_folio,
                soft_folio.fecha_folio,
                soft_folio.id_udn,

                alimentos,
                bebidas,
                guarniciones,
                domicilio,

                sales,


                (alimentos + bebidas) as totalAyB,


                id_venta,
                noHabitaciones,
                Hospedaje,
                AyB,
                Diversos,
                soft_restaurant_ventas.RupturaHabitaciones,
                soft_restaurant_ventas.costoDiversos,

                CASE
                    WHEN noHabitaciones != 0 THEN AyB / noHabitaciones
                    ELSE 0
                END AS promedio_total_ayb,

                CASE
                    WHEN noHabitaciones != 0 THEN noHabitaciones / 12
                    ELSE 0
                END AS porcOcupacion,

                CASE
                    WHEN noHabitaciones != 0 THEN alimentos / noHabitaciones
                    ELSE 0
                END AS promedio_alimentos,

                CASE
                    WHEN noHabitaciones != 0 THEN bebidas / noHabitaciones
                    ELSE 0
                END AS promedio_bebidas

            FROM
            {$this->bd2}soft_folio
            INNER JOIN {$this->bd2}soft_restaurant_ventas ON soft_restaurant_ventas.soft_folio = soft_folio.id_folio
            WHERE id_udn = ?
            AND DATE_FORMAT(fecha_folio,'%Y-%m-%d') = ?

            ";

        $sql = $this->_Read($query, $array);

        return !empty($sql) ? $sql[0] : null;
    }
}
