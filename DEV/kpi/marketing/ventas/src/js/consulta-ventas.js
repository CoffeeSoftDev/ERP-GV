let apiVentas = 'ctrl/ctrl-ventas2.php';
let app, lsudn, categorias;

$(async () => {
    const data = await useFetch({ url: apiVentas, data: { opc: "init" } });
    lsudn = data.udn;
    categorias = data.categorias;

    app = new ConsultaVentas(apiVentas, "root");
    app.render();
});

class ConsultaVentas extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "consultaVentas";
    }

    render() {
        this.layout();
        this.filterBar();
        this.listSales();
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
            title: "📊 Consulta de Ventas",
            subtitle: "Visualiza y gestiona las ventas diarias por unidad de negocio",
            onClick: () => this.redirectToHome()
        });
    }

    filterBar() {
        const filterContainer = $("<div>", {
            class: "bg-white rounded-lg shadow-sm p-4 mb-4"
        });

        this.createfilterBar({
            parent: `filterBar${this.PROJECT_NAME}`,
            data: [
                {
                    opc: "select",
                    id: "udn",
                    lbl: "Unidad de Negocio",
                    class: "col-sm-2",
                    data: lsudn,
                    onchange: `app.listSales()`,
                },
                {
                    opc: "select",
                    id: "anio",
                    lbl: "Año",
                    class: "col-sm-2",
                    data: Array.from({ length: 5 }, (_, i) => {
                        const year = moment().year() - i;
                        return { id: year, valor: year.toString() };
                    }),
                    onchange: `app.listSales()`,
                },
                {
                    opc: "select",
                    id: "mes",
                    lbl: "Mes",
                    class: "col-sm-2",
                    data: moment.months().map((m, i) => ({ id: i + 1, valor: m })),
                    onchange: `app.listSales()`,
                },
              
                {
                    opc: "button",
                    class: "col-sm-2",
                    id: "btnSyncMonth",
                    text: "Sincronizar Mes",
                    color_btn:' ',
                    className: "w-100 bg-orange-400 hover:bg-orange-700 text-white",
                    onClick: () => this.syncMonthToFolio(),
                },
            ],
        });

        const currentMonth = moment().month() + 1;
        setTimeout(() => {
            $(`#filterBar${this.PROJECT_NAME} #mes`).val(currentMonth).trigger("change");
        }, 100);
    }

    listSales() {
        const udn = $(`#filterBar${this.PROJECT_NAME} #udn`).val();
        const anio = $(`#filterBar${this.PROJECT_NAME} #anio`).val();
        const mes = $(`#filterBar${this.PROJECT_NAME} #mes`).val();
        const monthText = $(`#filterBar${this.PROJECT_NAME} #mes option:selected`).text();

     

        this.createTable({
            parent: `container${this.PROJECT_NAME}`,
            idFilterBar: `filterBar${this.PROJECT_NAME}`,
            data: { 
                opc: 'lsSales',
                udn: udn,
                anio: anio,
                mes: mes
            },
            coffeesoft: true,
            conf: { datatable: false, pag: 15 },
            attr: {
                id: "tbVentasDiarias",
                theme: 'corporativo',
                extends:true,
                center: [1, 2],
                right: []
            },
        });
    }

    addSale() {
        this.createModalForm({
            id: 'formSaleAdd',
            data: { opc: 'addSale' },
            bootbox: {
                title: '➕ Agregar Nueva Venta',
                closeButton: true,
                size: 'large'
            },
            json: this.jsonSale(),
            success: (response) => {
                if (response.status === 200) {
                    alert({
                        icon: "success",
                        title: "¡Éxito!",
                        text: response.message,
                        btn1: true,
                        btn1Text: "Aceptar"
                    });
                    this.listSales();
                } else {
                    alert({
                        icon: "error",
                        title: "Error",
                        text: response.message,
                        btn1: true,
                        btn1Text: "Cerrar"
                    });
                }
            }
        });
    }

    async editSale(id) {
        const request = await useFetch({
            url: this._link,
            data: { opc: "getSale", id: id }
        });

        if (request.status === 200) {
            this.createModalForm({
                id: 'formSaleEdit',
                data: { opc: 'editSale', id: id },
                bootbox: {
                    title: '✏️ Editar Venta',
                    closeButton: true,
                    size: 'large'
                },
                autofill: request.data,
                json: this.jsonSale(),
                success: (response) => {
                    if (response.status === 200) {
                        alert({
                            icon: "success",
                            title: "¡Actualizado!",
                            text: response.message,
                            btn1: true,
                            btn1Text: "Aceptar"
                        });
                        this.listSales();
                    } else {
                        alert({
                            icon: "error",
                            title: "Error",
                            text: response.message,
                            btn1: true,
                            btn1Text: "Cerrar"
                        });
                    }
                }
            });
        } else {
            alert({
                icon: "error",
                title: "Error",
                text: "No se pudieron obtener los datos de la venta",
                btn1: true,
                btn1Text: "Cerrar"
            });
        }
    }

    statusSale(id, active) {
        const action = active === 1 ? 'desactivar' : 'activar';
        const actionText = active === 1 ? 'desactivará' : 'activará';
        
        this.swalQuestion({
            opts: {
                title: `¿${action.charAt(0).toUpperCase() + action.slice(1)} esta venta?`,
                text: `Esta acción ${actionText} el registro en el sistema`,
                icon: "warning",
                confirmButtonText: `Sí, ${action}`,
                cancelButtonText: "Cancelar"
            },
            data: {
                opc: "statusSale",
                id: id,
                active: active
            },
            methods: {
                send: (response) => {
                    if (response.status === 200) {
                        alert({
                            icon: "success",
                            title: "¡Actualizado!",
                            text: response.message,
                            btn1: true,
                            btn1Text: "Aceptar"
                        });
                        this.listSales();
                    } else {
                        alert({
                            icon: "error",
                            title: "Error",
                            text: response.message,
                            btn1: true,
                            btn1Text: "Cerrar"
                        });
                    }
                }
            }
        });
    }

    syncToFolio(fecha, udn) {
        this.swalQuestion({
            opts: {
                title: "📤 Sincronizar Ventas",
                html: `
                    <div class="text-left">
                        <p class="mb-2">¿Deseas sincronizar las ventas del día <strong>${moment(fecha).format('DD/MM/YYYY')}</strong>?</p>
                        <div class="bg-blue-50 border-l-4 border-blue-500 p-3 mt-3">
                            <p class="text-sm text-blue-700">
                                <i class="icon-info-circled"></i> 
                                Esta acción verificará que exista un folio en <strong>soft_folio</strong> (si no existe, lo creará) y actualizará o creará el registro en <strong>soft_restaurant_ventas</strong> con los totales calculados.
                            </p>
                        </div>
                    </div>
                `,
                icon: "question",
                confirmButtonText: "Sí, sincronizar",
                cancelButtonText: "Cancelar",
                showCancelButton: true
            },
            data: {
                opc: "syncToFolio",
                fecha: fecha,
                udn: udn
            },
            methods: {
                send: (response) => {
                    if (response.status === 200) {
                        const data = response.data;
                        
                        let ventasHTML = '<ul class="space-y-1">';
                        
                        if (data.habitaciones !== undefined) {
                            ventasHTML += `<li>🏨 Habitaciones: <strong>${data.habitaciones}</strong></li>`;
                        }
                        
                        if (data.alimentos > 0) {
                            ventasHTML += `<li>🍽️ Alimentos: <strong>${formatPrice(data.alimentos)}</strong> <span class="text-xs text-gray-500">(+8% IVA)</span></li>`;
                        }
                        
                        if (data.bebidas > 0) {
                            ventasHTML += `<li>🍹 Bebidas: <strong>${formatPrice(data.bebidas)}</strong> <span class="text-xs text-gray-500">(+8% IVA)</span></li>`;
                        }
                        
                        if (data.AyB > 0) {
                            ventasHTML += `<li>🍴 A&B: <strong>${formatPrice(data.AyB)}</strong> <span class="text-xs text-gray-500">(+8% IVA)</span></li>`;
                        }
                        
                        if (data.hospedaje > 0) {
                            ventasHTML += `<li>🏨 Hospedaje: <strong>${formatPrice(data.hospedaje)}</strong> <span class="text-xs text-gray-500">(+10% IVA+IEPS)</span></li>`;
                        }
                        
                        if (data.otros > 0) {
                            ventasHTML += `<li>📦 Otros: <strong>${formatPrice(data.otros)}</strong> <span class="text-xs text-gray-500">(+8% IVA)</span></li>`;
                        }
                        
                        if (data.diversos > 0) {
                            ventasHTML += `<li>🎯 Diversos: <strong>${formatPrice(data.diversos)}</strong> <span class="text-xs text-gray-500">(+8% IVA)</span></li>`;
                        }
                        
                        ventasHTML += `<li class="border-t pt-2 mt-2 text-green-700 font-bold">✅ Total: <strong>${formatPrice(data.total)}</strong></li>`;
                        ventasHTML += '</ul>';
                        
                        alert({
                            icon: "success",
                            title: "¡Sincronización Exitosa!",
                            html: `
                                <div class="text-left">
                                    <p class="mb-3">${response.message}</p>
                                    <div class="bg-gray-50 rounded-lg p-3 text-sm">
                                        <p class="font-semibold mb-2">📋 Folio ID: <span class="text-blue-600">#${data.folio_id}</span></p>
                                        <p class="font-semibold mb-2">Resumen de ventas sincronizadas:</p>
                                        ${ventasHTML}
                                    </div>
                                </div>
                            `,
                            btn1: true,
                            btn1Text: "Aceptar"
                        });
                   
                    } else {
                        alert({
                            icon: "error",
                            title: "Error en la Sincronización",
                            text: response.message,
                            btn1: true,
                            btn1Text: "Cerrar"
                        });
                    }
                }
            }
        });
    }

    syncMonthToFolio() {
        const udn = $(`#filterBar${this.PROJECT_NAME} #udn`).val();
        const anio = $(`#filterBar${this.PROJECT_NAME} #anio`).val();
        const mes = $(`#filterBar${this.PROJECT_NAME} #mes`).val();
        const monthText = $(`#filterBar${this.PROJECT_NAME} #mes option:selected`).text();
        const udnText = $(`#filterBar${this.PROJECT_NAME} #udn option:selected`).text();

        this.swalQuestion({
            opts: {
                title: "📅 Sincronizar Mes Completo",
                html: `
                    <div class="text-left">
                        <p class="mb-2">¿Deseas sincronizar <strong>TODO EL MES</strong> de <strong>${monthText} ${anio}</strong> para <strong>${udnText}</strong>?</p>
                        <div class="bg-orange-50 border-l-4 border-orange-500 p-3 mt-3">
                            <p class="text-sm text-orange-700">
                                <i class="icon-attention"></i> 
                                <strong>Atención:</strong> Esta acción procesará todos los días del mes seleccionado. Para cada día:
                            </p>
                            <ul class="text-xs text-orange-600 mt-2 ml-4 list-disc">
                                <li>Verificará o creará el folio en <strong>soft_folio</strong></li>
                                <li>Calculará totales con impuestos (IVA 8%, IEPS 2% para hospedaje)</li>
                                <li>Actualizará o creará registros en <strong>soft_restaurant_ventas</strong></li>
                            </ul>
                            <p class="text-sm text-orange-700 mt-2">
                                ⏱️ Este proceso puede tardar varios segundos dependiendo de la cantidad de días.
                            </p>
                        </div>
                    </div>
                `,
                icon: "warning",
                confirmButtonText: "Sí, sincronizar mes completo",
                cancelButtonText: "Cancelar",
                showCancelButton: true
            },
            data: {
                opc: "syncMonthToFolio",
                udn: udn,
                anio: anio,
                mes: mes
            },
            methods: {
                send: (response) => {
                    if (response.status === 200) {
                        const exitosos = response.exitosos || 0;
                        const fallidos = response.fallidos || 0;
                        const resultados = response.resultados || [];
                        
                        let resultadosHTML = '<div class="max-h-64 overflow-y-auto">';
                        resultadosHTML += '<table class="w-full text-sm">';
                        resultadosHTML += '<thead class="bg-gray-100 sticky top-0"><tr><th class="p-2 text-left">Fecha</th><th class="p-2 text-left">Estado</th><th class="p-2 text-right">Total</th></tr></thead>';
                        resultadosHTML += '<tbody>';
                        
                        resultados.forEach(resultado => {
                            const statusIcon = resultado.status === 'success' ? '✅' : '❌';
                            const statusClass = resultado.status === 'success' ? 'text-green-600' : 'text-red-600';
                            const total = resultado.total ? formatPrice(resultado.total) : '-';
                            
                            resultadosHTML += `
                                <tr class="border-b">
                                    <td class="p-2">${moment(resultado.fecha).format('DD/MM/YYYY')}</td>
                                    <td class="p-2 ${statusClass}">${statusIcon} ${resultado.message}</td>
                                    <td class="p-2 text-right font-semibold">${total}</td>
                                </tr>
                            `;
                        });
                        
                        resultadosHTML += '</tbody></table></div>';
                        
                        alert({
                            icon: "success",
                            title: "¡Sincronización Mensual Completada!",
                            html: `
                                <div class="text-left">
                                    <p class="mb-3">${response.message}</p>
                                    <div class="bg-gray-50 rounded-lg p-3 mb-3">
                                        <div class="grid grid-cols-2 gap-4 text-center mb-3">
                                            <div class="bg-green-100 rounded p-2">
                                                <div class="text-2xl font-bold text-green-700">${exitosos}</div>
                                                <div class="text-xs text-green-600">Exitosos</div>
                                            </div>
                                            <div class="bg-red-100 rounded p-2">
                                                <div class="text-2xl font-bold text-red-700">${fallidos}</div>
                                                <div class="text-xs text-red-600">Fallidos</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-white rounded-lg border">
                                        <div class="bg-gray-100 p-2 font-semibold text-sm border-b">
                                            📋 Detalle de Sincronización
                                        </div>
                                        ${resultadosHTML}
                                    </div>
                                </div>
                            `,
                            btn1: true,
                            btn1Text: "Aceptar"
                        });
                        
                        this.listSales();
                   
                    } else if (response.status === 404) {
                        alert({
                            icon: "info",
                            title: "Sin Datos",
                            text: response.message,
                            btn1: true,
                            btn1Text: "Cerrar"
                        });
                    } else {
                        alert({
                            icon: "error",
                            title: "Error en la Sincronización",
                            text: response.message,
                            btn1: true,
                            btn1Text: "Cerrar"
                        });
                    }
                }
            }
        });
    }

    jsonSale() {
        return [
            {
                opc: "label",
                id: "lblInfo",
                text: "Información de la Venta",
                class: "col-12 fw-bold text-lg mb-2 border-b pb-2"
            },
            {
                opc: "select",
                id: "udn",
                lbl: "Unidad de Negocio",
                class: "col-12 col-md-6 mb-3",
                data: lsudn,
                text: "valor",
                value: "id"
            },
            {
                opc: "input",
                id: "fecha",
                lbl: "Fecha de Venta",
                type: "date",
                class: "col-12 col-md-6 mb-3"
            },
            {
                opc: "label",
                id: "lblDesglose",
                text: "Desglose por Categoría",
                class: "col-12 fw-bold text-lg mb-2 mt-3 border-b pb-2"
            },
            {
                opc: "input",
                id: "cantidad",
                lbl: "Cantidad de Ventas",
                tipo: "cifra",
                class: "col-12 mb-3",
                onkeyup: "validationInputForNumber('#cantidad')"
            },
            {
                opc: "select",
                id: "categoria",
                lbl: "Categoría de Venta",
                class: "col-12 mb-3",
                data: categorias,
                text: "valor",
                value: "id"
            }
        ];
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
