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
            <nav aria-label='breadcrumb'>
    <ol class='breadcrumb'>
        <li class='breadcrumb-item text-uppercase text-muted'>tics</li>

        <li class='breadcrumb-item fw-bold active'>Sucursales</li>
    </ol>
</nav>
<div class="row col-12">
    <form class="col-md-4" id="formSucursal">
        <div class="col-12 mb-3">
            <label for="cbPatron">Razón social</label>
            <div class="input-group">
                <button type="button" class="input-group-text btn-success"><i class="icon-plus"></i></button>
                <select id="cbPatron" class="form-select text-uppercase"></select>
            </div>
        </div>
        <div class="col-12 mb-3">
            <label for="iptUDN">Sucursal</label>
            <input list="listSucursal" id="iptUDN" class="form-control" placeholder="Nombre de sucursal"
                autocomplete="off" aria-labely="Nombre de sucursal">
            <span class="text-danger form-text hide">
                <i class="icon-attention"></i> El campo es requerido
            </span>
            <datalist id="listSucursal"></datalist>
        </div>
        <div class="col-12 mb-3">
            <button type="submit" class="btn btn-primary col-12" id="btnSucursal"><i class="icon-plus"></i>
                Sucursal</button>
        </div>
    </form>
    <div class="col-md-8" id="tbDatos"></div>
</div>
<script src='src/js/sucursales.js?t=1695567796'></script>
        </div>
    </main>
</body>

</html>