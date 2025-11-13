
let app, clientes, analitycs;
let udnData = [];

const api = "ctrl/ctrl-clientes.php";

$(async () => {
 

    const data = await useFetch({ url: api, data: { opc: "init" } });
    udnData = data.udn;

    app       = new App(api, "root");
    clientes  = new Clientes(api, "root");
    analitycs = new Analitycs(api, "root");

    app.render();
    clientes.render();
    analitycs.render();

});

class App extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "Gestion";
    }


    render() {
        this.layout();
    }


    layout() {

        this.primaryLayout({
            parent: 'root',
            id: this.PROJECT_NAME,
            class: '',
            card: {
                filterBar: { class: 'w-full my-2', id: 'filterBar' + this.PROJECT_NAME },
                container: { class: 'w-full h-full p-2', id: 'container' + this.PROJECT_NAME }
            }
        });

        $("#filterBar" + this.PROJECT_NAME).html(`
            <div class="px-4 pt-3 pb-3">
                <h2 class="text-2xl font-semibold">👥 Gestión de Clientes</h2>
                <p class="text-gray-400">Administración de información y seguimiento de clientes de las unidades de negocio.</p>
            </div>
        `);

        this.tabLayout({
            parent: `container${this.PROJECT_NAME}`,
            id: `tabs${this.PROJECT_NAME}`,
            theme: "light",
            type: "short",
            json: [

                {
                    id: "capture",
                    tab: "Captura de información",
                    active: true,
                    onClick: () => clientes.ls()
                },
                {
                    id: "analitycs",
                    tab: "Estadisticas de clientes",
                    class: "mb-1",
                    onClick: () => analitycs.ls()
                },

            ]
        });

        
        this.headerBar({
            parent: "filterBar" + this.PROJECT_NAME,
            title: "Módulo de Gestión de Clientes",
            subtitle: "Administración de información y seguimiento de clientes de las unidades de negocio.",
            onClick: () => app.redirectToHome(),
        });


        $('#content-tabs' + this.PROJECT_NAME).removeClass('h-screen');
    }


    redirectToHome() {
        const base = window.location.origin + '/ERP24';
        window.location.href = `${base}/kpi/marketing.php`;
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
            class: "relative flex justify-center items-center px-2 pt-3 pb-3"
        });

        // 🔵 Botón alineado a la izquierda (posición absoluta)
        const leftSection = $("<div>", {
            class: "absolute left-0"
        }).append(
            $("<button>", {
                class: `${opts.classBtn} font-semibold px-4 py-2 rounded transition flex items-center`,
                html: `<i class="${opts.icon} mr-2"></i>${opts.textBtn}`,
                click: () => typeof opts.onClick === "function" && opts.onClick()
            })
        );

        // 📜 Texto centrado
        const centerSection = $("<div>", {
            class: "text-center"
        }).append(
            $("<h2>", {
                class: "text-2xl font-bold",
                text: opts.title
            }),
            $("<p>", {
                class: "text-gray-400",
                text: opts.subtitle
            })
        );

        container.append(leftSection, centerSection);
        $(`#${opts.parent}`).html(container);
    }



}

