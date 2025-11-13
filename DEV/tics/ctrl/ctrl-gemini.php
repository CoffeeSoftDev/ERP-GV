
            <?php
            if(empty($_POST['opc'])) exit(0);


            require_once('../mdl/mdl-gemini.php');
            $obj = new Gemini;

            $encode = [];
            switch ($_POST['opc']) {
            case 'listUDN':
                    $encode = $obj->lsUDN();
                break;
            }

            echo json_encode($encode);
            ?>