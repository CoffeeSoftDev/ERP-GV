<?php
if(empty($_POST['opc']))
    exit(0);


require_once('../mdl/mdl-validaciones.php');
$obj = new Validaciones;

$encode = [];
switch ($_POST['opc']) {
    case 'prueba':
            $encode = $_POST;
        break;
}

echo json_encode($encode);
?>