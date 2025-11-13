<?php
class Copy{
const PERMISSIONS = 0777;
function copyRecursively($ruteSource,$ruteDestine) {
    try {
        //Comprobar si existe el directorio original
        if (!is_dir($ruteSource)) 
            throw new Exception("El directorio original no existe.");
                
        //Comprobar si existe el directorio de destino, si no crearlo.
        if (!file_exists($ruteDestine)) {
            if (!mkdir($ruteDestine, self::PERMISSIONS, true)) 
                throw new Exception("Error al crear el directorio de destino.");
            
            //Hacer un array con los archivos y subdirectorios de la carpeta Original
            $files = array_diff(scandir($ruteSource), ['.', '..']);
            foreach ($files as $file) {
                $origFilePath = "$ruteSource/$file";
                $destFilePath = "$ruteDestine/$file";
    
                if (is_dir($origFilePath)) { // Recursividad
                    $this->copyRecursively($origFilePath, $destFilePath); 
                } else if (is_file($origFilePath)) { //Realizar Copia de archivos y subdirectorios
                    if (!copy($origFilePath, $destFilePath)) 
                        throw new Exception("Error al copiar el archivo.");
                }
            }

            return true;
        } else {
            throw new Exception("El directorio ya éxiste.");
        }
    } catch (Exception $e) {
        return ["Error" => $e.getMessage()];
    }
}

function createFile($ruteDestine,$module,$submodule,$directory){
    try {
        $files = dirname($ruteDestine).'/';
        if (file_exists($files)) {
            if(!file_exists($ruteDestine)) {
                touch($ruteDestine);

                $scriptSubmodule = '';
                $module = str_replace('/','',$module);
                $bcSubmodule = '';
                if($submodule != ''){
                    $submodule = str_replace('/','',$submodule);
                    $bcSubmodule = "<li class='breadcrumb-item text-uppercase text-muted'>$submodule</li>";
                    $scriptSubmodule = $submodule.'/';
                }
                
                $breadcrumbActive = ucfirst(str_replace('-',' ',$directory));
                $breadcrumb = "
                <nav aria-label='breadcrumb'>
                    <ol class='breadcrumb'>
                        <li class='breadcrumb-item text-uppercase text-muted'>$module</li>
                        $bcSubmodule
                        <li class='breadcrumb-item fw-bold active'>$breadcrumbActive</li>
                    </ol>
                </nav>
                <div class=\"row mb-3 d-flex justify-content-end\">
                    <div class=\"col-12 col-sm-6 col-md-4 col-lg-3 mb-3\">
                        <label for=\"cbUDN\">Seleccionar UDN</label>
                        <select class=\"form-select\" id=\"cbUDN\"></select>
                    </div>
                    <div class=\"col-12 col-sm-6 col-md-4 col-lg-3 mb-3\">
                        <label for=\"iptDate\">Fecha</label>
                        <div class=\"input-group\">
                            <input type=\"text\" class=\"form-control datepicker\" id=\"iptDate\">
                            <span class=\"input-group-text\"><i class=\"icon-calendar\"></i></span>
                        </div>
                    </div>
                    <div class=\"col-12 col-md-4 col-lg-3 mb-3\">
                        <label for=\"btnOk\" col=\"col-12\"> </label>
                        <button type=\"button\" class=\"btn btn-primary col-12\" id=\"btnOk\">Botón</button>
                    </div>
                </div>
                
                <div class=\"row\" id=\"tbDatos\"></div>
                <script src='".$scriptSubmodule."src/js/$directory.js?t=".time()."'></script>
                ";
                
                file_put_contents($ruteDestine, $breadcrumb);
            }
        } else {
            throw new Exception("No existen las carpetas, no se puede crear el directorio principal");
        }

        //CREAR CONTROLADOR
        $files = dirname($ruteDestine).'/ctrl';
        if (!file_exists($files)) {
            if (!mkdir($files, self::PERMISSIONS, true)) {
                throw new Exception("Error al crear el directorio de controlador.");
            }
        }
        $newRute = $files.'/ctrl-'.$directory.'.php';
        
        $clase = ucfirst(str_replace("-","",$directory));
        if ( !file_exists($newRute)){
            touch($newRute);

            $contenido = "
            <?php
            if(empty(\$_POST['opc'])) exit(0);


            require_once('../mdl/mdl-$directory.php');
            \$obj = new $clase;

            \$encode = [];
            switch (\$_POST['opc']) {
            case 'listUDN':
                    \$encode = \$obj->lsUDN();
                break;
            }

            echo json_encode(\$encode);
            ?>";

            file_put_contents($newRute, $contenido);
        }

        //CREAR MODELO
        $files = dirname($ruteDestine).'/mdl';
        if (!file_exists($files)) {
            if (!mkdir($files, self::PERMISSIONS, true)) {
                throw new Exception("Error al crear el directorio principal.");
            }
        }

        $newRute = $files.'/mdl-'.str_replace(" ","-",$directory).'.php';

        $clase = ucfirst(str_replace("-","",$directory));
        if($submodule != '') $slash = '../';

        if ( !file_exists($newRute)) {
            touch($newRute);

            $contenido = "
            <?php
            require_once('../../".$slash."conf/_CRUD.php');

            class $clase extends CRUD{
            function lsUDN(){
                return \$this->_Select([
                    'table'  => 'udn',
                    'values' => 'idUDN AS id, UDN AS valor',
                    'where'  => 'Stado = 1',
            		'order' => ['ASC'=>'Antiguedad']
                ]);
            }
            function select(\$array){
                return \$this->_Select([
                    'table'  => '',
                    'values' => '',
                    'innerjoin' => ['table' => 'campo1 = campo2'],
                    'where'  => '',
            		'order' => ['ASC'=>'campo1,campo2','DESC'=>'campo3,campo4'],
                    'data'   => \$array
                ]);
            }
            function insert(\$array){
                return \$this->_Insert([
                    'table'  => '',
                    'values' => '',
                    'data'   => \$array
                ]);
            }
            function update(\$array){
                return \$this->_Update([
                    'table'  => '',
                    'values' => '',
                    'where'  => '',
                    'data'   => \$array
                ]);
            }
            function delete(\$array){
                	return \$this->_Delete([
                        'table'  => '',
                        'where'  => '',
                        'data'   => \$array
                    ]);
            }
            }
            ?>";

            file_put_contents($newRute, $contenido);
        }

        //CREAR JS
        $files = dirname($ruteDestine).'/src/js';
        if (!file_exists($files)) {
            if (!mkdir($files, self::PERMISSIONS, true)) {
                throw new Exception("Error al crear el directorio principal.");
            }
        }

        $newRute = $files.'/'.str_replace(" ","-",$directory).'.js';
        $subJS = '';
        if($submodule != '') $subJS = $submodule.'/';

        if ( !file_exists($newRute)){
            touch($newRute);

            $ctrlDirectory = "ctrl".str_replace("-","_",$directory);

            $contenido = "
                window.$ctrlDirectory = '".$subJS."ctrl/ctrl-$directory.php';

                $(function(){
                    listUDN();

                    $('.datepicker').daterangepicker(
                    { 
                        startDate: moment(), 
                        endDate: moment() 
                    },
                    function (startDate, endDate) {
                        alert({
                            icon: 'question',
                            title: 'Se han modificado las fechas',
                            text: '¿Desea actualizar la tabla?',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                console.log(startDate.format('YYYY-MM-DD'));
                                console.log(endDate.format('YYYY-MM-DD'));
                                alert();
                            }
                        });
                    });

                    $('.datepicker').next('span').on('click',function(){
                        $('.datepicker').click();
                    });

                    $('#tbDatos').create_table();

                    $('#btnOk').on('click',()=>{
                        swal_question('¿Esta seguro de realizar esta acción?').then((result)=>{
                            if(result.isConfirmed) alert();
                        });
                    });
                });

                function listUDN(){
                    let datos = new FormData();
                    datos.append('opc', 'listUDN');
                    send_ajax(datos, $ctrlDirectory).then((data) => {
                        $(\"#cbUDN\").option_select({data:data});
                    });
                }

                function updateModal(id,title){
                    bootbox.dialog({
                        title: ` EDITAR \"\${title.toUpperCase()}\" `,
                        message: `
                        <form id=\"modalForm\" novalidate>
                            <div class=\"col-12 mb-3\">
                                <label for=\"cbModal\" class=\"form-label fw-bold\">Select</label>
                                <div class=\"input-group-addon\">
                                    <select class=\"form-select text-uppercase\" name=\"cbModal\" id=\"cbModal\"></select>
                                </div>
                            </div>
                            <div class=\"col-12 mb-3\">
                                <label for=\"iptModal\" class=\"form-label fw-bold\">Input</label>
                                <input type=\"text\" class=\"form-control\" name=\"iptModal\" id=\"iptModal\" value=\"\${title}\" required>
                                <span class=\"form-text text-danger hide\">
                                    <i class=\"icon-warning-1\"></i>
                                    El campo es requerido.
                                </span>
                            </div>
                            <div class=\"col-12 mb-3 d-flex justify-content-between\">
                                <button type=\"submit\" class=\"btn btn-primary col-5\">Actualizar</button>
                                <button type=\"button\" class=\"btn btn-outline-danger col-5 bootbox-close-button\">Cancelar</button>
                            </div>
                        </form>
                        `,
                    }).on(\"shown.bs.modal\", function () {
                        const opciones = [
                            {'id':1,'valor':'Uno'},
                            {'id':2,'valor':'Dos'},
                            {'id':3,'valor':'Tres'}
                        ];

                        $('#cbModal').option_select({
                            data:opciones,
                            select2:true,
                            father:true,
                            placeholder:'- Seleccionar -'
                        });

                        $('#modalForm').validation_form({\"id\":id,\"opc\":\"update\"},datos=>{
                            for(x of datos) console.log(x);
                        });


                        // send_ajax(datos,$ctrlDirectory).then(data=>{
                        //      alert();
                        // });
                    });
                }

                function toggleStatus(id){
                    const BTN = $('#btnStatus'+id);
                    const ESTADO = BTN.attr('estado');

                    let estado = 0;
                    let iconToggle = '<i class=\"icon-toggle-off\"></i>';
                    let question = '¿DESEA DESACTIVARLO?';
                    if ( ESTADO == 0 ) {
                        estado = 1;
                        iconToggle = '<i class=\"icon-toggle-on\"></i>';
                        question = '¿DESEA ACTIVARLO?';
                    }

                    swal_question(question).then((result)=>{
                        if(result.isConfirmed){
                            //let datos = new FormData();
                            //datos.append('opc','');
                            //send_ajax(datos,$ctrlDirectory).then((data)=>{
                                // console.log(data);
                                BTN.html(iconToggle);
                                BTN.attr('estado',estado);
                            //});
                        }
                    });
                }
            ";

            file_put_contents($newRute, $contenido);
        }

        return true;
    } catch (Exception $e) {
        return $e->getMessage();
    }
}

