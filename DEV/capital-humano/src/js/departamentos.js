window.ctrlDpto = window.ctrlDpto || "ctrl/ctrl-departamentos.php";
window.listDepartamentos = [];

$(document).on("sobresJS", function () {
    $("#nav-departamento-tab").on("click", () => init_components_departamentos());
    $("#cbUDNDepto").on("change", () => tbDepartamentos());
});

function init_components_departamentos() {
    nav("nav-departamento-tab", "departamentos.php");
    setTimeout(() => {
        $("#formDpto").validation_form({ opc: "nuevo"}, (datos) => nuevo_departamento(datos));
        tbDepartamentos();
    }, 200);
}

// Ajax nuevo departamento
function nuevo_departamento(datos) {
    let busqueda = listDepartamentos.filter((e) => e.valor.toUpperCase() == $("#departamento").val().toUpperCase());
    if (busqueda.length == 0) {
        send_ajax(datos, ctrlDpto).then((data) => {
            if (data === true) {
                tbDepartamentos();
                alert({ title: "Se guardó con éxito.", icon: "success", btn1: true });
            } else log(data);
        });
    } else {
        alert({ title: "Este departamento ya existe.", icon: "error", btn1: true });
    }
}
// Tabla departamentos
function tbDepartamentos() {
    let datos = new FormData();
    datos.append("opc", "tbDepartamentos");
    datos.append("udn", $("#cbUDNDepto").val());

    send_ajax(datos, ctrlDpto, $("#tbDatosDpto")).then((data) => {
        listDepartamentos = data;
        let tbody = [];
        let icon_warning = '<i class="text-danger icon-attention-1"></i>';
        
        data.forEach((e) => {
            let colaboradores = e.colaboradores == 0 ? icon_warning + " No hay coladoradores" : "#" + e.colaboradores;
            let button = e.colaboradores == 0 ? '<button class="btn btn-sm btn-outline-danger" onClick="delete_dpto(' + e.id + ')"><i class="icon-trash"></i></button>' : "";
            let row = [{ html: e.valor }, { html: colaboradores, class: "text-center" }, { html: button, class: "text-center" }];
            tbody.push(row);
        });

        $("#tbDatosDpto")
            .html("")
            .create_table({ table: { id: "tbDpto" }, thead: ["departamento", "colaboradores", "opciones"], tbody });
        $("#tbDpto").table_format({ ordering: false });
    });
}

function delete_dpto(id) {
    alert({ title: "¿Está seguro de eliminar este departamento?", icon: "question" }).then((result) => {
        if (result.isConfirmed) {
            let datos = new FormData();
            datos.append("opc", "eliminar");
            datos.append("id", id);
            send_ajax(datos, ctrlDpto).then((data) => {
                if (data === true) {
                    alert();
                    tbDepartamentos();
                } else console.log(data);
            });
        }
    });
}
