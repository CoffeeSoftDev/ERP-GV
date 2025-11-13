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
                    <li class='breadcrumb-item text-uppercase text-muted'>CH</li>
                    <li class='breadcrumb-item fw-bold active'>Préstamos</li>
                </ol>
            </nav>
            <div class="row mb-3 hide" id="filterBar"></div>
            <div class="row" id="divDatos"></div>
        </div>
    </main>
    <script src='src/js/_CH.js?t=<?php echo time(); ?>'></script>
    <script src='src/js/_Prestamos.js?t=<?php echo time(); ?>'></script>
    <script src='src/js/prestamos.js?t=<?php echo time(); ?>'></script>
</body>
</html>