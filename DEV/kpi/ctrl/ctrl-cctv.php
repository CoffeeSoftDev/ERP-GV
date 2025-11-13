<?php
if(empty($_POST['opc'])) exit(0);

setlocale(LC_TIME, 'es_ES.UTF-8');
date_default_timezone_set('America/Mexico_City');

require_once('../mdl/mdl-cctv.php');
require_once('../../conf/_Utileria.php');

class Cctv extends MCctv {
    // INSTANCIAS
    private $util;
    
    public function __construct() {
        $this->util = new Utileria();
    }
    
public function tbBitacora(){
    $date1 = $_POST['dates'][0];
    $date2 = $_POST['dates'][1];
    
    $tbody   = $this->lsBitacoraSuites([$date1,$date2]);
    foreach ($tbody as $key => &$value) {
        $file = explode('/',$value['archivo']);
        $archivo = ($file[0] == 'erp_files') ? '../../'.$value['archivo'] : '../../ERP/'.$value['archivo'];

        $value['archivo'] = '<a href="'.$archivo.'?t='.time().'" class="btn btn-sm btn-success" target="_blank"><i class="icon-eye"></i></a>';

        $tbody[$key][] = ['html'=>$this->util->letterDate($value['fecha'])];
        $tbody[$key][] = ['html'=>$value['suite'],'class'=>'text-center'];
        $tbody[$key][] = ['html'=>$value['archivo'],'class'=>'text-center'];

        unset($value['fecha']);
        unset($value['suite']);
        unset($value['archivo']);
    }


    $thead = "Fecha,Suite,Descarga";
    return ['thead' => $thead, 'tbody' => $tbody];

}
public function dataCCTV(){
    
    $suite = $this->cantSuites([$_POST['date']]);

    $cctv = $this->_Select([
        'table'     => $this->getFzas()."suite",
        'values'    => 'CONCAT(rutaBitacora,nameBitacora) as file',
        'where'     => 'fechaSuite,id_UDN',
        'data'      => [$_POST['date'],1]
    ])[0]['file'];

    $file = isset($cctv) ?  '../../'.$cctv : null;

    return ['file'=>$file,'suite' => $suite];
}
public function cctvFile(){
    $idE   = $_POST['idE'];
    $date  = $_POST['date'];
    
    $fecha = date_create($date); //Objeto
    $fecha_format = date_format($fecha, 'dmY');
    $year  = date_format($fecha, 'Y');
    $month = date_format($fecha, 'm');
    $day   = date_format($fecha, 'd');
    $time  = date('H:m:s');
    $mes   = ucfirst(strftime('%B', $fecha->getTimestamp()));

    $destino   = "erp_files/suiteFile/{$year}/{$month}. {$mes}/";
    $extension = end(explode('.',$_FILES['file']['name']));
    $namefile  = $fecha_format.'bitacorasuites';

    $cantidad  = $this->cantSuites([$date]);

    $table = $this->getFzas().'suite';

    $this->_Delete([
        'table'  => $table,
        'where'  => 'id_UDN,fechaSuite',
        'data'   => [$idE,$date]
    ]);
    
    $array = [
        'id_UDN'       => $idE,
        'fechaSuite'   => $date,
        'rutaBitacora' => $destino,
        'cantidad'     => $cantidad,
        'nameBitacora' => $namefile.'.'.$extension,
    ];

    $exito = $this->_Insert($this->util->sql($array),$table);
    if($exito == true) return $this->util->upload_file($_FILES['file'],'../../../'.$destino,$namefile);
}   

}//FIN DE LA CLASE


$opc = $_POST['opc'];
unset($_POST['opc']);

$obj = new Cctv();
$encode = $obj->$opc();

echo json_encode($encode);
?>