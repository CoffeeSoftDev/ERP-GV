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
        redireccion($(this).attr("href"));
    });

});

function redireccion(ruta) {
    window.location.href = slash() + ruta;
}

function cerrar_sesion(){
    window.location.href = slash() + 'salir';
}

function directorios() {
    
    $.ajax({
        type: "POST",
        url: slash()+"acceso/ctrl-sidebar.php",
        contentType: false,
        processData: false,
        cache: false,
        dataType: "json",
        success: function (data) {
            let modelo = "";
            data.forEach((m) => {
                let directorios = "";
                if (m.directorios.length > 0) {
                    m.directorios.forEach((d) => {
                        directorios += `
                            <li><a href="${d.ruta}">${d.directorio}</a></li>
                        `;
                    });
                }

                let submodulo = "";
                if (m.submodulos.length > 0) {
                    m.submodulos.forEach((s) => {
                        let directorios2 = "";
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

            $("#sidebar").append(`<ul> ${modelo} </ul>`);
        },
    });
}

function slash(){
    
    let slashesCount = (window.location.href.match(/\//g) || []).length - 4;

    // Crear el string de slashes (../)
    let slash = "";
    for (let i = 0; i < slashesCount; i++) slash += "../";
    
    
    return slash;
}