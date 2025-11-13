<?php
session_start();
if (empty($_POST['opc'])) exit(0);
// conf. api
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
require_once '../mdl/mdl-tabulacion.php';
require_once('../../../conf/coffeSoft.php');

$encode = [];

class ctrl extends mdl {

    public function init() {

        $lsUDN     = $this->lsUDN();
        $lsPeriods = $this->getPeriods();
        // $lsStatus = $this->lsStatus();

        return [
            'udn'     => $lsUDN,
            'periods' => $lsPeriods,
            // 'status' => $lsStatus
        ];

    }

    function list() {
        $instancia = 'app';
        $__row     = [];

        $ls = $this->getEntities([
            'date_init' => $_POST['fi'],
            'date_end'  => $_POST['ff'],
            'udn'       => $_POST['udn'],
            'stado'     => $_POST['status']
        ]);

        foreach ($ls as $key) {

            $a   = [];

            $Evaluted = $this->getEvaluateds(['id_tabulation' => $key['id'],]);

            if($key['stado'] == 2){

                $a[] = [

                    'html'    => '<i class="icon-address-book-o"></i> Revisar',
                    'onclick' => "{$instancia}.revisar({$key['id']})",
                    'class'   => 'btn btn-sm btn-outline-primary w-28 '
                ];


            } else{

                $a[] = [
                    'html'    => '<i class="icon-play"></i> Reanudar',
                    'onclick' => "{$instancia}.reanudar({$key['id']},'".$key['udn']."')",
                    'class'   => 'btn btn-sm btn-outline-info  w-28'
                ];

            }

            $__row[] = [

                'id'                => $key['id'],
                'UDN'               => $key['udn'],
                'Periodo asignado'  => $key['period_name'],
                'Fecha de creación' => formatSpanishDate($key['date_init'], 'completa'),
                'No Evaluados'      => count($Evaluted),
                'Status'            => getEstatus($key['stado']),
                'a'                 => $a
            ];


        }

        return [
            "row" => $__row,
            "ls"  => $ls,
            "frm_head" => '
                <div class="flex flex-col">
                    <span class="text-lg font-bold">Tabulaciones</span>
                    <span class="text-gray-600 text-sm">Listado de tabulaciones por unidad de negocio y temporada</span>
                </div>
            ',

            "util" => [
            'date_init' =>  $_POST['fi'],
            'date_end'  => $_POST['ff'],]
        ];

    }

    function lsTabulationEvaluados(){

        $listEvaluateds  = $this->getEvaluated([$_POST['id_period']]);
        $valuesCorporate = $this->getValuesCorporate();



    }

    function addTabulacion() {

        $status  = 500;
        $message = 'No se pudo registrar la tabulación.';

        // Validar si ya existe tabulación previa
        if ($this->existsTabulation([$_POST['id_period'], $_POST['id_UDN']])) {

            return [
                'status'  => 500,
                'message' => 'Ya existe una tabulación para ese periodo y Unidad de Negocio. Verifica en la tabla.'
            ];

        }

        // Preparar datos de creación
        $_POST['date_creation'] = date('Y-m-d H:i:s');
        $_POST['stado']         = 1;

        $data         = $this->util->sql($_POST);
        $create       = $this->createTabulation($data);
        $idTabulacion = $this->maxTabulation([$_POST['id_UDN']]);

        // Si se creó correctamente la tabulación
        if ($create) {

            $listEvaluateds  = $this->getEvaluated([$_POST['id_period']]);
            $valuesCorporate = $this->getValuesCorporate();
            $data_scores     = [];

            foreach ($listEvaluateds as $evaluated) {

                $campos = [
                    'id_tabulation' => $idTabulacion,
                    'id_evaluated'  => $evaluated['id_evaluated'],
                ];
                $values = [];
                $sum    = 0;
                $count  = 0;

                $evaluations = $this -> getCountEvaluators([
                              $_POST['id_period'],
                        $evaluated['id_evaluated'],
                ]);

                $values['people_count'] =$evaluations['total'];

                foreach ($valuesCorporate as $value) {
                    $valor = $this->getEvaluatedScores([
                        $_POST['id_period'],
                        $evaluated['id_evaluated'],
                        $value['id']
                    ]);

                    $score = round(floatval($valor['promedio']), 2);
                    $values[$value['shortName']] = $score;

                    if ($score > 0) {
                        $sum += $score;
                        $count++;
                    }
                }

                $values['calf'] = ($count > 0) ? round($sum / $count, 2) : 0;
                $row            = array_merge($campos, $values);
                $data_scores[]  = $row;
                $data_sql       = $this->util->sql($row);

                $this->insertEvaluatedScore($data_sql);
            }

            $status  = 200;
            $message = 'Tabulación y calificaciones generadas correctamente.';
        }

        return [
            'status'  => $status,
            'message' => $message,
            'id'      => $idTabulacion
        ];
    }

    function get() {
        $get = $this->getById([$_POST['id']]);

        return [
            'status'  => $get ? 200 : 500,
            'message' => $get ? 'Datos obtenidos.' : 'Error al obtener.',
            'data'    => $get
        ];
    }

    function getPeriods() {

        $get = $this->getPeriodsByID([$_POST['udn']]);

        return [

            'status'  => $get ? 200 : 500,
            'message' => $get ? 'Datos obtenidos.' : 'Error al obtener.',
            'data'    => $get
        ];
    }

    function revisar() {
        // $edit = $this->update($this->util->sql($_POST, 1));
        $edit =true;
        return [
            'status'  => $edit ? 200 : 500,
            'message' => $edit ? 'Editado correctamente.' : 'Error al editar.'
        ];
    }

    function reanudar() {
        // lógica personalizada si se requiere actualizar status
        return [ 'status' => 200 ];
    }



}

function getEstatus($idstatus) {
    // 🔵 Definimos los estados con sus respectivos emojis y etiquetas
    $estados = [
        1 => '⏳  ACTIVO',
        2 => '✅ FINALIZADO',

    ];

    // 📌 Verificamos si el estado existe en la lista, de lo contrario, asignamos un valor por defecto
    return $estados[$idstatus] ?? '❓ DESCONOCIDO';
}

$obj    = new ctrl();
$fn     = $_POST['opc'];
$encode = $obj->$fn();
echo json_encode($encode);
