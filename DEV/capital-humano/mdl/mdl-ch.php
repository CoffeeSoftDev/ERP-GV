<?php
require_once('../../conf/_CRUD2.php');
class MCH extends CRUD {
    protected $bd_ch = 'rfwsmqex_gvsl_rrhh.';
    protected $bd_prod = 'rfwsmqex_gvsl_produccion.';
    public function getCH(){
        return $this->bd_ch;
    }
    
public function lsUDN(){
    return $this->_Select([
        'table'  => 'udn',
        'values' => 'idUDN AS id, UPPER(UDN) AS valor, Abreviatura AS alias',
        'where'  => 'Stado = 1',
        'order'  => ['ASC' => 'Antiguedad'],
    ]);
}
}
//13:44
?> 
