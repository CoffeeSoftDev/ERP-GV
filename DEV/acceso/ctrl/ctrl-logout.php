<?php
    // Obtener todas las cookies
    $cookies = $_COOKIE;

    // Recorrer todas las cookies y configurar su expiración en el pasado
    foreach($cookies as $key => $value) {
        setcookie($key, '', time() - 3600, '/');
    }

    echo "<script>
            const HREF = new URL(window.location.href);
            const HASH = HREF.pathname.split('/').filter(Boolean);
            const ERP = HASH[0];

            localStorage.clear();
            sessionStorage.clear();
            
            window.location.href = HREF.origin + '/' + ERP;
    </script>";
?>