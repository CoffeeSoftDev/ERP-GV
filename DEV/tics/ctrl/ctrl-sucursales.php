<?php
if(empty($_POST['opc'])){
    exit(0);
}

require_once('../mdl/mdl-sucursales.php');
$obj = new Sucursales;

$encode = [];
switch ($_POST['opc']) {
    case 'listPatron':
            $encode = $obj->listPatron();
    break;

    case 'newPatron':
            $patron = $_POST['patron'];
            
            $sqlPatron = $obj->searchPatron([$patron])[0];
            if( !isset($sqlPatron) ) {
                $encode = $obj->newPatron([$patron]);
            }
    break;
    
    case 'tbUDN': 
            $encode = $obj->tbUDN();
    break;
    
    case 'newSucursal': 
            $udn      = $_POST['sucursal'];
            $idPatron = $_POST['patron'];

            $sqlUDN = $obj->searchUDN([$udn]);
            if(!isset($sqlUDN[0])) $encode = $obj->newUDN([$udn,$idPatron]);
        break;
    case 'editSucursal': 
            $id       = $_POST['id'];
            $udn      = $_POST['sucursal'];
            $idPatron = $_POST['patron'];

            $encode = $obj->updateUDN([$udn,$idPatron,$id]);

        break;
    case 'stadoSucursal':
            $id     = $_POST['id'];
            $estado = $_POST['estado'];

            $encode = $obj->estadoUDN([$estado,$id]);
        break;
}

echo json_encode($encode);
?>