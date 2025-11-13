let link = "ctrl/ctrl-analisis-de-costos.php";

function tbCostos() {
    let datos = new FormData();
    datos.append("opc", "tbCostos");
    datos.append("dates", $("#iptDate").valueDates());
    datos.append("dates2", $("#iptDate2").valueDates());
    datos.append("year", $("#cbYears").val());

    send_ajax(datos, link, $("#tbDatos2")).then((data) => {
        console.log(data);

        $("#tbDatos2").html("<label class='fs-3 fw-bold text-center'>Resumen de costos</label>").create_table(data);

        //     // $("#tbCostos").table_format({
        //     //     ordering: false,
        //     //     paging: false,
        //     //     priority: "0,1,3,5",
        //     //     info: false,
        //     //     searching: false,
        //     // });

        //     // setTimeout(() => {
        //     //     $("#tbDatos div.col-sm-12").addClass("m-0 p-0");
        //     // }, 100);
    });
}

// MOSTRAR U OCULTAR CUENTAS
function toggleCosto(id) {
    $(".costo" + id).toggleClass("hide");
}
