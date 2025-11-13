<?php
require_once('../mdl/mdl-general.php');
$obj = new Gral;

$grado         = $obj->lsGradoEstudio();
$carrera       = $obj->lsCarrera();
$nacimiento    = $obj->lsLugarNacimiento();
$bancos        = $obj->lsBancos();
$patron        = $obj->lsPatron();
$departamentos = $obj->lsDepartamentos();
$puestos       = $obj->lsPuestos();
$udn           = $obj->lsUDN();

$encode        = [
    'patron'        => $patron,
    'grado'         => $grado,
    'carrera'       => $carrera,
    'nacimiento'    => $nacimiento,
    'bancos'        => $bancos,
    'departamentos' => $departamentos,
    'puestos'       => $puestos,
    'udn'           => $udn
];

echo json_encode($encode);
?>