<?php 
// Obtener la información actual con la zona horaria correcta
date_default_timezone_set('America/Mexico_City');
class Prestamos {
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

    public function listPrestamosColaboradores(){
        return [
            'udn'           => $this->c->listUDN(),
            'interes'       => 2
        ];
    }
}
?>