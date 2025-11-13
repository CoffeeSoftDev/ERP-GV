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
        <li class='breadcrumb-item text-uppercase text-muted'>tics</li>

        <li class='breadcrumb-item fw-bold active'>Validaciones</li>
    </ol>
</nav>
<!-- RESULTADO CREAR ELEMENTOS Y VALIDAR FORMULARIO -->
<form class="row mb-3" id="formDatos" novalidate></form>

<!-- COMO CREAR ELEMENTOS -->
<div class=" mb-3 row row-cols-1 row-cols-md-2 row-cols-lg-3">
    <div class="col">
        <div class="card border-success mb-3">
            <div class="card-header bg-transparent border-success">INPUT</div>
            <div class="card-body">
                <p>Existen 2 formas de crear INPUTS</p>
                <span class="form-text">1.- $('#crear_input').create_elements([<br>
                    { lbl: "Input", elemento: "input" }<br>
                    ]);</span><br><br>
                <span class="form-text">2.- $('#crear_input').create_elements([<br>
                    { lbl: "Input", type: "text" }<br>
                    ]);</span><br>
            </div>
            <script>
                $('#crear_input').create_elements([{ lbl: "INPUT", type: "text", div:"col-12"}]);
            </script>
            <div id="crear_input" class="card-footer bg-transparent border-success">
            </div>
        </div>
    </div>
</div>

<!-- COMO HACER UNA CARD -->
<div class="col-12 col-sm-12 col-md-12 card border-success mb-3">
    <div class="card-header bg-transparent border-success">¿CÓMO HACER ESTA CARD CON BOOTSTRAP 5?</div>
    <div class="card-body">
        <span class="fw-bold ">&lt;div class="card border-success mb-3"&gt;</span><br>
        <span class="fw-bold ms-3">&lt;div class="card-header bg-transparent
            border-success">Header&lt;/div&gt;</span><br>
        <span class="fw-bold ms-3">&lt;div class="card-body"&gt;</span><br>
        <span class="fw-bold ms-5">Body</span><br>
        <span class="fw-bold ms-3">&lt;/div&gt;</span><br>
        <span class="fw-bold ms-3">&lt;div class="card-footer bg-transparent border-success"&gt;</span><br>
        <span class="fw-bold ms-5">Footer</span><br>
        <span class="fw-bold ms-3">&lt;/div&gt;</span><br>
        <span class="fw-bold ">&lt;/div&gt;</span><br>
    </div>
    <div class="card-footer bg-transparent border-success">Listo!</div>
</div>

