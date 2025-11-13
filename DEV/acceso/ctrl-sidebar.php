<?php
require_once('../conf/_CRUD.php');

class Sidebar extends CRUD {
function allDirectory($array){
    $query = "SELECT
                modulos.idModulo AS idM, 
                modulos.modulo, 
                directorios.idDirectorio AS id, 
                directorios.directorio AS directorio, 
                directorios.dir_ruta AS ruta, 
                directorios.dir_submodulo AS idS, 
                submodulos.submodulo
            FROM
                permisos
                INNER JOIN directorios ON permisos.id_Directorio = directorios.idDirectorio
                INNER JOIN modulos ON directorios.dir_modulo = modulos.idModulo
                LEFT JOIN submodulos ON submodulos.idSubmodulo = dir_submodulo
            WHERE
                directorios.dir_estado = 1 AND
                directorios.dir_visible = 1 AND
                permisos.id_Perfil = ?
            ORDER BY
              modulos.mod_orden ASC,
              modulos.idModulo ASC,
              directorios.dir_orden ASC";
    return $this->_Read($query,$array);
}
}

$obj           = new Sidebar;
$tempModulo    = '';
$tempSubModulo = '';
$modulos       = [];
$submodulos    = [];

if(isset($_COOKIE['ACT'])){
    $sql = $obj->allDirectory([$_COOKIE['IDP']]);
    foreach ($sql as $row) {
            // Modulos
        if($tempModulo != $row['idM']){
            $tempModulo = $row['idM'];
            $modulos[]  = [
                "id"          => $row['idM'],
                "modulo"      => mb_strtoupper($row['modulo'],'utf-8'),
                "submodulos"  => [],
                "directorios" => [],
            ];
        }
            // Modulos/Submodulos
        if ( $tempSubModulo != $row['idS'] && isset($row['idS']) ) {
            $tempSubModulo = $row['idS'];
            foreach ($modulos as &$mod) {
                if ( $tempModulo == $mod["id"]) {
                    $mod['submodulos'][] = [
                        "id"          => $row['idS'],
                        "submodulo"   => mb_strtoupper($row['submodulo'],'utf-8'),
                        "directorios" => []
                    ];
                }
            }
        }
            // Modulos/Submodulos/Directorios
        if ( isset($row['idS']) ) {
            foreach ($modulos as &$mod) {
                if ( $tempModulo == $mod["id"]) {
                    foreach ($mod["submodulos"] as &$sub) {
                        if ( $sub["id"] == $row["idS"]) {
                            $sub['directorios'][] = [
                                "id"         => $row['id'],
                                "directorio" => $row['directorio'],
                                "ruta"       => $row['ruta']
                            ];
                        }
                    }
                }
            }
        }
            // Modulos/Directorios
        if(!isset($row['idS'])){
            foreach ($modulos as &$mod) {
                if ( $tempModulo == $mod["id"]) {
                    $mod['directorios'][] = [
                        "id"         => $row['id'],
                        "directorio" => $row['directorio'],
                        "ruta"       => $row['ruta']
                    ];
                }
            }
        }
    }
}

$encode = $modulos;

echo json_encode($encode);
?>