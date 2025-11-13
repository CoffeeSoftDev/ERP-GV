<header class="bg-dia">
    <section>
        <span type="button" id="btnSidebar"><i class="icon-menu"></i></span>
        <img onclick="location.reload()" class="d-none d-sm-block pointer"
            src="https://erp-varoch.com/ERP24/src/img/logos/logo_row_wh.png" alt="Grupo Varoch">
        <img onclick="location.reload()" class="d-block d-sm-none"
            src="https://erp-varoch.com/ERP24/src/img/logos/logo_icon_wh.png" alt="Grupo Varoch">
    </section>
    <style>
        .f-10 {
            font-size: 10.5px !important;
        }

        #notifications {
            position: relative;
        }

        .notification-count {
            /* position: absolute;
            top: -1px;
            right: -10px;
            background-color: red;
            color: white;
            border-radius: 50%;
            padding: 3px 3px;
            font-size: 12px;
            font-weight: bold; */
            position: absolute;
            top: -1px;
            right: -10px;
            background-color: red;
            color: white;
            border-radius: 50%;
            padding: 1px 3px 1px 3px;
            font-size: 12px;
            /* font-weight: bold; */
            min-width: 20px;
            min-height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
    <nav>
        <ul class="theme" id="navbar">
            <!-- <ul id="navbar"> -->
            <li class="">
                <a href="https://www.erp-varoch.com/erp" target="_blank" style="text-decoration:none;color:#FFF;"><i
                        class="icon-logout-3"></i><span class="d-none d-sm-block">ERP v.1</span></a>
            </li>
            <li class="hide">
                <i class="icon-sun-inv-1"></i>
            </li>
            <!--<li class="hide" id="notifications" data-bs-toggle="offcanvas" data-bs-target="#staticBackdrop"-->
            <!--    aria-controls="staticBackdrop">-->
            <!--    <i class="icon-bell"></i>-->
            <!--    <span class="notification-count">0</span>-->
            <!--</li>-->
            <li class="hide">
                <i class="icon-mail"></i>
                <ul>
                    <div id="mensage">
                        <li>Hola</li>
                    </div>
                </ul>
            </li>
            <li id="li_user">
                <div class="mx-2" id="perfil_photo_prev">
                    <img id="navbarPerfil" src="<?php echo $_COOKIE['PIC'] ?>" alt="Colaborador">
                </div>
                <p><?php echo $_COOKIE['USR']; ?></p>
                <ul>
                    <li onClick="redireccion('perfil/perfil.php');">
                        <i class="icon-user" id="btnPerfil"></i>Mi perfil
                    </li>
                    <!-- <li onClick="redireccion('perfil/perfil.php');">
                       <a href="https://www.erp-varoch.com/erp">ERP</a>
                    </li> -->
                    <li class="divider"></li>
                    <li onClick="cerrar_sesion();">
                        <i class="icon-off"></i>
                        Cerrar sesión
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</header>
<div class="offcanvas offcanvas-end" data-bs-backdrop="static" tabindex="-1" id="staticBackdrop"
    aria-labelledby="staticBackdropLabel">
    <div class="offcanvas-header">
        <h6 class="offcanvas-title" id="staticBackdropLabel">Notificaciones</h6>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body"></div>
</div>

<script src="https://erp-varoch.com/ERP24/layout/src/js/sidebar.js?t=<?php echo time(); ?>"></script>