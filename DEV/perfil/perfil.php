<?php 
    if( empty($_COOKIE["IDU"]) )  require_once('../acceso/ctrl/ctrl-logout.php');

    require_once('layout/head.php');
    require_once('layout/script.php'); 
?>
<body>
    <?php require_once('../layout/navbar.php'); ?>
    <main>
        <section id="sidebar"></section>
        <div id="main__content">
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item fw-bold active">Mi Perfil</li>
    </ol>
</nav>
<div class="row col-12 p-0 bg-none d-flex justify-content-between">
    <div class="col-12 col-md-3 mb-3 pb-2 mb-md-0 rounded-3 bg-light">
        <div class="col-12 p-0 mb-3 mt-3" id="content__perfil">
            <input type="file" class="hide" id="file-profile" accept=".jpg, .jpeg, .png">
            <img id="imgPerfil" src="../src/img/user.png" alt="Colaborador" />
            <span class="fs-6" onclick="$('#file-profile').click();" alt="Cambiar foto">
                <i class="icon-camera fs-4"></i>SUBIR FOTO</span>
            <p><i class='icon-pencil-5'></i></p>
        </div>
        <label class="col-12 fw-bold fs-4 text-primary text-center" id="principal">Colaborador</label>
        <hr>
        <label class="col-12 fw-bold fs-5 text-center" id="departamento">Departamento</label>
        <label class="col-12 text-muted fs-6 text-center" id="puesto">Puesto</label>
    </div>

    <div class="col-12 col-md-8 pb-2 rounded-3 bg-light" id="info_data">
        <div class="row p-2">
            <label class="mb-1 p-0 form-label fw-bold">INFORMACIÓN LABORAL</label>
            <hr class="m-0 pb-2">
            <div class="col-12 col-md-6">
                <label class="form-label" for="udn">Unidad de negocio</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="icon-industry"></i>
                    </span>
                    <input type="text" class="form-control" id="udn" disabled>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label" for="fecha_ingreso">Fecha de Ingreso</label>
                <div class="input-group">
                    <input type="text" class="form-control text-center" id="fecha_ingreso" disabled>
                    <span class="input-group-text">
                        <i class="icon-calendar"></i>
                    </span>
                </div>
            </div>
            <label class="mb-1 mt-2 p-0 form-label fw-bold">INFORMACIÓN PERSONAL</label>
            <hr class="m-0 pd-3">
            <div class="col-12 mb-2">
                <label class="form-label" for="nombre_completo">Nombre completo</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="icon-user"></i>
                    </span>
                    <input type="text" class="form-control" id="nombre_completo" disabled>
                </div>
            </div>
            <form  class="row m-0 p-0" id="form_perfil">
                <div class="col-12 col-md-4 mb-2">
                    <label class="form-label" for="usser">Usuario</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="icon-user"></i>
                        </span>
                        <input type="text" class="form-control" name="usser" id="usser" disabled>
                        <span class="btn btn-info input-group-text" data-bs-container="body"
                            data-bs-trigger="hover focus" data-bs-placement="top"
                            data-bs-content="Bloquear/Desbloquear">
                            <i class="icon-pencil"></i>
                        </span>
                    </div>
                </div>
                <div class="col-12 col-md-4 mb-2">
                    <label class="form-label" for="telefono">Teléfono Móvil</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="icon-phone"></i>
                        </span>
                        <input type="text" class="form-control" name="telefono" id="telefono" tipo="numero" maxlength="10"
                            disabled>
                        <span class="btn btn-info input-group-text" data-bs-container="body"
                            data-bs-trigger="hover focus" data-bs-placement="top"
                            data-bs-content="Bloquear/Desbloquear">
                            <i class="icon-pencil"></i>
                        </span>
                    </div>
                </div>
                <div class="col-12 col-md-4 mb-2">
                    <label class="form-label" for="correo">Correo electrónico</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="icon-at"></i>
                        </span>
                        <input type="email" class="form-control" name="correo" id="correo" disabled>
                        <span class="btn btn-info input-group-text" data-bs-container="body"
                            data-bs-trigger="hover focus" data-bs-placement="top"
                            data-bs-content="Bloquear/Desbloquear">
                            <i class="icon-pencil"></i>
                        </span>
                    </div>
                    <span class="text-danger form-text hide">
                        <i class="icon-warning-1"></i> El campo es requerido
                    </span>
                </div>
                <div class="col-12 col-md-6 mb-2">
                    <label class="form-label" for="clave1">Nueva contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text btn btn-outline-info" data-bs-container="body"
                            data-bs-trigger="hover focus" data-bs-placement="top"
                            data-bs-content="Generar contraseña automática" id="key_auto">
                            <i class="icon-key"></i>
                        </span>
                        <input type="password" class="form-control clave" name="clave1" id="clave1">
                        <span class="btn btn-outline-info input-group-text key_show" data-bs-container="body"
                            data-bs-trigger="hover focus" data-bs-placement="top"
                            data-bs-content="Mostrar/Ocultar contraseña" id="key_show">
                            <i class="icon-eye"></i>
                        </span>
                    </div>
                </div>
                <div class="col-12 col-md-6 mb-2">
                    <label class="form-label" for="clave2">Confirmar contraseña</label>
                    <div class="input-group">
                        <input type="password" class="form-control clave" name="clave2" id="clave2">
                    </div>
                </div>
                <!--<div class="col-12 col-md-4 mb-2 ">-->
                <!--    <label class="form-label" for="clave2">NIP de seguridad interna</label>-->
                <!--    <div class="input-group">-->
                <!--        <span class="input-group-text"><i class="icon-key-3"></i></span>-->
                <!--        <input type="password" class="form-control clave" name="clave2" id="clave2">-->
                <!--    </div>-->
                <!--</div>-->
                
                <button type="submit" class="btn btn-primary mt-3 col-12 col-md-6 ms-auto me-auto"
                    id="btn_update">Actualizar</button>
            </form>
        </div>
    </div>


</div>
<script src="src/js/perfil.js?t=<?php echo time(); ?>"></script>
        </div>
    </main>
</body>

</html>