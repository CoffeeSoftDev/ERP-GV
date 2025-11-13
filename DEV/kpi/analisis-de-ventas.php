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
                    <li class='breadcrumb-item text-uppercase text-muted'>dirección</li>

                    <li class='breadcrumb-item fw-bold active'>Análisis de ventas</li>
                </ol>
            </nav>
            <div class="row mb-3 row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 d-flex justify-content-end">
                <div class="col mb-3 date2 hide">
                    <label for="iptDate2" class="fw-bold">Fecha 1</label>
                    <div class="input-group">
                        <input type="text" class="form-control text-center" id="iptDate2">
                        <span class="input-group-text"><i class="icon-calendar"></i></span>
                    </div>
                </div>
                <div class="col date1 mb-3">
                    <label for="iptDate" class="fw-bold">Rango de consulta</label>
                    <div class="input-group">
                        <input type="text" class="form-control text-center calendar" id="iptDate">
                        <span class="input-group-text"><i class="icon-calendar"></i></span>
                    </div>
                </div>
                <div class="col mb-3">
                    <label for="cbYears" class="fw-bold">Año comparativo</label>
                    <select class="form-select" id="cbYears"></select>
                </div>
                <div class="col mb-3">
                    <label class="control-label"></label>
                    <button type="button" class="col-12 btn btn-primary" id="btnOK"><i class="icon-search"></i>
                        Consultar</button>
                </div>
            </div>

            <div class="row mb-3" id="tbDatos"></div>
            <div class="row line2" id="tbDatos2"></div>

            <script src='src/js/analisis-de-ventas.js?t=<?php echo time(); ?>'></script>
            <script src='src/js/analisis-de-costos.js?t=<?php echo time(); ?>'></script>
        </div>
    </main>
</body>

</html>