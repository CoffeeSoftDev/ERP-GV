<?php
// mdl-tabulation.php
require_once '../../../conf/_CRUD.php';
require_once '../../../conf/_Utileria.php';

class mdl extends CRUD {
    protected $util;
    protected $bd;

    public function __construct(){
        $this->util = new Utileria;
        $this->bd = 'rfwsmqex_erp.';
    }

    function getEntities($array){
      $values = [
            'val_tabulation_calification.id ',
            'val_tabulation_calification.te',
            'val_tabulation_calification.pr',
            'val_tabulation_calification.ap',
            'val_tabulation_calification.ps',
            'val_tabulation_calification.calf',
            'val_tabulation_calification.calf_manual',
            'val_tabulation.stado',
            'val_tabulation_calification.people_count',
            'empleados.Nombres as name'
        ];

        $innerjoin = [
            'val_tabulation' => 'val_tabulation.id = val_tabulation_calification.id_tabulation',
            'rfwsmqex_gvsl_rrhh.empleados' => 'val_tabulation_calification.id_evaluated = empleados.idEmpleado'
        ];

        $where = [

            'id_tabulation = ?'
        ];

        return $this->_Select([
            'table'     => 'val_tabulation_calification',
            'values'    => $values,
            'innerjoin' => $innerjoin,
            'where'     => $where,
            'order'     => ['DESC' => 'empleados.Nombres'],
            'data'      => array_values($array),
        ]);
    }

    function getById($array){
        return $this->_Select([
            'table' => 'val_tabulation_calification',
            'values' => '*',
            'where' => 'id = ?',
            'data' => $array
        ])[0];
    }

    function update($array){
        return $this->_Update([
            'table' => 'val_tabulation',
            'values' => $array['values'],
            'where' => $array['where'],
            'data' => $array['data'],
        ]);
    }

    // Calificacion.
    function updateCalificacion($array){
        return $this->_Update([
            'table' => 'val_tabulation_calification',
            'values' => $array['values'],
            'where' => $array['where'],
            'data' => $array['data'],
        ]);
    }

    function getEmployedByID($array){
        $query = "
            SELECT
                Nombres,
                FullName,
                idEmpleado,
                Telefono_Movil,
                idUser
            FROM
                rfwsmqex_gvsl_rrhh.empleados
            LEFT JOIN
                rfwsmqex_erp.usuarios ON rfwsmqex_erp.usuarios.usr_empleado  = rfwsmqex_gvsl_rrhh.empleados.idEmpleado
            WHERE idEmpleado = ?
        ";
        return $this->_Read($query, $array)[0];
    }
}
