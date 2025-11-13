
<?php
require_once('../../conf/_CRUD.php');

class Mercadotecnia extends CRUD{
    public $bd;
    public $bd2;

    public function __construct(){
        $this->bd = "rfwsmqex_gvsl_finanzas.";
        $this->bd2 = "rfwsmqex_gvsl_finanzas.";
    }
    
    function lsUDN(){
        $query = "
        SELECT idUDN AS id, UDN AS valor FROM udn 
        WHERE Stado = 1 AND idUDN != 10 AND idUDN != 8  
        ORDER BY UDN ASC";
        return $this->_Read($query, null);
    }

    // function lsUDN(){
    //     return $this->_Select([
    //         'table'  => 'udn',
    //         'values' => 'idUDN AS id, UDN AS valor',
    //         'where'  => 'Stado = 1 AND idUDN <> 8 ',
    //         'order' => ['DESC'=>'idUDN']
    //     ]);
    // }

    function lsVentas($array){

        $sql = "
        SELECT 
            idarea as id,
            arearestaurant,
            id_udn 
        FROM 
            rfwsmqex_gvsl_finanzas.soft_area_restaurant 
        WHERE 
        id_udn = ?
        AND   id_kpi = ?
        ORDER BY id_kpi asc ";

        return $this->_Read($sql, $array);
        
    }

    function lsTablero(){

        $sql = "
        SELECT 
          *
        FROM rfwsmqex_gvsl_finanzas.soft_tablero_kpi ";
        return $this->_Read($sql, null);

    }

    function lsTotalVentas($array){
        $total = 0;

        $query = "

            SELECT
                SUM(soft_ventas.total) as total,
                soft_ventas.id_venta,
                soft_ventas.soft_ventas_fecha,
                soft_ventas.id_area
            FROM
                rfwsmqex_gvsl_finanzas.soft_ventas
            WHERE MONTH(soft_ventas_fecha) = ? AND 
            YEAR(soft_ventas_fecha) = ?
            AND id_area = ?
        
        
        ";
        $sql  = $this->_Read($query, $array);

        foreach ($sql as $key ) {
            $total = $key['total'];
        }

        return $total;
        
    }


    function lsClientes($array) {
        $total = 0;

        $query = "

            SELECT

                soft_ventas.id_venta,
                soft_ventas.soft_ventas_fecha,
                soft_ventas.id_area,
                SUM(soft_ventas.personas) as personas
            FROM
            rfwsmqex_gvsl_finanzas.soft_ventas
            WHERE MONTH(soft_ventas_fecha) = ? AND 
            YEAR(soft_ventas_fecha) = ?
            AND id_area = ?

        
        
        ";
        $sql = $this->_Read($query, $array);

        foreach ($sql as $key) {
            $total = $key['personas'];
        }

        return $total;

    }

    function existe_folio($array){
        $folio = 0;
        $contador_ticket = 0;

        $query = "
            SELECT
                id_folio,
                file_productos_vendidos,
                file_ventas_dia,
                monto_productos_vendidos,
                monto_ventas_dia
            FROM
                rfwsmqex_gvsl_finanzas.soft_folio
            WHERE 
                date_format(fecha_folio,'%Y-%m-%d') = ?
            AND id_udn = ?
        ";

        $sql = $this->_Read($query, $array);
    

        return $sql;
    }
    
    function getVentas($array){
         $folio = 10;

        $query ="
            SELECT
            soft_ventas.id_area,
            SUM(soft_ventas.total) total,
            soft_ventas.soft_folio,
            soft_ventas.soft_ventas_fecha
            FROM
            rfwsmqex_gvsl_finanzas.soft_ventas

            WHERE soft_folio = ?
        ";

        $sql = $this->_Read($query, $array);


        foreach ($sql as $key) {
        $folio = $key['total'];
        }

        return $folio;
    }


}

class Kpismerca extends CRUD{

    public $bd2;
    public $bd;

    public function __construct() {
        $this->bd2 = "rfwsmqex_gvsl_finanzas.";
        $this->bd = "rfwsmqex_gvsl_finanzas.";
    }

