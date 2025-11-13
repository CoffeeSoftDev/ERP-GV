$(() => {
    $("#btnSidebar").on("click", () => {
        $("#sidebar").toggleClass("active");
        $("#main__content").toggleClass("active");
    });

    $("#navbar li:has(ul)").on("click", function (e) {
        $(this).children("ul").slideToggle();
    });

    $("#btnNavbarPerfil").on("click", function (e) {
        e.preventDefault();

        const HREF = $(this).attr("href");
        $.ajax({
            type: "POST",
            url: HREF,
            cache: false,
            success: function (data) {
                $("#main__content").html(data);

                if ($(window).width() <= 500) {
                    $("#sidebar").toggleClass("active");
                    $("#main__content").toggleClass("active");
                }
            },
        });
    });

    if ($(window).width() <= 500) {
        $("#sidebar").removeClass("active");
        $("#main__content").removeClass("active");
    }

    if (getCookies().IDP != 7 && getCookies().IDP != 10) {
        $("#notifications").removeClass("hide");
    }
    
    //CAMBIAR EL FONDO AUTOMATICAMENTE
    // const fondo = document.getElementById('navbarFondo');

    // // Array con las imágenes de fondo
    // const imagenes = [
    //   '../src/css/navidad.png',
    //   '../src/css/navidad1.jpg',
    //   '../src/css/navidad2.jpg',
    // ];
    
    // let indice = 0;
    
    // // Cambiar el fondo cada 3 segundos
    // setInterval(() => {
    //   // Cambiar el fondo al siguiente de la lista
    //   fondo.style.backgroundImage = `url('${imagenes[indice]}')`;
    
    //   // Incrementar el índice (volver al inicio si es el último)
    //   indice = (indice + 1) % imagenes.length;
    // }, 3600000); // Cambiar cada 3000 ms (3 segundos)
});
