<?php
require_once('_MAnticipos.php');
require_once('_MIncidencias.php');
require_once('_MColaboradores.php');
require_once('_MDestajo.php');

class MEnlace {
    private $destajo;
    private $anticipos;
    private $incidencias;
    private $colaboradores;

    public function __construct() {
        $this->destajo       = new MDestajo();
        $this->anticipos     = new MAnticipos();
        $this->incidencias   = new MIncidencias();
        $this->colaboradores = new MColaboradores();
    }

    public function __call($name, $arguments) {
        $instancias = [
            $this->destajo,
            $this->anticipos,
            $this->incidencias,
            $this->colaboradores,
        ];
        foreach ($instancias as $obj) {
            if (method_exists($obj, $name)) {
                return call_user_func_array([$obj, $name], $arguments);
            }
        }
        try {
            throw new BadMethodCallException("El método {$name} no existe en ninguna de las clases contenidas.");
        } catch (BadMethodCallException $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
?>