   function lsUDN(){
        $query = "
        SELECT idUDN AS id, UDN AS valor FROM udn 
        WHERE Stado = 1 AND idUDN != 10 AND idUDN != 8  AND idUDN != 7 
        ORDER BY UDN ASC";
        return $this->_Read($query, null);
    }

    // Ingresos  --
    
    function ingresosMensuales($array){

        $query = "
            SELECT  
                SUM(Hospedaje) as totalHospedaje,
                SUM(AyB) as totalAyB,
                SUM(alimentos) as totalAlimentos,
                SUM(bebidas) as totalBebidas,
                SUM(Diversos) as totalDiversos,
                (SUM(Hospedaje) + SUM(AyB) + SUM(Diversos)) AS totalGeneral,
                (SUM(alimentos) + SUM(bebidas) ) AS totalGralAyB,
                SUM(noHabitaciones) as totalHabitaciones
            FROM
            {$this->bd}soft_folio
            INNER JOIN {$this->bd}soft_restaurant_ventas ON soft_restaurant_ventas.soft_folio = soft_folio.id_folio
            WHERE id_udn = ? 
            AND YEAR(fecha_folio) = ?
            AND MONTH(fecha_folio) = ? 
            ORDER BY fecha_folio asc

    
        ";

        $sql = $this->_Read($query, $array);

        return $sql[0];
    }
    function getIngresosDayOfWeek($array){

        $query = "
            SELECT
                Hospedaje,
                AyB,
                Diversos,
                noHabitaciones,
                alimentos,
                bebidas,
                 (alimentos + bebidas) as totalGral,

                 CASE 
                    WHEN noHabitaciones != 0 THEN alimentos / noHabitaciones 
                    ELSE 0 
                
                END AS promedio_alimentos,
                
                CASE 
                    WHEN noHabitaciones != 0 THEN bebidas / noHabitaciones 
                    ELSE 0 
                
                END AS promedio_bebidas,

             
               
                (Hospedaje + AyB + Diversos) as total,

                DATE_FORMAT(fecha_folio,'%Y-%m-%d') as fecha,
                soft_folio.fecha_folio
            FROM
            {$this->bd}soft_folio
            INNER JOIN {$this->bd}soft_restaurant_ventas ON soft_restaurant_ventas.soft_folio = soft_folio.id_folio
            WHERE id_udn = ? 
            AND YEAR(fecha_folio) = ?
            AND MONTH(fecha_folio) = ? 
            AND DAYOFWEEK(fecha_folio) = ?
            ORDER BY fecha_folio asc

    
        ";

       return $this->_Read($query, $array);

       
    }

    function ingresoPorDia($array){

        $query = "
            SELECT  
                SUM(Hospedaje) as totalHospedaje,
                SUM(alimentos) as totalAlimentos,
                SUM(bebidas) as totalBebidas,
                SUM(AyB) as totalAyB,
                SUM(Diversos) as totalDiversos,
                (SUM(Hospedaje) + SUM(AyB) + SUM(Diversos)) AS totalGeneral,
                SUM(noHabitaciones) as totalHabitaciones,
                COUNT(DISTINCT fecha_folio) as totalDias

            FROM
            {$this->bd}soft_folio
            INNER JOIN {$this->bd}soft_restaurant_ventas ON soft_restaurant_ventas.soft_folio = soft_folio.id_folio
            WHERE id_udn = ? 

            
            AND YEAR(fecha_folio)      = ?
            AND MONTH(fecha_folio)     = ?
            AND DAYOFWEEK(fecha_folio) = ?

            ORDER BY fecha_folio asc

    
        ";

        $sql = $this->_Read($query, $array);

        return $sql[0];
    }

    function lsIngresosAyB($array){

        $query = "

            SELECT

            SUM(soft_productosvendidos.ventatotal) as ventaTotal

            FROM
            rfwsmqex_gvsl_finanzas.soft_productos
            INNER JOIN rfwsmqex_gvsl_finanzas.soft_productosvendidos ON soft_productosvendidos.id_productos = soft_productos.id_Producto
            INNER JOIN rfwsmqex_gvsl_finanzas.soft_grupoc_erp ON soft_productos.id_grupoc = soft_grupoc_erp.idgrupo
            INNER JOIN rfwsmqex_gvsl_finanzas.soft_folio ON soft_productosvendidos.idFolioRestaurant = soft_folio.id_folio
            WHERE soft_productos.id_udn = 1
            AND MONTH(fecha_folio) = ? AND YEAR(fecha_folio) = 2024 AND idgrupo <> 11


        ";

        return $this->_Read($query, $array);

    }

