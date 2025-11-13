<?php
if(empty($_POST['opc'])) exit(0);

require_once('../mdl/mdl-perfil.php');      $obj  = new Perfil;
require_once('../../conf/_Message.php');    $msg  = new Message;
require_once('../../conf/_Utileria.php');   $util = new Utileria;

$idUsr  = $_COOKIE['IDU'];
$encode = [];
switch ($_POST['opc']) {
case 'datos':
        $url      = $util->url();
        $usr_data = $obj->datos_usuario([$idUsr]);
        $emp_data = $obj->datos_empleado([$usr_data['usr_empleado']]);
        $encode   = array_merge($usr_data,$emp_data);

        $encode['foto_perfil'] = isset($encode['foto_perfil']) ? $url[0].$encode['foto_perfil'].'?t='.time() : $url[0].$url[1].'src/img/user.png';
        $encode['telefono']    = $util->format_phone($encode['telefono']);
        $encode['principal']   = $msg->tratamiento_nombre($encode['nombres'],$encode['apaterno']);

        unset($encode['usr_empleado']);
        unset($encode['nombres']);
        unset($encode['apaterno']);
        break;
case 'seguridad':
        $encode    = false;
        $datos     = $obj->contact_user([$idUsr]);
        $principal = $msg->tratamiento_nombre($datos['nombres'],$datos['apaterno']);
        if(isset($datos['telefono']) || $datos['correo']){
            $codigo    = $util->code_security();
            $exito     = $obj->update_code([$codigo,$idUsr]);
            $whatsapp = false;
            $correo   = false;

            $asunto  = "*Código de seguridad*\n";
            $mensaje = "Hola, {$principal}, tu código de seguridad es *{$codigo}* solo puede usarse una vez.\n\n_No compartas este código con nadie._";
            
            if ( isset($datos['telefono']) ) $whatsapp = $msg->whatsapp($datos['telefono'],$asunto."\n".$mensaje);
            if ( isset($datos['correo']) ) $correo     = $msg->correo($datos['correo'],str_replace(["*","_"],"",$asunto),str_replace(["*","_"],"",$mensaje));

            if( $whatsapp == true || $correo == true ) $encode = $codigo;
        }
    break;
case 'update':
        $idUser = $obj->validar_codigo([$_POST['codigo']]);
        $encode = false;
        if(isset($idUser)){
            if (!empty($_POST['clave1']) && $_POST['clave1'] == $_POST['clave2']) {
                $encode['key'] = $obj->update_keey([$_POST['clave1'],$idUser]);

                $datos = $obj->contact_user([$idUser]);
                $msg->whatsapp($datos['telefono'],'Tu nueva contraseña: *'.$_POST['clave1'].'*\nSe actualizó con éxito.');
            }

            if ( !empty($_POST['telefono']) || !empty($_POST['correo']) ) {
                $values = '';
                $array = [];
    
                if (!empty($_POST['telefono'])) {
                    $values .= 'Telefono_Movil,';
                    $array[] = $_POST['telefono'];
                }
                
                if (!empty($_POST['correo'])) {
                    $values .= 'Email,';
                    $array[] = $_POST['correo'];
                }
    
                $values             = substr($values,0,-1);
                $array[]            = $obj->idEmpleado([$idUser]);
                $encode['datos']    = $obj->update_contact($values,$array);
            }
            
            if(!empty($_POST['usser']))
                $encode['datos'] = $obj->update_nameUser([$_POST['usser'],$idUser]);
            

            if($encode) $obj->clean_code([$idUser]);
        }
    break;
case 'foto':
        $datos            = $obj->contact_user([$idUsr]);                                                        // Obtenemos los datos del usuario
        $nombre_principal = str_replace(" ","_",$msg->tratamiento_nombre($datos['nombres'],$datos['apaterno']));  // Le damos tratamiento a su nombre con 2 palabras.
        
        $destino       = "erp_files/usuarios/{$idUsr}_{$nombre_principal}/";     // Ruta destino son hashes
        $nombreArchivo = "{$idUsr}_perfil";                                      // Asignamos el nuevo nombre para el archivo
        $extension     = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);  // Obtenemos la extension del archivo
        $usr_photo     = $destino.$nombreArchivo.'.'.$extension;                 // Ruta + Nuevo nombre + extensión (guardar en BD)

        $encode = $obj->update_picture([$usr_photo,$idUsr]);

        if ( $encode ) $encode = $util->upload_file($_FILES['foto'],'../../../'.$destino,$nombreArchivo);
    break;
}

echo json_encode($encode);
?>