class Afluencia extends App {
    constructor(link, div_modulo) {
        super(link, div_modulo);
    }

    render() {
        this.layout();
        this.createFilterBar();
        this.lsAfluencia();
    }

    layout() {
        this.layoutWithFilters({
            parent: "tab-afluencia",
            card: {
                filterBar: { id: "filterBarConcentrado", className: "" },
                container: { id: "containerListConcentrado", className: "" },
            },
        });
    }

    createFilterBar(options) {
        $("#filterBarConcentrado").content_json_form({
            data: [
                {
                    opc: "input-calendar",
                    id: "iptCalendar",
                    class:'col-sm-3',
                    lbl:'Selector de fecha:'
                },
                {
                    opc      : "button",
                    id       : "btnBuscar",
                    text     : "Buscar",
                    onClick  : () => { this.lsAfluencia() },
                    className: 'w-100',
                    class    : "col-6 col-sm-3",
                },
            ],
            type: "",
        });

        dataPicker({ id: 'iptCalendar'});
    }

    lsAfluencia() {
        this.createTable({
            parent: "containerListConcentrado",
            data  : {
                opc: "getListAfluencia",
            },
            conf: { datatable: true, pag: 15 },

            attr: {
                id         : "tb",
                class_table: "table table-bordered table-sm table-striped",
                color_col: [1],
                right      : [2, 3],
                center     : [1],
                extends    : true,
            },
        });
    }
}
