<?php
if(empty($_POST['opc'])) exit(0);

require_once('../mdl/mdl-departamentos.php'); $obj  = new Departamentos;
require_once('../../conf/_Utileria.php');     $util = new Utileria;

$encode = [];
switch ($_POST['opc']) {
case 'nuevo':
        $idNuevo = $obj->nuevo_departamento([$_POST['departamento']]);
        $encode = $obj->relacion_udn_departamento([$idNuevo,$_POST['udn']]);
    break;
case 'tbDepartamentos':
        $sql = $obj->tbDepartamentos([$_POST['udn']]);
        foreach ($sql as $value) {
            $value += ['colaboradores' => $obj->cant_empleados([$value['id']])];
            $encode[] = $value;
        }
    break;
case 'eliminar':
        $encode = $obj->eliminar_departamento_udn([$_POST['id']]);
    break;
}

echo json_encode($encode);
?>