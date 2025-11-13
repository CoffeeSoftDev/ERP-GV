window.ctrlSucursal = window.ctrlSucursal || "ctrl/ctrl-sucursales.php";
window.jsonPatron = window.jsonPatron || {};
window.listUDN = window.listUDN || [];
$(function () {
    tbSucursales();
    listPatron();

    $("#cbPatron")
        .prev("button")
        .on("click", function () {
            modalPatron();
        });

    $("#formSucursal").on("submit", (e) => {
        e.preventDefault();
        const SUCURSAL = $("#iptUDN");

        if (!SUCURSAL.val()) {
            valor = false;
            SUCURSAL.focus();
            SUCURSAL.addClass("is-invalid");
            SUCURSAL.next("span").removeClass("hide");
            return;
        }

        if (listUDN.includes(SUCURSAL.val().toUpperCase())) {
            SUCURSAL.focus();
            SUCURSAL.addClass("is-invalid");
            SUCURSAL.next("span").removeClass("hide");
            swal_warning("Esta sucursal ya existe, intenta con otro nombre.");
            return;
        }

        swal_question("¿Los datos son correctos?").then((result) => {
            if (result.isConfirmed) {
                let datos = new FormData();
                datos.append("opc", "newSucursal");
                datos.append("patron", $("#cbPatron").val());
                datos.append("sucursal", SUCURSAL.val().toUpperCase().trim());
                send_ajax(datos, ctrlSucursal).then((data) => {
                    if (data === true) {
                        $("#formSucursal")[0].reset();
                        SUCURSAL.removeClass("is-invalid");
                        SUCURSAL.next("span").addClass("hide");
                        $("#listSucursal").append(
                            `<option>${SUCURSAL.val().toUpperCase()}</option>`
                        );
                        swal_success();
                        tbSucursales();
                    }
                });
            }
        });
    });
});

