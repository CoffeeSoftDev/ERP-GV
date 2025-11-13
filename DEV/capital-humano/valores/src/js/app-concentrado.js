let ctrl = "ctrl/ctrl-concentrado.php";
let app,udn,colaborador;

$(async () => {
    fn_ajax({ opc: "init" }, ctrl).then((data) => {
        udn         = data.udn;
        colaborador = data.colaborador;

        console.log(colaborador);
        app         = new App(ctrl);
        app.render();
    });
});

class App extends Templates {
    constructor(link, div_modulo = "") {
        super(link, div_modulo);
        this.PROJECT_NAME = "Concentrado";
    }

    render() {
        this.layout();
        this.filterBar();
        this.ls();
    }

    layout() {
        this.primaryLayout({
            parent: "root",
            id: this.Project,
        });
    }
    filterBar() {
        this.createfilterBar({
            parent: "filterBarEventos",
            data: [
                {
                    opc: "input-calendar",
                    class: "col-sm-2",
                    id: "calendar" + this.PROJECT_NAME,
                    lbl: "Consultar fecha: ",
                },
                {
                    opc: "select",
                    class: "col-sm-2",
                    id: "status",
                    lbl: "Seleccionar estados: ",
                    data: udn,
                    onchange: "app.ls()",
                },

                {
                    opc: "button",
                    className: "w-100",
                    class: "col-sm-2",
                    color_btn: "primary",
                    id: "btnNuevoEvento",
                    text: "Nuevo evento",

                    onClick: () => this.ls()
                },

            ],
        });

        // initialized.

        dataPicker({
            parent: "calendar" + this.PROJECT_NAME,
            rangepicker: {
                startDate: moment().startOf("month"),
                endDate: moment().endOf("month"),
                showDropdowns: true,
                ranges: {
                    "Mes actual": [moment().startOf("month"), moment().endOf("month")],
                    "Semana actual": [moment().startOf("week"), moment().endOf("week")],
                    "Proxima semana": [moment().add(1, "week").startOf("week"), moment().add(1, "week").endOf("week")],
                    "Proximo mes": [moment().add(1, "month").startOf("month"), moment().add(1, "month").endOf("month")],
                    "Mes anterior": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")]
                },
            },
            onSelect: (start, end) => {
                this.ls();
            },
        });
    }
    
    ls(options) {

        let rangePicker = getDataRangePicker("calendar");
        const udn = $('#udn').val();

        this.createTable({
            parent: "container"+this.Project,
            idFilterBar: "filterBar"+this.Project,
            data: { opc: "ls", fi: rangePicker.fi, ff: rangePicker.ff },
            conf: { datatable: false, /*pag: 3*/ },

            attr: {
                color_th: "bg-primary",
                id: "table"+this.Project,
                class: 'table table-bordered uppercase',
                center: [1,2],
                // right: [4],
                extends: true
            },
        });
    }
}
