<?php
if(empty($_POST)) exit(0);
require_once('../mdl/mdl-reclutamiento.php');   $obj  = new Reclutamiento;
require_once('../../conf/_Utileria.php');       $util = new Utileria;

$encode = [];

# INFORMACIÓN PERSONAL
$nombre           = mb_strtoupper($_POST['nombre'],'UTF-8');
$apaterno         = mb_strtoupper($_POST['apaterno'],'UTF-8');
$amaterno         = mb_strtoupper($_POST['amaterno'],'UTF-8');
$email            = mb_strtolower($_POST['email'],'UTF-8');
$telefono         = $_POST['telefono'];
$curp             = mb_strtoupper($_POST['curp'],'UTF-8');
$fecha_nacimiento = $_POST['fecha_nacimiento'];
// $genero           = (mb_strtoupper($_POST['genero'],'UTF-8') == "MASCULINO" ) ? "H" : "M";
$genero           = $_POST['genero'];
$grado_estudio    = $_POST['grado_estudio'];
$carrera          = $grado_estudio             == 1 ? null : $_POST['carrera'];
$lugar_nacimiento = $_POST['lugar_nacimiento'] == 0 || $_POST['lugar_nacimiento'] == '' ? null : $_POST['lugar_nacimiento'];
$codigo_postal    = $_POST['codigo_postal'];
$direccion        = ($_POST['direccion'] != '') ? $_POST['direccion'] : null;

# INFORMACION LABORAL POST
$patron         = $_POST['patron'];
$udn            = $_POST['udn'];
$departamento   = $_POST['departamento'];
$puesto         = $_POST['puesto'];
$fecha_ingreso  = $_POST['fecha_ingreso'];
$salario_diario = ($_POST['salario_diario'] != '') ? $_POST['salario_diario'] : null;
$salario_fiscal = ($_POST['salario_fiscal'] != '') ? $_POST['salario_fiscal'] : null;
$anticipo       = ($_POST['anticipo'] != '') ? $_POST['anticipo'] : null;
$fecha_imss     = ($_POST['fecha_imss'] != '') ? $_POST['fecha_imss'] : null;
$nss            = ($_POST['nss'] != '') ? $_POST['nss'] : null;
$rfc            = ($_POST['rfc'] != '') ? $_POST['rfc'] : null;
$banco          = ($_POST['banco'] != '0') ? $_POST['banco'] : null;
$cuenta         = $banco != '0' ? $_POST['cuenta'] : null;
$opiniones      = ($_POST['opiniones'] != '') ? $_POST['opiniones'] : null;

# Comprobamos que no existe un correo, curp o telefono igual para evitar duplicidad de datos
$encode['email'] = ($email != null ) ? $obj->searchEmail([$email]) : null;
$encode['curp']  = ($curp != null) ? $obj->searchCURP([$curp]) : null;
$encode['phone'] = ($telefono != null ) ? $obj->searchPhone([$telefono]) : null;

# Si especificamente ninguno existe se inserta el nuevo empleado
if (
    $encode['email'] === null &&
    $encode['curp']  === null &&
    $encode['phone'] === null
) {
    # Se eliminan los array del encode para no mandarlos al frontend
    unset($encode['email']);
    unset($encode['curp']);
    unset($encode['phone']);

    $ruta  = null; #Declaramos una variable ruta para la foto
    $IDMAX = $obj->new_id(); #Obtenemos el nuevo ID para asignarle nombre a su carpeta de expediente y la foto

    # Se comprueba la existencia de una foto y se mueve al servidor
    if ( $_FILES['error'] == UPLOAD_ERR_OK && $_FILES['foto']['name'] != '') {
        $nombre_principal = str_replace(" ","_",$util->tratamiento_nombre($nombre,$apaterno)); # Obtenemos el nombre del colaborador y su apellido
        $url              = $util->url(); # Obtenemos la url del dominio para crear la carpeta
        $ruta             = "erp_files/capital_humano/{$IDMAX}_{$nombre_principal}/"; # creamos la ruta donde se guardara la foto
        $archivo          = "foto_{$IDMAX}"; #Asignamos un nuevo nombre
        $destino          = $url[0].$ruta.$archivo.'.'.pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION); # ruta completa para guardar en la BD
        $encode           = $util->upload_file($_FILES['foto'],'../../../'.$ruta,$archivo); # Realizamos el movimiento de la foto dentro del servidor
    }

    # Se crea el array de datos para insertarlo 
    // $prueba = [
    //     'id'             => $IDMAX,
    //     'fullName'       => "{$nombre} {$apaterno} {$amaterno}",
    //     'Name'           => $nombre,
    //     'APaterno'       => $apaterno,
    //     'AMaterno'       => $amaterno,
    //     'correo'         => $email,
    //     'tel'            => $telefono,
    //     'curp'           => $curp,
    //     'date_nac'       => $fecha_nacimiento,
    //     'genero'         => $genero,
    //     'gradoEstudio'   => $grado_estudio,
    //     'carrera'        => $carrera,
    //     'lugar_nac'      => $lugar_nacimiento,
    //     'cp'             => $codigo_postal,
    //     'direcc'         => $direccion,
    //     'patron'         => $patron,
    //     'udn'            => $udn,
    //     'dpto'           => $departamento,
    //     'puesto'         => $puesto,
    //     'alta'           => $fecha_ingreso,
    //     'sd'             => $salario_diario,
    //     'sf'             => $salario_fiscal,
    //     'porcntAnticipo' => $anticipo,
    //     'ims'            => $fecha_imss,
    //     'nss'            => $nss,
    //     'rfc'            => $rfc,
    //     'bank'           => $banco,
    //     'cuent'          => $cuenta,
    //     'opiniones'      => $opiniones,
    //     'destino'        => $destino,
    //     'code'           => code_empleado($IDMAX,$fecha_ingreso) # Obtenemos un codigo de empleado con su fecha de alta y el ID
    // ];
    $array = [
        $IDMAX,
        "{$nombre} {$apaterno} {$amaterno}",
        $nombre,
        $apaterno,
        $amaterno,
        $email,
        $telefono,
        $curp,
        $fecha_nacimiento,
        $genero,
        $grado_estudio,
        $carrera,
        $lugar_nacimiento,
        $codigo_postal,
        $direccion,
        $patron,
        $udn,
        $departamento,
        $puesto,
        $fecha_ingreso,
        $salario_diario,
        $salario_fiscal,
        $anticipo,
        $fecha_imss,
        $nss,
        $rfc,
        $banco,
        $cuenta,
        $opiniones,
        $destino,
        code_empleado($IDMAX,$fecha_ingreso) # Obtenemos un codigo de empleado con su fecha de alta y el ID
    ];
    // $encode = $array;
    $idEmpleado = $obj->new_employed($array);

    if($idEmpleado !== false) $encode = $obj->bitacora_empleado([$udn,1,$fecha_ingreso,'Alta',$idEmpleado]);
}

echo json_encode($encode);


function code_empleado($max,$fecha_alta){
    $fecha = explode('-',$fecha_alta);
    return substr($fecha[0], -2).$fecha[1].$fecha[2].$max;
}
?>