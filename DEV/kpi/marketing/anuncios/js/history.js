class AnnualHistory extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "History";
        this.apiHistory = 'ctrl/ctrl-history.php';
    }

    render() {
        this.layout();
        this.filterBar();
        this.lsCPC();
    }

    layout() {
        this.primaryLayout({
            parent: `container-history`,
            id: this.PROJECT_NAME,
            card: {
                filterBar: { class: 'w-full border-b pb-2', id: `filterBar${this.PROJECT_NAME}` },
                container: { class: 'w-full my-2 h-full', id: `container${this.PROJECT_NAME}` }
            }
        });

        this.tabLayout({
            parent: `container${this.PROJECT_NAME}`,
            id: `tabs${this.PROJECT_NAME}`,
            theme: "light",
            class: '',
            type: "short",
            json: [
                {
                    id: "cpc",
                    tab: "Reporte CPC",
                    class: "mb-1",
                    active: true,
                    onClick: () => this.lsCPC()
                },
                {
                    id: "cac",
                    tab: "Reporte CAC",
                    onClick: () => this.lsCAC()
                }
            ]
        });
    }

    filterBar() {
        this.createfilterBar({
            parent: `filterBar${this.PROJECT_NAME}`,
            data: [
                {
                    opc: "select",
                    id: "udn_id",
                    lbl: "Unidad de Negocio",
                    class: "col-sm-3",
                    data: udn,
                    onchange: `history.updateReport()`,
                },
                {
                    opc: "select",
                    id: "red_social_id",
                    lbl: "Red Social",
                    class: "col-sm-3",
                    data: red_social,
                    onchange: `history.updateReport()`,
                },
                {
                    opc: "select",
                    id: "año",
                    lbl: "Año",
                    class: "col-sm-3",
                    data: Array.from({ length: 5 }, (_, i) => {
                        const year = moment().year() - i;
                        return { id: year, valor: year.toString() };
                    }),
                    onchange: `history.updateReport()`,
                },
            ],
        });
    }

    updateReport() {
        // const activeTab = $(`#tabs${this.PROJECT_NAME} .active`).attr('id');
        // if (activeTab == 'tab-cpc') {
            this.lsCPC();
        // } else {
            this.lsCAC();
        // }
    }

    lsCPC() {
        $(`#container-cpc`).html(`
            <div class="px-2 pt-2 pb-2">
                <h2 class="text-2xl font-semibold ">📊 Historial Anual - Reporte CPC</h2>
                <p class="text-gray-400">Inversión total, clics y CPC promedio por mes</p>
            </div>
            <div id="container-table-cpc"></div>
        `);

        const tempLink = this._link;
        this._link = this.apiHistory;

        this.createTable({
            parent: "container-table-cpc",
            idFilterBar: `filterBar${this.PROJECT_NAME}`,
            data: { opc: 'lsCPC' },
            coffeesoft: true,
            conf: { datatable: false, pag: 15 },
            attr: {
                id: "tbCPC",
                theme: 'corporativo',
                right: [1, 2, 3],
                striped: true,

            },
            success: () => {
                this._link = tempLink;
            }
        });
    }

    lsCAC() {
        $(`#container-cac`).html(`
            <div class="px-2 pt-2 pb-2">
                <h2 class="text-2xl font-semibold ">📊 Historial Anual - Reporte CAC</h2>
                <p class="text-gray-400">Inversión total, número de clientes y CAC por mes</p>
            </div>
            <div id="container-table-cac"></div>
        `);

        const tempLink = this._link;
        this._link = this.apiHistory;

        this.createTable({
            parent: "container-table-cac",
            idFilterBar: `filterBar${this.PROJECT_NAME}`,
            data: { opc: 'lsCAC' },
            coffeesoft: true,
            conf: { datatable: false, pag: 15 },
            attr: {
                id: "tbCAC",
                theme: 'corporativo',
                right: [1,2, 3],
                striped: true,
            },
            success: () => {
                this._link = tempLink;
            }
        });
    }
}
