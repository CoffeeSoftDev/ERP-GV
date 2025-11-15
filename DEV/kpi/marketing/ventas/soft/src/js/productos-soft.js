let apiProductos = 'ctrl/ctrl-productos-soft.php';
let app, lsudn, lsgrupos;

$(async () => {
    const data = await useFetch({ url: apiProductos, data: { opc: "init" } });
    lsudn = data.udn;
    lsgrupos = data.grupos;

    app = new ProductosSoft(apiProductos, "root");
    app.render();
});

class ProductosSoft extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "productosSoft";
    }

    render() {
        this.layout();
        this.filterBarProductos();
        this.lsProductos();
    }

    layout() {
        this.primaryLayout({
            parent: "root",
            id: this.PROJECT_NAME,
            class: "w-full",
            card: {
                filterBar: { class: "w-full mb-3", id: `filterBar${this.PROJECT_NAME}` },
                container: { class: "w-full h-full", id: `container${this.PROJECT_NAME}` }
            }
        });

        this.headerBar({
            parent: `filterBar${this.PROJECT_NAME}`,
            title: "🍽️ Productos Soft Restaurant",
            subtitle: "Consulta y gestión de productos del sistema",
            onClick: () => this.redirectToHome()
        });

        this.tabLayout({
            parent: `container${this.PROJECT_NAME}`,
            id: "tabsProductos",
            theme: "light",
            type: "short",
            json: [
                {
                    id: "list-productos",
                    tab: "Productos Soft",
                    active: true,
                    onClick: () => this.lsProductos()
                },
                {
                    id: "concentrado-productos",
                    tab: "Concentrado por Periodos",
                    onClick: () => this.lsConcentrado()
                }
            ]
        });
    }

    filterBarProductos() {
        const currentYear = moment().year();
        const currentMonth = moment().month() + 1;

        this.createfilterBar({
            parent: `filterBar${this.PROJECT_NAME}`,
            data: [
                {
                    opc: "select",
                    id: "udn",
                    lbl: "Unidad de Negocio",
                    class: "col-sm-2",
                    data: lsudn,
                    onchange: `app.lsProductos()`,
                },
                {
                    opc: "select",
                    id: "grupo",
                    lbl: "Grupo de Producto",
                    class: "col-sm-2",
                    data: [{ id: 'all', valor: 'Todos los Grupos' }, ...lsgrupos],
                    onchange: `app.lsProductos()`,
                },
                {
                    opc: "select",
                    id: "anio",
                    lbl: "Año",
                    class: "col-sm-2",
                    data: Array.from({ length: 5 }, (_, i) => {
                        const year = currentYear - i;
                        return { id: year, valor: year.toString() };
                    }),
                    onchange: `app.lsProductos()`,
                },
                {
                    opc: "select",
                    id: "mes",
                    lbl: "Mes",
                    class: "col-sm-2",
                    data: moment.months().map((m, i) => ({ id: i + 1, valor: m })),
                    onchange: `app.lsProductos()`,
                }
            ],
        });

        setTimeout(() => {
            $(`#filterBar${this.PROJECT_NAME} #mes`).val(currentMonth).trigger("change");
        }, 100);
    }

    filterBarConcentrado() {
        const currentYear = moment().year();

        $("#container-concentrado-productos").html(`
            <div id="filterBarConcentrado" class="mb-3"></div>
            <div id="tableConcentrado"></div>
        `);

        this.createfilterBar({
            parent: "filterBarConcentrado",
            data: [
                {
                    opc: "select",
                    id: "udnConcentrado",
                    lbl: "Unidad de Negocio",
                    class: "col-sm-3",
                    data: lsudn,
                    onchange: `app.lsConcentrado()`,
                },
                {
                    opc: "select",
                    id: "grupoConcentrado",
                    lbl: "Grupo de Producto",
                    class: "col-sm-3",
                    data: [{ id: 'all', valor: 'Todos los Grupos' }, ...lsgrupos],
                    onchange: `app.lsConcentrado()`,
                },
                {
                    opc: "select",
                    id: "anioConcentrado",
                    lbl: "Año",
                    class: "col-sm-2",
                    data: Array.from({ length: 5 }, (_, i) => {
                        const year = currentYear - i;
                        return { id: year, valor: year.toString() };
                    }),
                    onchange: `app.lsConcentrado()`,
                },
                {
                    opc: "select",
                    id: "periodo",
                    lbl: "Periodo",
                    class: "col-sm-2",
                    data: [
                        { id: '3', valor: '3 Meses' },
                        { id: '6', valor: '6 Meses' },
                        { id: '9', valor: '9 Meses' }
                    ],
                    onchange: `app.lsConcentrado()`,
                }
            ],
        });
    }

    lsProductos() {
        const udn = $(`#filterBar${this.PROJECT_NAME} #udn`).val();
        const grupo = $(`#filterBar${this.PROJECT_NAME} #grupo`).val();
        const anio = $(`#filterBar${this.PROJECT_NAME} #anio`).val();
        const mes = $(`#filterBar${this.PROJECT_NAME} #mes`).val();

        this.createTable({
            parent: "container-list-productos",
            idFilterBar: `filterBar${this.PROJECT_NAME}`,
            data: { 
                opc  : 'lsProductos',
                udn  : udn,
                grupo: grupo,
                anio : anio,
                mes  : mes
            },
            // coffeesoft: true,
            conf: { datatable: false, pag: 25 },
            attr: {
                id     : "tbProductosSoft",
                theme  : 'corporativo',
                extends: true,
                striped: true,
                f_size : 12,
                center : [2]
            },
        });
    }

    lsConcentrado() {
        this.filterBarConcentrado();

        setTimeout(() => {
            const udn = $("#filterBarConcentrado #udnConcentrado").val();
            const grupo = $("#filterBarConcentrado #grupoConcentrado").val();
            const anio = $("#filterBarConcentrado #anioConcentrado").val();
            const periodo = $("#filterBarConcentrado #periodo").val();

            this.createTable({
                parent: "tableConcentrado",
                idFilterBar: "filterBarConcentrado",
                data: { 
                    opc    : 'lsConcentrado',
                    udn    : udn,
                    grupo  : grupo,
                    anio   : anio,
                    periodo: periodo
                },
                conf: { datatable: false, pag: 50 },
                attr: {
                    id     : "tbConcentrado",
                    theme  : 'corporativo',
                    extends: true,
                    striped: false,
                    f_size : 11
                },
            });
        }, 100);
    }

    headerBar(options) {
        const defaults = {
            parent: "root",
            title: "Título por defecto",
            subtitle: "Subtítulo por defecto",
            icon: "icon-home",
            textBtn: "Inicio",
            classBtn: "border-2 border-blue-700 text-blue-600 hover:bg-blue-700 hover:text-white transition-colors duration-200",
            onClick: null,
        };

        const opts = Object.assign({}, defaults, options);

        const container = $("<div>", {
            class: "relative flex justify-center items-center px-4 py-4 bg-white rounded-lg shadow-sm mb-4"
        });

        const leftSection = $("<div>", {
            class: "absolute left-4"
        }).append(
            $("<button>", {
                class: `${opts.classBtn} font-semibold px-4 py-2 rounded-lg transition flex items-center`,
                html: `<i class="${opts.icon} mr-2"></i>${opts.textBtn}`,
                click: () => typeof opts.onClick === "function" && opts.onClick()
            })
        );

        const centerSection = $("<div>", {
            class: "text-center"
        }).append(
            $("<h2>", {
                class: "text-2xl font-bold text-[#103B60]",
                text: opts.title
            }),
            $("<p>", {
                class: "text-gray-500 mt-1",
                text: opts.subtitle
            })
        );

        container.append(leftSection, centerSection);
        $(`#${opts.parent}`).prepend(container);
    }

    redirectToHome() {
        const base = window.location.origin + '/ERP24';
        window.location.href = `${base}/kpi/marketing.php`;
    }
}
