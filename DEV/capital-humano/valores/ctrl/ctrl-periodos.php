<?php
if(empty($_POST['opc'])) exit(0);

setlocale(LC_TIME, 'es_ES.UTF-8');
date_default_timezone_set('America/Mexico_City');

require_once('../mdl/mdl-periodos.php');

require_once('../../../conf/coffeSoft.php');


class PeriodController extends mdl {

    function init() {
        $lsUDN     = $this-> lsUDN();
        $udnZero = ["id" => "0", "valor" => "TODAS LAS UDNS"];
        array_unshift($lsUDN, $udnZero);
        return [
            'udn' => $lsUDN
        ];
    }

    function listPeriods() {

        $instancia = 'app';
        $__row     = [];
        $fi        = $_POST['fi'];
        $ff        = $_POST['ff'];

        $ls = $this->getPeriods([
            'udn'    => $_POST['udn'],
            'fi'     => $fi, 'ff' => $ff ,
            'status' => $_POST['status']
        ]);

        foreach ($ls as $key) {
            $a   = [];

            if($key['status'] == 1){ // en proceso

                $a[] = [
                    'html'    => '<i class="icon-pencil"></i>',
                    'icon'    => 'icon-pencil',
                    'onclick' => "{$instancia}.edit({$key['id']})",
                    'class'   => 'btn btn-sm btn-outline-info me-1'
                ];

                $a[] = [
                    'html'    => '<i class="icon-cancel"></i>',
                    'onclick' => "{$instancia}.cancel({$key['id']})",
                    'class'   => 'btn btn-sm btn-outline-danger'
                ];
            }else{
                $a[] = [
                    'html'    => '<i ></i>',
                    'class'   => 'text-green-600'
                ];
            }


            $__row[] = [

                'id'                => $key['id'],
                'Unidad de negocio' => $key['UDN'],
                'periodo'           => $key['name'],

                'Fecha inicial'     => formatSpanishDate($key['start_date']),
                'Fecha final'       => formatSpanishDate($key['end_date']),
                'Estado'            => status($key['status']),
                'a'                 => $a
            ];
        }

        return [
            'thead' => '',
            'row'   => $__row,
            $ls

        ];
    }

    function getPeriod() {
        $status = 500;
        $message = 'No se pudo obtener los datos.';
        $get = $this->getPeriodById([$_POST['id']]);

        if ($get) {
            $status = 200;
            $message = 'Datos obtenidos correctamente.';
        }

        return [
            'status'  => $status,
            'message' => $message,
            'data'    => $get[0]
        ];
    }

    function addPeriod() {
        $status = 500;
        $message = 'Error al agregar el periodo.';

         // Validar si ya existe un periodo activo para esa UDN
        if ($this->existsActivePeriod($_POST['id_UDN']) > 0) {
            return [
                'status'  => 400,
                'message' => '⚠️ Ya existe un periodo activo para esta UDN. '
            ];
        }


        $create = $this->createPeriod($this->util->sql($_POST));

        if ($create) {
            $status = 200;
            $message = 'Agregado correctamente.';
        }

        return [
            'status'  => $status,
            'message' => $message,
        ];
    }


    function editPeriod() {
        $status  = 500;
        $message = 'Error al editar el periodo.';

        $edit = $this->updatePeriod($this->util->sql($_POST, 1));
        if ($edit) {
            $status = 200;
            $message = 'Periodo editado correctamente.';
        }

        return [
            'status'  => $status,
            'message' => $message
        ];
    }


    function deletePeriod() {
        $id = $_POST['id'];
        $used = $this->existPeriod($id);

        if ($used > 0) {
            return [
                'status'  => 400,
                'message' => 'No se puede eliminar. El periodo ya ha sido utilizado en evaluaciones.'
            ];
        }

        $status          = 500;
        $message         = 'Error al desactivar periodo.';

        $sql    = $this->util->sql($_POST, 1);
        $delete = $this->updatePeriod($sql);
        if ($delete) {
            $status = 200;
            $message = 'Periodo eliminado correctamente.';
        }

        return [
            'status'  => $status,
            'message' => $message
        ];
    }


}

function status($idEstado){
    switch ($idEstado) {

        case 1:
            return '<span class=" w-32 text-xs font-semibold mr-2 px-3 py-1 ">⏳ EN PROCESO  </span>';
        case 2:
            return '<span class=" w-32 text-xs font-semibold mr-2 px-3 py-1 ">✅ FINALIZADO  </span>';
        default:
            return '<span class=" w-32 text-xs font-semibold mr-2 px-3 py-1 ">❌ CANCELADO   </span>';

    }
}


// Instancia del objeto
$opc = $_POST['opc'];
unset($_POST['opc']);

$obj = new PeriodController();
$encode = $obj->$opc();

echo json_encode($encode);
?>
