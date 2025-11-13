class AppAdmin extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "Admin";
    }

    render() {
        this.layout();
        adminMetrics.render();
        adminSocialNetWork.render();
    }

    layout() {
        this.tabLayout({
            parent: `container-admin`,
            id: `tabs${this.PROJECT_NAME}`,
            theme: "light",
            type: "button",
            json: [
                {
                    id: "metrics",
                    tab: "Métricas",
                    active: true,
                    onClick: () => adminMetrics.listMetrics()
                },
                {
                    id: "networks",
                    tab: "Redes Sociales",
                    onClick: () => adminSocialNetWork.lsSocialNetworks()
                },
            ]
        });
    }
}

class AdminMetrics extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "AdminMetrics";
    }

    render() {
        this.layout();
        this.filterBar();
        this.listMetrics();
    }

    layout() {
        this.primaryLayout({
            parent: `container-metrics`,
            id: this.PROJECT_NAME,
            class: 'w-full',
            card: {
                filterBar: { class: 'w-full border-b pb-2', id: `filterBar${this.PROJECT_NAME}` },
                container: { class: 'w-full my-2 h-full', id: `container${this.PROJECT_NAME}` }
            }
        });
    }

    filterBar() {
        this.createfilterBar({
            parent: `filterBar${this.PROJECT_NAME}`,
            data: [
                {
                    opc: "select",
                    id: "active",
                    lbl: "Estado",
                    class: "col-sm-3",
                    data: [
                        { id: "1", valor: "Activos" },
                        { id: "0", valor: "Inactivos" }
                    ],
                    onchange: `adminMetrics.listMetrics()`,
                },
                {
                    opc: "select",
                    id: "socialNetwork",
                    lbl: "Red Social",
                    class: "col-sm-3",
                    data: [
                        { id: "", valor: "-- Todas las redes sociales --" },
                        ...socialNetworks
                    ],
                    onchange: `adminMetrics.listMetrics()`,
                },
                {
                    opc: "button",
                    class: "col-sm-3",
                    id: "btnNewMetric",
                    text: "Nueva Métrica",
                    onClick: () => this.addMetric(),
                },
            ],
        });
    }

    listMetrics() {
        $(`#container${this.PROJECT_NAME}`).html(`
           
            <div id="container-table-metrics"></div>
        `);

        this.createTable({
            parent: "container-table-metrics",
            idFilterBar: `filterBarAdminMetrics`,
            data: { opc: 'lsMetrics' },
            coffeesoft: true,
            conf: { datatable: true, pag: 10 },
            attr: {
                id: "tbMetrics",
                theme: 'corporativo',
                center: [2, 3],
                striped: true
            },
        });
    }

    addMetric() {
        this.createModalForm({
            id: 'formMetricAdd',
            data: { opc: 'addMetric' },
            bootbox: {
                title: 'Agregar Métrica',
            },
            json: this.jsonMetric(),
            success: (response) => {
                if (response.status === 200) {
                    alert({ icon: "success", text: response.message });
                    this.listMetrics();
                } else {
                    alert({ icon: "info", title: "Oops!...", text: response.message, btn1: true, btn1Text: "Ok" });
                }
            }
        });
    }

    async editMetric(id) {
        const request = await useFetch({
            url: this._link,
            data: {
                opc: "getMetric",
                id: id,
            },
        });

        const metric = request.data;

        this.createModalForm({
            id: 'formMetricEdit',
            data: { opc: 'editMetric', id: metric.id },
            bootbox: {
                title: 'Editar Métrica',
            },
            autofill: metric,
            json: this.jsonMetric(),
            success: (response) => {
                if (response.status === 200) {
                    alert({ icon: "success", text: response.message });
                    this.listMetrics();
                } else {
                    alert({ icon: "info", title: "Oops!...", text: response.message });
                }
            }
        });
    }

    statusMetric(id, active) {
        this.swalQuestion({
            opts: {
                title: "¿Desea cambiar el estado de la métrica?",
                text: "Esta acción activará o desactivará la métrica.",
                icon: "warning",
            },
            data: {
                opc: "statusMetric",
                active: active === 1 ? 0 : 1,
                id: id,
            },
            methods: {
                send: () => this.listMetrics(),
            },
        });
    }

    jsonMetric() {
        return [
            {
                opc: "select",
                id: "red_social_id",
                lbl: "Red Social",
                class: "col-12 mb-3",
                data: socialNetworks,
                text: "valor",
                value: "id"
            },
            {
                opc: "input",
                id: "nombre",
                lbl: "Nombre de la Métrica",
                class: "col-12 mb-3"
            },

        ];
    }
}

