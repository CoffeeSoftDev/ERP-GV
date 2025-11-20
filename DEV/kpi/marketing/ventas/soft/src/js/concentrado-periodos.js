let apiConcentrado = 'ctrl/ctrl-concentrado-periodos.php';
let concentrado, lsudn;

$(async () => {
    const data = await useFetch({ url: apiConcentrado, data: { opc: "init" } });
    lsudn = data.udn;

    concentrado = new ConcentradoPeriodos(apiConcentrado, "root");
    concentrado.render();
});

class ConcentradoPeriodos extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "Concentrado";
    }

    render() {
        this.layout();
        this.filterBarConcentrado();
        this.loadGruposConcentrado();

        setTimeout(() => {
            this.lsConcentrado();
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
            title: "📊 Concentrado por Periodos",
            subtitle: "Análisis de productos por periodos mensuales",
            onClick: () => this.redirectToHome()
        });
    }

    filterBarConcentrado() {
        const currentYear = moment().year();

        this.createfilterBar({
            parent: `filterBar${this.PROJECT_NAME}`,
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
                opc: 'lsConcentrado',
                udn: $("#filterBarConcentrado #udnConcentrado").val(),
                grupo: $("#filterBarConcentrado #grupoConcentrado").val(),
                anio: $("#filterBarConcentrado #anioConcentrado").val(),
                mes: $("#filterBarConcentrado #mesConcentrado").val(),
                periodo: $("#filterBarConcentrado #periodo").val()
            }
        });

        this.createConcentradoTable({
            parent: `container${this.PROJECT_NAME}`,
            id: "tbConcentrado",
            theme: 'corporativo',
            data: {
                thead: response.thead || [],
                row: response.row || [],
                theadGroups: response.theadGroups || []
            },
            f_size: 11,
            colGroup: true
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
            colGroup: true
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
                    class: `text-center px-3 py-3 font-semibold text-xs ${group.color || opts.color_th_group}`,
                    text: group.label || ''
                });
                groupRow.append(th);
            });
            thead.append(groupRow);
        }

        if (opts.data.thead && opts.data.thead.length > 0) {
            const headerRow = $('<tr>');
            opts.data.thead.forEach((header, idx) => {
                const th = $('<th>', {
                    class: `text-center px-3 py-2 text-xs font-semibold uppercase ${opts.color_th}`,
                    text: header
                });
                headerRow.append(th);
            });
            thead.append(headerRow);
        } else if (opts.data.row && opts.data.row.length > 0) {
            const autoHeaderRow = $("<tr>");
            for (let clave in opts.data.row[0]) {
                if (!["opc", "id", "colGroup", "subrow"].includes(clave)) {
                    clave = (clave === 'btn' || clave === 'btn_personalizado' || clave === 'a' || clave === 'dropdown')
                        ? '<i class="icon-gear"></i>'
                        : clave;
                    autoHeaderRow.append($("<th>", {
                        class: `px-3 py-2 ${opts.color_th} capitalize text-center font-semibold`,
                        style: `font-size:${opts.f_size}px;`
                    }).html(clave));
                }
            }
            thead.append(autoHeaderRow);
        }

        table.append(thead);

        const tbody = $("<tbody>");

        opts.data.row.forEach((data, i) => {
            const isExpandable = data.colGroup || (data.opc === 1);
            const isSubrow = data.subrow || false;

            const tr = $("<tr>", {
                class: `${isSubrow ? 'subrow' : ''} ${isExpandable ? 'expandable-row' : ''}`,
                'data-parent-id': data.id,
                'data-row-index': i
            });

            let colIdx = 0;
            Object.keys(data).forEach((key) => {
                if (["id", "opc", "colGroup", "subrow"].includes(key)) return;

                let cellAttributes = {
                    class: `px-3 py-2`,
                    style: `font-size:${opts.f_size}px;`
                };

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

        if (opts.colGroup) {
            this.toggleTable(opts.id);
        }

        $("<style>").text(`
            #${opts.id} { 
                border-collapse: collapse; 
                border: 1px solid #d1d5db;
                border-radius: 0.5rem;
            }
            #${opts.id} thead tr th { 
                border-bottom: 1px solid #d1d5db; 
            }
            #${opts.id} tbody tr td { 
                border-bottom: 1px solid #e5e7eb; 
            }
            #${opts.id} tbody tr:last-child td { 
                border-bottom: none; 
            }
            #${opts.id} .subrow { 
                display: none; 
            }
            #${opts.id} .subrow.show { 
                display: table-row; 
            }
            #${opts.id} .expand-icon { 
                transition: transform 0.2s ease;
                display: inline-block;
            }
            #${opts.id} .expandable-row.expanded .expand-icon { 
                transform: rotate(90deg); 
            }
        `).appendTo("head");
    }

    toggleTable(tableId) {
        setTimeout(() => {
            $(`#${tableId} .expandable-row`).off('click').on('click', function(e) {
                e.stopPropagation();
                const $row = $(this);
                const parentId = $row.data('parent-id');
                const isExpanded = $row.hasClass('expanded');

                $row.toggleClass('expanded');

                let $nextRow = $row.next();
                while ($nextRow.length && $nextRow.hasClass('subrow') && $nextRow.data('parent-id') === parentId) {
                    $nextRow.toggleClass('show');
                    if (isExpanded) {
                        $nextRow.hide();
                    } else {
                        $nextRow.show();
                    }
                    $nextRow = $nextRow.next();
                }
            });
        }, 100);
    }
}
