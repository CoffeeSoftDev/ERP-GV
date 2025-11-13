<?php
if(empty($_POST['opc'])) exit(0);

include_once('../mdl/mdl-index.php');    $obj = new Index;
require_once('../../conf/_Message.php'); $msg = new Message;


$encode = [];

switch ($_POST['opc']) {
case 'index':
    $user = mb_strtoupper(str_replace("'","",$_POST['user']), 'UTF-8');
    $pass = str_replace("'","",$_POST['pass']);

    $sqlUser = $obj->consulta_user([$user,$user,$user,$pass,$pass]);

    if ( count($sqlUser) > 0 ) {
        $idUser       = $sqlUser['idUser'];
        $udn          = isset($sqlUser['usr_udn']) ? $sqlUser['usr_udn'] : '8';
        $idPerfil     = $sqlUser['usr_perfil'];
        $activado     = $sqlUser['activacion'];
        $HTTPS        = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://';
        $foto_perfil  = $HTTPS.$_SERVER['HTTP_HOST'].'/';
        $foto_perfil .= isset($sqlUser['user_photo']) ? $sqlUser['user_photo'].'?t='.time() : 'DEV/src/img/user.png?t='.time();
        $ruta         = (isset($activado)) ? $sqlUser['dir_ruta'] : "perfil/perfil.php";
        $modelo       = (isset($activado)) ? $sqlUser['mod_ruta'] : "perfil";
        $submodelo    = (isset($activado) && isset($sqlUser['submodelo'])) ? $sqlUser['submodelo'] : "null";

        $datos = $obj->name_empleado([$idUser]);
        if(isset($datos))
            $nombre_principal = $msg->tratamiento_nombre($datos['nombres'],$datos['apaterno']);
        else 
            $nombre_principal = $user;
        
        $expira = time() + (365 * 24 * 60 * 60);
        setcookie( "IDU", $idUser , $expira, "/");
        setcookie( "IDE", $udn , $expira, "/");
        setcookie( "IDP", $idPerfil , $expira, "/");
        setcookie( "TEL", $datos['telefono'] , $expira, "/");
        setcookie( "IDA", $datos['area'] , $expira, "/");
        setcookie( "IDAP", $datos['area'] , $expira, "/");
        setcookie( "USR", $nombre_principal , $expira, "/");
        setcookie( "ACT", $activado , $expira, "/");
        setcookie( "PIC", $foto_perfil , $expira, "/");
        
        $encode = [
            "success"   => true,
            "ruta"      => $ruta,
            "modelo"    => $modelo,
            "submodelo" => $submodelo,
        ];

    } else {
        $encode = [
            "success"      => false,
        ];
    }
break;
case 'forgot':
    $encode = false;
    $clave  = clave_aleatoria();
    $array  = array_fill(0, 3, $_POST['cuenta']);
    $datos = $obj->datos_empleado($array);
    
    if(isset($datos))
        $encode =  $obj->update_clave_temporal([$clave,$datos['id']]);
    
    if($encode) {        
        $nombre = ucwords(mb_strtolower($datos['nombre'],'utf-8'));
        if(str_word_count($nombre) === 1 || str_word_count($nombre) > 2 ) 
            $nombre = explode(' ',$nombre)[0].' '.ucfirst(explode(' ',mb_strtolower($datos['aPaterno'],'utf-8'))[0]);

        // Estructura del mensaje
        $asunto = "Hola, {$nombre}.";
        $message = "No puedes acceder al ERP, no te preocupes te asignamos una clave temporal para que puedas acceder y cambiarla.\n\n";
        $message .= "*Contraseña:* {$clave}\n\n";
        $message .= "Ingresa aquí para continuar:\n _https://www.erp-varoch.com/ERP24_";
        $message .= "\n\n_Si no haz sido tú, ignora este mensaje._\n_Recuerda que puedes actualizar tus datos desde tu perfil en ERP._";
        
        if ( isset($datos['telefono']) ) $whatsapp = $msg->whatsapp($datos['telefono'],$asunto."\n\n".$message);
        if ( isset($datos['correo']) ) $correo = $msg->correo($datos['correo'],$asunto,str_replace(["*","_"],"",$message));
        
        $encode = [
            "whatsapp" => $whatsapp,
            "correo"   => $correo
        ];
    }
break;
}
echo json_encode($encode);

function clave_aleatoria() {
    $longitud = 6;
    $caracteres = '0123456789';
    $clave = '';

    $caracteresLength = strlen($caracteres);
    $bytes = random_bytes($longitud);

    for ($i = 0; $i < $longitud; $i++) {
        $clave .= $caracteres[ord($bytes[$i]) % $caracteresLength];
    }

    return $clave;
}


?>