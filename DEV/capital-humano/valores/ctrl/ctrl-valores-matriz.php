<?php
if(empty($_POST['opc'])) exit(0);

setlocale(LC_TIME, 'es_ES.UTF-8');
date_default_timezone_set('America/Mexico_City');

require_once('../mdl/mdl-valores-matriz.php');
require_once('../../../conf/_Utileria.php');
require_once('../../../conf/coffeSoft.php');

class Valoresmatriz extends MValoresmatriz {
    function init(){
        $lsUDN     = $this-> lsUDN();
        $lsEstatus = $this-> lsStatus();
        $udnZero = ["id" => "0", "valor" => "TODAS LAS UDN"];
        array_unshift($lsUDN, $udnZero);
        return[
            'udn'      => $lsUDN,
            'estados'   => $lsEstatus,
        ];
    }

    function lsMatrizEvaluacion(){
        $__row = [];
           
        $ls = $this->lsMatrix([
                'fi'  => $_POST['fi'],
                'ff'  => $_POST['ff'],
                'udn' => $_POST['udn'],
                'employee' => $_POST['employee']
        ]);

        foreach ($ls as $key) {       
            $a   = [];
            if ($key['status'] == 1) {
                $a[] = [
                    'html'    => '<i class="icon-pencil"></i>',
                    'icon'    => 'icon-pencil',
                    'onclick' => "matrix.editMatriz({$key['id']})",
                    'class'   => 'btn btn-sm btn-outline-info me-1'
                ];

                $a[] = [
                    'html'    => '<i class="icon-cancel"></i>',
                    'onclick' => "matrix.cancelMatriz({$key['id']})",
                    'class'   => 'btn btn-sm btn-outline-danger'
                ];
            }
            
            $evaluadores   = $this->listEvaluadores([$key['id']]);
            $lsEvaluadores = '';

            foreach ($evaluadores as $evaluador) {
                $lsEvaluadores .= ($lsEvaluadores ? ', <br>' : '') . "<span class='text-[.7rem]'>{$evaluador['Nombres']}</span>";
            }

            $__row[] = [
                'id'          => $key['id'],
                'fecha'       => formatSpanishDate($key['date']),
                'udn'         => $key['UDN'],
                'evaluados'   => $key['Nombres'],
                'evaluadores' => ['html'=>$lsEvaluadores,'class' => ' text-gray-500 space-y-2  bg-white text-[.8rem]'],
                'a'           => $a
            ];
        }
    
        return [
            "row"   => $__row,
        ];
    }

    function lsEvaluators() {
        $__row = [];

        $fi  = $_POST['fi'];
        $ff  = $_POST['ff'];

        $emp = $_POST['employee'];

        $ls = $this->lsMatrix([
            'fi'       => $fi,
            'ff'       => $ff,
            'employee' => $emp
        ]);

        foreach ($ls as $key) {
            $evaluadores = $this->listEvaluadores([$key['id']]);

            foreach ($evaluadores as $evaluador) {
                $a = [];

                if ($key['status'] == 1) {
                    $a[] = [
                        'html'    => '<i class="icon-trash"></i> Eliminar',
                        'onclick' => "matrix.deleteEvaluator({$key['id']}, {$evaluador['id_evaluator']}, {$key['id_evaluated']})",
                        'class'   => 'btn btn-outline-danger btn-sm'
                    ];
                }

                $__row[] = [
                    'id'          => $key['id'],
                    'udn'         => $evaluador['UDN'] ?? '',
                    'evaluadores' => $evaluador['Nombres'] ?? '',
                    'a'           => $a
                ];
            }
        }
        return [
            'row' => $__row
        ];
    }

    function getEvaluated(){
        return  $this->lsEmployedForUdn([$_POST['udn']]);
    }

    function getEvaluators(){
        return  $this->lsEmployed([$_POST['udn']]);
    }

    function getMatrix(){
        $matrix = $this->getMatrixById([$_POST['id']]);
        $evaluadores = $this->getEvaluator([$_POST['id']]);
        return [
            'matrix'      => $matrix,
            'evaluadores' => $evaluadores
        ];
    }

