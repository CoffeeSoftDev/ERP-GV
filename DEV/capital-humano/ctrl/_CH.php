<?php
setlocale(LC_TIME, 'es_ES.UTF-8');
require_once('../mdl/_MCH.php');
require_once('../../conf/_Utileria.php');
require_once('_Anticipos.php');
require_once('_Colaboradores.php');
require_once('_Incidencias.php');
require_once('_Destajo.php');
require_once('_Prestamos.php');

class CH {
    public $obj;
    public $util;
    private $anticipos;
    private $colaboradores;
    private $incidencias;
    private $destajo;
    private $prestamo;

    private $_var = [
        'idE'    => '',
        'date1'  => '',
        'date2'  => '',
        'filtro' => ''
    ];

    public function __construct() {
        $this->obj           = new MEnlace();
        $this->util          = new Utileria();
        $this->anticipos     = new Anticipos($this);
        $this->colaboradores = new Colaboradores($this);
        $this->incidencias   = new Incidencias($this);
        $this->destajo       = new Destajo($this);
        $this->prestamo      = new Prestamos($this);
    }

    public function __call($name, $arguments) {
        $instancias = [
            $this->anticipos,
            $this->colaboradores,
            $this->incidencias,
            $this->destajo,
            $this->prestamo,
        ];
        foreach ($instancias as $obj) {
            if (method_exists($obj, $name)) {
                return call_user_func_array([$obj, $name], $arguments);
            }
        }
        try {
            throw new BadMethodCallException("El método _{$name}()_ no existe en ninguna de las clases contenidas.");
        } catch (BadMethodCallException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function getVar($nombre = null) {
        return ($nombre == null) ? $this->_var : $this->_var[$nombre];
    }
    public function setVar($nombre, $valor) {
        $this->_var[$nombre] = $valor;
    }
    
}
?>