let apiGrupos = 'ctrl/ctrl-grupos-udn.php';
let gruposUdn, lsudn;

$(async () => {
    const data = await useFetch({ url: apiGrupos, data: { opc: "init" } });
    lsudn = data.udn;

    gruposUdn = new GruposUdn(apiGrupos, "root");
    gruposUdn.render();
});

class GruposUdn extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "GruposUdn";
    }

    render() {
        this.layout();
        this.filterBarGrupos();
        setTimeout(() => {
            this.loadGruposCards();
        }, 100);
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
            title: "📦 Grupos por UDN",
            subtitle: "Visualización de grupos de productos por unidad de negocio",
            onClick: () => this.redirectToHome()
        });
    }

    filterBarGrupos() {
        this.createfilterBar({
            parent: `filterBar${this.PROJECT_NAME}`,
            data: [
                {
                    opc: "select",
                    id: "udnGrupos",
                    lbl: "Unidad de Negocio",
                    class: "col-sm-3",
                    data: lsudn,
                    onchange: "gruposUdn.loadGruposCards()"
                },
                {
                    opc: "button",
                    id: "btnVolverGrupos",
                    text: "Regresar a Grupos",
                    class: "col-sm-2 d-none",
                    onClick: () => this.loadGruposCards()
                }
            ]
        });
    }

    async loadGruposCards() {
        const udn = $(`#filterBar${this.PROJECT_NAME} #udnGrupos`).val();

        $("#btnVolverGrupos").addClass('d-none');

        const response = await useFetch({
            url: this._link,
            data: {
                opc: 'lsGroups',
                udn: udn
            }
        });

        if (response && response.status === 200) {
            if (response.grupos.length === 0) {
                $(`#container${this.PROJECT_NAME}`).html(`
                    <div class="text-center py-8">
                        <i class="icon-info-circle text-4xl text-gray-400 mb-3"></i>
                        <p class="text-gray-600">No hay grupos disponibles para esta UDN</p>
                    </div>
                `);
            } else {
                this.renderGruposCards(response.grupos);
            }
        } else {
            alert({
                icon: 'error',
                title: 'Error al cargar grupos',
                text: response.message || 'Ocurrió un error inesperado'
            });
        }
    }

    renderGruposCards(grupos) {
        const container = $(`#container${this.PROJECT_NAME}`);
        container.html(`
            <div class="px-4 py-3">
                <h3 class="text-xl font-bold text-[#103B60] mb-2">Grupos de Productos</h3>
                <p class="text-gray-500 text-sm mb-4">Selecciona un grupo para ver sus productos</p>
                <div id="gruposGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3"></div>
            </div>
        `);

        const grid = $("#gruposGrid");

        grupos.forEach(grupo => {
            const card = $(`
                <div class="grupo-card bg-white rounded-lg shadow-sm border border-gray-100 p-3 hover:shadow-md hover:border-blue-400 transition-all duration-200 cursor-pointer relative">
                    <div class="absolute top-2 right-2">
                        <i class="icon-right-open text-gray-300 text-sm"></i>
                    </div>
                    
                    <div class="flex flex-col items-center text-center mb-2">
                        <div class="w-10 h-10 bg-orange-50 rounded-lg flex items-center justify-center mb-2">
                            <i class="icon-food text-orange-500 text-xl"></i>
                        </div>
                        <h4 class="font-semibold text-xs text-gray-800 uppercase tracking-wide line-clamp-2 leading-tight">${grupo.valor}</h4>
                    </div>
                    
                    <div class="flex flex-col items-center pt-2 border-t border-gray-100">
                        <span class="text-2xl font-bold text-[#103B60]">${grupo.cantidad_productos}</span>
                        <span class="text-xs text-gray-500">Productos</span>
                    </div>
                    
                    <div class="mt-2 flex justify-center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700">
                            Activo
                        </span>
                    </div>
                </div>
            `);

            card.on('click', () => {
                this.showProductosByGrupo(grupo.id, grupo.valor);
            });

            grid.append(card);
        });
    }

    async showProductosByGrupo(idGrupo, nombreGrupo) {
        $("#btnVolverGrupos").removeClass('d-none');

        const udn = $(`#filterBar${this.PROJECT_NAME} #udnGrupos`).val();

        const container = $(`#container${this.PROJECT_NAME}`);
        container.html(`
            <div class="px-4">
                <div id="tableProductosGrupo"></div>
            </div>
        `);

        this.createTable({
            parent: "tableProductosGrupo",
            idFilterBar: `filterBar${this.PROJECT_NAME}`,
            data: { opc: 'lsProductos', grupo: idGrupo, udn: udn },
            coffeesoft: true,
            conf: { datatable: true, pag: 15 },
            attr: {
                id: "tbProductosGrupo",
                theme: 'corporativo',
                title: nombreGrupo,
                center: [2],
                right: [4, 5, 6, 7]
            }
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
