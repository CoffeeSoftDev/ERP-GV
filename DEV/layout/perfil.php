<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item fw-bold active">Mi Perfil</li>
    </ol>
</nav>

<div class="row col-12 p-0 bg-none d-flex justify-content-between">
    <!-- IMAGEN PERFIL / PASSWORD -->
    <div class="col-12 col-md-3 mb-3 pb-2 mb-md-0 rounded-3 bg-light">
        <div class="col-12 p-0 mb-3 mt-3" id="content__perfil">
            <input type="file" class="hide" id="file-profile" accept=".jpg, .jpeg, .png">
            <img id="imgPerfil" src="../src/img/user.png" alt="Colaborador" />
            <span class="fs-6" onclick="$('#file-profile').click();" alt="Cambiar foto">
                <i class="icon-camera fs-4"></i>SUBIR FOTO</span>
            <p><i class='icon-pencil-5'></i></p>
        </div>
        <label class="col-12 fw-bold fs-4 text-primary text-center">Leonardo Martínez</label>
        <hr>
        <label class="col-12 fw-bold fs-5 text-center">TICS</label>
        <label class="col-12 text-muted fs-6 text-center">Jefe de Departamento</label>
    </div>
    <!-- INFORMACIÓN -->
    <div class="col-12 col-md-8 pb-2 rounded-3 bg-light">
        <form class="row p-2">
            <label class="mb-1 p-0 form-label fw-bold">INFORMACIÓN LABORAL</label>
            <hr class="m-0 pb-2">
            <div class="col-12 col-md-6">
                <label class="form-label" for="iptPerfilEmpresa">Unidad de negocio</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="icon-industry"></i>
                    </span>
                    <input type="password" class="form-control" id="iptPerfilEmpresa" disabled>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label" for="iptPerfilIngreso">Fecha de Ingreso</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="icon-calendar"></i>
                    </span>
                    <input type="password" class="form-control" id="iptPerfilIngreso" disabled>
                </div>
            </div>
            <label class="mb-1 mt-2 p-0 form-label fw-bold">INFORMACIÓN PERSONAL</label>
            <hr class="m-0 pd-3">
            <div class="col-12 mb-2">
                <label class="form-label" for="iptPerfilName">Nombre completo</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="icon-user"></i>
                    </span>
                    <input type="password" class="form-control" id="iptPerfilName" disabled>
                </div>
            </div>
            <div class="col-12 col-md-4 mb-2">
                <label class="form-label" for="iptUser">Usuario</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="icon-at"></i>
                    </span>
                    <input type="text" class="form-control" id="iptUser" disabled>
                    <button type="button" class="btn btn-info input-group-text">
                        <i class="icon-pencil"></i>
                    </button>
                </div>
            </div>
            <div class="col-12 col-md-4 mb-2">
                <label class="form-label" for="iptPerfilEmail">Correo electrónico</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="icon-at"></i>
                    </span>
                    <input type="text" class="form-control" id="iptPerfilEmail" disabled>
                    <button type="button" class="btn btn-info input-group-text">
                        <i class="icon-pencil"></i>
                    </button>
                </div>
            </div>
            <div class="col-12 col-md-4 mb-2">
                <label class="form-label" for="iptPerfilTelefono">Teléfono</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="icon-phone"></i>
                    </span>
                    <input type="text" class="form-control" id="iptPerfilTelefono" disabled>
                    <button type="button" class="btn btn-info input-group-text">
                        <i class="icon-pencil"></i>
                    </button>
                </div>
            </div>
            <div class="col-12 col-md-6 mb-2">
                <label class="form-label" for="iptPerfilPass1">Nueva contraseña</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="icon-key"></i>
                    </span>
                    <input type="password" class="form-control" id="iptPerfilPass1">
                    <button type="button" class="btn btn-info input-group-text">
                        <i class="icon-eye"></i>
                    </button>
                </div>
            </div>
            <div class="col-12 col-md-6 mb-2">
                <label class="form-label" for="iptPerfilPass2">Confirmar nueva contraseña</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="icon-key"></i>
                    </span>
                    <input type="password" class="form-control" id="iptPerfilPass2">
                    <button type="button" class="btn btn-info input-group-text">
                        <i class="icon-eye"></i>
                    </button>
                </div>
            </div>

            <button class="btn btn-primary mt-3 col-12 col-md-6 ms-auto me-auto">Actualizar</button>
        </form>
    </div>
</div>
<script src="../src/js/perfil.js?t=<?php echo time(); ?>"></script>