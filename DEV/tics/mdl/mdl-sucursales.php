<?php
require_once('../../conf/_CRUD.php');

class Sucursales extends CRUD{
    // SELECT
    function tbUDN(){
        $query = "SELECT
                    udn.idUDN AS idE, 
                    udn.UDN AS sucursal, 
                    rh_patron.idPatron AS idP, 
                    rh_patron.patron AS patron, 
                    udn.Stado AS estado
                FROM
                    udn
                INNER JOIN rh_patron ON udn.udn_patron = rh_patron.idPatron
                ORDER BY 
                    udn.Stado DESC,
                    rh_patron.patron ASC
                ";
        return $this->_Read($query,null);
    }
    function listPatron(){
        $query = "SELECT idPatron AS id, patron AS valor FROM rh_patron WHERE patron_estado = 1 ORDER BY valor ASC";
        return $this->_Read($query,null);
    }
    function searchPatron($array){
        $query = "SELECT idPatron FROM rh_patron WHERE patron = ?";
        return $this->_Read($query,$array);
    }
    function searchUDN($array){
        $query ="SELECT idUDN FROM udn WHERE udn = ?";
        return $this->_Read($query, $array);
    }

    // INSERT
    function newPatron($array){
        $query = "INSERT INTO rh_patron (patron) VALUE (?)";
        return $this->_CUD($query,$array);
    }
    function patron_udn($array){
        $query = "INSERT INTO udn_patron (id_Patron,id_UDN) VALUE (?,?)";
        return $this->_CUD($query,$array);
    }
    function newUDN($array){
        $query = "INSERT INTO udn (udn,udn_patron) VALUE (?,?)";
        return $this->_CUD($query, $array);
    }

    // UPDATE
    function updateUDN($array){
        $query = "UPDATE udn SET UDN = ?, udn_patron = ? WHERE idUDN = ?";
        return $this->_CUD($query,$array);
    }
    function estadoUDN($array){
        $query = "UPDATE udn SET Stado = ? WHERE idUDN = ?";
        return $this->_CUD($query,$array);
    }
}
?>