class Clientes extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "Clientes";
    }

    render() {
        this.layoutClients();
        this.filterBar();
        this.ls();
    }


    layoutClients() {

        this.primaryLayout({
            parent: 'container-capture',
            id: this.PROJECT_NAME,
            class: '',
            card: {
                filterBar: { class: 'w-full my-2', id: 'filterBar' + this.PROJECT_NAME },
                container: { class: 'w-full h-full ', id: 'container' + this.PROJECT_NAME }
            }
        });

    }

    filterBar() {
        this.createfilterBar({
            parent: "filterBar" + this.PROJECT_NAME,
            data: [
                {
                    opc: "select",
                    id: "udn_id",
                    lbl: "Unidad de Negocio",
                    class: "col-12 col-md-2",
                    data: [
                        { id: "all", valor: "Todas las unidades" },
                        ...udnData
                    ],
                    onchange: 'clientes.ls()'
                },
                {
                    opc: "select",
                    id: "active",
                    lbl: "Estatus",
                    class: "col-12 col-md-2",
                    data: [
                        { id: "1", valor: "Activos" },
                        { id: "0", valor: "Inactivos" }
                    ],
                    onchange: 'clientes.ls()'
                },
                {
                    opc: "select",
                    id: "vip",
                    lbl: "Tipo de Cliente",
                    class: "col-12 col-md-2",
                    data: [
                        { id: "all", valor: "Todos" },
                        { id: "1", valor: "VIP" },
                        { id: "0", valor: "Regular" }
                    ],
                    onchange: 'clientes.ls()'
                },
                {
                    opc: "button",
                    class: "col-12 col-md-3",
                    id: "btnNuevoCliente",
                    text: "Agregar Cliente",
                    onClick: () => this.addCliente()
                }
            ]
        });
    }

    ls() {
        this.createTable({
            parent: "container" + this.PROJECT_NAME,
            idFilterBar: "filterBar" + this.PROJECT_NAME,
            data: { opc: "listClientes" },
            coffeesoft: true,
            conf: { datatable: true, pag: 10 },
            attr: {
                id: "tbClientes",
                theme: 'corporativo',
                right: [7],
                center: [5, 6, 7]
            }
        });

        // Agregar event listeners a los badges VIP después de que se renderice la tabla
        setTimeout(() => {
            this.attachVipBadgeEvents();
        }, 500);
    }

    attachVipBadgeEvents() {
        // Remover event listeners existentes para evitar duplicados
        $(document).off('click', '.vip-badge');

        // Agregar event listener para los badges VIP
        $(document).on('click', '.vip-badge', (event) => {
            event.preventDefault();
            event.stopPropagation();

            const badge = $(event.currentTarget);
            const clientId = badge.data('client-id');
            const currentVipStatus = badge.data('vip-status');

            if (clientId) {
                this.updateClientVipStatus(clientId, currentVipStatus);
            }
        });
    }

    // Clients.

    addCliente() {
        this.createModalForm({
            id: 'formClienteAdd',
            data: { opc: 'addCliente' },
            bootbox: {
                title: 'Agregar Cliente',
                size: 'large'
            },
            json: this.jsonFormCliente(),
            success: (response) => {
                if (response.status === 200) {
                    alert({ icon: "success", text: response.message });
                    this.ls();
                } else if (response.status === 409) {
                    alert({ icon: "warning", title: "Cliente Duplicado", text: response.message });
                } else if (response.status === 400) {
                    alert({ icon: "error", title: "Datos Inválidos", text: response.message });
                } else {
                    alert({ icon: "error", title: "Error", text: response.message });
                }
            }
        });
    }

    async editCliente(id) {
        const request = await useFetch({
            url: this._link,
            data: {
                opc: "getCliente",
                id: id
            }
        });

        if (request.status !== 200) {
            alert({ icon: "error", text: request.message });
            return;
        }

        const cliente = request.data;

        const autofillData = {
            ...cliente,
            ...(cliente.domicilio || {})
        };

        this.createModalForm({
            id: 'formClienteEdit',
            data: { opc: 'editCliente', id: cliente.id },
            bootbox: {
                title: '✏️ Editar Cliente',
                size: 'large'
            },
            autofill: autofillData,
            json: this.jsonFormCliente(),
            success: (response) => {
                if (response.status === 200) {
                    alert({ icon: "success", text: response.message });
                    this.ls();
                } else if (response.status === 409) {
                    alert({ icon: "warning", title: "Teléfono Duplicado", text: response.message });
                } else if (response.status === 400) {
                    alert({ icon: "error", title: "Datos Inválidos", text: response.message });
                } else {
                    alert({ icon: "error", title: "Error", text: response.message });
                }
            }
        });
    }

    statusCliente(id, active) {
        const accion = active == 1 ? 'desactivar' : 'activar';
        const textoAccion = active == 1
            ? 'El cliente no estará disponible para nuevos pedidos.'
            : 'El cliente volverá a estar disponible para pedidos.';

        const nuevoEstado = active == 1 ? 0 : 1;

        this.swalQuestion({
            opts: {
                title: `¿Desea ${accion} este cliente?`,
                text: textoAccion,
                icon: "warning"
            },
            data: {
                opc: "statusCliente",
                active: nuevoEstado,
                id: id,
            },
            methods: {
                send: (response) => {
                    if (response.status === 200) {
                        alert({ icon: "success", text: response.message });
                        this.ls();
                    } else {
                        alert({ icon: "error", text: response.message });
                    }
                }
            }
        });
    }

    updateClientVipStatus(id, currentVipStatus) {
        const isVip = currentVipStatus == 1;
        const newStatus = isVip ? 0 : 1;
        const statusText = isVip ? 'Regular' : 'VIP';
        const currentStatusText = isVip ? 'VIP' : 'Regular';

        const title = isVip
            ? '¿Deseas cambiar este cliente a Regular?'
            : '¿Deseas actualizar este cliente a VIP?';

        const text = isVip
            ? 'El cliente perderá los beneficios VIP y será tratado como cliente regular.'
            : 'El cliente obtendrá beneficios especiales y prioridad en el servicio.';

        // Mostrar estado de carga en el badge
        const badge = $(`[data-client-id="${id}"]`);
        badge.addClass('updating').html('⏳ Actualizando...');

        this.swalQuestion({
            opts: {
                title: title,
                text: text,
                icon: "question",
                confirmButtonText: `Sí, cambiar a ${statusText}`,
                cancelButtonText: "Cancelar"
            },
            data: {
                opc: "updateClientStatus",
                id: id,
                vip: newStatus
            },
            methods: {
                send: (response) => {
                    if (response.status === 200) {
                        // Actualizar el badge dinámicamente sin recargar la tabla
                        this.updateVipBadgeInTable(id, newStatus);

                        alert({
                            icon: "success",
                            title: "¡Estado actualizado!",
                            text: `Cliente cambiado a ${statusText} exitosamente.`
                        });

                        this.ls()
                    } else {
                        // Restaurar el badge al estado anterior en caso de error
                        this.updateVipBadgeInTable(id, currentVipStatus);

                        alert({
                            icon: "error",
                            title: "Error",
                            text: response.message || "No se pudo actualizar el estado del cliente."
                        });
                    }
                },
                cancel: () => {
                    // Restaurar el badge si el usuario cancela
                    this.updateVipBadgeInTable(id, currentVipStatus);
                }
            }
        });
    }

    updateVipBadgeInTable(clientId, newVipStatus) {
        // Buscar el badge en la tabla y actualizarlo
        const badge = $(`[data-client-id="${clientId}"]`);

        if (badge.length > 0) {
            // Remover clase de actualización
            badge.removeClass('updating');

            if (newVipStatus == 1) {
                // Cambiar a VIP
                badge.removeClass('bg-gray-100 text-gray-600 hover:bg-gray-200')
                    .addClass('bg-orange-100 text-yellow-600 hover:bg-orange-200')
                    .html('<i class="icon-star"></i> VIP')
                    .attr('data-vip-status', '1')
                    .attr('title', 'Clic para cambiar a Regular');
            } else {
                // Cambiar a Regular
                badge.removeClass('bg-orange-100 text-yellow-600 hover:bg-orange-200')
                    .addClass('bg-gray-100 text-gray-600 hover:bg-gray-200')
                    .html('Regular')
                    .attr('data-vip-status', '0')
                    .attr('title', 'Clic para cambiar a VIP');
            }
        }
    }

    jsonFormCliente() {
        return [
            {
                opc: "div",
                class: "col-12 mb-3",
                html: '<h5 class="text-lg font-bold border-b pb-2">📋 Información Personal</h5>'
            },
            {
                opc: "select",
                id: "udn_id",
                lbl: "Unidad de Negocio ",
                class: "col-12 col-md-4 mb-3",
                data: udnData,
                text: "valor",
                value: "id"
            },

            {
                opc        : "input",
                id         : "nombre",
                lbl        : "Nombre *",
                class      : "col-12 col-md-8 mb-3",
                placeholder: "Nombre completo"
            },

            {
                opc: "div",
                class: "col-12 mb-3 mt-1",
                html: '<h5 class="text-lg font-bold border-b pb-2">📞 Información de Contacto</h5>'
            },
            {
                opc: "input",
                id: "telefono",
                lbl: "Teléfono *",
                tipo: "tel",
                class: "col-12 col-md-4 mb-3",
                placeholder: "10 dígitos",
                onkeyup: "validationInputForNumber('#telefono')"
            },
            {
                opc: "input",
                id: "correo",
                lbl: "Correo Electrónico",
                tipo: "email",
                class: "col-12 col-md-4 mb-3",
                placeholder: "ejemplo@correo.com",
                required: false
            },
            {
                opc: "input",
                id: "fecha_cumpleaños",
                lbl: "Fecha de Cumpleaños",
                type: "date",
                class: "col-12 col-md-4 mb-3",
                required: false
            },


            {
                opc: "div",
                class: "col-12 mt-2",
                html: '<p class="text-sm text-gray-500"><strong>*</strong> Campos obligatorios</p>'
            }
        ];
    }
}

