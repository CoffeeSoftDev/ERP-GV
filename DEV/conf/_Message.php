<?php
class Message {    
// ENVIAR CORREO ELECTRONICO
public function correo($destinatario,$asunto,$mensaje){
    // Cabeceras para el correo electrónico
    $headers = 'From: soporte@erp-varoch.com' . "\r\n" .
    'Reply-To: soporte@erp-varoch.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

    // Enviar el correo electrónico
    if(mail($destinatario,$asunto,$mensaje, $headers)) return true;
    else return false;
}
// ENVIAR MENSAJE WHATSAPP
public function whatsapp($telefono = '',$mensaje){
    $destinatario = (strlen($telefono) == 10) ? '+52'.$telefono : $telefono;
    
    if( is_array($telefono) ) {
        $destinatario = '';
        foreach ($telefono as $i => $tel) {
            $destinatario .= (strlen($tel) == 10) ? '+52'.$tel.',' : $tel.',';
        }

        $destinatario = substr($destinatario, 0, strlen($destinatario) - 1);
    }
    

    $params=array(
        'token'       => 'pjsvyuxnqx2rj4ed',
        'to'          => $destinatario,
        'body'        => $mensaje,
        'priority'    => '10',
        'referenceId' => '',
        'msgId'       => '',
        'mentions'    => ''
    );
    
    $curl = curl_init();
    
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.ultramsg.com/instance50238/messages/chat",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => http_build_query($params),
        CURLOPT_HTTPHEADER => array("content-type: application/x-www-form-urlencoded"),
    ));
    
    $response = curl_exec($curl);
    $err = curl_error($curl);
    
    curl_close($curl);
    
    if ($err) {
        $message  =  "[ https://api.ultramsg.com ] ::  WHATSAPP";
        $message .=  "\n[ ERROR cURL] :: ". $err."\n";
        $this->writeToLog($message); 

        return "cURL Error #:" . $err;
    } else {
        return true;
    }    
}
// ENVIAR MENSAJE Y ARCHIVO WHATSAPP
public function whatsapp_file($telefono,$mensaje,$ruta,$file){
    $destinatario = (strlen($telefono) == 10) ? '+52'.$telefono : $telefono;
    
    if( is_array($telefono) ) {
        $destinatario = '';
        foreach ($telefono as $i => $tel) {
            $destinatario .= (strlen($tel) == 10) ? '+52'.$tel.',' : $tel.',';
        }

        $destinatario = substr($destinatario, 0, strlen($destinatario) - 1);
    }

    $params=array(
        'token'       => 'pjsvyuxnqx2rj4ed',
        'to'          => $destinatario,
        'filename'    => $file,
        'document'    => $ruta.'/'.$file,
        'caption'     => $mensaje,
        'priority'    => '',
        'referenceId' => '',
        'nocache'     => '',
        'msgId'       => '',
        'mentions'    => ''
    );

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.ultramsg.com/instance50238/messages/document",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => http_build_query($params),
        CURLOPT_HTTPHEADER => array(
        "content-type: application/x-www-form-urlencoded"
        ),
    ));
    
    $response = curl_exec($curl);
    $err = curl_error($curl);
    
    curl_close($curl);
    
    if ($err) {
        $message  =  "[ https://api.ultramsg.com ] ::  WHATSAPP";
        $message .=  "\n[ ERROR cURL] :: ". $err."\n";
        $this->writeToLog($message); 

        return "cURL Error #:" . $err;
    } else {
        return true;
    }
}
//Tratamiento de nombre
public function tratamiento_nombre($name,$last_name){
    $nombre = ucwords(mb_strtolower($name,'utf-8'));
    if(str_word_count($nombre) === 1 || str_word_count($nombre) > 2 ) 
        $nombre = explode(' ',$nombre)[0].' '.ucfirst(explode(' ',mb_strtolower($last_name,'utf-8'))[0]);

    return $nombre;
}
}
?>