window.ctrlCH = "ctrl/ctrl-ch.php";
const prestamos = new Prestamos(ctrlCH);
prestamos.filterBar = "#filterBar";

$(async () => {
    createTabs();
    historyTabs();
});

// TABS
async function createTabs() {
    const data = [
        {
            tab: "Simulador",
            fn: "tabSimulador()",
            class: "text-dark",
            active: true,
            // card: { head: { html: $("#cbUDN option:selected").text() + " - Simulador" }, body: { id: "cardSimulador" } },
            contenedor: [
                { class: "row d-flex justify-content-end", id: "filterSimulador" },
                { class: "row", id: "tbDatosSimulador" },
            ],
        },
        {
            tab: "Compras",
            fn: "alert()",
            class: "text-dark hide",
            card: { head: { html: $("#cbUDN option:selected").text() + " - Compras" } },
            contenedor: [
                { class: "row d-flex justify-content-end", id: "datosCompras" },
                { class: "row", id: "tbDatosCompras" },
            ],
        },
    ];
    $("#divDatos").simple_json_nav(data);
}
async function historyTabs() {
    const tab = sessionStorage.getItem("tab");
    if ($("#" + tab).length) $("#" + tab).click();
    else $("#simulador").click();
}

function tabSimulador() {
    sessionStorage.setItem("tab", "simulador");
    prestamos.container = "#filterSimulador";
    prestamos.tbContainer = "#tbDatosSimulador";
    prestamos.simulardorInit();
}
const simulador = () => prestamos.simulador();
