<?php
if(empty($_POST['opc'])) exit(0);

require_once('../mdl/mdl-usuarios.php');
$obj = new Usuarios;

require_once('../../conf/_Message.php');
$msg = new Message;

$encode = [];
switch ($_POST['opc']) {
case 'lsUDN': 
        $encode['udn']     = $obj->listUDN();
        $encode['usr_udn'] = $obj->listUDNUSR();
    break;
case 'lsColaboradores':
        $encode = $obj->lsColaboradores([$_POST['udn']]);
    break;
case 'lsPerfil': 
        $encode = $obj->lsPerfiles();
    break;
case 'tbUser': 
        /* OBTENER LOS DATOS DE LA TABLA */
        $table['table'] = ["id" => "tbUser"];
        $table['thead'] = ["usuario","Perfil","Colaborador","teléfono","correo","opciones"];

        $udn = (isset($_POST['udn']) && $_POST['udn'] != '0') ? [$_POST['udn']] : null;
        $sqlUser = $obj->userList($udn);
        foreach ($sqlUser as $tr) {
            $table['tbody'][] = [
                ["html" => $tr['valor']],
                ["html" => $tr['perfil']],
                ["html" => $tr['nombres']],
                ["html" => $tr['mobil'], "class" => "text-center"],
                ["html" => $tr['correo'], "class" => "text-center"],
                [
                    "elemento" => "button",
                    "button" => [
                            "id" => [$tr['id'],$tr['id']],
                            "click" => ["modalEditarUsuario({$tr['id']},'{$tr['valor']}',{$tr['idP']})"]
                        ]
                ]
            ];
        }

        // OBTENER UNA LISTA DE TODOS LOS NOMBRES DE USUARIO
        $users = [];
        $sqlUser = $obj->userList(null);
        foreach ($sqlUser as $value) $users[] = $value['valor'];

        $encode = ["table" => $table, "users" => $users];
        
    break;
case 'newUser':
        $idEmpleado = $_POST['colaborador'];
        $usuario    = $_POST['usuario'];
        $clave      = $_POST['clave'];

        $encode = $obj->newUser([
            $_POST['udn'],
            $idEmpleado,
            $_POST['perfil'],
            mb_strtoupper($usuario, 'UTF-8'),
            $clave
        ]);

    
        if ( $encode === true) { // Si se incerto correctamente
            $datos = $obj->datosEmpleado([$idEmpleado]);
            $nombre = ucwords(mb_strtolower($datos['nombre'],'utf-8'));
            if(str_word_count($nombre) === 1 || str_word_count($nombre) > 2 ) 
                $nombre = explode(' ',$nombre)[0].' '.ucfirst(explode(' ',mb_strtolower($datos['aPaterno'],'utf-8'))[0]);
            
            // Creamos el mensaje de bienvenida
            $bienvenida = "Bienvenido, {$nombre}.";
            $message = "Ahora ya puedes acceder al nuevo ERP de Grupo Varoch con las siguientes credenciales.\n\n";
            $message .= "*Usuario:* {$usuario}\n";
            $message .= "*Contraseña:* {$clave}\n\n";
            $message .= "Ingresa aquí para continuar:\n _https://www.erp-varoch.com/ERP24_";

            if ( isset($datos['telefono']) ) $whatsapp = $msg->whatsapp($datos['telefono'],$bienvenida."\n\n".$message);
            if ( isset($datos['correo']) ) $correo = $msg->correo($datos['correo'],$bienvenida,str_replace(["*","_"],"",$message));
            
            $encode = [
                "whatsapp" => $whatsapp,
                "correo"   => $correo
            ];
            
            // $encode = [
            //     "whatsapp" => true,
            //     "correo"   => true
            // ];
        }
    break;
case 'statusUser':
        $encode = $obj->userToggle([$_POST['estado'],$_POST['id']]);
    break;
case 'updateUser':
        $perfil  = $_POST['perfil'];
        $usuario = mb_strtoupper(str_replace("'","",$_POST['usuario']), 'UTF-8');
        $clave   = $_POST['clave'];
        $id      = $_POST['id'];
        $user    = mb_strtoupper(str_replace("'","",$_POST['user']), 'UTF-8');
        $idP     = $_POST['idP'];

        // Arreglo para modificación completa
        $array = [$usuario,$clave,$perfil,$id];

        // Arreglo sin clave
        if ( $clave == '' || $clave == null ) 
            $array = [$usuario,$perfil,$id];

        $usser = $obj->updateUser($array);

        if($usser === true) { // Si la modificación fue exitosa
            // $datos = $obj->datosEmpleadoUser([$id]);
            // $nombre = ucwords(mb_strtolower($datos['nombre'],'utf-8'));
            // if(str_word_count($nombre) === 1 || str_word_count($nombre) > 2 ) 
            //     $nombre = explode(' ',$nombre)[0].' '.ucfirst(explode(' ',mb_strtolower($datos['aPaterno'],'utf-8'))[0]);

            // $bienvenida = "Hola, {$nombre}.";
            // $message    = '';

            // if($user != $usuario && $clave != '') {
            //     $message .= "Tus credenciales de acceso al ERP fueron modificados.\n\n";
            //     $message .= "*Usuario:* {$usuario} \n";
            //     $message .= "*Contraseña:* {$clave} \n\n";
            // } else if($user != $usuario){
            //     $message .= "Tu usuario de acceso al ERP fue modificado.\n\n";
            //     $message .= "*Usuario:* {$usuario} \n";
            // } else if($clave != ''){
            //     $message .= "Tus clave de acceso al ERP fue modificado.\n\n";
            //     $message .= "*Clave:* {$clave} \n";
            // } else if ( $idP != $perfil )  {
            //     $message .= "Tus permisos de acceso han sido modificados.\n";
            // }
            // $message .= "\n_Si no haz sido tú o no solicitaste este cambio, comunicate con el departamento de TIC'S._\n\n";

            // $http = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ?  'https://' : 'http://' ;
            // $dominio = $_SERVER['HTTP_HOST'];
            // $ERP = explode('/',$_SERVER['REQUEST_URI'])[1];
            // $message .= "Ingresa aquí para continuar:\n_{$http}{$dominio}/{$ERP}_";

            // if ( isset($datos['telefono']) ) $whatsapp = $msg->whatsapp($datos['telefono'],$bienvenida."\n\n".$message);
            // if ( isset($datos['correo']) ) $correo = $msg->correo($datos['correo'],$bienvenida,str_replace(["*","_"],"",$message));
            
            $encode = [
                "whatsapp" => true,
                "correo"   => true
            ];
        }
    break;
}
echo json_encode($encode);
?>