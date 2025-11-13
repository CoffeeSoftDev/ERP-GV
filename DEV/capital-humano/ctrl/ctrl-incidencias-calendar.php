<?php
if(empty($_POST['opc'])) exit(0);


require_once('../mdl/mdl-incidencias-calendar.php');
$obj = new Incidenciascalendar;

$encode = [];
switch ($_POST['opc']) {
case 'tbCalendar':
        $year = date('Y');
        $sql = $obj->tbIncidenciasCalendar();

        $encode['table'] = ['id'=>'tbCalendar'];
        $encode['thead'] = ['Inicio del período','Final del período','eliminar'];
        $encode['tbody'] = [];
        foreach ($sql as $value) {
            $encode['tbody'][] = [
                [
                    'html'=>$value['inicio'],
                    'class'=>'text-center'
                ],
                [
                    'html'=>$value['fin'],
                    'class'=>'text-center'
                ],
                [
                    'html'=>'<button class="btn btn-sm btn-outline-danger" onClick="delete_periodo('.$value['id'].')"><i class="icon-trash"></i></button>',
                    'class'=>'text-center'
                ]
            ];
        }
    break;
case 'new_period':
        $dates  = explode(',',$_POST['dates']);
        $encode = false;

        if ( $obj->existencia($dates) == true ) {
            $fecha   = strtotime($dates[1]);
            $dates[] = date('Y',$fecha);
            $encode  = $obj->new_period($dates);
        }
    break;
case 'delete':
        $encode = $obj->delete_periodo([$_POST['id']]);
    break;
}

echo json_encode($encode);
?>