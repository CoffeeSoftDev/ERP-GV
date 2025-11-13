<?php
require_once('../../conf/_CRUD.php');

class Administracion extends CRUD{
function lsUDN(){
    $query ="SELECT idUDN AS id, UDN AS valor FROM udn WHERE Stado = 1 ORDER BY Antiguedad ASC";
    return $this->_Read($query, null);
}
}
?>