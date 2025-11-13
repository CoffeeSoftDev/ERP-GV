<?php
if(empty($_POST['opc'])) exit(0);
require_once('../mdl/mdl-destajo-anual.php');
require_once('../../conf/_Utileria.php');

$opc = $_POST['opc'];
unset($_POST['opc']);

class DestajoAnual extends MDestajoanual{
    public $util;
    public function __construct() {
        $this->util = new Utileria();
    }
}



$obj = new CH();
$encode = $obj->$opc();
echo json_encode($encode);
?>