    function lsIngresosHospedaje($array){

        $query = "
        SELECT
            SUM(soft_productosvendidos.ventatotal) AS ventaTotal
        FROM
        rfwsmqex_gvsl_finanzas.soft_productos
        INNER JOIN rfwsmqex_gvsl_finanzas.soft_productosvendidos ON soft_productosvendidos.id_productos = soft_productos.id_Producto
        INNER JOIN rfwsmqex_gvsl_finanzas.soft_folio ON soft_productosvendidos.idFolioRestaurant = soft_folio.id_folio
        INNER JOIN rfwsmqex_gvsl_finanzas.soft_grupoc ON soft_productosvendidos.grupo = soft_grupoc.idgrupo
        WHERE
        soft_productos.id_udn = 1 AND
        MONTH(fecha_folio) = ? AND
        YEAR(fecha_folio) = 2024 
        AND soft_grupoc.grupo = 27


        ";

        return $this->_Read($query, $array);

    }

    function lsIngresosGRAL($array){

        $query = "

        SELECT
        SUM(soft_productosvendidos.ventatotal) AS ventaTotal
        FROM
        rfwsmqex_gvsl_finanzas.soft_productos
        INNER JOIN rfwsmqex_gvsl_finanzas.soft_productosvendidos ON soft_productosvendidos.id_productos = soft_productos.id_Producto
        INNER JOIN rfwsmqex_gvsl_finanzas.soft_folio ON soft_productosvendidos.idFolioRestaurant = soft_folio.id_folio
        INNER JOIN rfwsmqex_gvsl_finanzas.soft_grupoc ON soft_productosvendidos.grupo = soft_grupoc.idgrupo
        WHERE
        soft_productos.id_udn = 1 AND
        MONTH(fecha_folio) = ? AND
        YEAR(fecha_folio) = 2024 
       


        ";

        return $this->_Read($query, $array);

    }


    // Historial de ventas  ---

    function getFolio($array){
    
        $query ="

        SELECT
            id_folio,
            file_productos_vendidos,
            file_ventas_dia,
            monto_productos_vendidos,
            monto_ventas_dia

        FROM
            {$this->bd}soft_folio
        WHERE 
            date_format(fecha_folio,'%Y-%m-%d') = ?
         AND id_udn = ? ";

        $sql = $this->_Read($query, $array);

        return $sql[0];
    }

    function getsoft_ventas($array){

        $query = "
            SELECT
                soft_folio.id_folio,
                soft_folio.fecha_folio,
                soft_folio.id_udn,
                
                alimentos,
                bebidas,
                guarniciones,
                domicilio,

                sales,

                
                (alimentos + bebidas) as totalAyB,


                id_venta,
                noHabitaciones,
                Hospedaje,
                AyB,
                Diversos,
                soft_restaurant_ventas.RupturaHabitaciones,
                soft_restaurant_ventas.costoDiversos,

                CASE 
                    WHEN noHabitaciones != 0 THEN AyB / noHabitaciones 
                    ELSE 0 
                END AS promedio_total_ayb,

                CASE 
                    WHEN noHabitaciones != 0 THEN noHabitaciones / 12 
                    ELSE 0 
                END AS porcOcupacion,
                
                CASE 
                    WHEN noHabitaciones != 0 THEN alimentos / noHabitaciones 
                    ELSE 0 
                END AS promedio_alimentos,

                CASE 
                    WHEN noHabitaciones != 0 THEN bebidas / noHabitaciones 
                    ELSE 0 
                END AS promedio_bebidas

            FROM
            {$this->bd2}soft_folio
            INNER JOIN {$this->bd2}soft_restaurant_ventas ON soft_restaurant_ventas.soft_folio = soft_folio.id_folio
            WHERE id_udn = ? 
            AND DATE_FORMAT(fecha_folio,'%Y-%m-%d') = ?

            ";

        $sql = $this->_Read($query, $array);

        return $sql[0];
    }

