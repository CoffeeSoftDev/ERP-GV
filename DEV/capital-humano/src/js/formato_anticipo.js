window.ctrlCH = "ctrl/ctrl-ch.php";
$(async () => {
    await buscar();
    imprimir();
});

async function buscar() {
    console.log("Hola ");
    let fechas = sessionStorage.getItem("fechas").split(",");
    const idAdvance = sessionStorage.getItem("anticipo");

    const data = await fn_ajax({ opc: "printAdvance", idAdvance }, ctrlCH);

    console.log(data);

    const date1 = fechas[0].split("-");
    const date2 = fechas[1].split("-");

    let periodo = date1[2] + "-" + date1[1] + "-" + date1[0] + " al " + date2[2] + "-" + date2[1] + "-" + date2[0];
    $("#fecha").html("Fecha: " + data.fecha);
    $("#hora").html("Hora: " + data.hora);
    $("#folio").html("Folio: FA-" + data.folio);
    $("#udn").html(data.udn);
    $("#solicito").html(data.colaborador);

    $("#datos_cliente")
        .find("*")
        .each(function () {
            let name = $(this).attr("name");
            for (const x in data) if (x == name) $(this).html(data[x]);
            if (name == "periodo") $(this).html(periodo);
        });
}

function imprimir() {
    window.print();
    sessionStorage.removeItem("anticipo");
    sessionStorage.removeItem("fechas");
    window.close();
}
