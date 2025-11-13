<?php
if(empty($_POST['opc'])) exit(0);

require_once('../mdl/mdl-editar-colaborador.php');  $obj  = new Editarcolaborador;
require_once('../../conf/_Utileria.php');           $util = new Utileria;

$encode = [];

switch ($_POST['opc']) {
    case 'consulta':
            $encode = $obj->employed([$_POST['id']]);
        break;
    case 'modificar':
            $ID               = $_POST['id'];
            $dataEmployed = $obj->employed([$ID]);
        
            # INFORMACIÓN PERSONAL
            $nombre           = mb_strtoupper($_POST['nombre'],'UTF-8');
            $apaterno         = mb_strtoupper($_POST['apaterno'],'UTF-8');
            $amaterno         = mb_strtoupper($_POST['amaterno'],'UTF-8');
            $email            = mb_strtolower($_POST['email'],'UTF-8');
            $telefono         = $_POST['telefono'];
            $curp             = mb_strtoupper($_POST['curp'],'UTF-8');
            $fecha_nacimiento = $_POST['fecha_nacimiento'];
            $genero           = $_POST['genero'];
            $grado_estudio    = $_POST['grado_estudio'];
            $carrera          = ($grado_estudio == 1 || $_POST['carrera'] == "") ? null : $_POST['carrera'];
            $lugar_nacimiento = $_POST['lugar_nacimiento'] == 0 || $_POST['lugar_nacimiento'] == '' ? null : $_POST['lugar_nacimiento'];
            $codigo_postal    = $_POST['codigo_postal'];
            $direccion        = ($_POST['direccion'] != '') ? $_POST['direccion'] : null;

            # INFORMACION LABORAL POST
            $patron         = $_POST['patron'];
            $udn            = $_POST['udn'];
            $departamento   = $_POST['departamento'];
            $puesto         = $_POST['puesto'];
            $fecha_ingreso  = $_POST['fecha_ingreso'];
            $telefono_emp   = $_POST['Telefono_Empresa'];
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
            $encode['email'] = ($email != null ) ? $obj->searchEmail([$email,$ID]) : null;
            $encode['curp']  = ($curp != null) ? $obj->searchCURP([$curp,$ID]) : null;
            $encode['phone'] = ($telefono != null ) ? $obj->searchPhone([$telefono,$ID]) : null;

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

                # Se comprueba la existencia de una foto y se mueve al servidor
                if ( $_FILES['error'] == UPLOAD_ERR_OK && $_FILES['foto']['name'] != '') {
                    $nombre_principal = str_replace(" ","_",$util->tratamiento_nombre($nombre,$apaterno)); # Obtenemos el nombre del colaborador y su apellido
                    $url              = $util->url(); # Obtenemos la url del dominio para crear la carpeta
                    $ruta             = "erp_files/capital_humano/{$ID}_{$nombre_principal}/"; # creamos la ruta donde se guardara la foto
                    $archivo          = "foto_{$ID}"; #Asignamos un nuevo nombre
                    $destino          = $url[0].$ruta.$archivo.'.'.pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION); # ruta completa para guardar en la BD
                    $encode           = $util->upload_file($_FILES['foto'],'../../../'.$ruta,$archivo); # Realizamos el movimiento de la foto dentro del servidor
                }

                # Se crea el array de datos para insertarlo 
                $array = [
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
                    $telefono_emp,
                    $ID
                ];
                // $encode = $array;
                $encode = $obj->edit_employed($array);

                // if($encode == true) {
                //     $encode = [
                //         'udn'    => $udn.' = '.$dataEmployed['udn'],
                //         'dpto'   => $departamento.' = '.$dataEmployed['departamento'],
                //         'puesto' => $puesto.' = '.$dataEmployed['puesto'],
                //         'patron' => $patron.' = '.$dataEmployed['patron'],
                //         'sd'     => $salario_diario.'='.$dataEmployed['salario_diario'],
                //         'estado' => $dataEmployed['estado'],
                //         'date'   => date('Y-m-d')
                //     ];

                //     if($udn != $dataEmployed['udn']) 
                //         $encode = $obj->bitacora_empleado([date('Y-m-d'),$udn,$dataEmployed['estado'],"Traslado UDN [{$udn} != {$dataEmployed['udn']}]",$ID]);

                //     if($departamento != $dataEmployed['departamento']) 
                //         $encode = $obj->bitacora_empleado([date('Y-m-d'),$udn,$dataEmployed['estado'],"Cambio de departamento [{$departamento} != {$dataEmployed['departamento']}]",$ID]);

                //     if($puesto != $dataEmployed['puesto']) 
                //         $encode = $obj->bitacora_empleado([date('Y-m-d'),$udn,$dataEmployed['estado'],"Cambio de puesto [{$puesto} != {$dataEmployed['puesto']}]",$ID]);

                //     if($patron != $dataEmployed['patron']) 
                //         $encode = $obj->bitacora_empleado([date('Y-m-d'),$udn,$dataEmployed['estado'],"Cambio de patron [{$patron} != {$dataEmployed['patron']}]",$ID]);

                //     if($salario_diario != $dataEmployed['salario_diario']) 
                //         $encode = $obj->bitacora_empleado([date('Y-m-d'),$udn,$dataEmployed['estado'],"Cambio de salario [{$dataEmployed['salario_diario']} != {$salario_diario}]",$ID]);
                // }
            }
        break;
}

echo json_encode($encode);

?>