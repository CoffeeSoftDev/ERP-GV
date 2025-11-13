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
                    <li class='breadcrumb-item text-uppercase text-muted'>kpi</li>
                    <li class='breadcrumb-item fw-bold active'>CCTV Tabachines</li>
                </ol>
            </nav>
            <div class="row mb-3 d-flex justify-content-end">
                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                    <label for="cbUDN">Seleccionar UDN</label>
                    <select class="form-select" id="cbUDN">
                        <option value="1">QUINTA TABACHINES</option>
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                    <label for="iptDate">Fecha</label>
                    <div class="input-group">
                        <input type="text" class="form-control datepicker" id="iptDate">
                        <span class="input-group-text"><i class="icon-calendar"></i></span>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                    <label for="" class="col-12"></label>
                    <button type="button" class="btn btn-primary col-12" onclick="uploadBitacoraCCTV();">Subir bitacora</button>
                    <input type="file" id="cctvFile" class="hide"/>
                </div>
            </div>

            <div class="row" id="tbDatos"></div>
            <script src='src/js/cctv.js?t=<?php echo time(); ?>'></script>
        </div>
    </main>
</body>

</html>