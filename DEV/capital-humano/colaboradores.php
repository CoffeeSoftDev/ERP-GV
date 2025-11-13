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
            <nav class="p-2 p-sm-0" class="p-2 p-sm-0">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item text-muted">CH</li>
                    <li class="breadcrumb-item fw-bold active">Colaboradores</li>
                </ol>
            </nav>
            <div class="row d-flex justify-content-end mb-3" id="filterBar"></div>
            <div class="row" id="tbDatos"></div>

            <script src="src/js/_CH.js?t=<?php echo time(); ?>"></script>
            <script src="src/js/_Colaboradores.js?t=<?php echo time(); ?>"></script>
            <script src="src/js/colaboradores.js?t=<?php echo time(); ?>"></script>
        </div>
    </main>
</body>

</html>