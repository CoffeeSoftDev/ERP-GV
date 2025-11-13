window.ctrlReclutamiento = window.ctrlReclutamiento || "ctrl/ctrl-reclutamiento.php";
window.gral = window.gral || "ctrl/ctrl-general.php";
window.departamentos = window.departamentos || "";
window.puestos = window.puestos || "";

$(function () {
    init_component();

    $("#formDatos").validation_form({}, (datos) => {
        const TEL = $("#iptTelefono");
        // const CURP = $("#iptCURP");

        if (TEL.val().length < 10) TEL.next("span").removeClass("hide").html('<i class="icon-warning-1"></i> Ingrese un teléfono a 10 dígitos.');
        // else if (CURP.val().length < 18) CURP.addClass("is-invalid").next("span").removeClass("hide").html('<i class="icon-attention"></i> Ingresa una CURP válida.');
        else if ($("#file-profile").val().length < 1) {
            alert({
                icon: "question",
                title: "Aún no se ha subido la foto del colaborador",
                text: "¿Desea continuar de todas formas?",
            }).then((result) => {
                if (result.isConfirmed) nuevoColaborador(datos);
            });
        } else nuevoColaborador(datos);
    });

    $("#cbBanco").on("change", () => $("#iptCuenta").removeAttr("disabled"));
    $("#cbUDN").on("change", () => selectDptos());
    $("#cbDepto").on("change", () => selectPuestos());
    $("#iptTelefono").on("input", () => validar_telefono());
    $("#iptCURP")
        .on("input", () => validar_curp())
        .on("blur", () => analizar_curp());

    $("#cbGradoEstudio").on("change", function () {
        if ($(this).val() >= 5) $("#cbCarrera").removeAttr("disabled");
        else $("#cbCarrera").attr("disabled", "disabled");
    });
    $("#iptIngreso").on("change", function () {
        let fechas = diferenciaFechas($(this).val());
        $("#iptCrecimiento").val(fechas.s);
    });
    $("#file-profile").on("change", () => {
        var file = $("#file-profile")[0].files[0];
        if (file) {
            $("#photo__perfil span").css({ display: "flex" });
            $("#photo__perfil span").html('<i class="animate-spin icon-spin6"></i> Analizando');
            setTimeout(() => {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $("#photoColaborador").attr("src", e.target.result);
                };
                reader.readAsDataURL(file);
                $("#photo__perfil span").hide();
                $("#photo__perfil span").removeAttr("style");
                $("#photo__perfil span").html('<i class="icon-camera"></i><br> Subir foto');
            }, 500);
        } else {
            $("#photoColaborador").attr("src", "../src/img/user.png");
        }
    });

    $("#iptNacimiento").on("blur", function () {
        let edad = diferenciaFechas($(this).val());
        $("#iptEdad").val(edad.s);
    });
});

