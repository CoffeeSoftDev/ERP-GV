window.ctrlCH = "ctrl/ctrl-ch.php";
const colaborador = new Colaboradores(ctrlCH);
colaborador.container = "#filterBar";
colaborador.tbContainer = "#tbDatos";

$(async () => {
    await colaborador.filterBar();
    if (sessionStorage.getItem("udn")) colaborador.udn.val(sessionStorage.getItem("udn")).change();

    colaborador.tbColaboradores();
    colaborador.udn.on("change", () => {
        sessionStorage.setItem("udn", colaborador.udn.val());
        colaborador.tbColaboradores();
    });
    colaborador.filtro.on("change", () => colaborador.tbColaboradores());
    $("#btnNuevoColaborador").on("click", () => redireccion("capital-humano/reclutamiento.php"));
});

const active = (val) => colaborador.modalActive(val);
const low = (val) => colaborador.modalLow(val);
const edit = (val) => colaborador.editCollaborator(val);
const birthday = () => colaborador.birthday();
