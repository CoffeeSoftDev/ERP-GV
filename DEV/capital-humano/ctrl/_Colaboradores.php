<?php 
class Colaboradores {
    // INSTANCIAS
    private $c;
    private $obj;
    private $util;
    private $bd_ch;

    public function __construct($ch) {
        $this->c     = $ch;
        $this->obj   = $this->c->obj;
        $this->util  = $this->c->util;
        $this->bd_ch = $this->obj->getCH();
    }
    
public function listUDN() {
    $idE = $this->c->getVar('idE');
    
    $result = [];
    $sql = $this->obj->lsUDN();
    if ( $idE == 8 )
        $result = $sql;
    else 
        foreach ($sql as $key => $value)  if($value['id'] == $idE) $result[] = $value;

    return $result;
}
public function lsColaboradores($filtroIncidencias = null){
    $idE    = $this->c->getVar('idE');
    $filtro = $this->c->getVar('filtro');   

    return $this->obj->lsColaboradores([$filtro,$idE],$filtroIncidencias);
}
public function lowCollaborator(){
    // INSERTAR BITACORA
    $table  = $this->bd_ch.'bitacora_ab';
    $array  = $this->util->sql($_POST);
    $result = $this->obj->_Insert($array,$table);

    // ACTUALIZAR EMPLEADO
    if($result == true) {
        $table = $this->bd_ch.'empleados';
        
        $array = [
            'values' => 'Estado',
            'where'  => 'idEmpleado',
            'data'   => [$_POST['Estado_ab'], $_POST['AB_Empleados']],
        ];

        return $this->obj->_Update($array,$table);
    }
    return $encode;
    
}
public function lsBirthday(){
    $mes = $_POST['mes'];
    $sql = $this->obj->lsBirthday([$mes]);
    foreach ($sql as &$item):
        $mes = $this->util->formatDate($item['fecha'],'thead');
        $item['fecha'] = $mes;
    endforeach;

    return $sql;
}
}
?>