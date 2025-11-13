window.ctrlAdministracionCH = "ctrl/ctrl-administracion.php";
window.udnoption = window.udnoption || "";

$(() => {
    $(document).trigger("sobresJS");

    let tab = sessionStorage.getItem("nav");

    if (tab == null) {
        sessionStorage.setItem("nav", "nav-incidencias-tab");
        sessionStorage.setItem("url", "incidencias-calendar.php");
        $("#nav-incidencias-tab").click();
    } else {
        setTimeout(() => {
            $("#" + tab).click();
        }, 100);
    }
});

function lsUDN() {
    let datos = new FormData();
    datos.append("opc", "listUDN");
    send_ajax(datos, ctrlAdministracionCH).then((data) => $(".cbUDN").option_select({ data }));
}

function nav(nav, url) {
    sessionStorage.setItem("nav", nav);
    sessionStorage.setItem("url", url);
    lsUDN();
}
