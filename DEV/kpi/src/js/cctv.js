window.ctrl = "ctrl/ctrl-cctv.php";

$(async () => {
    $(".datepicker").daterangepicker(
        {
            startDate: moment().subtract(7, "days"),
            endDate: moment(),
            showDropdowns: true,
            ranges: {
                "Última semana": [moment().subtract(7, "days"), moment()],
            },
        },
        () => tbBitacora()
    );

    $(".datepicker")
        .next("span")
        .on("click", function () {
            $(".datepicker").click();
        });

    tbBitacora();
});

async function tbBitacora() {
    const dates = $(".datepicker").valueDates();
    const data = await fn_ajax({ opc: "tbBitacora", dates }, ctrl);
    $("#tbDatos").html("").create_table(data);
}
async function uploadBitacoraCCTV() {
    const fechaActual = format_date($(".datepicker").valueDates()[1]);
    const data = await fn_ajax({ opc: "dataCCTV", date: $(".datepicker").valueDates()[1] }, ctrl);
    if (!data || data.suite == 0) alert({ icon: "info", title: "No hay suites registradas en turnos del día " + fechaActual + ".", timer: 3000 });
    else {
        if (data.file) {
            bootbox
                .dialog({
                    closeButton: true,
                    title: "SE REGISTRARON <b>" + data.suite + "</b> SUITES EL DÍA <b>" + fechaActual + "</b>.",
                    message:
                        '<a href="' + data.file + '" class="btn btn-success col-12" target="_blank" >Ver archivo</a><button class="btn btn-info col-12 mt-3" onclick="fileCCTV()">Actualizar</button>',
                })
                .on("shown.bs.modal", () => {
                    $(".bootbox-close-button").css({
                        color: "red",
                        "font-size": "20px",
                        "text-shadow": "2px 2px 4px rgba(0, 0, 0, 0.5)",
                    });
                });
        } else fileCCTV();
    }
}
async function fileCCTV() {
    const fechaActual = format_date($(".datepicker").valueDates()[1]);
    $("#cctvFile")
        .click()
        .off("change")
        .on("change", async () => {
            const file = $("#cctvFile")[0].files[0];
            if (file) {
                const result = await alert({ icon: "question", title: '¿Estas seguro de subir el archivo "' + file.name + '", el día ' + fechaActual + "?" });

                if (result.isConfirmed) {
                    let datos = new FormData();
                    datos.append("opc", "cctvFile");
                    datos.append("file", file);
                    datos.append("date", $(".datepicker").valueDates()[1]);
                    datos.append("idE", $("#cbUDN").val());

                    const data = await send_ajax(datos, ctrl);
                    if (data === true) {
                        alert();
                        tbBitacora();
                        $("#cctvFile").val('');
                    } else console.error(data);
                }
            }
        });
}