    function getAyB($array){

        $query = "

        SELECT
            soft_productos.descripcion,
            soft_productos.id_udn,
            ROUND(SUM(soft_productosvendidos.ventatotal),2) AS total,
            soft_grupoproductos.grupoproductos,
            soft_grupoproductos.idgrupo
        FROM
        {$this->bd2}soft_productosvendidos
        INNER JOIN {$this->bd2}soft_productos ON soft_productosvendidos.id_productos = soft_productos.id_Producto
        INNER JOIN {$this->bd2}soft_grupoproductos ON soft_productos.id_grupo_productos = soft_grupoproductos.idgrupo
        WHERE idFolioRestaurant = ?

        AND idgrupo !=69 and idgrupo != 70 
        
        ";

        return $this->_Read($query, $array);
    }

    function getHospedaje($array){

        $query = "

       SELECT
            soft_productos.descripcion,
            soft_productos.id_udn,
            ROUND(SUM(soft_productosvendidos.ventatotal),2) AS total,
            soft_grupoproductos.grupoproductos,
            soft_grupoproductos.idgrupo
        FROM
        {$this->bd2}soft_productosvendidos
        INNER JOIN {$this->bd2}soft_productos ON soft_productosvendidos.id_productos = soft_productos.id_Producto
        INNER JOIN {$this->bd2}soft_grupoproductos ON soft_productos.id_grupo_productos = soft_grupoproductos.idgrupo
        WHERE idFolioRestaurant = ?

       
        
         AND idgrupo = 69 
        
        ";

        return $this->_Read($query, $array);
    }

    function getDiversos($array){

        $query = "

       SELECT
            soft_productos.descripcion,
            soft_productos.id_udn,
            ROUND(SUM(soft_productosvendidos.ventatotal),2) AS total,
            soft_grupoproductos.grupoproductos,
            soft_grupoproductos.idgrupo
        FROM
        {$this->bd2}soft_productosvendidos
        INNER JOIN {$this->bd2}soft_productos ON soft_productosvendidos.id_productos = soft_productos.id_Producto
        INNER JOIN {$this->bd2}soft_grupoproductos ON soft_productos.id_grupo_productos = soft_grupoproductos.idgrupo
        WHERE idFolioRestaurant = ?

       
        
         AND idgrupo = 70 
        
        ";

        return $this->_Read($query, $array);
    }

    function updateVentas($array) {

        
        return $this->_Update([
        
            'table'  => "{$this->bd}soft_restaurant_ventas",
            'values' => $array['values'],
            'where'  => $array['where'],
            'data'   => $array['data']
        
        ]);

    }

    function getFolioSoft($array){
      
      $query = "

        SELECT
            id_folio,
            fecha_folio,
            id_udn
        FROM rfwsmqex_gvsl_finanzas.soft_folio 
        WHERE fecha_folio = ?
        AND id_udn = ? ";

        return $this->_Read($query, $array);

    }

    // Notifications.
    function lsNotificaciones($array){
    
        $query ="
            SELECT
                notifications.idNoti,
                notifications.title,
                notifications.description,
                notifications.creation,
                notifications.id_Module,
                notifications.id_Area,
                notifications.id_User,
                notifications.id_UDN,
                usuarios.usser,
                rh_area.Area
            FROM
            rfwsmqex_erp.notifications
            INNER JOIN rfwsmqex_erp.usuarios ON notifications.id_User = usuarios.idUser
            INNER JOIN rfwsmqex_erp.rh_area_udn ON notifications.id_Area = rh_area_udn.idAreaUDN
            INNER JOIN rfwsmqex_erp.rh_area ON rh_area_udn.id_Area = rh_area.idArea
        ";

        return $this->_Read($query, null);

   
    }

  

}

class Afluencia extends CRUD{
  public $bd;
    public $bd2;

    public function __construct(){
        $this->bd = "rfwsmqex_gvsl_finanzas.";
        $this->bd2 = "rfwsmqex_gvsl_finanzas.";
    }

}


?>