class AdminSocialNetWork extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "AdminNetworks";
    }

    render() {
        this.layout();
        this.filterBar();
        this.lsSocialNetworks();
    }

    layout() {
        this.primaryLayout({
            parent: `container-networks`,
            id: this.PROJECT_NAME,
            class: 'w-full',
            card: {
                filterBar: { class: 'w-full border-b pb-2', id: `filterBar${this.PROJECT_NAME}` },
                container: { class: 'w-full my-2 h-full', id: `container${this.PROJECT_NAME}` }
            }
        });
    }

    filterBar() {
        this.createfilterBar({
            parent: `filterBar${this.PROJECT_NAME}`,
            data: [

                {
                    opc: "select",
                    id: "active",
                    lbl: "Estado",
                    class: "col-sm-3",
                    data: [
                        { id: "1", valor: "Activos" },
                        { id: "0", valor: "Inactivos" }
                    ],
                    onchange: `adminSocialNetWork.lsSocialNetworks()`,
                },
                {
                    opc: "button",
                    class: "col-sm-3",
                    id: "btnNewNetwork",
                    text: "Nueva Red Social",
                    onClick: () => this.addSocialNetwork(),
                },
            ],
        });
    }

    lsSocialNetworks() {
        $(`#container${this.PROJECT_NAME}`).html(`<div id="container-table-networks"></div>`);

        this.createTable({
            parent: "container-table-networks",
            idFilterBar: `filterBar${this.PROJECT_NAME}`,
            data: { opc: 'lsSocialNetworks' },
            coffeesoft: true,
            conf: { datatable: true, pag: 10 },
            attr: {
                id: "tbNetworks",
                theme: 'corporativo',
                center: [2, 3]
            },
        });
    }

    addSocialNetwork() {
        this.createModalForm({
            id: 'formSocialNetworkAdd',
            data: { opc: 'addSocialNetwork' },
            bootbox: {
                title: 'Agregar Red Social',
            },
            json: this.jsonSocialNetwork(),
            success: (response) => {
                if (response.status === 200) {
                    alert({ icon: "success", text: response.message });
                    this.lsSocialNetworks();
                } else {
                    alert({ icon: "info", title: "Oops!...", text: response.message, btn1: true, btn1Text: "Ok" });
                }
            }
        });
    }

    async editSocialNetwork(id) {
        const request = await useFetch({
            url: this._link,
            data: {
                opc: "getSocialNetwork",
                id: id,
            },
        });

        const network = request.data;

        this.createModalForm({
            id: 'formSocialNetworkEdit',
            data: { opc: 'editSocialNetwork', id: network.id },
            bootbox: {
                title: 'Editar Red Social',
            },
            autofill: network,
            json: this.jsonSocialNetwork(),
            success: (response) => {
                if (response.status === 200) {
                    alert({ icon: "success", text: response.message });
                    this.lsSocialNetworks();
                } else {
                    alert({ icon: "info", title: "Oops!...", text: response.message });
                }
            }
        });
    }

    statusSocialNetwork(id, active) {
        this.swalQuestion({
            opts: {
                title: "¿Desea cambiar el estado de la red social?",
                text: "Esta acción activará o desactivará la red social.",
                icon: "warning",
            },
            data: {
                opc: "statusSocialNetwork",
                active: active === 1 ? 0 : 1,
                id: id,
            },
            methods: {
                send: () => this.lsSocialNetworks(),
            },
        });
    }

    jsonSocialNetwork() {
        return [
            {
                opc: "input",
                id: "nombre",
                lbl: "Nombre de la Red Social",
                class: "col-12 mb-3"
            },
            {
                opc: "input",
                id: "icono",
                lbl: "Icono ",
                class: "col-12 mb-3",
                placeholder: "icon-facebook"
            },
            {
                opc: "input",
                id: "color",
                lbl: "Color (Hex)",
                class: "col-12 mb-3",
                placeholder: "#1877F2"
            },
        ];
    }

}
