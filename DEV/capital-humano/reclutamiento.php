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
            <link rel="stylesheet" href="src/css/reclutamiento.css">
            <link rel="stylesheet" href="src/css/colaboradores.css">
            <nav aria-label="breadcrumb" class="p-2 p-sm-0">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item text-muted">CH</li>
                    <li class="breadcrumb-item pointer" onClick="redireccion('capital-humano/colaboradores.php');">
                        Colaboradores
                    </li>
                    <li class="breadcrumb-item fw-bold active">Reclutamiento</li>
                </ol>
            </nav>
            <form class="row" id="formDatos" novalidate>
                <div class="accordion p-0 m-0" id="accordionRH">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="informacionPersonalRH">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseRH" aria-expanded="true" aria-controls="collapseRH">
                                Información personal
                            </button>
                        </h2>
                        <div id="collapseRH" class="accordion-collapse collapse show"
                            aria-labelledby="informacionPersonalRH" data-bs-parent="#accordionRH">
                            <div class="accordion-body row">
                                <div class="col-12 col-sm-12 col-md-2 col-lg-3 p-0 mb-3 mt-3" id="photo__perfil">
                                    <input type="file" class="hide" name="foto" id="file-profile"
                                        accept=".jpg, .jpeg, .png">
                                    <img id="photoColaborador" src="../src/img/user.png" alt="Colaborador">
                                    <span class="fs-6" onclick="$('#file-profile').click();" alt="Cambiar foto">
                                        <i class="icon-camera fs-4"></i>SUBIR FOTO
                                    </span>
                                    <p><i class='icon-pencil-5'></i></p>
                                </div>
                                <div class="col-12 col-sm-12 col-md-8 col-lg-9">
                                    <div class="row">
                                        <div class="col-12 col-sm-12 col-md-12 col-lg-4 mb-3">
                                            <label for="iptNombre" class="form-label fw-bold">Nombre<sup>*</sup></label>
                                            <input type="text" class="form-control text-uppercase" name="nombre"
                                                id="iptNombre" placeholder="Nombre(s) del colaborador"
                                                aria-label="Nombre o nombres" required />
                                            <span class="form-text text-danger hide">
                                                <i class="icon-warning-1"></i>
                                                El campo es requerido
                                            </span>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6 col-lg-4 mb-3">
                                            <label for="iptAPaterno" class="form-label fw-bold">Apellido
                                                paterno<sup>*</sup></label>
                                            <input type="text" class="form-control text-uppercase" name="apaterno"
                                                id="iptAPaterno" placeholder="Apellido paterno"
                                                aria-label="Apellido paterno" required />
                                            <span class="form-text text-danger hide">
                                                <i class="icon-warning-1"></i>
                                                Campo requerido.
                                            </span>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6 col-lg-4 mb-3">
                                            <label for="iptAMaterno" class="form-label fw-bold">Apellido
                                                materno<sup>*</sup></label>
                                            <input type="text" class="form-control text-uppercase" name="amaterno"
                                                id="iptAMaterno" placeholder="Apellido materno"
                                                aria-label="Apellido materno" required />
                                            <span class="form-text text-danger hide">
                                                <i class="icon-warning-1"></i>
                                                Campo requerido.
                                            </span>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6 col-lg-4 mb-3">
                                            <label for="iptEmail" class="form-label fw-bold">Correo
                                                electrónico<sup>*</sup></label>
                                            <input type="email" class="form-control text-lowercase" id="correo"
                                                name="email" placeholder="ejemplo@ejemplo.com"
                                                aria-label="Correo electrónico" autocomplete="off" required />
                                            <span class="form-text text-danger hide">
                                                <i class="icon-warning-1"></i>
                                                Campo requerido.
                                            </span>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6 col-lg-4 mb-3">
                                            <label for="iptTelefono"
                                                class="form-label fw-bold">Teléfono<sup>*</sup></label>
                                            <input type="text" class="form-control" tipo="numero" name="telefono"
                                                id="iptTelefono" placeholder="962 123 45 67" aria-label="Teléfono"
                                                maxlength="10" autocomplete="off" required />
                                            <span class="form-text text-danger hide">
                                                <i class="icon-warning-1"></i>
                                                Campo requerido.
                                            </span>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6 col-lg-4 mb-3">
                                            <label for="iptCURP" class="form-label fw-bold">CURP</label>
                                            <input type="text" class="form-control text-uppercase" name="curp"
                                                id="iptCURP" tipo="textoNum" placeholder="xxxx050101xxxxxx00"
                                                aria-label="CURP" maxlength="18" />
                                            <span class="form-text text-danger hide">
                                                <i class="icon-warning-1"></i>
                                                Campo requerido.
                                            </span>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6 col-lg-4 mb-3">
                                            <label for="iptNacimiento" class="form-label fw-bold">Fecha
                                                nacimiento<sup>*</sup>
                                            </label>
                                            <input type="date" class="form-control" name="fecha_nacimiento"
                                                id="iptNacimiento" aria-label="Fecha nacimiento" required>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6 col-lg-4 mb-3">
                                            <label for="iptEdad" class="form-label fw-bold">Edad</label>
                                            <input type="text" class="form-control" readonly id="iptEdad"
                                                placeholder="XX años" aria-label="Edad">
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6 col-lg-4 mb-3">
                                            <label for="iptGenero" class="form-label fw-bold">Género</label>
                                            <div class="input-group">
                                                <!-- <input type="text" readonly class="form-control" name="genero" id="iptGenero"
                                        placeholder="Femenino / Masculino" aria-label="Género"> -->
                                                <select name="genero" class="form-select" readonly aria-label="Género"
                                                    id="iptGenero">
                                                    <option value="H">MASCULINO</option>
                                                    <option value="M">FEMENINO</option>
                                                    <option value="I">INDEFINIDO</option>
                                                </select>
                                                <span class="input-group-text">
                                                    <i class="icon-male"></i><i class="icon-female"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                    <div class="row">
                                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                                            <label for="cbGradoEstudio" class="form-label fw-bold">Último grado de
                                                estudio</label>
                                            <select class="form-select text-uppercase" name="grado_estudio"
                                                id="cbGradoEstudio" aria-label="Último grado de estudio"></select>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                                            <label for="cbCarrera" class="form-label fw-bold">Carrera</label>
                                            <select class="form-select" name="carrera" id="cbCarrera" disabled></select>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                                            <label for="iptLugarNac" class="form-label fw-bold">Lugar de
                                                nacimiento</label>
                                            <select name="lugar_nacimiento" class="form-select text-uppercase"
                                                id="cbLugarNacimiento"></select>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                                            <label for="iptCodigoPostal" class="form-label fw-bold">Código
                                                postal</label>
                                            <input type="number" class="form-control" name="codigo_postal"
                                                id="iptCodigoPostal" placeholder="30700" aria-label="Código postal"
                                                tipo="numero" maxlength="5" />
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-8 col-lg-12 mb-3">
                                            <label for="iptDireccion" class="form-label fw-bold">Dirección</label>
                                            <input type="text" class="form-control" name="direccion" id="iptDireccion"
                                                placeholder="Escribe la dirección completa calle, número y colonia"
                                                aria-label="Dirección" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header text-center" id="accordionRH2">
                            <button class="accordion-button collapsed " type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseRH2" aria-expanded="false" aria-controls="collapseRH2">
                                Información laboral
                            </button>
                        </h2>
                        <div id="collapseRH2" class="accordion-collapse collapse" aria-labelledby="accordionRH2"
                            data-bs-parent="#accordionRH">
                            <div class="accordion-body row">
                                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                                    <label for="cbPatron" class="form-label fw-bold">Patrón<sup>*</sup></label>
                                    <select class="form-select text-uppercase" name="patron" id="cbPatron"
                                        aria-label="Patrón" required>
                                    </select>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                                    <label for="cbUDN" class="form-label fw-bold">Unidad de negocio<sup>*</sup></label>
                                    <select class="form-select text-uppercase" name="udn" id="cbUDN"
                                        aria-label="Ubicación laboral" required></select>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                                    <label for="cbDepto" class="form-label fw-bold">Departamento<sup>*</sup></label>
                                    <select class="form-select text-uppercase" name="departamento" id="cbDepto"
                                        aria-label="Departamento" required disabled></select>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                                    <label for="cbPuesto" class="form-label fw-bold">Puesto<sup>*</sup></label>
                                    <select class="form-select text-uppercase" name="puesto" id="cbPuesto"
                                        aria-label="Puesto" required disabled></select>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                                    <label for="iptIngreso" class="form-label fw-bold">Fecha ingreso<sup>*</sup></label>
                                    <input type="date" class="form-control" name="fecha_ingreso" id="iptIngreso"
                                        aria-label="Fecha de ingreso" required>
                                    <span class="text-danger hide"><i class="icon-attention"></i>Este campo es
                                        requerido</span>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                                    <label for="iptCrecimiento" class="form-label fw-bold">Crecimiento laboral</label>
                                    <div class="input-group">
                                        <input type="text" readonly class="form-control"
                                            aria-label="Crecimiento laboral" id="iptCrecimiento" disabled>
                                        <span class="input-group-text"><i class="icon-chart-line"></i></span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                                    <label for="iptSalarioDiario" class="form-label fw-bold">Salario
                                        diario<sup>*</sup></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="icon-dollar"></i></span>
                                        <input type="text" class="form-control text-end" placeholder="00.00"
                                            name="salario_diario" id="iptSalarioDiario" aria-label="Salario diario"
                                            tipo="cifra" required />
                                    </div>
                                    <span class="form-text text-danger hide"><i class="icon-attention"></i>Este campo es
                                        requerido</span>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                                    <label for="iptSalarioFiscal" class="form-label fw-bold">Salario fiscal</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="icon-dollar"></i></span>
                                        <input type="text" class="form-control text-end" placeholder="00.00"
                                            name="salario_fiscal" id="iptSalarioFiscal" aria-label="Salario fiscal"
                                            tipo="cifra" />
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                                    <label for="iptPorcentajeAnti" class="form-label fw-bold">Porcentaje de
                                        anticipo<sup>*</sup></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control text-end" value="30"
                                            aria-label="Porcentaje de anticipo" name="anticipo" id="iptPorcentajeAnti"
                                            value="30" tipo="numero" required />
                                        <span class="input-group-text"><i class="icon-percent"></i></span>
                                    </div>
                                    <span class="form-text text-danger hide"><i class="icon-attention"></i>Este campo es
                                        requerido</span>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                                    <label for="iptImss" class="form-label fw-bold">Alta ante en IMSS</label>
                                    <div class="input-group">
                                        <input type="date" class="form-control" placeholder="01-01-2005"
                                            name="fecha_imss" id="iptImss" aria-label="Alta ante en IMSS">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                                    <label for="iptNSS" class="form-label fw-bold">NSS</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" aria-label="Número de Seguro Social"
                                            name="nss" id="iptNSS" placeholder="0000000000" tipo="numero"
                                            maxlength="11" />
                                        <span class="input-group-text"><i class="icon-barcode"></i></span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                                    <label for="iptRFC" class="form-label fw-bold">RFC</label>
                                    <input type="text" class="form-control text-uppercase" name="rfc" id="iptRFC"
                                        placeholder="xxxx050101XXX" aria-label="Registro Federal del Contribuyente"
                                        maxlength="13" tipo="textoNum" />
                                </div>
                                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                                    <label for="cbBanco" class="form-label fw-bold">Banco</label>
                                    <select class="form-select" name="banco" id="cbBanco" aria-label="Banco"></select>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                                    <label for="iptCuenta" class="form-label fw-bold">No. Cuenta ó clave</label>
                                    <input type="text" class="form-control" name="cuenta" id="iptCuenta"
                                        placeholder="XXXX XXXX XXXX" aria-label="No. Cuenta" tipo="numero" disabled
                                        maxlength="18" />
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="iptOpiniones" class="form-label fw-bold">Opiniones sobre el
                                        colaborador</label>
                                    <input type="text" class="form-control" name="opiniones" id="iptOpiniones"
                                        placeholder="Escribe alguna opinión, expectativa o apoyo adicional sobre el colaborador."
                                        aria-label="Observaciones" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <span class="form-text"><sup>*</sup> Los campos son obligatorios</span>
                </div>
                <div class="col-12 mt-4 mb-4">
                    <button type="submit"
                        class="btn btn-primary col-12 col-sm-6 offset-sm-3 col-md-4 offset-md-4">Guardar
                        información</button>
                </div>
            </form>
            <script src="src/js/reclutamiento.js?t=<?php echo time(); ?>"></script>

        </div>
    </main>
</body>

</html>