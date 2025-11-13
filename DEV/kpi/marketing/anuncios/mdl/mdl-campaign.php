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

    // Campaign Methods.

    function listCampaigns($array) {
        $leftjoin = [
            $this->bd . 'red_social' => 'campaña.red_social_id = red_social.id'
        ];

        return $this->_Select([
            'table'    => $this->bd . 'campaña',
            'values'   => "
                campaña.id,
                campaña.nombre,
                campaña.estrategia,
                DATE_FORMAT(campaña.fecha_creacion, '%d/%m/%Y %H:%i') as fecha_creacion,
                campaña.udn_id,
                campaña.red_social_id,
                red_social.nombre as red_social,
                campaña.active
            ",
            'leftjoin' => $leftjoin,
            'where'    => 'campaña.active = ? AND campaña.udn_id = ?',
            'order'    => ['DESC' => 'campaña.id'],
            'data'     => $array
        ]);
    }

    function getCampaignById($array) {
        return $this->_Select([
            'table'  => $this->bd . 'campaña',
            'values' => '*',
            'where'  => 'id = ?',
            'data'   => $array
        ])[0];
    }

    function getAnnouncementsByCampaign($array) {
        return $this->_Select([
            'table'  => $this->bd . 'anuncio',
            'values' => '*',
            'where'  => 'campaña_id = ?',
            'data'   => $array
        ]);
    }

    function createCampaign($array) {
        return $this->_Insert([
            'table'  => $this->bd . 'campaña',
            'values' => $array['values'],
            'data'   => $array['data']
        ]);
    }

    function updateCampaign($array) {
        return $this->_Update([
            'table'  => $this->bd . 'campaña',
            'values' => $array['values'],
            'where'  => $array['where'],
            'data'   => $array['data']
        ]);
    }

    function getLastCampaignId() {
        $query = "SELECT MAX(id) as last_id FROM {$this->bd}campaña";
        $result = $this->_Read($query, []);
        return $result[0]['last_id'] ?? 0;
    }

    // Announcement Methods
    function listAnnouncements($campaña_id = null, $udn_id = null, $red_social_id = null) {
        $query = "
            SELECT
                anuncio.id AS id,
                anuncio.nombre AS anuncio_nombre,
                DATE_FORMAT(anuncio.fecha_inicio, '%d/%m/%Y') AS fecha_inicio,
                DATE_FORMAT(anuncio.fecha_fin, '%d/%m/%Y') AS fecha_fin,
                anuncio.imagen,
                campaña.nombre AS campaña_nombre,
                tipo_anuncio.nombre AS tipo_nombre,
                clasificacion_anuncio.nombre AS clasificacion_nombre,
                anuncio.campaña_id,
                campaña.udn_id,
                campaña.red_social_id,
                anuncio.fecha_resultado,
                anuncio.total_monto,
                anuncio.total_clics
            FROM {$this->bd}anuncio
            LEFT JOIN {$this->bd}tipo_anuncio ON anuncio.tipo_id = tipo_anuncio.id
            LEFT JOIN {$this->bd}clasificacion_anuncio ON anuncio.clasificacion_id = clasificacion_anuncio.id
            LEFT JOIN {$this->bd}campaña ON anuncio.campaña_id = campaña.id
            WHERE 1 = 1
        ";

        $data = [];

        if (!empty($udn_id)) {
            $query .= " AND campaña.udn_id = ?";
            $data[] = $udn_id;
        }

        if (!empty($red_social_id)) {
            $query .= " AND campaña.red_social_id = ?";
            $data[] = $red_social_id;
        }

        $query .= " ORDER BY anuncio.campaña_id DESC, anuncio.id ASC";

        return $this->_Read($query, $data);

    }

    function getAnnouncementById($array) {
        return $this->_Select([
            'table'  => $this->bd . 'anuncio',
            'values' => '*',
            'where'  => 'id = ?',
            'data'   => $array
        ])[0];
    }

    function createAnnouncement($array) {
        return $this->_Insert([
            'table'  => $this->bd . 'anuncio',
            'values' => $array['values'],
            'data'   => $array['data']
        ]);
    }

    function updateAnnouncement($array) {
        return $this->_Update([
            'table'  => $this->bd . 'anuncio',
            'values' => $array['values'],
            'where'  => $array['where'],
            'data'   => $array['data']
        ]);
    }

    function deleteAnnouncement($array) {
        return $this->_Delete([
            'table'  => $this->bd . 'anuncio',
            'where'  => $array['where'],
            'data'   => $array['data']
        ]);
    }

    function maxAnnouncement() {
        return $this->_Select([
            'table'  => $this->bd . 'anuncio',
            'values' => 'MAX(id) AS id'
        ])[0]['id'];
    }


    // Catalog lists
    function lsTypes($array = []) {
        return $this->_Select([
            'table'  => $this->bd . 'tipo_anuncio',
            'values' => 'id, nombre as valor',
            'where'  => 'active = 1',
            'order'  => ['ASC' => 'id']
        ]);
    }

    function lsClassifications($array = []) {
        return $this->_Select([
            'table'  => $this->bd . 'clasificacion_anuncio',
            'values' => 'id, nombre as valor',
            'where'  => 'active = 1',
            'order'  => ['ASC' => 'nombre']
        ]);
    }

    function lsRedSocial($array = []) {
        return $this->_Select([
            'table'  => $this->bd . 'red_social',
            'values' => 'id, nombre as valor, color',
            'where'  => 'active = 1',
            'order'  => ['ASC' => 'nombre']
        ]);
    }

    function lsUDN($array = []) {
         $query = "
            SELECT idUDN AS id, UDN AS valor
            FROM udn
            WHERE Stado = 1 AND idUDN NOT IN (8, 10, 7)
            ORDER BY UDN DESC
        ";
        return $this->_Read($query, null);
    }

    function countAnnouncementsWithoutResults($array) {
        $query = "
            SELECT COUNT(id) AS count
            FROM {$this->bd}anuncio
            WHERE fecha_resultado IS NULL
            AND campaña_id = ?
        ";
        $result = $this->_Read($query, $array);
        return $result[0]['count'] ?? 0;
    }
}
