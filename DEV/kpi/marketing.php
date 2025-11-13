<?php
    if( empty($_COOKIE["IDU"]) )  require_once('../acceso/ctrl/ctrl-logout.php');

    require_once('layout/head.php');
?>
  <script src="../src/plugin/jquery/jquery-3.7.0.min.js"></script>
    <!--BOOTSTRAP-->
    <script src="../src/plugin/bootstrap-5/js/bootstrap.min.js"></script>
    <script src="../src/plugin/bootstrap-5/js/bootstrap.bundle.js"></script>
    <!--SELECT2-->
    <script src="../src/plugin/select2/bootstrap/select2.min.js"></script>
    <!--BOOTBOX-->
    <script src="../src/plugin/bootbox.min.js"></script>
    <!-- SWEETALERT -->
    <script src="../src/plugin/sweetalert2/sweetalert2.all.min.js"></script>
    <!--DATERANGEPICKER-->
    <script src="../src/plugin/daterangepicker/moment.min.js"></script>
    <script src="../src/plugin/daterangepicker/daterangepicker.js"></script>
    <!--DATATABLES-->
    <script src="../src/plugin/datatables/datatables.min.js"></script>
    <script src="../src/plugin/datatables/dataTables.responsive.min.js"></script>
    <script src="../src/plugin/datatables/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <!-- Graficas -->
     <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
   <!--PERSONALIZADOS-->
   <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&amp;display=swap" rel="stylesheet">
    <script src="../src/js/navbar.js"></script>
    <script src="../src/js/sidebar.js"></script>


<script src = "https://erp-varoch.com/ERP24/gestor-de-actividades/src/js/CoffeeSoft.js"></script>
<script src = "https://rawcdn.githack.com/SomxS/Grupo-Varoch/refs/heads/main/src/js/plugins.js"></script>

<script src="https://www.plugins.erp-varoch.com/ERP/JS/complementos.js"></script>

<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

<body>
    <?php require_once('../layout/navbar.php'); ?>
    <main>
        <section id="sidebar"></section>
        <div id="main__content">
            <nav aria-label='breadcrumb'>
                <ol class='breadcrumb'>
                    <li class='breadcrumb-item text-uppercase text-muted'>Kpi</li>
                    <li class='breadcrumb-item fw-bold active'>Marketing</li>
                </ol>
            </nav>
            <div class="bg-[#DBDBDC] main-container" id="root"></div>
            <script src='src/js/kpis.js?t=<?php echo time(); ?>'></script>
        </div>
    </main>
</body>
</html>
