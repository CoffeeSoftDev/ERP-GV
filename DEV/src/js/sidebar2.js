$(() => {
    directorios();

    $("#sidebar").on("click", "li", function (e) {
        e.stopPropagation(); // Evitar la propagación del evento
        e.preventDefault();

        $("#sidebar li:has(div)").not($(this).parents("li")).children("div").removeClass("active");
        $("#sidebar li:has(ul)").not($(this).parents("li")).children("ul").slideUp();

        // Si el <li> tiene un <ul> hijo, se muestra u oculta
        if ($(this).find("div").length > 0) {
            if ($(this).children("ul").is(":visible")) {
                $(this).children("ul").slideUp();
                $(this).children("div").removeClass("active");
            } else {
                $(this).children("ul").slideDown();
                $(this).children("div").addClass("active");
            }
        }

        if ($(this).find("a").length > 0) {
            $("#sidebar a").removeClass("active");
            $(this).children("a").toggleClass("active");
        }
    });

    $("#sidebar").on("click", "a", function () {
        const RUTA = $(this).attr("href");
        redireccion(RUTA);
    });

    if (localStorage.getItem('url')) {
        const RUTA = localStorage.getItem('url');
        redireccion(RUTA);
    } else {
        redireccion('layout/perfil.php');
    }
});

function directorios() {
    $.ajax({
        type: "POST",
        url: '../acceso/ctrl-sidebar.php',
        contentType: false,
        processData: false,
        cache: false,
        dataType: "json",
        success: function (data) {
            let modelo = '';
            data.forEach((m) => {
                let directorios = '';
                if (m.directorios.length > 0) {
                    m.directorios.forEach((d) => {
                        directorios += `
                            <li><a href="${d.ruta}">${d.directorio}</a></li>
                        `;
                    });
                }

                let submodulo = '';
                if (m.submodulos.length > 0) {
                    m.submodulos.forEach((s) => {
                        let directorios2 = '';
                        s.directorios.forEach((d) => {
                            directorios2 += `
                                <li><a href="${d.ruta}">${d.directorio}</a></li>
                            `;
                        });

                        submodulo += `
                        <li>
                            <div>
                                <span>${s.submodulo}</span>
                                <i class="icon-down-dir"></i>
                            </div>
                            <ul>
                                ${directorios2}
                            </ul>
                        </li>
                        `;
                    });
                }

                modelo += `
                <li>
                    <div>
                        <span>${m.modulo}</span>
                        <i class="icon-down-dir"></i>
                    </div>
                    <ul>
                        ${directorios}
                        ${submodulo}
                    </ul>
                </li>
                `;
            });

            $('#sidebar').append(`
            <ul>
                ${modelo}
            </ul>
            `);
        },
    });
}

function redireccion(ruta) {
    const PART = ruta.split('/').filter(Boolean);
    MODELO = PART[0];

    const HREF = new URL(window.location.href);
    const HASH = HREF.pathname.split('/').filter(Boolean);
    const ERP = HASH[0];
    const RUTA = HREF.origin + '/' + ERP + '/' + ruta;
    

    localStorage.setItem('url', ruta);
    if ((!localStorage.getItem('modelo') || localStorage.getItem('modelo') != MODELO) && MODELO != 'layout') {
        localStorage.setItem('modelo', MODELO);
        window.location.href = HREF.origin + '/' + ERP + '/' + MODELO;
    } else {
        $.ajax({
            type: "POST",
            url: RUTA,
            cache: false,
            success: function (data) {
                $("#main__content").html(data);

                if ($(window).width() <= 500) {
                    $("#sidebar").removeClass("active");
                    $("#main__content").removeClass("active");
                }
            },
        });
    }
}