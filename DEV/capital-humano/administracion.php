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
            <script src='src/js/administracion.js?t=<?php echo time(); ?>'></script>
<nav aria-label='breadcrumb'>
    <ol class='breadcrumb'>
        <li class='breadcrumb-item text-uppercase text-muted'>ch</li>

        <li class='breadcrumb-item fw-bold active'>Administracion</li>
    </ol>
</nav>
<div class="row">
    <nav>
        <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <button class="nav-link text-black active" id="nav-incidencias-tab" data-bs-toggle="tab" data-bs-target="#nav-incidencias"
                type="button" role="tab" aria-controls="nav-incidencias" aria-selected="true">Incidencias</button>
            <button class="nav-link text-black" id="nav-departamento-tab" data-bs-toggle="tab" data-bs-target="#nav-departamento"
                type="button" role="tab" aria-controls="nav-departamento" aria-selected="true">Departamentos</button>
            <button class="nav-link text-black" id="nav-puestos-tab" data-bs-toggle="tab" data-bs-target="#nav-puestos"
                type="button" role="tab" aria-controls="nav-puestos" aria-selected="false">Puestos</button>
        </div>
    </nav>
    <div class="tab-content" id="nav-tabContent">
        <div class="tab-pane fade show active pt-3" id="nav-incidencias" role="tabpanel" aria-labelledby="nav-incidencias-tab">
            <?php require_once('incidencias-calendar.php') ?>
        </div>
        <div class="tab-pane fade show pt-3" id="nav-departamento" role="tabpanel" aria-labelledby="nav-departamento-tab">
            <?php require_once('departamentos.php') ?>
        </div>
        <div class="tab-pane fade pt-3" id="nav-puestos" role="tabpanel" aria-labelledby="nav-puestos-tab">
            <?php require_once('puestos.php') ?>
        </div>
    </div>
</div>
        </div>
    </main>
</body>
</html>