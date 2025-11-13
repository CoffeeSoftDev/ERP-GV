<?php
if(isset($_COOKIE['IDU'])){
    echo '<script> 
        let ruta = localStorage.getItem("url");
        const HREF = new URL(window.location.href);
        const ERP = HREF.pathname.split("/").filter(Boolean)[0];
        const MODELO = ruta.split("/").filter(Boolean)[0];
        window.location.href = HREF.origin + "/" + ERP + "/" + MODELO;
    </script>';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="src/img/logos/logo_icon.png" type="image/x-icon">
    <title>Grupo Varoch</title>
    <link rel="stylesheet" href="src/plugin/fontello/css/fontello.css">
    <link rel="stylesheet" href="src/plugin/fontello/css/animation.css">
    <link rel="stylesheet" href="src/plugin/bootstrap-5/css/bootstrap.min.css">
    <link rel="stylesheet" href="src/plugin/sweetalert2/sweetalert2.min.css">
    <link rel="stylesheet" href="acceso/src/css/index.css">
</head>

<body>
    <section>
        <div id="logo">
            <img id="img1" src="src/img/logos/logo_col.png" alt="">
            <img id="img2" src="src/img/logos/logo_row.png" alt="">
        </div>
        <div id="form" class="p-5">
            <form id="form_login" novalidate>
                <h4>¡ B I E N V E N I D O !</h4>
                <div class="col-12 input-group mt-5 mb-5">
                    <span class="input-group-text"><i class="icon-user"></i></span>
                    <input type="text" class="form-control" name="usuario" id="usuario" placeholder="Usuario, correo o teléfono" required>
                </div>
                <div class="col-12 input-group mb-5">
                    <span class="input-group-text"><i class="icon-key"></i></span>
                    <input type="password" class="form-control" name="clave" id="clave" placeholder="••••••••••" required>
                    <button type="button" class="input-group-text" id="btnEye"><i class="icon-eye"></i></button>
                </div>
                <div class="col-12 mb-5">
                    <button type="submit" class="col-12 btn btn-info">Iniciar sesión</button>
                </div>
                <div class="col-12 text-center">
                    <u class="pointer">¿Olvidaste tu contraseña?</u>
                </div>
            </form>
        </div>
    </section>


    <script src="src/plugin/jquery/jquery-3.7.0.min.js"></script>
    <script src="src/plugin/bootstrap-5/js/bootstrap.min.js"></script>
    <script src="src/plugin/bootbox.min.js"></script>
    <script src="src/plugin/sweetalert2/sweetalert2.all.min.js"></script>
    <script src="src/js/complementos.js"></script>
    <script src="acceso/src/js/index.js?t=<?php echo time(); ?>"></script>
</body>

</html>