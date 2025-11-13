window.ctrlAnalisis_Ventas = window.ctrlAnalisis_Ventas || "ctrl/ctrl-analisis-de-ventas.php";
window.modal = window.modal || "";

$(() => {
    initComponent().then(() => {
        input_date_range();
        tbVentas();
        tbCostos();
    });

    $("#btnOK").on("click", () => {
        tbVentas();
        tbCostos();
    });

    $("#cbYears").on("change", function () {
        if ($(this).val() == "0") {
            $(".date2").removeClass("hide");
            $(".date1 label").html("Fecha 2");

            $("#iptDate").daterangepicker({
                startDate: moment().subtract(7, "days"),
                endDate: moment().subtract(1, "days"),
            });
            $("#iptDate2").daterangepicker({
                startDate: moment().subtract(7, "days"),
                endDate: moment().subtract(1, "days"),
            });
        } else {
            input_date_range();
            $(".date2").addClass("hide");
            $(".date1 label").html("Rango de fechas");
        }
    });
});

function input_date_range(date) {
    let startDate = date != undefined ? moment(date).subtract(6, "days") : moment().subtract(7, "days");
    let endDate = date != undefined ? moment(date) : moment().subtract(1, "days");

    $("#iptDate").daterangepicker({
        startDate,
        endDate,
        ranges: {
            "Última semana": [moment().subtract(7, "days"), moment().subtract(1, "days")],
            "Últimas 2 semanas": [moment().subtract(14, "days"), moment().subtract(1, "days")],
            "Últimas 3 semanas": [moment().subtract(21, "days"), moment().subtract(1, "days")],
            "Últimas 4 semanas": [moment().subtract(28, "days"), moment().subtract(1, "days")],
            "Mes Actual": [moment().startOf("month"), moment().subtract(1, "days")],
            "Mes Anterior": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")],
            "Año actual": [moment().startOf("year"), moment().subtract(1, "days")],
            "Año anterior": [moment().subtract(1, "year").startOf("year"), moment().subtract(1, "year").endOf("year")],
        },
    });

    $("#iptDate2").daterangepicker({
        startDate,
        endDate,
    });
}
// MOSTRAR U OCULTAR CUENTAS
function toggleCuenta(id) {
    $(".cuenta" + id).toggleClass("hide");
    $(".iconUDN" + id).toggleClass("icon-right-dir");
    $(".iconUDN" + id).toggleClass("icon-down-dir");
}
function initComponent() {
    return new Promise((resolve) => {
        let datos = new FormData();
        datos.append("opc", "listYears");
        send_ajax(datos, ctrlAnalisis_Ventas).then((data) => {
            data.unshift({ id: 0, valor: "Personalizado" });
            $("#cbYears").option_select({ data });
            let fechaActual = new Date();
            let yearAnterior = fechaActual.getFullYear() - 1;
            $("#cbYears").val(yearAnterior).change();
            resolve();
        });
    });
}
function tbVentas() {
    let datos = new FormData();
    datos.append("opc", "tbVentas");
    datos.append("dates", $("#iptDate").valueDates());
    datos.append("dates2", $("#iptDate2").valueDates());
    datos.append("year", $("#cbYears").val());
    send_ajax(datos, ctrlAnalisis_Ventas, $("#tbDatos")).then((data) => {
        $("#tbDatos").html("<label class='fs-3 fw-bold text-center'>Resumen de ventas</label>").create_table(data);
        $("#tbIngresos").table_format({
            ordering: false,
            paging: false,
            priority: "0,1,3,5",
            info: false,
            searching: false,
        });
        setTimeout(() => {
            $("#tbDatos div.col-sm-12").addClass("m-0 p-0");
        }, 100);
    });
}

function guia_virtual() {
    let steps = guia_desk;
    if ($(window).width() < 800 && $(window).height() < 700) steps = guia_movil;

    $("#iptDate").click();

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
        element: $("#iptDate")[0],
        title: "Rango de consulta.",
        intro: "Aquí podrás filtrar y seleccionar el rango de fechas que deseas analizar.",
    },
    {
        element: $(".ranges")[0],
        title: "Rango de consulta.",
        intro: "Aquí podrás filtrar y seleccionar el rango de fechas que deseas analizar.",
    },
    {
        title: "¡IMPORTANTE!.",
        intro: "Al ser la primera vez que ingresas es necesario actualizar tu contraseña, para porder acceder a los módulos.",
    },
];
guia_desk = [
    {
        element: $(".daterangepicker .ranges ul")[0],
        title: "Rango de consulta.",
        intro: "Aquí podrás filtrar y seleccionar el rango de fechas que deseas analizar.",
    },
];
