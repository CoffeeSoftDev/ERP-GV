window.ctrlPerfil = window.ctrlPerfil || "ctrl/ctrl-perfil.php";
window.guia_movil = window.guia_movil || '';
window.guia_desk  = window.guia_desk || '';

$(function () {
    datos_usuario();
    pop();
    if (getCookies().ACT === undefined) guia_virtual();

    $("#correo")
        .next("span")
        .on("click", () => {
            if ($("#correo").attr("disabled") !== undefined) $("#correo").attr("placeholder", $("#correo").val()).val("").removeAttr("disabled");
            else $("#correo").val($("#correo").attr("placeholder")).attr("disabled", "disabled");

            $("#correo").focus();
            $("#correo").validar_entrada();
        });

    $("#telefono")
        .next("span")
        .on("click", () => {
            if ($("#telefono").attr("disabled") !== undefined) $("#telefono").attr("placeholder", $("#telefono").val()).val("").removeAttr("disabled");
            else $("#telefono").val($("#telefono").attr("placeholder")).attr("disabled", "disabled");

            $("#telefono").focus();
            $("#telefono").validar_entrada();
        });
        
    $("#usser")
        .next("span")
        .on("click", () => {
            if ($("#usser").attr("disabled") !== undefined) $("#usser").attr("placeholder", $("#usser").val()).val("").removeAttr("disabled");
            else $("#usser").val($("#usser").attr("placeholder")).attr("disabled", "disabled");

            $("#usser").focus();
            $("#usser").validar_entrada();
        });

    $("#key_auto").on("click", () => generarKey());
    $("#key_show").on("click", () => mostrarKey());

    $("#form_perfil").on("submit", (e) => {
        e.preventDefault();

        if (validar_formulario()) {
            codigo_seguridad().then(() => {
                modalSeguridad();
            });
        }
    });

    $("#file-profile").on("change", () => foto_perfil().then(() => subir_foto()));
});
function foto_perfil() {
    return new Promise((resolve, reject) => {
        let file = $("#file-profile")[0].files[0];
        if (file) {
            $("#content__perfil span").css({ display: "flex" });
            $("#content__perfil span").html('<i class="animate-spin icon-spin6"></i> Analizando');
            setTimeout(() => {
                let reader = new FileReader();
                reader.readAsDataURL(file);
                $("#content__perfil span").hide();
                $("#content__perfil span").removeAttr("style");
                $("#content__perfil span").html('<i class="icon-camera"></i><br> Subir foto');
                reader.onload = function (e) {
                    $("#imgPerfil").attr("src", e.target.result);
                    $("#navbarPerfil").attr("src", e.target.result);
                    resolve();
                };
            }, 500);
        } else {
            $("#imgPerfil").attr("src", "../src/img/user.png");
            $("#navbarPerfil").attr("src", "../src/img/user.png");
            reject();
        }
    });
}
function subir_foto() {
    let foto = $("#file-profile")[0].files[0];
    if (foto) {
        let datos = new FormData();
        datos.append("opc", "foto");
        datos.append("foto", foto);
        send_ajax(datos, ctrlPerfil).then((data) => {
            if (data === true) alert();
            else console.log(data);
        });
    }
}
function generarKey() {
    $(".clave").val(clave_aleatoria());
    $(".clave").prop("type", "text");
    $(".key_show").html('<i class="icon-eye-off"></i>');
}
function mostrarKey() {
    const INPUT = $(".clave");

    INPUT.prop("type", INPUT.prop("type") === "text" ? "password" : "text");

    iconEye = INPUT.prop("type") === "text" ? "icon-eye-off" : "icon-eye";
    $(".key_show").html(`<i class="${iconEye}"></i>`);
}
function validar_formulario() {
    valor = true;
    if ($("#telefono").attr("disabled") == undefined && !$("#telefono").val()) {
        valor = false;
        $("#telefono").focus();
        $("#telefono").addClass("is-invalid");
        $("#telefono").parent().next("span.text-danger").removeClass("hide");
    } else if ($("#correo").attr("disabled") == undefined && !$("#correo").val()) {
        valor = false;
        $("#correo").focus();
        $("#correo").addClass("is-invalid");
        $("#telefono").parent().next("span.text-danger").removeClass("hide");
    } else if ($("#clave1").val() && $("#clave1").val().length < 6 && $("#clave1").val().length > 0 ) {
        valor = false;
        $("#clave1").addClass("is-invalid");
        $("#clave1").focus();
        $("#clave2").addClass("is-invalid");
        if ($("#clave1").parent().next("span").length > 0)
            $("#clave1").parent().next("span.text-danger").removeClass("hide").html('<i class="icon-warning-1"></i> Por seguridad ingresa 6 caracteres mínimo.');
        else {
            const span = $("<span>", { class: "form-text text-danger", html: '<i class="icon-warning-1"></i>  Por seguridad ingresa 6 caracteres mínimo.' });
            $("#clave1").parent().parent().append(span);
        }
    } else if ($("#clave1").val() !== $("#clave2").val()) {
        valor = false;
        $("#clave2").focus();
        if ($("#clave2").next("span").length > 0)
            $("#clave2").next("span.text-danger").removeClass("hide").html('<i class="icon-warning-1"></i> Las contraseñas no coinciden.');
        else {
            const span = $("<span>", { class: "form-text text-danger", html: '<i class="icon-warning-1"></i>  Las contraseñas no coinciden.'});
            $("#clave2").parent().append(span);
        }
    }

    return valor;
}
function codigo_seguridad() {
    return new Promise((resolve) => {
        let datos = new FormData();
        datos.append("opc", "seguridad");
        send_ajax(datos, ctrlPerfil, "Te enviaremos un código de seguridad, espera un momento.").then((data) => {
            console.log(data);
            if (data == false) alert({ icon: "warning", title: "No se pudo generar el código, comunicate con Capital Humano." });
            else resolve();
        });
    });
}
function modalSeguridad() {
    let form = $("<form>", { novalidate: true });

    form.create_elements2([
        {
            lbl: "Ingrese el código de seguridad",
            id: "codigo",
            type: "number",
            required: true,
        },
        {
            elemento: "modal_button",
        },
    ]);

    bootbox.dialog({
        title: "CÓDIGO DE SEGURIDAD",
        message: form,
        onShown: function () {
            form.validation_form({ opc: "update" }, (datos) => {
                actualizar_datos(datos);
            });
        },
    });
}
function actualizar_datos(fomrData) {
    let datos = new FormData($("#form_perfil")[0]);
    fomrData.forEach(function (value, key) {
        datos.append(key, value);
    });

    send_ajax(datos, ctrlPerfil).then((data) => {
        // console.log(data);
        if (data == false) alert({ icon: "warning", title: "El código no coincide.", btn1: true });
        else if (data.key === true)
            alert({ icon: "success", title: "La contraseña se actualizó con éxito, cerraremos la sesión para activar tu  nueva cuenta.", btn1: true }).then((result) =>
                result.isConfirmed ? (location.href = "../salir") : ""
            );
        else if (data.datos === true) alert({ icon: "success", title: "Los datos se actualizarón con éxito." });
        else alert({ icon: "error", title: "Ocurrio un error inesperado, intentalo más tarde.", text: "Si el problema persiste comunicate con el área de TIC'S", btn1: true });
    });
}
function datos_usuario() {
    let datos = new FormData();
    datos.append("opc", "datos");
    send_ajax(datos, ctrlPerfil).then((data) => {
        console.log(data);
        if(data != null && data.foto_perfil != null ) $('#imgPerfil').attr('src',data.foto_perfil);
        for (const index in data) {
            if ($("#" + index).is("input")) $("#" + index).val(data[index]);
            else $("#" + index).text(data[index]);
            delete data[index];
        }
    });
}
function guia_virtual() {
    let steps = guia_desk;
    if ($(window).width() < 800 && $(window).height() < 700) steps = guia_movil;

    introJs()
        .setOptions({
            exitOnOverlayClick: false,
            // dontShowAgain: true,
            steps,
        })
        .start();
}
guia_movil = [
    {
        title: "¡ B I E N V E N I D O !",
        intro: "Hola, al ser tu primera vez aquí, te guiaremos en una pequeña guía virtual.",
    },
    {
        element: $("#btnSidebar")[0],
        title: "Menú",
        intro: "Con este botón, podrás mostrar u ocultar el menú para acceder a los módulos.",
    },
    {
        element: $("#perfil_photo_prev")[0],
        title: "Perfil",
        intro: "Aquí podrás acceder a tu perfil y cerrar sesión.",
    },
    {
        element: $("#content__perfil")[0],
        intro: "Aquí podrás actualizar tú foto de perfil.",
    },
    {
        element: $("#telefono").parent().parent()[0],
        title: "Editar información",
        intro: 'Da click en el <i class="icon-pencil"></i>, para desbloquear el campo de texto y poder editar la información.',
    },
    {
        element: $("#key_auto").parent().parent()[0],
        title: "Contraseña automática",
        intro: 'Da click en la <i class="icon-key"></i>, para generar una clave aleatoria.',
    },
    {
        element: $("#key_show").parent().parent()[0],
        title: "Mostrar/Ocultar contraseña",
        intro: 'Da click en el <i class="icon-eye"></i>, para mostrar u ocultar la contraseña.',
    },
    {
        element: $("#btn_update")[0],
        title: "Confirmación de seguridad.",
        intro: "Al presionar este botón se te enviará un código de seguridad para confirmar que seas tú, por ello, es importante mantener tu información de contacto actualizada.",
    },
    {
        title: "¡IMPORTANTE!.",
        intro: "Al ser la primera vez que ingresas es necesario actualizar tu contraseña, para porder acceder a los módulos.",
    },
];
guia_desk = [
    {
        title: "¡ B I E N V E N I D O !",
        intro: "Hola, al ser tu primera vez aquí, te guiaremos en una pequeña guía virtual.",
    },
    {
        element: $("#btnSidebar")[0],
        title: "Menú",
        intro: "Con este botón, podrás mostrar u ocultar el menú para acceder a los módulos.",
    },
    {
        element: $("#perfil_photo_prev")[0],
        title: "Perfil",
        intro: "Aquí podrás acceder a tu perfil y cerrar sesión.",
    },
    {
        element: $("#content__perfil")[0],
        title: "Foto de perfil",
        intro: "Aquí podrás actualizar tú foto de perfil.",
    },
    {
        element: $("#info_data")[0],
        title: "Información",
        intro: "Aquí podrás ver y actualizar tu información personal, que proporcionaste a Capital Humano al darte de alta.",
    },
    {
        element: $("#telefono").next("span")[0],
        title: "Editar campo",
        intro: "Da click aquí, para desbloquear el campo de texto y poder editar la información.",
    },
    {
        element: $("#key_auto")[0],
        title: "Contraseña automática",
        intro: "Da click aquí, para generar una clave aleatoria.",
    },
    {
        element: $("#key_show")[0],
        title: "Mostrar/Ocultar contraseña",
        intro: "Da click aquí, para mostrar u ocultar la contraseña.",
    },
    {
        element: $("#btn_update")[0],
        title: "Confirmación de seguridad.",
        intro: "Al presionar este botón se te enviará un código de seguridad para confirmar que seas tú, por ello, es importante mantener tu información de contacto actualizada.",
    },
    {
        title: "¡IMPORTANTE!.",
        intro: "Al ser la primera vez que ingresas es necesario actualizar tu contraseña, para porder acceder a los módulos.",
    },
];
