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
                },
                {
                    id: "grupos-homologacion",
                    tab: "Grupos por Homologar",
                    // onClick: () => this.renderGruposHomologacion()
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

        setTimeout(() => {
            $(`#filterBar${this.PROJECT_NAME} #mes`).val(currentMonth).trigger("change");
        }, 100);
    }

    async loadGruposByUdn() {
        const udn = $(`#filterBar${this.PROJECT_NAME} #udn`).val();
        
        console.log('🔍 Cargando grupos para UDN:', udn);
        
        try {
            const response = await useFetch({
                url: this._link,
                data: {
                    opc: 'getGruposByUdn',
                    udn: udn
                }
            });

            console.log('📦 Respuesta del servidor:', response);

            if (response && response.status === 200 && response.grupos) {
                const grupoSelect = $(`#filterBar${this.PROJECT_NAME} #grupo`);
                grupoSelect.empty();
                
                grupoSelect.append($('<option>', {
                    value: 'all',
                    text: 'Todos los grupos'
                }));

                if (response.grupos.length > 0) {
                    console.log(`✅ Se cargaron ${response.grupos.length} grupos`);
                    response.grupos.forEach(grupo => {
                        grupoSelect.append($('<option>', {
                            value: grupo.id,
                            text: grupo.valor
                        }));
                    });
                } else {
                    console.warn('⚠️ No hay grupos disponibles para esta UDN');
                    grupoSelect.append($('<option>', {
                        value: '',
                        text: 'No hay grupos disponibles'
                    }));
                }

                grupoSelect.val('all');
                this.lsProductos();
            } else {
                console.error('❌ Error al cargar grupos:', response);
            }
        } catch (error) {
            console.error('❌ Error en loadGruposByUdn:', error);
        }
    }

    lsProductos() {


        this.createTable({
            parent: "container-list-productos",
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

    // Tab concentrado. 

    filterBarConcentrado() {
        const currentYear = moment().year();
        const currentMonth = moment().month() + 1;

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
                    class: "col-sm-2",
                    data: lsudn,
                    onchange: `app.loadGruposConcentrado()`,
                },
                {
                    opc: "select",
                    id: "grupoConcentrado",
                    lbl: "Grupo de Producto",
                    class: "col-sm-2",
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
                    id: "mesConcentrado",
                    lbl: "Mes",
                    class: "col-sm-2",
                    data: moment.months().map((m, i) => ({ id: i + 1, valor: m })),
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

        setTimeout(() => {
            $("#filterBarConcentrado #mesConcentrado").val(currentMonth).trigger("change");
        }, 100);
    }

    async loadGruposConcentrado() {
        const udn = $("#filterBarConcentrado #udnConcentrado").val();
        
        try {
            const response = await useFetch({
                url: this._link,
                data: {
                    opc: 'getGruposByUdn',
                    udn: udn
                }
            });

            if (response && response.status === 200 && response.grupos) {
                const grupoSelect = $("#filterBarConcentrado #grupoConcentrado");
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
                this.lsConcentrado();
            } else {
                console.error('Error al cargar grupos:', response);
            }
        } catch (error) {
            console.error('Error en loadGruposConcentrado:', error);
        }
    }


    lsConcentrado() {
        if (!$("#filterBarConcentrado").length) {
            this.filterBarConcentrado();
        }

        setTimeout(() => {
            this.createTable({
                parent: "tableConcentrado",
                idFilterBar: "filterBarConcentrado",
                data: { 
                    opc: 'lsConcentrado'
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

    async renderGruposHomologacion() {
        const container = $("#container-grupos-homologacion");
        container.html(`
            <div id="filterBarGrupos" class="mb-3"></div>
            <div id="contentGrupos"></div>
        `);

        this.createfilterBar({
            parent: "filterBarGrupos",
            data: [
                {
                    opc: "select",
                    id: "udnGrupos",
                    lbl: "Unidad de Negocio",
                    class: "col-sm-3",
                    data: lsudn,
                    onchange: "app.loadGruposCards()"
                },
                {
                    opc: "button",
                    id: "btnVolverGrupos",
                    text: "Volver a Grupos",
                    class: "col-sm-2 d-none",
                    onClick: () => this.loadGruposCards()
                }
            ]
        });

        setTimeout(() => {
            this.loadGruposCards();
        }, 100);
    }

    async loadGruposCards() {
        const udn = $("#filterBarGrupos #udnGrupos").val();

        $("#btnVolverGrupos").addClass('d-none');

        console.log('🔍 Cargando grupos para UDN:', udn);

        const response = await useFetch({
            url: this._link,
            data: {
                opc: 'getGruposConHomologacion',
                udn: udn
            }
        });

        console.log('📦 Respuesta del servidor:', response);

        if (response && response.status === 200) {
            console.log(`✅ Se cargaron ${response.grupos.length} grupos`);
            this.renderGruposCards(response.grupos);
        } else {
            console.error('❌ Error al cargar grupos:', response);
        }
    }

    renderGruposCards(grupos) {
        const container = $("#contentGrupos");
        container.html(`
            <div class="px-4 py-3">
                <h3 class="text-xl font-bold text-[#103B60] mb-2">Grupos de Productos</h3>
                <p class="text-gray-600 mb-4">Selecciona un grupo para ver sus productos</p>
                <div id="gruposGrid" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3"></div>
            </div>
        `);

        const grid = $("#gruposGrid");

        console.log('📊 Renderizando', grupos.length, 'grupos');

        grupos.forEach(grupo => {
            const porcentaje = grupo.total_productos > 0 
                ? Math.round((grupo.productos_homologados / grupo.total_productos) * 100) 
                : 0;

            const card = $(`
                <div class="bg-white border-2 border-gray-200 rounded-lg p-3 hover:border-blue-500 hover:shadow-lg transition-all cursor-pointer">
                    <h4 class="font-bold text-xs text-gray-800 mb-2 text-center uppercase line-clamp-2 h-8">${grupo.grupoproductos}</h4>
                    <div class="text-center mb-2">
                        <span class="text-lg font-bold text-gray-600">$NAN</span>
                    </div>
                    <div class="space-y-1 text-xs">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total:</span>
                            <span class="font-semibold">${grupo.total_productos}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-green-600">✓</span>
                            <span class="font-semibold text-green-600">${grupo.productos_homologados}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-red-600">✗</span>
                            <span class="font-semibold text-red-600">${grupo.productos_sin_homologar}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                            <div class="bg-green-500 h-1.5 rounded-full" style="width: ${porcentaje}%"></div>
                        </div>
                        <div class="text-center text-gray-600 text-xs">${porcentaje}%</div>
                    </div>
                </div>
            `);

            card.on('click', () => {
                console.log('🖱️ Click en grupo:', grupo.idgrupo, grupo.grupoproductos);
                this.showProductosByGrupo(grupo.idgrupo, grupo.grupoproductos);
            });
            grid.append(card);
        });
    }

    async showProductosByGrupo(idGrupo, nombreGrupo) {
        $("#btnVolverGrupos").removeClass('d-none');

        const udn = $("#filterBarGrupos #udnGrupos").val();

        const response = await useFetch({
            url: this._link,
            data: {
                opc: 'getProductosByGrupo',
                grupo: idGrupo,
                udn: udn
            }
        });

        if (response && response.status === 200) {
            const container = $("#contentGrupos");
            container.html(`
                <div class="px-4 py-3">
                    <h3 class="text-xl font-bold text-[#103B60] mb-2">${nombreGrupo}</h3>
                    <p class="text-gray-600 mb-4">Productos del grupo</p>
                    <div id="tableProductosGrupo"></div>
                </div>
            `);

            this.createTable({
                parent: "tableProductosGrupo",
                data: { opc: 'getProductosByGrupo', grupo: idGrupo, udn: udn },
                coffeesoft: true,
                conf: { datatable: true, pag: 15 },
                attr: {
                    id: "tbProductosGrupo",
                    theme: 'corporativo',
                    center: [3],
                    right: [4, 5]
                }
            });
        }
    }
}


// Función global para mostrar alerta de múltiples enlaces
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
