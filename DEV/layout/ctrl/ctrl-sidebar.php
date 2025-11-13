<?php
if (empty($_POST['opc']))
    exit(0);

// incluir tu modelo
require_once ('../../layout/mdl/mdl-sidebar.php');
require_once('../../conf/coffeSoft.php');
// sustituir 'mdl' extends de acuerdo al nombre que tiene el modelo
class ctrl extends Calendarizacion{


    function Notificaciones(){
  
        $lsNotificaciones = $this->lsNotificaciones([$_COOKIE['IDE']]);
        $notifications    = null;
        $task             = null;
        
        
        foreach ($lsNotificaciones as $key) {

            // if($key['id_User']  ){  // tengo el campo agregado

            // if($key['id_User'] == $_COOKIE['IDU'] )  
                $task[] =  [

                    'color'       => sprintf('#%06X', mt_rand(0, 0xFFFFFF)),
                    'title'       => " {$key['title']} " ,
                    'time'        =>  formatSpanishDate($key['date']),
                    'description' => $key['description'],

                ];
            // }else{          
            // }         

  
        }

        // if($task)

        $notifications[] = [
            'title' => 'ERP',
            'notification' => $task
        ];

        return $notifications;
    }

    

    function deleteNotification(){

        return ['ok'=> 'ok'];
    }


}



  


// Instancia del objeto

$obj = new ctrl();
$fn = $_POST['opc'];
$encode = $obj->$fn();

echo json_encode($encode);