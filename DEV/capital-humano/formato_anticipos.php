<?php 
    if( empty($_COOKIE["IDU"]) )  require_once('../acceso/ctrl/ctrl-logout.php');

    // require_once('layout/head.php');
    // require_once('layout/script.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../src/img/logos/logo_icon.png" type="image/x-icon">
    <title>Formato anticipos</title>
    <!--BOOTSTRAP-->
    <link rel="stylesheet" href="../src/plugin/bootstrap-5/css/bootstrap.min.css">
</head>
<style>

.vertical-center {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
}

.mt-200 {
    margin-top: 200px !important;
}

.mt-100 {
    margin-top: 100px !important;
}

.mt-50 {
    margin-top: 50px !important;
}

@page {
    margin-top: 20px;
    margin-bottom: 20px;
    margin-right: 20px;
    margin-left: 20px;
}
</style>

<body>
    <header class="row m-0">
        <div class="col-3">
            <img src="../src/img/logos/logo_row.png" style="height:60px;" alt="">
        </div>
        <div class="col-6 vertical-center">
            <p class="fw-bold text-uppercase m-0 fs-5">FORMATO DE ANTICIPO</p>
            <p class="text-uppercase m-0" id="udn"></p>
        </div>
        <div class="col-3 text-end">
            <p class="fw-bold m-0" id="fecha">Fecha: 00-00-0000</p>
            <p class="fw-bold m-0" id="hora">Hora: 00:00:00</p>
            <p class="fw-bold m-0" id="folio">Folio: FA-0000</p>
        </div>
    </header>
    <main class="row m-0 mt-50" id="datos_cliente">
        <div class="col-6 m-0 p-1 row">
            <div class="col-12" name="colaborador"></div>
        </div>
        <div class="col-6 m-0 p-1 row">
            <div class="col-4">Departamento:</div>
            <div class="col-8" name="departamento"></div>
        </div>
        <div class="col-6 m-0 p-1 row">
            <div class="col-4">Período:</div>
            <div class="col-8" name="periodo"></div>
        </div>
        <div class="col-6 m-0 p-1 row">
            <div class="col-4">Puesto:</div>
            <div class="col-8" name="puesto"></div>
        </div>
        <div class="col-6 border-end m-0 mt-50 pb-4 row" id="tbDatos">
            <table class="table">
                <thead>
                    <tr>
                        <th class="text-center">CONCEPTO</th>
                        <th class="text-center">CANTIDAD</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="">Anticipos anteriores</td>
                        <td class=" text-end" name="anterior"></td>
                    </tr>
                    <tr style="border-bottom:10px;">
                        <td class="">Anticipo solicitado</td>
                        <td class=" text-end" name="solicitado"></td>
                    </tr>
                    <tr>
                        <td class="">Acumulado del período</td>
                        <td class=" text-end" name="acumulado"></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-6 m-0 mt-50 pb-4 row" id="tbDatos2">
            <table class="table">
                <thead>
                    <tr>
                        <th class="text-center">CONCEPTO</th>
                        <th class="text-center">CANTIDAD</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="">Anticipo permitido</td>
                        <td class=" text-end" name="porcentaje_sueldo"></td>
                    </tr>
                    <tr style="border-bottom:10px;">
                        <td class="">Acumulado del período</td>
                        <td class=" text-end" name="acumulado2"></td>
                    </tr>
                    <tr>
                        <td class="">Anticipo disponible</td>
                        <td class=" text-end" name="disponible"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </main>
    <footer class="row m-0 mt-100 datos_cliente">
        <div class="col-6">
            <p class="fw-bold text-center m-0" style="border-bottom:10px;" id="solicito"></p>
            <p class="text-center m-0" style="border-top:solid 1px;">Solicitó</p>
        </div>
        <div class="col-6">
            <p class="fw-bold text-center m-0" style="border-bottom:10px;" id="autorizo"> </p>
            <p class="text-center m-0" style="border-top:solid 1px;">Autorizó</p>
        </div>
    </footer>

    <!--JQUERY-->
    <script src="../src/plugin/jquery/jquery-3.7.0.min.js"></script>
    <!--BOOTSTRAP-->
    <script src="../src/plugin/bootstrap-5/js/bootstrap.min.js"></script>
    <script src="../src/plugin/bootstrap-5/js/bootstrap.bundle.js"></script>
    <!-- PERSONALIZADO -->
<script src="https://www.plugins.erp-varoch.com/ERP/JS/complementos.js"></script>
    <script src="src/js/formato_anticipo.js?t=<?php echo time(); ?>"></script>
</body>

</html>