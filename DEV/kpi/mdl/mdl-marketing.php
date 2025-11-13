
            <?php
            require_once('../../conf/_CRUD.php');

            class Marketing extends CRUD{
            function lsUDN(){
                return $this->_Select([
                    'table'  => 'udn',
                    'values' => 'idUDN AS id, UDN AS valor',
                    'where'  => 'Stado = 1',
            		'order' => ['ASC'=>'Antiguedad']
                ]);
            }
            function select($array){
                return $this->_Select([
                    'table'  => '',
                    'values' => '',
                    'innerjoin' => ['table' => 'campo1 = campo2'],
                    'where'  => '',
            		'order' => ['ASC'=>'campo1,campo2','DESC'=>'campo3,campo4'],
                    'data'   => $array
                ]);
            }
            function insert($array){
                return $this->_Insert([
                    'table'  => '',
                    'values' => '',
                    'data'   => $array
                ]);
            }
            function update($array){
                return $this->_Update([
                    'table'  => '',
                    'values' => '',
                    'where'  => '',
                    'data'   => $array
                ]);
            }
            function delete($array){
                	return $this->_Delete([
                        'table'  => '',
                        'where'  => '',
                        'data'   => $array
                    ]);
            }
            }
            ?>