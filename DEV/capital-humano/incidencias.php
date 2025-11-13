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
            <link rel="stylesheet" href="src/css/incidencias.css">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item text-muted">CH</li>
                    <li class="breadcrumb-item fw-bold active">Incidencias</li>
                </ol>
            </nav>

            <div class="row d-flex justify-content-end mb-3" id="filterBar"></div>

            <div class="row">
                <div class="col-12 d-flex justify-content-end mt-2 mb-3">
                    <button class="btn btn-outline-warning" id="btnNomenglatura"><i
                            class="icon-help-circled-alt"></i></button>
                    <button class="btn btn-outline-info hide ms-2" id="btnLock">
                        <i class="icon-lock-open"></i></button>
                    <button class="btn btn-outline-info hide ms-2" id="btnLockOff">
                        <i class="icon-lock"></i></button>
                </div>
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 m-0 p-0 mb-3 hide"
                    id="nomenglatura">
                </div>
                <div class="col-12 table-responsive" id="tbDatos"></div>
            </div>

            <script src="src/js/_CH.js?t=<?php echo time(); ?>"></script>
            <script src="src/js/_Incidencias.js?t=<?php echo time(); ?>"></script>
            <script src="src/js/incidencias.js?t=<?php echo time(); ?>"></script>
        </div>
    </main>
</body>

</html>