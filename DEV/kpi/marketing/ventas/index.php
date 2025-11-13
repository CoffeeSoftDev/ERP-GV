<?php
    require_once('layout/head.php');
    require_once('layout/core-libraries.php');
?>

<!-- CoffeeSoft Framework -->
<script src="https://plugins.erp-varoch.com/coffee-lib/coffeeSoft.js"></script>
<script src="https://rawcdn.githack.com/SomxS/Grupo-Varoch/refs/heads/main/src/js/plugins.js"></script>
<script src="https://www.plugins.erp-varoch.com/ERP/JS/complementos.js"></script>

<style>
    /* 🎗️ Ribbon dorado tipo metálico */
    .ribbon-gold {
    background: linear-gradient(
        135deg,
        #f6e27a 0%,
        #f6d365 10%,
        #fda085 25%,
        #eac15a 40%,
        #d4af37 60%,
        #b78e1e 85%,
        #f5ce62 100%
    );
    padding: 4px 0;
    width: 160px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    letter-spacing: 0.5px;
    text-transform: uppercase;
    border-radius: 2px;
    backdrop-filter: brightness(1.05);
    }

    /* 🤖 Botón Consultar con IA */
    .ask-ai-btn {
        background: linear-gradient(90deg,rgb(129, 177, 255), #8B5CF6, #3B82F6);
        background-size: 200% 200%;
        animation: moveGradient 4s ease infinite;
    }

    .ask-ai-btn:hover {
        animation-duration: 2s; /* más rápido al hover */
    }

    @keyframes moveGradient {
        0%   { background-position: 0% 50%; }
        50%  { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

</style>
<body>
    <?php require_once('../../../layout/navbar.php'); ?>

    <main>
        <section id="sidebar"></section>

        <div id="main__content">
            <nav aria-label='breadcrumb'>
                <ol class='breadcrumb'>
                    <li class='breadcrumb-item text-uppercase text-muted'>KPI</li>
                    <li class='breadcrumb-item fw-bold active'>Ventas</li>
                </ol>
            </nav>

            <div class="main-container" id="root"></div>

            <!-- Module Scripts -->
            <script src="src/js/kpi-ventas.js?t=<?php echo time(); ?>"></script>
            <script src="src/js/dashboard.js?t=<?php echo time(); ?>"></script>
            <script src="src/js/calendario.js?t=<?php echo time(); ?>"></script>
        
        </div>
    </main>
</body>
</html>
