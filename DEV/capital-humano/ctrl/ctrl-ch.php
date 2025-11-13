<?php
if(empty($_POST['opc'])) exit(0);

$opc = $_POST['opc'];
unset($_POST['opc']);

$encode = [];

require_once('_CH.php');
$obj = new CH();

// VALIDAR IDE
if(empty($_POST['idE']))  $obj->setVar('idE',$_COOKIE['IDE']);
else  {
    $obj->setVar('idE',$_POST['idE']);
    unset($_POST['idE']);
}

// VALIDAR DATE1 Y DATE2
if(!empty($_POST['date1'])) {
    $obj->setVar('date1',$_POST['date1']);
}
if(!empty($_POST['date2'])) {
    $obj->setVar('date2',$_POST['date2']);
    unset($_POST['date1']);
    unset($_POST['date2']);
} else {
    $obj->setVar('date2',$_POST['date1']);
    unset($_POST['date1']);
}

// VARIABLE COMODIN PARA ALGUN FILTRO EXTRA
if(!empty($_POST['filtro'])){ 
    $obj->setVar('filtro',$_POST['filtro']);
    unset($_POST['filtro']);
}

$encode = $obj->$opc();
echo json_encode($encode);
?>