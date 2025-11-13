<?php
require_once('../../conf/_CRUD.php');

class MCctv extends CRUD{
    protected $bd_fzas = 'rfwsmqex_gvsl_finanzas.';
    public function getFzas(){
        return $this->bd_fzas;
    }
public function lsBitacoraSuites($array){
    return $this->_Select([
        'table'     => "{$this->bd_fzas}suite",
        'values'    => 'fechaSuite AS fecha,cantidad AS suite,CONCAT( rutaBitacora,nameBitacora) AS archivo',
        'where'     => 'fechaSuite BETWEEN ? AND ?',
        'order'     => ['DESC'=>'fechaSuite'],
        'data'      => $array
    ]);
}
public function cantSuites($array){
    return $this->_Select([
        'table'     => $this->getFzas()."turno",
        'values'    => 'IFNULL(SUM(suite),0) AS suite',
        'where'     => 'fecha_turno',
        'data'      => $array
    ])[0]['suite'];
}
}
?>