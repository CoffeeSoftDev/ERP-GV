<?php
if (empty($_COOKIE["IDU"]))  require_once('../acceso/ctrl/ctrl-logout.php');
require_once('layout/head.php');
require_once('layout/script.php');
?>
<body>
<?php require_once('../../layout/navbar.php'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dom-to-image/2.6.0/dom-to-image.min.js"></script>


    <main>
        <section id="sidebar"></section>
        <div id="main__content">
            <nav aria-label='breadcrumb'>
                <ol class='breadcrumb'>
                    <li class='breadcrumb-item text-uppercase text-muted'>ch</li>
                        
                    <li class='breadcrumb-item fw-bold active'>Concentrado</li>
                </ol>
            </nav>
            <div class="row mb-3 d-flex justify-content-end" id="root"></div>
            <script src='src/js/concentrado-app.js?t=<?= time(); ?>'></script>
        </div>
    </main>
</body>
</html>