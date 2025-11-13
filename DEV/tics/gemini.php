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
<link rel="stylesheet" href="src/css/gemini.css?t=<?php echo time(); ?>">
<nav aria-label='breadcrumb'>
    <ol class='breadcrumb'>
        <li class='breadcrumb-item text-uppercase text-muted'>tics</li>

        <li class='breadcrumb-item fw-bold active'>Gemini</li>
    </ol>
</nav>
<div class="row" id="tbDatos">
    <h1>Formulario de Consulta a GEMINI</h1>
    <div class="col-12 mb-3">
        <textarea class="form-control resize" rows="5" id="consulta"></textarea>
    </div>
    <div class="col-12 d-flex justify-content-center">
        <button type="button" class="btn btn-primary col-12 col-sm-8 col-md-6 col-lg-4 btn-lg mb-3" id="botonConsulta">Consultar</button>
    </div>
    <button class="btn btn-success col-1 offset-11" id="copyBtn"><i class="icon-clipboard-1"></i></button>
    <pre id="resultadoConsulta"></pre>
</div>


<script type="importmap" src="src/gemini/map.js">
</script>

<script type="module" src="src/js/gemini.js"></script>

<script>
$(function(){
    $('#copyBtn').on("click", function() {
        alert();
        // Obtener el texto de la etiqueta <pre>
        var texto = $("#resultadoConsulta").text();

        // Crear un nuevo objeto `ClipboardItem`
        var item = new ClipboardItem({ "text/plain": texto });

        // Copiar el texto al portapapeles
        navigator.clipboard.write([item]);

        // Mostrar un mensaje al usuario
        alert("El texto se ha copiado al portapapeles.");
    });
});
</script>
        </div>
    </main>
</body>

</html>