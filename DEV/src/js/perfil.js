$(document).ready(function () {
    let modal = null;
    $("#iptPerfilEmail")
        .siblings("button")
        .on("click", function () {
            $("#iptPerfilEmail").removeAttr('disabled');
            $("#iptPerfilEmail").focus();
        });

    $("#iptPerfilTelefono")
        .siblings("button")
        .on("click", function () {
            $("#iptPerfilTelefono").removeAttr('disabled');
            $("#iptPerfilTelefono").focus();
        });

    $("#iptPerfilPass1")
        .siblings("button")
        .on("click", function () {
            const KEY = $("#iptPerfilPass1");
            showPassword(KEY);
        });

    $("#iptPerfilPass2")
        .siblings("button")
        .on("click", function () {
            const KEY = $("#iptPerfilPass2");
            showPassword(KEY);
        });

    $("#iptPerfilPass2")
        .on("keyup", function () {
            if($(this).val() !== $("#iptPerfilPass1").val() ){
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
});

function showPassword(KEY) {
    if (KEY.attr("type") === "text") {
        KEY.attr("type", "password");
    } else {
        KEY.attr("type", "text");
    }
}

function cerrarModal() {
    modal.modal("hide");
}
function modalModulo() {
    modal = bootbox.dialog({
        title: `AUTORIZAR CAMBIOS`,
        message: `
                <div class="col-12 mb-3">
                    <label for="cbModulo" class="fw-bold">Clave de autorización</label>
                    <input list="modulos" class="form-control" id="iptText" placeholder="Escríbe el nombre del nuevo módulo">
                    <span class="form-text text-danger hide">
                        <i class="icon-warning-1"></i>
                        El área ya éxiste, intenta con otro nombre.
                    </span>
                </div>
                <div class="col-12 mb-3 d-flex justify-content-between">
                    <button  class="btn btn-primary col-5" onclick="nuevoModulo();">Actualizar</button>
                    <button class="btn btn-outline-danger col-5" onClick="cerrarModal();">Cancelar</button>
                </div>
            `,
    });
}
