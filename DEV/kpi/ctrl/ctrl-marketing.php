
            <?php
            if(empty($_POST['opc'])) exit(0);


            require_once('../mdl/mdl-marketing.php');
            $obj = new Marketing;

            $encode = [];
            switch ($_POST['opc']) {
            case 'listUDN':
                    $encode = $obj->lsUDN();
                break;
            }

            echo json_encode($encode);
            ?>