<!-- FORMAS DE USAR ALERT -->
<div class="mb-3 row row-cols-1 row-cols-sm-2 row-cols-lg-4">
    <h3 class="col-12 col-sm-12 col-lg-12 text-center mb-3">FORMAS DE USAR ALERTAS CON SWEETALERT</h3>

    <div class="col">
        <div class="card border-success mb-3">
            <div class="card-header bg-transparent border-success">Success</div>
            <div class="card-body">
                <span class="fw-bold">alert();</span><br>
            </div>
            <div class="card-footer bg-transparent border-success">
                <button type="button" class="btn btn-info col-12" onClick="alert();">Click Me!</button>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card border-warning mb-3">
            <div class="card-header bg-transparent border-warning">Warning</div>
            <div class="card-body">
                <span class="fw-bold">alert({icon:'warning'});</span><br>
            </div>
            <div class="card-footer bg-transparent border-warning">
                <button type="button" class="btn btn-info col-12" onClick="alert({icon:'warning'});">Click Me!</button>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card border-danger mb-3">
            <div class="card-header bg-transparent border-danger">Error</div>
            <div class="card-body">
                <span class="fw-bold">alert({icon:'error'});</span><br>
            </div>
            <div class="card-footer bg-transparent border-danger">
                <button type="button" class="btn btn-info col-12" onClick="alert({icon:'error'});">Click Me!</button>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card border-info mb-3">
            <div class="card-header bg-transparent border-info">Info</div>
            <div class="card-body">
                <span class="fw-bold">alert({icon:'info'});</span>
            </div>
            <div class="card-footer bg-transparent border-info">
                <button type="button" class="btn btn-info col-12" onClick="alert({icon:'info'});">Click Me!</button>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card border-info mb-3">
            <div class="card-header bg-transparent border-info">Mostrar título o texto</div>
            <div class="card-body">
                <span class="fw-bold">alert({</span><br>
                <span class="ms-3 fw-bold">icon:'info',</span><br>
                <span class="ms-3 fw-bold">title:'Título'</span><br>
                <span class="ms-3 fw-bold">text:'Texto opcional'</span><br>
                <span class="fw-bold">});</span><br>
            </div>
            <div class="card-footer bg-transparent border-info">
                <button type="button" class="btn btn-info col-12"
                    onClick="alert({icon:'info',title:'Título',text:'Texto opcional'});">Click
                    Me!</button>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card border-info mb-3">
            <div class="card-header bg-transparent border-info">Ocultar despues de X segundos</div>
            <div class="card-body">
                <span class="form-text">//1000 es igual a 1 segundo</span><br>
                <span class="form-text">//500 es igual a 0.5 segundos</span><br>
                <span class="fw-bold">alert({</span><br>
                <span class="ms-3 fw-bold">icon:'success',</span><br>
                <span class="ms-3 fw-bold">title:'Se guardó con éxito'</span><br>
                <span class="ms-3 fw-bold">btn1:false</span><br>
                <span class="ms-3 fw-bold">timer:1000</span><br>
                <span class="fw-bold">});</span><br>
            </div>
            <div class="card-footer bg-transparent border-info">
                <button type="button" class="btn btn-info col-12"
                    onClick="alert({icon:'success',title:'Se guardó con éxito',btn1:false,timer:1000,});">Click
                    Me!</button>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card border-info mb-3">
            <div class="card-header bg-transparent border-info">Preguntas</div>
            <div class="card-body">
                <span class="fw-bold">alert({</span><br>
                <span class="ms-3 fw-bold">icon:'question',</span><br>
                <span class="ms-3 fw-bold">title:'¿Pregunta?'</span><br>
                <span class="fw-bold">}).then(result=>{</span><br>
                <span class="ms-3 fw-bold">if(result.isConfirmed)</span><br>
                <span class="ms-5 fw-bold">alert();</span><br>
                <span class="fw-bold">});</span><br>
            </div>
            <div class="card-footer bg-transparent border-info">
                <button type="button" class="btn btn-info col-12"
                    onClick="alert({icon:'question',title:'¿Pregunta?'}).then(result=>{ if(result.isConfirmed) alert() });">Click
                    Me!</button>
            </div>
        </div>
    </div>

    <div class="col col-sm-12 col-md-12 col-lg-12">
        <div class="card border-info mb-3">
            <div class="card-header bg-transparent border-info">Botones</div>
            <div class="card-body">
                <span class="form-text">//Los botones se relacionan con sweetalert2</span><br>
                <span class="form-text">//btn1 = buttonConfirm</span><br>
                <span class="form-text">//btn2 = buttonCancel</span><br>
                <span class="form-text">//btn3 = buttonDeny</span><br>
                <span class="fw-bold">alert({</span><br>
                <span class="ms-3 fw-bold">icon:'info',</span><br>
                <span class="ms-3 fw-bold">title:'Existen 3 botones'</span><br>
                <span class="ms-3 fw-bold">btn1:true</span><br>
                <span class="ms-3 fw-bold">btn1Text:'OK, Guardar, Continuar'</span><br>
                <span class="ms-3 fw-bold">btn1Class:'btn btn-success'</span><br>
                <span class="ms-3 fw-bold">btn2:true</span><br>
                <span class="ms-3 fw-bold">btn2Text:'Cerrar o Cancelar'</span><br>
                <span class="ms-3 fw-bold">btn2Class:'btn btn-info'</span><br>
                <span class="ms-3 fw-bold">btn3:true</span><br>
                <span class="ms-3 fw-bold">btn3Text:'Denegar'</span><br>
                <span class="ms-3 fw-bold">btn3Class:'btn btn-danger'</span><br>
                <span class="fw-bold">}).then(result=>{</span><br>
                <span class="ms-5 fw-bold">if(result.isConfirmed)
                    alert({title:'JSON.stringify(result)'});</span><br>
                <span class="ms-5 fw-bold">if(result.isDeny) alert({title:'JSON.stringify(result)'});</span><br>
                <span class="fw-bold">});</span><br>
            </div>
            <div class="card-footer bg-transparent border-info">
                <button type="button" class="btn btn-info col-12"
                    onClick="alert({icon:'info',title:'Existen 3 botones',btn1:true,btn1Text:'OK, Guardar, Continuar',btn1Class:'btn btn-success',btn2:true,btn2Text:'Cerrar o Cancelar',btn2Class:'btn btn-outline-danger',btn3:true,btn3Text:'Denegar',btn3Class:'btn btn-warning'}).then(result=>{ if(result.isConfirmed) alert({title:'Confirmado'}); if(result.isDenied) alert({title:'Denegado',icon:'warning',}) });">Click
                    Me!</button>
            </div>
        </div>
    </div>
</div>

<div class="mb-3 row" id="contenedor"></div>

<script src="src/js/validaciones.js?<?php echo time(); ?>"></script>

        </div>
    </main>
</body>

</html>
