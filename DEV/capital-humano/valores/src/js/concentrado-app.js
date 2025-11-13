let ctrl = "ctrl/ctrl-concentrado.php";
let app, concentrado;
let udn, colaborador;

$(async () => {
    fn_ajax({ opc: "init" }, ctrl).then((data) => {
        udn = data.udn;
        colaborador = data.colaborador;

        app = new App(ctrl);
        concentrado = new Concentrados(ctrl);
        app.render();
        // concentrado.render();
    });
});

class App extends Templates {
    constructor(link, div_modulo = "") {
        super(link, div_modulo);
        this.Project = "Encuestas";
    }

    render() {
        this.layout();
        this.filterBar();
        this.ls();
    }

    layout() {
        this.tabsLayout({
            parent: "root",
            json: [
                { tab: "Detallado", id: "tab-encuestas", active: true },
                {
                    tab: "Concentrado",
                    id: "tab-concentrado",
                    onClick: () => concentrado.render(),
                    contenedor: [
                        { id: "filterBarConcentrados", class: "col-12 line" },
                        { id: "containerConcentrados", class: "col-12 border border-2 p-3" },
                    ],
                },
            ],
        });

        this.primaryLayout({
            parent: "tab-encuestas",
            id: this.Project,
        });
    }

    filterBar() {
        this.createfilterBar({
            parent: "filterBar" + this.Project,
            data: [
                {
                    opc: "select",
                    class: "col-sm-3",
                    id: "udn",
                    lbl: "Seleccionar udn: ",
                    data: udn,
                    onchange: "app.getPeriods('periods','udn'); ",
                },
                {
                    opc: "select",
                    class: "col-sm-3",
                    id: "periods",
                    lbl: "Seleccionar período: ",
                    data: [{ id: 0, valor: "SELECCIONAR PERIODO" }],
                    onchange: "app.getEvaluated('colaborador','periods'); ",
                },
                {
                    opc: "select",
                    class: "col-sm-3",
                    id: "colaborador",
                    lbl: "Seleccionar colaborador: ",
                    data: [{ id: 0, valor: "SELECCIONAR COLABORADOR" }],
                    onchange: "app.ls()",
                },
            ],
        });

        // initialized.
        dataPicker({
            parent: "calendar",
            onSelect: (start, end) => {
                this.ls();
            },
        });
    }
    async ls() {
        if ($("#colaborador").val() == 0) $("#container" + this.Project).html('<strong><center><i class="icon-attention"></i> No se encontraron datos</center></strong>');
        else {
            const data = await fn_ajax(
                {
                    opc: "table",
                    type: "detallado",
                    udn: $("#udn").val(),
                    periodo: $("#periods").val(),
                    colaborador: $("#colaborador").val(),
                },
                ctrl
            );
            $("#container" + this.Project)
                .html("")
                .create_table(data);
                
            $("#tbConcentrado-detallado").css("background-color", "white").removeClass("table-bordered table-hover").addClass("table-borderless");
            $(".td_gral-detallado").html(data.promedio_gral);
        }
    }

    async getPeriods(periodo, udn) {
        const data = await useFetch({ url: ctrl, data: { opc: "lsPeriodos", id: $("#" + udn).val() } });
        if (data.periodos.length == 0) $("#" + periodo).option_select({ data: [{ id: 0, valor: "NO HAY PERIODOS DISPONIBLES" }] });
        else {
            $("#" + periodo).option_select({ data: data.periodos });
            $("#" + periodo).change();
        }
    }
    async getEvaluated(colaborador, periodo) {
        const data = await useFetch({ url: ctrl, data: { opc: "lsEvaluateds", id: $("#" + periodo).val() } });

        if (data.evaluados.length == 0) $("#" + colaborador).option_select({ data: [{ id: 0, valor: "NO HAY COLABORADORES EVALUADOS" }] });
        else {
            $("#" + colaborador).option_select({ data: data.evaluados });
            $("#" + colaborador).change();
        }
    }
}

class Concentrados extends Templates {
    constructor(link, div_modulo = "") {
        super(link, div_modulo);
        this.Project = "Concentrados";
    }

    render() {
        this.layout();
        this.filterBar();
        this.ls();
    }

    layout() {
        this.primaryLayout({
            parent: "box-1",
            id: this.Project,
        });
    }

    filterBar() {
        $("#filterBar" + this.Project).html("");
        this.createfilterBar({
            parent: "filterBar" + this.Project,
            data: [
                {
                    opc: "select",
                    class: "col-sm-3",
                    id: "udn2",
                    lbl: "Seleccionar udn: ",
                    data: udn,
                    onchange: "app.getPeriods('periods2','udn2'); ",
                },
                {
                    opc: "select",
                    class: "col-sm-3",
                    id: "periods2",
                    lbl: "Seleccionar período: ",
                    data: [{ id: 0, valor: "SELECCIONAR PERIODO" }],
                    onchange: "app.getEvaluated('colaborador2','periods2'); ",
                },
                {
                    opc: "select",
                    class: "col-sm-3",
                    id: "colaborador2",
                    lbl: "Seleccionar colaborador: ",
                    data: [{ id: 0, valor: "SELECCIONAR COLABORADOR" }],
                    onchange: "concentrado.ls()",
                },
                {
                    opc: "button",
                    class: "col-sm-3",
                    id: "btn",
                    className: "w-100",
                    text: "Exportar Imagen",
                    onClick: () => {
                        // convertPDF({
                        //     divElement: "containerConcentrados",
                        // });
                        convertIMG();
                    },
                },
            ],
        });
    }

    async ls() {
        if ($("#colaborador2").val() == 0) $("#container" + this.Project).html('<strong><center><i class="icon-attention"></i> No se encontraron datos</center></strong>');
        else {
            const data = await fn_ajax(
                {
                    opc: "table",
                    type: "concentrado",
                    udn: $("#udn2").val(),
                    periodo: $("#periods2").val(),
                    colaborador: $("#colaborador2").val(),
                },
                ctrl
            );
            $("#container" + this.Project)
                .html("")
                .create_table(data);

            $("#tbConcentrado-concentrado").css("background-color", "white").removeClass("table-bordered table-hover").addClass("table-borderless");

            $(".td_gral-concentrado").html(data.promedio_gral);
        }
    }
}


function convertIMG() {
    const node = document.getElementById("tbConcentrado-concentrado");

    domtoimage
        .toPng(node)
        .then(function (dataUrl) {
            const link = document.createElement("a");
            link.download = "tabla.jpeg";
            link.href = dataUrl;
            link.click();
        })
        .catch(function (error) {
            console.error("Error al exportar:", error);
        });
}
