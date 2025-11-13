window.ctrlIndex = window.ctrlIndex || "acceso/ctrl/ctrl-index.php";
$(function () {
    if ($(window).width() > 950) {
        setTimeout(() => {
            $("#logo").addClass("active");
            $("#form").addClass("active");
            $("#form h4").addClass("active");
        }, 100);
    }

    $("#btnEye").on("click", () => showKey());

    $("form").on("submit", (e) => {
        e.preventDefault();
        if (validar_form()) log_in();
    });

    $("u").on("click", () => modalForgot());
});
// LOGIN
function showKey() {
    const KEY = $("#clave");
    const isTextType = KEY.attr("type") === "text";

    KEY.attr("type", isTextType ? "password" : "text");
    KEY.attr("placeholder", isTextType ? "••••••••••" : "Contraseña");

    const iconClass = isTextType ? "icon-eye" : "icon-eye-off";
    $("#btnEye").html(`<i class="${iconClass}"></i>`);
}
function log_in() {
    let datos = new FormData();
    datos.append("opc", "index");
    datos.append("user", $("#usuario").val().toUpperCase());
    datos.append("pass", $("#clave").val());
    send_ajax(datos, ctrlIndex).then((data) => {
        if (data.success === true) storage(data);
        else showErrorAccess();
    });
}
function validar_form() {
    let valor = true;

    if (!$("#usuario").val()) {
        valor = false;
        $("#usuario").focus();
        $("#usuario").addClass("is-invalid");
    }
    if (!$("#clave").val()) {
        valor = false;
        $("#clave").focus();
        $("#clave").addClass("is-invalid");
    }

    return valor;
}
function showErrorAccess() {
    alert({ icon: "error", title: "Usuario y/o contraseña incorrectos" });
}
function storage(data) {
    localStorage.clear();
    sessionStorage.clear();
    localStorage.setItem("url", data.ruta);
    localStorage.setItem("modelo", data.modelo);
    localStorage.setItem("submodelo", data.submodelo);

    redireccion(data.ruta);
}
function redireccion(modelo) {
    const HREF = new URL(window.location.href);
    const HASH = HREF.pathname.split("/").filter(Boolean);
    const ERP = HASH[0];

    window.location.href = HREF.origin + "/" + ERP + "/" + modelo;
}
// RECUPERAR CONTRASEÑA
function modalForgot() {
    bootbox.dialog({
        title: `RECUPERAR CONTRASEÑA`,
        message: `
                <form id="form_forgot" novalidate>
                    <div class="col-12 mb-3">
                        <label for="iptText" class="form-label">Ingresa tu número de teléfono ó correo electrónico.</label>
                        <input type="text" class="form-control" id="cuenta" name="cuenta" placeholder="Se asignará una clave temporal" required>
                        <span class="form-text text-danger hide">
                            <i class="icon-warning-1"></i>
                            Campo obligatorio
                        </span>
                    </div>
                    <div class="col-12 mb-3 d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary col-5">Continuar</button>
                        <button type="button" class="btn btn-outline-danger bootbox-close-button col-5" id="btnCerrarModal">Cancelar</button>
                    </div>
                </form>
            `,
        onShow: () => {
            $("#form_forgot").validation_form({ opc: "forgot" }, (datos) => recovery_key(datos));
        },
    });
}
function recovery_key(datos) {
    send_ajax(datos, ctrlIndex, "Espera un momento, se esta analizando los datos.").then((data) => {
        console.log(data);
        // if (!data) errorClave();
        // else successClave(data).then(() => bootbox.hideAll());
    });
}
function successClave(data) {
    return new Promise((resolve, reject) => {
        if (data.whatsapp === true || data.correo === true) {
            text = "Se ha enviado por ";
            text += (data.whatsapp === true ? "whatsapp" : "") + (data.whatsapp === true && data.correo === true ? " y " : "") + (data.correo === true ? "correo" : "");
            text += ".";

            alert({
                icon: "success",
                title: "Se te ha asignado una clave temporal.",
                text,
                btn1: true,
            }).then((result) => resolve(result));
        } else {
            alert({
                icon: "warning",
                title: "No se pudo enviar la clave temporal.",
                text: "No se encontró medio de comunicación, comunicate con el área de Capital Humano para actualizar tus datos.",
                btn1: true,
            }).then((result) => resolve(result));
        }
    });
}
function errorClave() {
    return new Promise((resolve, reject) => {
        alert({
            icon: "error",
            title: "No se ha encontrado ninguna cuenta asociada a estos datos.",
            text: "Comunicate a Capital Humano para actualizarlos.",
            btn1: true,
        }).then((result) => resolve(result));
    });
}