function moveFile($beforeRute,$beforeDirectory,$afterRute,$afterDirectory){
    $movExit = null;
    try {
        // MOVER DIRECTORIO PRINCIPAL
        $files = dirname($afterRute).'/';
        if(file_exists($files)){
            $fileBeforeRute = "$beforeRute$beforeDirectory.php";
            $fileAfterRute = "$afterRute$afterDirectory.php";
            // $movExit = rename($beforeRute.'.php', $afterRute.'.php');
            if(rename($fileBeforeRute, $fileAfterRute)) 
                $movExit = true;
            else 
                throw new Exception("No se pudo mover y renombrar el directorio principal. [$fileBeforeRute] | [$fileAfterRute]");
        } else {
            throw new Exception("No existen las carpetas, no se puede mover el directorio principal.");
        }

        // MOVER CONTROLADOR
        $files = $afterRute.'/ctrl';
        if(!file_exists($files)){
            if (!mkdir($files, self::PERMISSIONS, true)) 
                throw new Exception("Error al crear el directorio de controlador.");
        }

        if(file_exists($files) && $movExit == true ){
            $fileBeforeRute = $beforeRute."ctrl/ctrl-$beforeDirectory.php";
            $fileAfterRute = $afterRute."ctrl/ctrl-$afterDirectory.php";
            // $movExit = rename($fileBeforeRute, $fileAfterRute);
            if(rename($fileBeforeRute, $fileAfterRute))
                $movExit = true;
            else
                throw new Exception("No se pudo mover y renombrar el directorio controlador. [$fileBeforeRute] | [$fileAfterRute]");
        } else {
            throw new Exception("No existe el controlador, y no se movio el directio principal.");
        }

        // MOVER MODELO
        $files = $afterRute.'/mdl';
        if(!file_exists($files)){
            if (!mkdir($files, self::PERMISSIONS, true)) 
                throw new Exception("Error al crear el directorio de controlador.");
        }

        if(file_exists($files) && $movExit == true ){
            $fileBeforeRute = $beforeRute."mdl/mdl-$beforeDirectory.php";
            $fileAfterRute = $afterRute."mdl/mdl-$afterDirectory.php";
            // $movExit = rename($fileBeforeRute, $fileAfterRute);
            if(rename($fileBeforeRute, $fileAfterRute))
                $movExit = true;
            else
                throw new Exception("No se pudo mover y renombrar el directorio modelo. [$fileBeforeRute] | [$fileAfterRute]");
        } else {
            throw new Exception("No existe el modelo, y no se movio el directio controlador.");
        }

        // MOVER JS
        $files = $afterRute.'/src/js';
        if(!file_exists($files)){
            if (!mkdir($files, self::PERMISSIONS, true)) 
                throw new Exception("Error al crear el directorio de controlador.");
        }

        if(file_exists($files) && $movExit == true ){
            $fileBeforeRute = $beforeRute."src/js/$beforeDirectory.js";
            $fileAfterRute = $afterRute."src/js/$afterDirectory.js";
            // $movExit = rename($fileBeforeRute, $fileAfterRute);
            if(rename($fileBeforeRute, $fileAfterRute))
                return true;
            else
                throw new Exception("No se pudo mover y renombrar el directorio JS. [$fileBeforeRute] | [$fileAfterRute]");
        } else {
            throw new Exception("No existe el controlador, y no se movio el directio principal.");
        }
    } catch (Exception $e) {
        return $e->getMessage();
    }
}

