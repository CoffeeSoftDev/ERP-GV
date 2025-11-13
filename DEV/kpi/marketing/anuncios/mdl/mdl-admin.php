<?php
require_once '../../../../conf/_CRUD.php';
require_once '../../../../conf/_Utileria.php';
session_start();

class mdl extends CRUD {
    protected $util;
    public $bd;

    public function __construct() {
        $this->util = new Utileria;
        $this->bd = "rfwsmqex_marketing.";
    }

    // Tipo Anuncio Methods.

    function listTypes($array) {
        return $this->_Select([
            'table'  => $this->bd . 'tipo_anuncio',
            'values' => "
                id,
                nombre as valor,
                active
            ",
            'where'  => 'active = ?',
            'order'  => ['ASC' => 'nombre'],
            'data'   => $array
        ]);
    }

    function getTypeById($array) {
        return $this->_Select([
            'table'  => $this->bd . 'tipo_anuncio',
            'values' => '*',
            'where'  => 'id = ?',
            'data'   => $array
        ])[0];
    }

    function createType($array) {
        return $this->_Insert([
            'table'  => $this->bd . 'tipo_anuncio',
            'values' => $array['values'],
            'data'   => $array['data']
        ]);
    }

    function updateType($array) {
        return $this->_Update([
            'table'  => $this->bd . 'tipo_anuncio',
            'values' => $array['values'],
            'where'  => 'id = ?',
            'data'   => $array['data']
        ]);
    }

    // Clasificacion Anuncio Methods

    function listClassifications($array) {
        return $this->_Select([
            'table'  => $this->bd . 'clasificacion_anuncio',
            'values' => "
                id,
                nombre as valor,
                active
            ",
            'where'  => 'active = ?',
            'order'  => ['ASC' => 'nombre'],
            'data'   => $array
        ]);
    }

    function getClassificationById($array) {
        return $this->_Select([
            'table'  => $this->bd . 'clasificacion_anuncio',
            'values' => '*',
            'where'  => 'id = ?',
            'data'   => $array
        ])[0];
    }

    function createClassification($array) {
        return $this->_Insert([
            'table'  => $this->bd . 'clasificacion_anuncio',
            'values' => $array['values'],
            'data'   => $array['data']
        ]);
    }

    function updateClassification($array) {
        return $this->_Update([
            'table'  => $this->bd . 'clasificacion_anuncio',
            'values' => $array['values'],
            'where'  => 'id = ?',
            'data'   => $array['data']
        ]);
    }
}
