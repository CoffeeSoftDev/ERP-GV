<?php
require_once ('../../conf/_CRUD.php');
class Calendarizacion extends CRUD
{

    private $bd2;
    private $bd;

    public function __construct()
    {
        $this->bd = "rfwsmqex_erp.";
    }

    // Notifications.
    function lsNotifyByUser($array) {

        $query = "
            SELECT
                notifications.idNoti,
                notifications.title,
                notifications.description,
                notifications.creation,
                notifications.id_Module,
                notifications.id_Area,
                notifications.id_UDN,
                noti_user.idNoti,
                noti_user.idUser,
                noti_user.estado,
                noti_user.create,
                noti_user.view,
                rh_area_udn.id_Area
            FROM
            {$this->bd}notifications
            INNER JOIN {$this->bd}noti_user ON noti_user.idNoti = notifications.idNoti
            INNER JOIN {$this->bd}rh_area_udn ON notifications.id_Area = rh_area_udn.idAreaUDN
            WHERE notifications.id_UDN = ? ";

        return $this->_Read($query, $array);


    }

    function lsNotificaciones($array){

    $query = "
        SELECT
            notifications.idNoti,
            notifications.title,
            notifications.description,
            date_format(creation,'%Y-%m-%d') as date,
            notifications.id_Module,
            notifications.id_Area,
            notifications.id_UDN,
            rh_area_udn.idAreaUDN,
            rh_area_udn.id_Area,
            rh_area_udn.id_UDN,
            rh_area_udn.stado,
            rh_area.idArea,
            rh_area.Area,
            rh_area.Abreviatura,
            rh_area.area_estado
        FROM
            {$this->bd}notifications
        INNER JOIN {$this->bd}rh_area_udn ON notifications.id_Area = rh_area_udn.idAreaUDN
        INNER JOIN {$this->bd}rh_area ON rh_area_udn.id_Area = rh_area.idArea

        ORDER BY idNoti desc
        LIMIT 5 
        
        ";

    return $this->_Read($query, $array);

}




}