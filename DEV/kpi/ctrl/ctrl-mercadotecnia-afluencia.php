<?php
if (empty($_POST['opc']))
    exit(0);

// incluir tu modelo
require_once ('../mdl/mdl-mercadotecnia.php');
require_once('../../conf/coffeSoft.php');
require_once('../../conf/_Utileria.php');

class ctrl extends Afluencia{

      public $util;

    public function __construct() {
        parent::__construct(); // Llama al constructor de la clase padre
        // $this->util = new Utileria(); 
    }


    function getListAfluencia(){
        # Declarar variables
        $__row = [];
        $hrs   = range(6, 20);
        $days  = listDays();
       
        foreach ($hrs as $hora) {

            $dias   = [];
            
            $campos = ['id' => '','Hrs' => $hora ];
            foreach ($days  as $noDias => $Days):
                
              $dias[$Days] = ['text'=>mt_rand(1, 100), 'class' => 'text-end'];

            endforeach;

            $dias['opc'] = 0;
            $__row[]       = array_merge($campos,$dias);

        }

        #encapsular datos
        return [
            "thead" => '',
            "row" => $__row,
            'hr' => $hrs
        ];
    }

   

}



// Instancia del objeto

$obj    = new ctrl();
$fn     = $_POST['opc'];
$encode = $obj->$fn();

echo json_encode($encode);