class Analitycs extends Templates {

    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "Comportamiento";
    }

    render() {
        this.layout();
        this.filterBar();
        this.ls();
    }


    layout() {

        this.primaryLayout({
            parent: 'container-analitycs',
            id: this.PROJECT_NAME,
            class: '',
            card: {
                filterBar: { class: 'w-full ', id: 'filterBar' + this.PROJECT_NAME },
                container: { class: 'w-full h-full ', id: 'container' + this.PROJECT_NAME }
            }
        });


    }


    filterBar() {
        this.createfilterBar({
            parent: "filterBar" + this.PROJECT_NAME,
            data: [
                {
                    opc: "select",
                    id: "udn_id",
                    lbl: "Unidad de Negocio",
                    class: "col-12 col-md-3",
                    data: [
                        { id: "all", valor: "Todas las unidades" },
                        ...udnData
                    ],
                    onchange: 'analitycs.ls()'
                },
                {
                    opc: "select",
                    id: "active",
                    lbl: "Estatus",
                    class: "col-12 col-md-2",
                    data: [
                        { id: "1", valor: "Activos" },
                        { id: "0", valor: "Inactivos" }
                    ],
                    onchange: 'analitycs.ls()'
                },
                {
                    opc: "button",
                    class: "col-12 col-md-3",
                    id: "btnTopClientes",
                    text: "🏆 Top Clientes",
                    onClick: () => this.showTopClientes()
                }
            ]
        });
    }


    ls() {
        this.createTable({
            parent: "container" + this.PROJECT_NAME,
            idFilterBar: "filterBar" + this.PROJECT_NAME,
            data: { opc: "listComportamiento" },
            coffeesoft: true,
            conf: { datatable: true, pag: 10 },
            attr: {
                id: "tbComportamiento",
                theme: 'corporativo',
                title: '📊 Comportamiento de Clientes',
                subtitle: 'Análisis de frecuencia de compra, última visita y patrones de consumo.',
                right: [3, 4, 5, 6, 9],                                                                        // Columna de acciones
                center: [8]                                                                   // Total Pedidos, Días sin Comprar, Frecuencia
            }
        });
    }


    async verDetalle(id) {
        const request = await useFetch({
            url: this._link,
            data: {
                opc: "getComportamiento",
                id: id
            }
        });

        if (request.status !== 200) {
            alert({ icon: "error", text: request.message });
            return;
        }

        const data = request.data;
        const cliente = data.cliente;
        const historial = data.historial;

        const nombreCompleto = `${cliente.nombre} `;
        const iniciales = this.getInitials(nombreCompleto);

        const badgeVIP = cliente.vip == 1
            ? '<span class="px-2 py-1 rounded-md text-xs font-semibold bg-yellow-500 text-white ml-2"><i class="icon-star"></i> VIP</span>'
            : '';

        let historialHTML = '';
        if (historial && historial.length > 0) {
            historial.forEach(pedido => {
                const fechaPedido = new Date(pedido.fecha_pedido);
                const fechaFormateada = fechaPedido.toLocaleDateString('es-MX', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });

                historialHTML += `
                    <div class="flex justify-between items-center p-3 border-b border-gray-200 hover:bg-gray-50 transition">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-semibold text-gray-800">Pedido #${pedido.id}</span>
                                <span class="text-xs px-2 py-1 rounded-full ${pedido.envio_domicilio == 1 ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700'}">
                                    ${pedido.envio_domicilio == 1 ? '🏠 Domicilio' : '🏪 Recoger'}
                                </span>
                            </div>
                            <div class="text-xs text-gray-500">
                                ${fechaFormateada} • ${pedido.udn_nombre || 'N/A'}
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-bold text-gray-600">$${parseFloat(pedido.monto).toFixed(2)}</span>
                        </div>
                    </div>
                `;
            });
        } else {
            historialHTML = `
                <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                        <i class="icon-inbox text-3xl"></i>
                    </div>
                    <p class="text-sm font-medium">No hay pedidos registrados</p>
                    <p class="text-xs">Los pedidos del cliente aparecerán aquí</p>
                </div>
            `;
        }

        bootbox.dialog({
            title: `
             <div class="bg-white">
                        <div class="flex items-center gap-4">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-blue-800 flex items-center justify-center text-white font-bold text-sm ">
                                ${iniciales}
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="text-xl font-bold text-gray-800">${nombreCompleto}</h3>
                                   
                                </div>
                                <div class="flex flex-wrap gap-3 text-sm text-gray-600">
                                    <span class="flex items-center gap-1">
                                        <i class="icon-phone "></i>
                                        ${cliente.telefono}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <i class="icon-mail "></i>
                                        ${cliente.correo || 'No registrado'}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <i class="icon-building "></i>
                                        ${cliente.udn_nombre}
                                    </span>

                                    <span class"flex items-center gap-1">
                                     ${badgeVIP}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
            `,
            message: `
                <div class="w-full bg-gray-50">
                    <!-- Header con Avatar -->
                   

                    <!-- Métricas Grid -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-6">
                        <div class="bg-blue-50 rounded p-3 border ">
                            <div class="flex items-center justify-center w-10 h-10 bg-blue-500 rounded-full mb-3 mx-auto">
                                <i class="icon-shopping-bag text-white text-sm"></i>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-700">${cliente.total_pedidos || 0}</div>
                                <div class="text-xs text-blue-600 font-medium mt-1">TOTAL PEDIDOS</div>
                            </div>
                        </div>

                        <div class="bg-green-50 rounded p-3 border border-green-200">
                            <div class="flex items-center justify-center w-10 h-10 bg-green-500 rounded-full mb-3 mx-auto">
                                <i class="icon-dollar text-white text-sm"></i>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-700">$${parseFloat(cliente.monto_total || 0).toFixed(2)}</div>
                                <div class="text-xs text-green-600 font-medium mt-1">MONTO TOTAL</div>
                            </div>
                        </div>

                        <div class="bg-cyan-50 rounded p-3 border border-cyan-200">
                            <div class="flex items-center justify-center w-10 h-10 bg-cyan-500 rounded-full mb-3 mx-auto">
                                <i class=" icon-user-3 text-white text-sm"></i>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-cyan-700">$${parseFloat(cliente.ticket_promedio || 0).toFixed(2)}</div>
                                <div class="text-xs text-cyan-600 font-medium mt-1">TICKET PROMEDIO</div>
                            </div>
                        </div>

                        <div class="bg-orange-50 ${cliente.dias_sin_comprar > 60 ? '' : ''} rounded-xl p-3 border">
                            <div class="flex items-center justify-center w-10 h-10 ${cliente.dias_sin_comprar > 60 ? 'bg-red-500' : 'bg-orange-500'} rounded-full mb-3 mx-auto">
                                <i class="icon-clock text-white text-sm"></i>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold ${cliente.dias_sin_comprar > 60 ? 'text-red-700' : 'text-orange-700'}">${cliente.dias_sin_comprar || 'N/A'}</div>
                                <div class="text-xs ${cliente.dias_sin_comprar > 60 ? 'text-red-600' : 'text-orange-600'} font-medium mt-1">DÍAS SIN COMPRAR</div>
                            </div>
                        </div>
                    </div>

                    <!-- Fechas -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 px-6 pb-6">
                        <div class="bg-white rounded p-4 border border-gray-200 ">
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded flex items-center justify-center">
                                    <i class="icon-calendar text-blue-600"></i>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-500 font-medium">🔵 Primera Compra</div>
                                    <div class="text-sm font-semibold text-gray-800">${cliente.primera_compra ? new Date(cliente.primera_compra).toLocaleDateString('es-MX', { year: 'numeric', month: 'long', day: 'numeric' }) : 'Sin registro de compras'}</div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded p-4 border border-gray-200 ">
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0 w-10 h-10 bg-purple-100 rounded flex items-center justify-center">
                                    <i class="icon-clock text-purple-600"></i>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-500 font-medium">🟣 Última Compra</div>
                                    <div class="text-sm font-semibold text-gray-800">${cliente.ultima_compra ? new Date(cliente.ultima_compra).toLocaleDateString('es-MX', { year: 'numeric', month: 'long', day: 'numeric' }) : 'Sin registro de compras'}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Historial -->
                    <div class="px-6 pb-6">
                        <div class="bg-white rounded border border-gray-200  overflow-hidden">
                            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                                <div class="flex items-center gap-2">
                                    <i class="icon-list text-gray-600"></i>
                                    <h6 class="font-semibold text-gray-800">Últimos 10 Pedidos</h6>
                                </div>
                            </div>
                            <div class="max-h-80 overflow-y-auto">
                                ${historialHTML}
                            </div>
                        </div>
                    </div>
                </div>
            `,
            size: 'large',
            closeButton: true,
            buttons: {
                close: {
                    label: '<i class="icon-x mr-1"></i> Cerrar',
                    className: 'btn-secondary'
                }
            }
        });
    }

    getInitials(name) {
        const nameParts = name.trim().split(' ');
        if (nameParts.length >= 2) {
            return (nameParts[0][0] + nameParts[1][0]).toUpperCase();
        }
        return name.substring(0, 2).toUpperCase();
    }

 


    async showTopClientes(){
        const udnId = $("#filterBarComportamiento #udn_id").val();

        const request = await useFetch({
            url: this._link,
            data: {
                opc: "getTopClientes",
                limit: 10,
                udn_id: udnId
            }
        });

         this.topClientesComponent({ data:  request.data });

    }

     createTitleModal(options = {}) {
        const defaults = {
            parent: "root",
            class: "space-y-2",
            icon: "icon-trophy",
            title: "Top 10 Clientes",
            subtitle: "Ranking por monto total de compras",
            color: "bg-blue-600",
        };

        const opts = Object.assign({}, defaults, options);


        const card = $(`
        <div class="flex items-center space-x-3 px-2  ${opts.class}">
            <div class="w-10 h-10 ${opts.color} rounded flex items-center justify-center">
                <i class="${opts.icon} text-white text-sm"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-800 mb-0">${opts.title}</h3>
                <p class="text-sm text-gray-600 mb-0">${opts.subtitle}</p>
            </div>
        </div>
    `);

        return card;
    }


     topClientesComponent(options = {}) {
        const defaults = {
            parent: "body",
            udn_id: $("#udn_id").val(),
            limit: 10,
            data: [],
        };

        const opts = Object.assign({}, defaults, options);
        const topClientes = opts.data;

        let topHTML = "";

        if (topClientes && topClientes.length > 0) {
            topClientes.forEach((cliente, index) => {
                const nombreCompleto = `${cliente.nombre} ${cliente.apellido_paterno || ""} ${cliente.apellido_materno || ""}`.trim();
                const badgeVIP = cliente.vip == 1
                    ? '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-400  text-white "><i class="icon-star mr-1 text-yellow-900"></i>VIP</span>'
                    : "";
                const medalIcon =
                    index === 0
                        ? "🥇"
                        : index === 1
                            ? "🥈"
                            : index === 2
                                ? "🥉"
                                : `<span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-600 font-semibold text-sm">${index + 1}</span>`;

                const bgColor =
                    index === 0
                        ? "bg-orange-50 border-orange-200"
                        : index === 1
                            ? "bg-gray-50  border-gray-200"
                            : index === 2
                                ? "bg-gray-50  border-gray-200"
                                : "bg-white border-gray-200";

                topHTML += `
                    <div class="relative mb-3 p-2 rounded border-1 ${bgColor} transition-all duration-300 transform hover:-translate-y-1">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    ${medalIcon}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-2 mb-1">
                                        <h3 class="text-lg font-bold text-gray-800 truncate">${nombreCompleto}</h3>
                                        ${badgeVIP}
                                    </div>
                                    <div class="flex flex-wrap items-center text-sm text-gray-600 space-x-4">
                                        <span class="inline-flex items-center">
                                            <i class="icon-building mr-1 text-blue-500"></i>
                                            ${cliente.udn_nombre}
                                        </span>
                                        <span class="inline-flex items-center">
                                            <i class="icon-shopping-cart mr-1 text-green-500"></i>
                                            ${cliente.total_pedidos} pedidos
                                        </span>
                                        <span class="inline-flex items-center">
                                            <i class="icon-calendar mr-1 text-purple-500"></i>
                                            ${cliente.ultima_compra ? new Date(cliente.ultima_compra).toLocaleDateString('es-MX') : 'N/A'}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right ml-4">
                                <div class="text-LG font-bold text-[#103B60] mb-1">
                                    $${parseFloat(cliente.monto_total).toLocaleString('es-MX', { minimumFractionDigits: 2 })}
                                </div>
                                <div class="text-sm text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
                                    Ticket: $${parseFloat(cliente.ticket_promedio).toLocaleString('es-MX', { minimumFractionDigits: 2 })}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
        } else {
            topHTML = `
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <div class="w-24 h-24 bg-gray-100 rounded flex items-center justify-center mb-4">
                        <i class="icon-users text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-600 mb-2">No hay datos disponibles</h3>
                    <p class="text-gray-500">Selecciona una unidad de negocio para ver el ranking</p>
                </div>
            `;
        }

        bootbox.dialog({
            title: this.createTitleModal({
                title: "Top 10 Clientes",
            }),
            message: `
                <div class="-mx-4 -mb-4 px-6 py-5">
                    <div class="max-h-96 overflow-y-auto pr-2 " style="scrollbar-width: thin; scrollbar-color: #CBD5E0 #F7FAFC;">
                        ${topHTML}
                    </div>
                    
                </div>
            `,
            size: 'large',
            className: 'top-clients-modal',
            closeButton: true
        });
    }
}