    function addMatrix(){
        $evaluadores = $_POST['id_evaluator'];  // Evaluadores
        unset($_POST['id_evaluator']);

        // Verificar que no haya una matriz del mismo empleado en el mes actual
        $exist = $this->existMatrix([$_POST['id_udn'], $_POST['id_evaluated']]);
        if ($exist > 0) {
            return [
                'status'  => 409,
                'message' => 'No puedes agregar una matriz para este empleado en el mes actual. ',
            ];
        }

        // Desactivar matriz anterior
        $this->updateMatrix($this->util->sql([
            'status' => "0",
            'id_evaluated' => $_POST['id_evaluated'],
        ], 1));

        // Crear matriz
        $status  = 500;
        $message = 'Error al crear la Matriz.';
        $_POST['date_create'] = date('Y-m-d H:i:s'); // Agregar fecha al post
        $create  = $this->createMatrix($this->util->sql($_POST));

        if ($create == true) {
              // Crear evaluadores
            $id_matrix = $this->maxMatrix();
            $message   = 'Error al agregar evaluadores.';
         
              // Crear array para inserción múltiple
            $evaluators = [];
            foreach ($evaluadores as $id) {
                $evaluators[] = [
                    'id_evaluator' => trim($id),
                    'id_matrix'    => $id_matrix
                ];
            }

            $add = $this->createEvaluators($this->util->sql($evaluators));
            if ($add == true) {
                $status  = 200;
                $message = 'Matriz creada correctamente.';
            }
        }

        return [
            'status'  => $status,
            'message' => $message,
        ];
    }

    function editMatrix(){
        $status  = 500;
        $message = 'Error al editar la Matriz.';

        // $isCreated = $this->isMatrixUsed($_POST['id_matrix']);
        
        // if ($isCreated == true) {
        //     $status  = 409;
        //     $message = 'No puedes editar una matriz que ya fue utilizada. ';
        // }

        // Verificar evaluadores existentes
        $evaluadoresExistentes = $this->listEvaluadores([$_POST['id_matrix']]);
        $evaluadoresRepetidos  = [];

        if (!empty($evaluadoresExistentes)) {
            $idsExistentes = array_column($evaluadoresExistentes, 'id_evaluator');

            foreach ($_POST['id_evaluator'] as $nuevo) {
                if (in_array($nuevo, $idsExistentes)) {
                    $evaluadoresRepetidos[] = $nuevo;
                }
            }
        }

        if (!empty($evaluadoresRepetidos)) {
            $status  = 409;
            $message = 'No puedes agregar evaluadores ya existentes en esta matriz.';
        } else {
            // Crear evaluadores    
            $evaluators = [];
            foreach ($_POST['id_evaluator'] as $id) {
                $evaluators[] = [
                    'id_evaluator' => trim($id),
                    'id_matrix'    => $_POST['id_matrix']
                ];
            }
            $add = $this->createEvaluators($this->util->sql($evaluators));
            if ($add == true) {
                $status  = 200;
                $message = 'Matriz editada correctamente.';
            }
            $status  = 200;
            $message = 'Evaluadores agregados correctamente';
        }

        return [
            'status'  => $status,
            'message' => $message,
            'data'    => $deleteEvaluators,
        ];
    }

    function cancelMatrix() {
        $status   = 500;
        $message  = 'Error al cancelar la matriz.';
        $idMatrix = $_POST['idMatrix'];
        $isCreated = $this->isMatrixUsed($idMatrix);

        // Validar uso
        if ($isCreated) {
            return [
                'status'  => 400,
                'message' => 'Opps❗La matriz está siendo utilizada en evaluaciones. No puede ser eliminada.'
            ];
        }
        // Desactivación lógica
        $cancel = $this->updateMatrix($this->util->sql($_POST, 1));
        if ($cancel) {
            $status  = 200;
            $message = '✅ Matriz cancelada correctamente.';
        }
        return [
            'status'  => $status,
            'message' => $message,
            $_POST['idMatrix'],
            $this->isMatrixUsed($idMatrix)
        ];
    }

    function deleteEvaluator() {
        $status   = 500;
        $message  = 'Error al eliminar el evaluador.';
        $delete = $this->deleteEvaluators($this->util->sql($_POST, 2));
        if ($delete) {
            $status  = 200;
            $message = '✅ Evaluador eliminado correctamente.';
        }
        return [
            'status'  => $status,
            'message' => $message,
        ];
    }
}


$opc = $_POST['opc'];
unset($_POST['opc']);

$obj = new Valoresmatriz();
$encode = $obj->$opc();

echo json_encode($encode);
?>