//FUNCIONES PARA LA CARGA INICIAL
function init_component() {
    simple_ajax(gral).then((data) => {
        const select2 = true;
        const placeholder = "- SELECCIONAR -";

        $("#cbGradoEstudio").option_select({ data: data.grado });
        $("#cbLugarNacimiento").option_select({ data: data.nacimiento, placeholder, select2 });
        $("#cbBanco").option_select({ data: data.bancos, placeholder });
        $("#cbCarrera").option_select({ data: data.carrera, placeholder, select2 });
        $("#cbPatron").option_select({ data: data.patron, placeholder });
        $("#cbUDN").option_select({ data: data.udn, placeholder });
        $("#cbDepto").option_select({ data: {}, placeholder });
        $("#cbPuesto").option_select({ data: {}, placeholder });

        departamentos = data.departamentos;
        puestos = data.puestos;
    });
}
function selectDptos() {
    const placeholder = "- SELECCIONAR -";
    let data = departamentos.filter((e) => e.udn == $("#cbUDN").val());

    $("#cbDepto")
        .prop("disabled", false)
        .option_select({
            data,
            placeholder,
            select2: data.length > 20 ? true : false,
        });

    $("#cbPuesto").option_select({ data: {}, placeholder });
}
function selectPuestos() {
    let data = puestos.filter((e) => e.dpto == $("#cbDepto").val());

    $("#cbPuesto")
        .prop("disabled", false)
        .option_select({
            data,
            placeholder: "- SELECCIONAR -",
            select2: data.length > 20 ? true : false,
        });
}
function nuevoColaborador(datos) {
    alert({ icon: "question", title: "¿Todos los datos son correctos?" }).then((result) => {
        if (result.isConfirmed) {
            let title = '<i class="animate-spin icon-spin4"></i> Guardando';

            send_ajax(datos, ctrlReclutamiento, title).then((data) => {
                console.error(data);
                
                $("#formDatos").find(":submit").prop("disabled", false);
                let errores = "";

                if (data === true) {
                    $("#formDatos")[0].reset();
                    $("#photoColaborador").attr("src", "../src/img/user.png");
                    alert({ title: "Los datos se guardaron con éxito.", btn1: true });
                } else {
                    if (data.email == true) {
                        $("#iptEmail").addClass("is-invalid");
                        errores += "Correo, ";
                    }
                    if (data.curp == true) {
                        $("#iptCURP").addClass("is-invalid");
                        errores += "CURP, ";
                    }
                    if (data.phone == true) {
                        $("#iptTelefono").addClass("is-invalid");
                        errores += "Teléfono";
                    }

                    if (data.email === true || data.curp === true || data.phone === true) {
                        alert({
                            icon: "error",
                            title: errores,
                            text: "Ya existe otro colaborador con estos campos.",
                            btn1: true,
                        });
                    }
                }
            });
        }
    });
}

function validar_telefono() {
    const TEL = $("#iptTelefono");
    if (TEL.val().length > 0 && TEL.val().length < 10) {
        TEL.addClass("is-invalid");

        if (TEL.next("span").hasClass("hide")) TEL.next("span").removeClass("hide").html('<i class="icon-attention-1"></i> Ingresa una teléfono válido.');
    }

    if (TEL.val().length === 10) TEL.removeClass("is-invalid").next("span").addClass("hide");
}
function validar_curp() {
    const CURP = $("#iptCURP");
    if (CURP.val().length > 0 && CURP.val().length < 18) {
        CURP.addClass("is-invalid");

        if (CURP.next("span").hasClass("hide")) CURP.next("span").removeClass("hide").html('<i class="icon-attention"></i> Ingresa una CURP válida.');
    }

    if (CURP.val().length === 18) CURP.removeClass("is-invalid").next("span").addClass("hide");
}
function analizar_curp() {
    let curp = $("#iptCURP").val();
    // Obtener fecha de nacimiento
    let dia = curp.slice(8, 10);
    let mes = curp.slice(6, 8);
    // Obtener el año con un algoritmo
    let year = new Date().getFullYear();
    let ynow = year.toString().slice(2, 4);
    let y1 = curp.slice(4, 6);

    if (parseInt(y1) > parseInt(ynow)) year = parseInt(y1) + 1900;
    if (parseInt(y1) <= parseInt(ynow)) year = parseInt(y1) + 2000;

    let fecha_nacimiento = year + "-" + mes + "-" + dia;
    $("#iptNacimiento").val(fecha_nacimiento);

    // Calcular edad
    let edad = diferenciaFechas(fecha_nacimiento);
    $("#iptEdad").val(edad.s);
    if (edad.y > 80)
        $("#iptEdad").addClass("text-danger").css({
            "background-image": 'url("https://image.similarpng.com/very-thumbnail/2021/06/Attention-sign-icon.png")',
            "background-size": "20px",
            "background-repeat": "no-repeat",
            "padding-left": "20px",
        });
    else $("#iptEdad").removeClass("text-danger").removeAttr("style");

    // Obtener el genero
    let genero = curp.slice(10, 11);
    if (genero.toUpperCase() == "H") $("#iptGenero").val("H").change();
    if (genero.toUpperCase() == "M") $("#iptGenero").val("M").change();
    if (genero.toUpperCase() == "I") $("#iptGenero").val("I").change();

    // Obtener el RFC
    let rfc = curp.slice(0, 10);
    $("#iptRFC").val(rfc);
}
