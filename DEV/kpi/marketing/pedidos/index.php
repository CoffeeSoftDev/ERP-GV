<?php
    require_once('layout/head.php');
    require_once('layout/core-libraries.php');
?>


<!-- CoffeeSoft Framework -->
<!--<script src="../../../src/js/coffeeSoft.js"></script>-->
 <script src="https://plugins.erp-varoch.com/coffee-lib/coffeeSoft.js"></script>
<script src="https://rawcdn.githack.com/SomxS/Grupo-Varoch/refs/heads/main/src/js/plugins.js"></script>
<script src="https://www.plugins.erp-varoch.com/ERP/JS/complementos.js"></script>

<body>
    <?php require_once('../../../layout/navbar.php'); ?>

    <main>

        <section id="sidebar"></section>

        <div id="main__content">

            <nav aria-label='breadcrumb'>
                <ol class='breadcrumb'>
                    <li class='breadcrumb-item text-uppercase text-muted'>KPI</li>
                    <li class='breadcrumb-item fw-bold active'>Pedidos</li>
                </ol>
            </nav>

            <div class=" main-container" id="root"></div>

            <!-- Módulos del Sistema -->
        
            <script src="js/pedidos.js?t=<?php echo time(); ?>"></script>
            <script src="js/report.js?t=<?php echo time(); ?>"></script>
            <script src="js/administrador.js?t=<?php echo time(); ?>"></script>

        </div>
    </main>
</body>
</html>

