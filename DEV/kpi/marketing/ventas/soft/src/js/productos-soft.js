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
        this.loadGruposByUdn();
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
    }

    filterBarProductos() {
        const currentYear = moment().year();

        this.createfilterBar({
            parent: `filterBar${this.PROJECT_NAME}`,
            data: [
                {
                    opc: "select",
                    id: "udn",
                    lbl: "Unidad de Negocio",
                    class: "col-sm-3",
                    data: lsudn,
                    onchange: `app.loadGruposByUdn()`,
                },
                {
                    opc: "select",
                    id: "grupo",
                    lbl: "Grupo de Producto",
                    class: "col-sm-3",
                    onchange: `app.lsProductos()`,
                },
                {
                    opc: "select",
                    id: "anio",
                    lbl: "Año",
                    class: "col-sm-3",
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
                    class: "col-sm-3",
                    data: moment.months().map((m, i) => ({ id: i + 1, valor: m })),
                    onchange: `app.lsProductos()`,
                }
            ],
        });
    }

    async loadGruposByUdn() {
        const udn = $(`#filterBar${this.PROJECT_NAME} #udn`).val();
        
        const response = await useFetch({
            url: this._link,
            data: {
                opc: 'getGruposByUdn',
                udn: udn
            }
        });

        if (response && response.status === 200 && response.grupos) {
            const grupoSelect = $(`#filterBar${this.PROJECT_NAME} #grupo`);
            grupoSelect.empty();
            
            grupoSelect.append($('<option>', {
                value: 'all',
                text: 'Todos los grupos'
            }));

            if (response.grupos.length > 0) {
                response.grupos.forEach(grupo => {
                    grupoSelect.append($('<option>', {
                        value: grupo.id,
                        text: grupo.valor
                    }));
                });
            } else {
                grupoSelect.append($('<option>', {
                    value: '',
                    text: 'No hay grupos disponibles'
                }));
            }

            grupoSelect.val('all');
            this.lsProductos();
        }
    }

    lsProductos() {
        this.createTable({
            parent: `container${this.PROJECT_NAME}`,
            idFilterBar: `filterBar${this.PROJECT_NAME}`,
            data: {
                opc: 'lsProductos',
            },
            coffeesoft: true,
            conf: { datatable: true, pag: 25 },
            attr: {
                id: "tbProductosSoft",
                theme: 'corporativo',
                extends: true,
                striped: true,
                f_size: 12,
                center: [2]
            },
        });
    }

    headerBar(options) {
        const defaults = {
            parent: "root",
            title: "Título por defecto",
            subtitle: "Subtítulo por defecto",
            icon: "icon-home",
            textBtn: "Inicio",
            classBtn: "border-1 border-blue-700 text-blue-600 hover:bg-blue-700 hover:text-white transition-colors duration-200",
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
                class: `${opts.classBtn} font-semibold px-4 py-2 rounded transition flex items-center`,
                html: `<i class="${opts.icon} mr-2"></i>${opts.textBtn}`,
                click: () => typeof opts.onClick === "function" && opts.onClick()
            })
        );

        const centerSection = $("<div>", {
            class: "text-center"
        }).append(
            $("<label>", {
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

function mostrarAlertaMultiplesEnlaces(idProducto, enlaces, total) {
    alert({
        icon: 'warning',
        title: '⚠️ Múltiples Enlaces Detectados',
        html: `
            <div class="text-left">
                <p class="mb-3"><strong>Producto ID:</strong> ${idProducto}</p>
                <p class="mb-3"><strong>Total de enlaces:</strong> ${total}</p>
                <p class="mb-2"><strong>IDs Homologados:</strong></p>
                <div class="bg-gray-100 p-3 rounded">
                    <code class="text-sm">${enlaces}</code>
                </div>
                <p class="mt-3 text-sm text-gray-600">
                    <i class="icon-info-circle"></i> 
                    Este producto tiene múltiples homologaciones. Se recomienda revisar y mantener solo una.
                </p>
            </div>
        `,
        btn1: true,
        btn1Text: 'Entendido'
    });
}
