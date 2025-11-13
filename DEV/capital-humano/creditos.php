<link rel="stylesheet" href="src/css/colaboradores.css">
<nav aria-label="breadcrumb" class="p-2 p-sm-0">
    <ol class="breadcrumb">
        <li class="breadcrumb-item text-muted">CH</li>
        <li class="breadcrumb-item pointer" onClick="redireccion('ch/colaboradores.php');">Colaboradores
        </li>
        <li class="breadcrumb-item fw-bold active">Créditos</li>
    </ol>
</nav>
<div class="row d-flex justify-content-between mb-3">
    <div class="col-12 col-sm-12 col-md-6 col-lg-9">
        <p class="fw-bold fs-5 m-0 p-0">HISTORIAL DE CRÉDITOS</p>
        <sub class="fw-bold m-0 p-0" id="titleCredito"></sub>
    </div>
    <div class="col-12 col-sm-12 col-md-4 col-lg-3">
        <button class="btn btn-primary col-12" title="Añadir nuevo colaborador" id="btnNuevoCredito">
            <i class="icon-plus"></i> Nuevo crédito
        </button>
    </div>
</div>
<div class="row">
    <table class="table table-sm table-hover table-bordered table-responsive nowrap" id="tbCreditos">
        <thead>
            <tr role="row">
                <th rowspan="2" class="align-middle">TIPO DE CRÉDITO</th>
                <th rowspan="2" class="align-middle"># CRÉDITO</th>
                <th rowspan="2" class="align-middle">ALTA</th>
                <th colspan="2" class="text-center">DESCUENTOS</th>
                <th rowspan="2" class="align-middle">NUM. DE PAGOS</th>
                <th rowspan="2" class="align-middle">OPCIONES</th>
            </tr>
            <tr role="row">
                <th>QUNCENAL</th>
                <th>MENSUAL</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Infonavit</td>
                <td class="text-center">34</td>
                <td>SI</td>
                <td class="text-end">$ 400</td>
                <td class="text-end">$ 800</td>
                <td class="text-center">2</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-info" id="btnEdit1"
                        onClick="modalEditarFormCredito(1);" title="Editar">
                        <i class="icon-pencil"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" estado="1" id="btnStatus1"
                        onClick="toggleStatus(1);" title="Baja">
                        <i class="icon-toggle-on"></i>
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<script src="src/js/creditos.js?t=<?php echo time(); ?>"></script>