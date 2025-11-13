<?php
// if (empty($_COOKIE["IDU"]))  require_once('../../acceso/ctrl/ctrl-logout.php');
require_once('layout/head.php');
require_once('layout/script.php');
?>
<body>
<?php require_once('../../layout/navbar.php'); ?>

<style>
.container-main {
    min-height: calc(100vh - calc(4rem + 1px) - calc(4rem + 1px));
}
</style>

    <main>
        <section id="sidebar"></section>
        <div id="main__content">
            <nav aria-label='breadcrumb'>
                <ol class='breadcrumb'>
                    <li class='breadcrumb-item text-uppercase text-muted'>ch</li>
                    <li class='breadcrumb-item fw-bold active'>Periodos</li>
                </ol>
            </nav>
            
            <div class="container-main" id="root"></div>
            <script src='src/js/periodos.js?t=<?php echo time(); ?>'></script>
        </div>
    </main>
</body>
</html>