// FUNCIONES DE INICIO
function listPatron() {
    let datos = new FormData();
    datos.append("opc", "listPatron");
    send_ajax(datos, ctrlSucursal).then((data) => {
        jsonPatron = data;
        $("#cbPatron").option_select({data:data});
    });
}
function tbSucursales() {
    let datos = new FormData();
    datos.append("opc", "tbUDN");
    tb_ajax(datos, ctrlSucursal, "#tbDatos").then((data) => {
        let tbody = "";
        let arrayUDN = [];
        $("#listSucursal").html("");
        data.forEach((e) => {
            arrayUDN.push(e.sucursal.toUpperCase());
            $("#listSucursal").append(
                `<option value="${e.sucursal}"></option>`
            );

            const ESTADO = e.estado;
            let iconToggle = "icon-toggle-on";
            if (ESTADO == 0) iconToggle = "icon-toggle-off";

            tbody += `
                <tr>
                    <td>${e.patron}</td>
                    <td>${e.sucursal}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-info" onClick="updateModal(${e.idE},${e.idP},'${e.sucursal}')">
                            <i class="icon-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" estado="${ESTADO}" id="btnStatus${e.idE}" onClick="toggleStatus(${e.idE});">
                            <i class="${iconToggle}"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
        listUDN = arrayUDN;
        $("#tbDatos").html(`
            <table class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th>Razón social</th>
                        <th>Sucursal</th>
                        <th>Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    ${tbody}
                </tbody>
            </table>
        `);
    });
}

// FUNCIONES DE PATRON
function modalPatron() {
    bootbox
        .dialog({
            title: ` NUEVA RAZÓN SOCIAL`,
            message: `
                <div class="col-12 mb-3">
                    <label for="iptModalPatron" class="form-label fw-bold">Razón social</label>
                    <input list="listPatronModal" class="form-control" id="iptModalPatron">
                    <span class="form-text text-danger hide">
                        <i class="icon-warning-1"></i>
                        El campo es requerido.
                    </span>
                    <datalist id="listPatronModal"></datalist>
                </div>
                <div class="col-12 mb-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-primary col-5" onclick="newPatron();">Actualizar</button>
                    <button class="btn btn-outline-danger col-5 offset-2 bootbox-close-button col-5" id="btnCerrarModal">Cancelar</button>
                </div>
            `,
        })
        .on("shown.bs.modal", () => {
            $("#listPatronModal").option_list(jsonPatron);
        });
}
function newPatron() {
    const INPUT = $("#iptModalPatron");
    if (!INPUT.val()) {
        INPUT.focus();
        INPUT.addClass("is-invalid");
        INPUT.next("span").removeClass("hide");
        return;
    }

    if (
        jsonPatron.some(
            (e) => e.valor.toUpperCase() == INPUT.val().toUpperCase()
        )
    ) {
        INPUT.focus();
        INPUT.addClass("is-invalid");
        INPUT.next("span").removeClass("hide");
        swal_warning("Esta razón social ya existe, intenta con otro nombre.");
        return;
    }

    swal_question("¿Los datos son correctos?").then((result) => {
        if (result.isConfirmed) {
            let datos = new FormData();
            datos.append("opc", "newPatron");
            datos.append("patron", INPUT.val().toUpperCase().trim());
            send_ajax(datos, ctrlSucursal).then((data) => {
                if (data === true) {
                    listPatron();
                    swal_success();
                    $("#btnCerrarModal").click();
                }
            });
        }
    });
}
// FUNCIONES DE SUCURSAL
function updateModal(idE, idP, udn) {
    bootbox
        .dialog({
            title: `EDITAR INFORMACIÓN`,
            message: `
                <div class="col-12 mb-3">
                    <label for="cbModalPatron" class="form-label fw-bold">Razón Social</label>
                    <select class="form-select" id="cbModalPatron"></select>
                </div>
                <div class="col-12 mb-3">
                    <label for="iptModalUDN" class="form-label fw-bold">Sucursal</label>
                    <input list="listModalUDN" class="form-control" id="iptModalUDN" value="${udn}">
                    <span class="form-text text-danger hide">
                        <i class="icon-warning-1"></i>
                        El campo es requerido.
                    </span>
                    <datalist id="listModalUDN"></datalist>
                </div>
                <div class="col-12 mb-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-primary col-5" onclick="updateSucursal(${idE},'${udn}');">Actualizar</button>
                    <button class="btn btn-outline-danger col-5 offset-2 bootbox-close-button col-5" id="btnCerrarModal">Cancelar</button>
                </div>
            `,
        })
        .on("shown.bs.modal", () => {
            listUDN.forEach(e => {
                $('#listModalUDN').append('<option value="'+e+'"></option>');
            });
            $("#cbModalPatron").option_select(jsonPatron);
            $('#cbModalPatron option[value="' + idP + '"]').prop(
                "selected",
                true
            );
        });
}
function updateSucursal(id,udn) {
    let SUCURSAL = $("#iptModalUDN");

    if (!SUCURSAL.val()) {
        SUCURSAL.focus();
        SUCURSAL.addClass("is-invalid");
        SUCURSAL.next("span").removeClass("hide");
        return;
    }

    if (listUDN.includes(SUCURSAL.val().toUpperCase()) && SUCURSAL.val().toUpperCase() != udn) {
        SUCURSAL.focus();
        SUCURSAL.addClass("is-invalid");
        SUCURSAL.next("span").removeClass("hide");
        swal_warning("Esta sucursal ya existe, intenta con otro nombre.");
        return;
    }



    swal_question('¿Los datos son correctos?').then((result) => {
        if (result.isConfirmed) {
            let datos = new FormData();
            datos.append("opc", "editSucursal");
            datos.append("id", id);
            datos.append("sucursal", SUCURSAL.val().toUpperCase().trim());
            datos.append("patron", $("#cbModalPatron").val());
            send_ajax(datos, ctrlSucursal).then((data) => {
                if (data === true) {
                    $("#btnCerrarModal").click();
                    tbSucursales();
                    swal_success();
                }
                console.log(data);
            });
        }
    });
}
// DESACTIVAR SUCURSAL
function toggleStatus(id) {
    const BTN = $("#btnStatus" + id);
    const ESTADO = BTN.attr("estado");

    let estado = 0;
    let iconToggle = '<i class="icon-toggle-off"></i>';
    let question = "¿DESEA DESACTIVARLO?";
    if (ESTADO == 0) {
        estado = 1;
        iconToggle = '<i class="icon-toggle-on"></i>';
        question = "¿DESEA ACTIVARLO?";
    }

    swal_question(question).then((result) => {
        if (result.isConfirmed) {
            let datos = new FormData();
            datos.append("opc", "stadoSucursal");
            datos.append("id", id);
            datos.append("estado", estado);
            send_ajax(datos, ctrlSucursal).then((data) => {
                if (data === true) {
                    BTN.html(iconToggle);
                    BTN.attr("estado", estado);
                }
            });
        }
    });
}
