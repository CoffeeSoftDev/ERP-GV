let apiProductos = 'ctrl/ctrl-productos-soft.php';
let app, lsudn, lsgrupos;

let concentrado;

$(async () => {
    const data = await useFetch({ url: apiProductos, data: { opc: "init" } });
    lsudn = data.udn;
    lsgrupos = data.grupos;

    app = new ProductosSoft(apiProductos, "root");
    app.render();

    concentrado = new Concentrado(apiProductos,'');
    concentrado.render();

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
        // this.lsProductos();
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
                 
                    onClick: () => this.lsProductos()
                },
                {
                    id: "concentrado-productos",
                    tab: "Concentrado por Periodos",
                    active: true,
                    onClick: () => concentrado.lsConcentrado()
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

        // setTimeout(() => {
        //     $(`#filterBar${this.PROJECT_NAME} #mes`).val(currentMonth).trigger("change");
        // }, 100);
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

class Concentrado extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "Concentrado";
    }

    render(){

        this.filterBarConcentrado();
        this.loadGruposConcentrado();
        

        setTimeout(() => {
            this.lsConcentrado();
        }, 100);

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
                    onchange: `concentrado.loadGruposConcentrado()`,
                },
                {
                    opc: "select",
                    id: "grupoConcentrado",
                    lbl: "Grupo de Producto",
                    class: "col-sm-2",
                    onchange: `concentrado.lsConcentrado()`,
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
                    onchange: `concentrado.lsConcentrado()`,
                },
                {
                    opc: "select",
                    id: "mesConcentrado",
                    lbl: "Mes",
                    class: "col-sm-2",
                    data: moment.months().map((m, i) => ({ id: i + 1, valor: m })),
                    onchange: `concentrado.lsConcentrado()`,
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
                    onchange: `concentrado.lsConcentrado()`,
                }
            ],
        });

        // setTimeout(() => {
        //     $("#filterBarConcentrado #mesConcentrado").val(currentMonth).trigger("change");
        // }, 100);
    }

    async loadGruposConcentrado() {
        const udn = $("#filterBarConcentrado #udnConcentrado").val();

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
        }
    }


    async lsConcentrado() {
        const response = await useFetch({
            url: this._link,
            data: {
                opc    : 'lsConcentrado',
                udn    : $("#filterBarConcentrado #udnConcentrado").val(),
                grupo  : $("#filterBarConcentrado #grupoConcentrado").val(),
                anio   : $("#filterBarConcentrado #anioConcentrado").val(),
                mes    : $("#filterBarConcentrado #mesConcentrado").val(),
                periodo: $("#filterBarConcentrado #periodo").val()
            }
        });


            this.createConcentradoTable({
                parent: "tableConcentrado",
                id: "tbConcentrado",
                theme: 'corporativo',
                data: {
                    thead: response.thead || [],
                    row: response.row || [],
                    theadGroups: response.theadGroups || []
                },
                f_size: 11,
                expandable: true
            });
     
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


    // Components.
    createConcentradoTable(options) {
        const defaults = {
            parent: "root",
            id: "concentradoTable",
            title: null,
            subtitle: null,
            data: { thead: [], row: [], theadGroups: [] },
            theme: 'dark',
            color_th: "bg-[#1e3a5f] text-white",
            color_th_group: "bg-[#1e3a5f] text-white",
            color_row: "bg-white",
            color_row_expandable: "bg-gray-50",
            border_table: "border border-gray-300 rounded-lg",
            border_row: "border-b border-gray-300",
            f_size: 11,
            expandable: true
        };

        const opts = Object.assign({}, defaults, options);

        const container = $("<div>", {
            class: "rounded-lg h-full overflow-x-auto"
        });

        if (opts.title) {
            const titleRow = $(`
                <div class="flex flex-col py-3 px-2">
                    <span class="text-lg font-semibold text-gray-800">${opts.title}</span>
                    ${opts.subtitle ? `<p class="text-sm text-gray-600 mt-1">${opts.subtitle}</p>` : ''}
                </div>
            `);
            container.append(titleRow);
        }

        const table = $("<table>", {
            id: opts.id,
            class: `w-full border-collapse ${opts.border_table}`
        });

        const thead = $("<thead>");

        if (opts.data.theadGroups && opts.data.theadGroups.length > 0) {
            const groupRow = $('<tr>');
            opts.data.theadGroups.forEach((group, idx) => {
                const th = $('<th>', {
                    colspan: group.colspan || 1,
                    class: `text-center px-3 py-3 font-semibold text-xs ${group.color || opts.color_th_group} border-b border-r border-gray-300`,
                    text: group.label || ''
                });
                if (idx === 0) {
                    th.addClass('border-l');
                }
                groupRow.append(th);
            });
            thead.append(groupRow);
        }

        if (opts.data.thead && opts.data.thead.length > 0) {
            const headerRow = $('<tr>');
            opts.data.thead.forEach((header, idx) => {
                const th = $('<th>', {
                    class: `text-center px-3 py-2 text-xs font-semibold uppercase ${opts.color_th} border-b border-r border-gray-300`,
                    text: header
                });
                if (idx === 0) {
                    th.addClass('border-l');
                }
                headerRow.append(th);
            });
            thead.append(headerRow);
        } else if (opts.data.row && opts.data.row.length > 0) {
            const autoHeaderRow = $("<tr>");
            for (let clave in opts.data.row[0]) {
                if (!["opc", "id", "expandable", "subrow"].includes(clave)) {
                    clave = (clave === 'btn' || clave === 'btn_personalizado' || clave === 'a' || clave === 'dropdown') 
                        ? '<i class="icon-gear"></i>' 
                        : clave;
                    autoHeaderRow.append($("<th>", {
                        class: `px-3 py-2 ${opts.color_th} capitalize text-center font-semibold border-b border-r border-gray-300`,
                        style: `font-size:${opts.f_size}px;`
                    }).html(clave));
                }
            }
            thead.append(autoHeaderRow);
        }

        table.append(thead);

        const tbody = $("<tbody>");

        opts.data.row.forEach((data, i) => {
            const isExpandable = data.expandable || (data.opc === 1);
            const isSubrow = data.subrow || false;

            const tr = $("<tr>", {
                class: `${isSubrow ? 'subrow' : ''} ${isExpandable ? 'expandable-row' : ''}`,
                'data-parent-id': data.id,
                'data-row-index': i
            });

            let colIdx = 0;
            Object.keys(data).forEach((key) => {
                if (["id", "opc", "expandable", "subrow"].includes(key)) return;

                let cellAttributes = {
                    class: `px-3 py-2 border-b border-r border-gray-300`,
                    style: `font-size:${opts.f_size}px;`
                };

                if (colIdx === 0) {
                    cellAttributes.class += ' border-l';
                }

                if (typeof data[key] === 'object' && data[key] !== null) {
                    cellAttributes.html = data[key].html || '';
                    cellAttributes.class += ` ${data[key].class || ''}`;
                } else {
                    cellAttributes.html = data[key];
                    cellAttributes.class += ` ${opts.color_row}`;
                }

                if (isExpandable && colIdx === 0) {
                    cellAttributes.class += ' cursor-pointer';
                    cellAttributes.html = `<span class="inline-flex items-center">
                        <i class="icon-right-open mr-2 expand-icon text-gray-600"></i>
                        ${cellAttributes.html}
                    </span>`;
                }

                tr.append($("<td>", cellAttributes));
                colIdx++;
            });

            tbody.append(tr);
        });

        table.append(tbody);
        container.append(table);
        $(`#${opts.parent}`).html(container);

        if (opts.expandable) {
            this.setupExpandableConcentrado(opts.id);
        }

        $("<style>").text(`
            #${opts.id} { border-collapse: collapse; }
            #${opts.id} thead tr:first-child th:first-child { border-top-left-radius: 0.5rem; }
            #${opts.id} thead tr:first-child th:last-child { border-top-right-radius: 0.5rem; }
            #${opts.id} tbody tr:last-child td:first-child { border-bottom-left-radius: 0.5rem; }
            #${opts.id} tbody tr:last-child td:last-child { border-bottom-right-radius: 0.5rem; }
            #${opts.id} tbody tr:last-child td { border-bottom: 1px solid #d1d5db; }
            #${opts.id} .subrow { display: none; }
            #${opts.id} .subrow.show { display: table-row; }
            #${opts.id} .expand-icon { 
                transition: transform 0.2s ease;
                display: inline-block;
            }
            #${opts.id} .expandable-row.expanded .expand-icon { 
                transform: rotate(90deg); 
            }
        `).appendTo("head");
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
