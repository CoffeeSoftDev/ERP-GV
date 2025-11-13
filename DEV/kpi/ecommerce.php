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
                    <li class='breadcrumb-item text-uppercase text-muted'>direccion</li>

                    <li class='breadcrumb-item fw-bold active'>Ecommerce</li>
                </ol>
            </nav>



                <div class="mb-2" id="filterBar"></div>
                
                <div class="row" id="contentData"></div>

                   <!-- externo -->
                <script src="https://plugins.erp-varoch.com/ERP/JS/complementos.js?t=<?php echo time(); ?>"></script>
                <script src="https://plugins.erp-varoch.com/ERP/JS/plugin-table.js?t=<?php echo time(); ?>"></script>
                <script src="https://plugins.erp-varoch.com/ERP/JS/plugin-forms.js?t=<?php echo time(); ?>"></script>
                <script src="https://15-92.com/ERP3/src/js/CoffeSoft.js?t=<?php echo time(); ?>"></script>

                
                <script src='src/js/ecommerce.js?t=<?php echo time(); ?>'></script>
        </div>

    </main>


</body>

</html>