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
            <nav aria-label="breadcrumb" class="p-2 p-sm-0">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item text-muted">CH</li>
                    <li class="breadcrumb-item fw-bold active">Anticipos</li>
                </ol>
            </nav>

            <div class="row d-flex justify-content-end mb-3" id="filterBar"></div>
            
            <div id="tbDatos"></div>
            
            <script src="src/js/_CH.js?t=<?php echo time(); ?>"></script>
            <script src="src/js/_Anticipos.js?t=<?php echo time(); ?>"></script>
            <script src="src/js/anticipos.js?t=<?php echo time(); ?>"></script>
        </div>
    </main>
</body>

</html>