function deleteFile($modelo,$submodelo,$directorio){
    $exito = false;
    try {
        // DIRECTORIO PRINCIPAL
        $ruta = $modelo."$submodelo/$directorio.php";
        if (is_dir($ruta)) {
            if (unlink($ruta)) $exito = true;
            else 
                throw new Exception("No es posible eliminar el directorio principal. \n [$ruta]");
        } else {
            throw new Exception("Este directorio principal no existe.");
        }

        // CONTROLADOR
        if($exito){
            $ruta = $modelo."$submodelo/ctrl/ctrl-$directorio.php";
            if (is_dir($ruta)) {
                if (unlink($ruta)) $exito = true;
                else
                    throw new Exception("No es posible eliminar el directorio controlador. \n[$ruta]" );
            } else {
                throw new Exception("Este directorio principal no existe.");
            }
        }

        // MODELO
        if($exito){
            $ruta = $modelo."$submodelo/mdl/mdl-$directorio.php";
            if (is_dir($ruta)) {
                if (unlink($ruta)) $exito = true;
                else
                    throw new Exception("No es posible eliminar el directorio modelo. \n[$ruta]" );
            } else {
                throw new Exception("Este directorio principal no existe.");
            }
        }

        // JAVASCRIPT
        if($exito){
            $ruta = $modelo."$submodelo/src/js/$directorio.js";
            if (is_dir($ruta)) {
                if (unlink($ruta)) return true;
                else
                    throw new Exception("No es posible eliminar el directorio modelo. \n[$ruta]" );
            } else {
                throw new Exception("Este directorio principal no existe.");
            }
        }

    } catch (Exception $e) {
        return $e->getMessage();
    }
}
}
?>