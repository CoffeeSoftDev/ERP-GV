window.ctrlPuestos = window.ctrlPuestos || "ctrl/ctrl-puestos.php";
window.listPuestos = [];

$(document).on("sobresJS", function () {
    $("#nav-puestos-tab").on("click", () => init_components_puestos());
    $("#cbUDNPuestos").on("change", () => lsDepartamentos());
});

function init_components_puestos() {
    nav("nav-puestos-tab", "puestos.php");
    lsDepartamentos();
    $("#formPuesto").validation_form({ opc: "nuevo" }, (datos) => {
        let busqueda = listPuestos.filter((e) => e.valor.toUpperCase() == $("#puesto").val().toUpperCase());
        if (busqueda.length == 0) {
            send_ajax(datos, ctrlPuestos).then((data) => {
                if (data === true) {
                    alert();
                    tbPuestos();
                } else console.log(data);
            });
        } else {
            alert({ title: "Este departamento ya existe.", icon: "error", btn1: true });
        }
    });
}

function lsDepartamentos() {
    setTimeout(() => {
        let datos = new FormData();
        datos.append("opc", "lsDepartamentos");
        datos.append("udn", $("#cbUDNPuestos").val());
        send_ajax(datos, ctrlPuestos).then((data) => {
            $("#cbDepartamento").option_select({ data });
            tbPuestos();
        });
    }, 200);
}

function tbPuestos() {
    let datos = new FormData();
    datos.append("opc", "lsPuestos");
    datos.append("udn", $("#cbUDNPuestos").val());
    send_ajax(datos, ctrlPuestos, $("#tbDatosPuestos")).then((data) => {
        listPuestos = data;
        let tbody = [];
        let icon_warning = '<i class="text-danger icon-attention-1"></i>';

        data.forEach((e) => {
            let button = e.colaboradores == 0 ? '<button class="btn btn-sm btn-outline-danger" onClick="delete_puesto(' + e.id + ')"><i class="icon-trash"></i></button>' : "";
            let colaboradores = e.colaboradores == 0 ? icon_warning + " No hay colaboradores" : "# " + e.colaboradores;
            let row = [{ html: e.Area }, { html: e.valor }, { html: colaboradores, class: "text-center" }, { html: button, class: "text-center" }];
            tbody.push(row);
        });

        let table = {
            table: { id: "tbPuestos" },
            thead: ["departamentos", "puestos", "colaboradores", "opciones"],
            tbody,
        };

        $("#tbDatosPuestos").html("").create_table(table);
        $("#tbPuestos").table_format();
    });
}

function delete_puesto(id) {
    alert({ title: "¿Desea eliminar este puesto?", icon: "question" }).then((result) => {
        if (result.isConfirmed) {
            let datos = new FormData();
            datos.append("opc", "eliminar");
            datos.append("id", id);
            send_ajax(datos, ctrlPuestos).then((data) => {
                if (data === true) {
                    alert();
                    tbPuestos();
                } else console.log(data);
            });
        }
    });
}
