<?php 
require_once('mdl-ch.php');
class MDestajo extends MCH {
    function lsDestajoColabordaores($array) {
        $values = [
            'idEmpleado AS id',
            'NombreEmpleado AS colaborador',
            'id_area AS ida',
            'id_ColaboradorRH AS ch',
            'COUNT( pagodestajo ) AS cont',
            '( SUM( pagodestajo )  / COUNT( pagodestajo ) ) AS promedio',
            'SUM( pagodestajo ) AS destajo',
            'SUM( DiasExtras ) AS bono',
            'SUM( Fonacot ) AS fonacot',
            'SUM( Infonavit ) AS infonavit',
            'SUM( perdidaMaterial ) AS perdida',
            'SUM( prestamoPersonal ) AS prestamo'
        ];

        $innerjoin = [
            "{$this->bd_prod}formatopago"  => "id_Pago = idPago",
            "{$this->bd_prod}colaborador"  => "id_Colaborador = idEmpleado",
            "{$this->bd_prod}almacen_area" => "id_area = idArea ",
        ];

        return $this->_Select([
            'table'     => "{$this->bd_prod}destajo",
            'values'    => $values,
            'innerjoin' => $innerjoin,
            'where'     => "DATE_FORMAT( FechaPago, '%Y-%m-%d' ) BETWEEN ? AND ?",
            'group'     => 'idEmpleado',
            'order'     => ['ASC'=>'idArea'],
            'data'      => $array
        ]);
    }
    function lsDestajoMensual($array) {
        $values = [
            '( SUM( pagodestajo )  / COUNT( pagodestajo ) ) AS promedio',
            'SUM( pagodestajo ) AS destajo',
            'SUM( DiasExtras ) AS bono',
            'SUM( Fonacot ) AS fonacot',
            'SUM( Infonavit ) AS infonavit',
            'SUM( perdidaMaterial ) AS perdida',
            'SUM( prestamoPersonal ) AS prestamo'
        ];


        return $this->_Select([
            'table'     => "{$this->bd_prod}destajo",
            'values'    => $values,
            'innerjoin' => ["{$this->bd_prod}formatopago"  => "id_Pago = idPago"],
            'where'     => "id_Colaborador,MONTH( FechaPago ),YEAR( FechaPago )",
            'data'      => $array
        ])[0];
    }
}
?>