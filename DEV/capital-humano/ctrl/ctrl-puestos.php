<?php
if(empty($_POST['opc'])) exit(0);


require_once('../mdl/mdl-puestos.php');
$obj = new Puestos;

$encode = [];
switch ($_POST['opc']) {
case 'lsDepartamentos':
        $encode = $obj->lsDepartamentos([$_POST['udn']]);
    break;
case 'lsPuestos':
        $sql = $obj->lsPuestos([$_POST['udn']]);
        foreach ($sql as $value) {            
            $value += ['colaboradores' => $obj->cant_empleados([$value['id']])];
            $encode[] = $value;
        }
    break;
case 'nuevo':
        $max = $obj->insert_puesto([$_POST['puesto']]);

        if($max != false) $encode = $obj->insert_puesto_udn([$max,$_POST['departamento']]);
    break;
case "eliminar":
        $encode = $obj->delete_puesto([$_POST['id']]);
    break;
}

echo json_encode($encode);
?>