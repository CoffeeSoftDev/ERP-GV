<?php
require_once ('../../conf/_CRUD.php');

class Ecommerce extends CRUD
{
    function lsBitacoraSuiteQT($date1, $date2)
    {
        $array = array($date1, $date2);
        $query = "SELECT
                    * 
                FROM
                    rfwsmqex_gvsl_finanzas.suite 
                WHERE
                    fechaSuite BETWEEN ? AND ? AND
                    nameBitacora != 0 
                ORDER BY fechaSuite DESC";
        $sql = $this->_Read($query, $array);
        return $sql;
    }

    function lsPedidosSonoras($date1, $date2)
    {
        $array = array($date1, $date2);
        $query = "SELECT
                    *
                FROM
                    rfwsmqex_ecommerce.bitacorapedidos
                WHERE
                    DATE_FORMAT(bitacorapedidos.nuevaCompra,'%Y-%m-%d') BETWEEN ? AND ?
                ORDER BY
                    bitacorapedidos.id_Orden DESC";


        $sql = $this->_Read($query, $array);
        return $sql;
    }
    function lsPedidosFogaza($date1, $date2)
    {
        $array = array($date1, $date2);
        $query = "SELECT
                    *
                FROM
                    rfwsmqex_ecommerce_fz.bitacorapedidos
                WHERE
                    DATE_FORMAT(bitacorapedidos.nuevaCompra,'%Y-%m-%d') BETWEEN ? AND ?
                ORDER BY
                    bitacorapedidos.id_Orden DESC";

        $sql = $this->_Read($query, $array);
        return $sql;
    }
    

    // function selectBitacoraSuiteAll($date1,$date2){
    //     $array = array($date1,$date2);
    //     $query = "SELECT
    //                 * 
    //             FROM
    //                 suite 
    //             WHERE
    //                 fechaSuite BETWEEN ? AND ? 
    //                 ORDER BY fechaSuite DESC";
    //     $sql = $this->_Read($query,$array,"5");
    //     return $sql;
    // }
}
?>