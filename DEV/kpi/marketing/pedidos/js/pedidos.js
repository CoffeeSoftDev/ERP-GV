const api = 'ctrl/ctrl-pedidos.php';
const api_dashboard = 'ctrl/ctrl-dashboard-order.php';

let app, pedidos, dashboard, canal, dashboardOrder;
let canales, productos, campanas, lsudn, udn, redes_sociales, anuncios, clients;

// - tab report.
let api_report = 'ctrl/ctrl-report.php';

// - tab administrador.
let admin, channel, product, migration;
const api_productos = 'ctrl/ctrl-admin-productos.php';
const apiCanales = 'ctrl/ctrl-canal.php';
   let cookies = getCookies();
$(async () => {

    const data = await useFetch({ url: api, data: { opc: "init" } });
    // vars.
    udn = data.udn;
    lsudn = data.udn;
    canales = data.canales;
    productos = data.productos;
    campanas = data.campanas;
    redes_sociales = data.redes_sociales;
    anuncios = data.anuncios;
    clients = data.clients || [];

    // Instancias.
    app = new App(api, "root");
    pedidos = new Pedidos(api, 'root');
    report = new Report(api_report, "root");
    dashboardOrder = new DashboardOrder(api_dashboard, "root");

    // administrador.
    admin = new Admin(api, "root");
    channel = new AdminChannel(apiCanales, "root");
    product = new AdminProducts(api_productos, "root");
    migration = new Migration(api, "root");

    if (cookies.IDU == 75) {
        app.render();
        pedidos.render();
        admin.render();
    } else {
        app.render();
        pedidos.render();
        report.render();
        dashboardOrder.render();
        admin.render();
        migration.render();
    }


});

class App extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "Pedido";
    }

    render() {
        this.layout();
    }

    layout() {
        this.primaryLayout({
            parent: "root",
            id: this.PROJECT_NAME,
            card: {
                filterBar: { class: "w-full", id: "filterBarPedidos" },
                container: { class: "w-full flex-1 overflow-hidden", id: "containerPedidos" },
            },
        });

        this.headerBar({
            parent: `filterBarPedidos`,
            title: "Módulo de Pedidos 🛵",
            subtitle: "Administra tus pedidos de manera eficiente.",
            onClick: () => this.redirectToHome(),
        });

        let jsonTabs = {};
        if (getCookies().IDU == 75) {
            jsonTabs = [
                {
                    id: "pedidos",
                    tab: "Pedidos",
                    class: 'h-full',
                    active: true,
                    onClick: () => pedidos.render()
                },
                {
                    id: "admin",
                    tab: "Administrador",
                },
            ]
        } else {

            jsonTabs = [
                {
                    id: "dashboard",
                    tab: "Dashboard",
                    class: 'h-full',
                    onClick: () => dashboardOrder.renderDashboard()
                },
                {
                    id: "pedidos",
                    tab: "Pedidos",
                    class: 'h-full',
                    active: true,

                    // onClick: () => pedidos.render()
                },
                {
                    id: "history",
                    tab: "Historial Anual",
                    onClick: () => report.lsResumenPedidos()
                },
                {
                    id: "admin",
                    tab: "Administrador",
                },
            ]

        }
        this.tabLayout({
            parent: `containerPedidos`,
            id: `tabs${this.PROJECT_NAME}`,
            theme: "light",
            class: '',
            content: {
            },
            type: "short",
            json: jsonTabs
        });

        $('#content-tabsPedidos').addClass('h-full flex flex-col');
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

        let leftSection = '';

        if (cookies.IDU != 75) {
            // 🔵 Botón alineado a la izquierda (posición absoluta)
            leftSection = $("<div>", {
                class: "absolute left-0"
            }).append(
                $("<button>", {
                    class: `${opts.classBtn} font-semibold px-4 py-2 rounded transition flex items-center`,
                    html: `<i class="${opts.icon} mr-2"></i>${opts.textBtn}`,
                    click: () => typeof opts.onClick === "function" && opts.onClick()
                })
            );
        }

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

class Pedidos extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "Pedido";
        this.productosFiltrados = [];
    }

    render() {
        this.layout();
        this.filterBar();
        this.lsPedidos();
    }

    layout() {
        this.primaryLayout({
            parent: `container-pedidos`,
            id: this.PROJECT_NAME,
            card: {
                filterBar: { class: 'w-full pb-3', id: `filterBar${this.PROJECT_NAME}` },
                container: { class: 'w-full h-full', id: `container${this.PROJECT_NAME}` }
            }
        });
    }

    filterBar() {
        const currentDate = new Date();
        const currentMonth = String(currentDate.getMonth() + 1).padStart(2, '0');
        const currentYear = String(currentDate.getFullYear());

        this.createfilterBar({
            parent: `filterBar${this.PROJECT_NAME}`,
            data: [
                {
                    opc: "select",
                    id: "udn_id",
                    lbl: "Unidad de Negocio",
                    class: "col-sm-2",
                    data: udn,
                    onchange: `pedidos.lsPedidos()`,
                },
                {
                    opc: "select",
                    id: "anio",
                    class: "col-md-2",
                    lbl: "Año",
                    value: currentYear,
                    data: [
                        { id: "2025", valor: "2025" },
                        { id: "2024", valor: "2024" }
                    ],
                    onchange: "pedidos.lsPedidos()"
                },
                {
                    opc: "select",
                    id: "mes",
                    class: "col-md-2",
                    lbl: "Mes",
                    value: currentMonth,
                    data: [
                        { id: "01", valor: "Enero" },
                        { id: "02", valor: "Febrero" },
                        { id: "03", valor: "Marzo" },
                        { id: "04", valor: "Abril" },
                        { id: "05", valor: "Mayo" },
                        { id: "06", valor: "Junio" },
                        { id: "07", valor: "Julio" },
                        { id: "08", valor: "Agosto" },
                        { id: "09", valor: "Septiembre" },
                        { id: "10", valor: "Octubre" },
                        { id: "11", valor: "Noviembre" },
                        { id: "12", valor: "Diciembre" }
                    ],
                    onchange: "pedidos.lsPedidos()"
                },
                {
                    opc: "button",
                    class: "col-sm-3",
                    id: "btnNewOrder",
                    text: "<i class='icon-plus'></i> Nuevo Pedido",
                    onClick: () => pedidos.showModalAddPedido()
                },
            ],
        });
    }

    lsPedidos() {
        this.createTable({
            parent: "containerPedido",
            idFilterBar: "filterBarPedido",
            data: {
                opc: "lsPedido",
                udn: $("#filterBarPedido #udn_id").val(),
                anio: $("#filterBarPedido #anio").val(),
                mes: $("#filterBarPedido #mes").val()
            },
            conf: { datatable: true, pag: 15 },
            coffeesoft: true,
            attr: {
                id: "tbPedidos",
                theme: 'corporativo',
                center: [1, 6, 8, 9],
                right: [7]
            }
        });
    }

    showModalAddPedido() {
        const selectedUdn = $("#filterBarPedido #udn_id").val();
        let selectedClienteId = null;

        // Filtrar productos por unidad de negocio seleccionada
        pedidos.productosFiltrados = productos.filter(p => p.udn_id == selectedUdn);

        // Filtrar anuncios por unidad de negocio seleccionada
        const anunciosFiltrados = anuncios.filter(a => a.udn_id == selectedUdn);
        let anunciosOptions = '';
        anunciosFiltrados.forEach(anuncio => {
            const imagenUrl = anuncio.imagen ? `https://www.erp-varoch.com/ERP24/${anuncio.imagen}` : 'https://sublimac.com/wp-content/uploads/2017/11/default-placeholder.png';
            anunciosOptions += `
                <div class="flex items-center px-3 py-2 cursor-pointer hover:bg-gray-100"
                     data-value="${anuncio.id}"
                     data-icon="${imagenUrl}">
                    <img src="${imagenUrl}" class="w-8 h-8 mr-2 rounded object-cover">
                    ${anuncio.valor}
                </div>
            `;
        });

        const formHtml = `
            <form id="formPedidoAdd" class="row">
                <div class="col-12 fw-bold text-lg mb-3 border-b pb-2">Datos del pedido</div>

                <div class="col-12 col-lg-4 mb-3">
                    <label class="form-label">Fecha de pedido</label>
                    <input type="date" class="form-control" id="fecha_pedido" value="${new Date().toISOString().split('T')[0]}" required>
                </div>

                <div class="col-12 col-lg-4 mb-3">
                    <label class="form-label">Canal</label>
                    <select class="form-control" id="canal_id" required>
                        <option value="">Seleccione...</option>
                        ${canales.map(c => `<option value="${c.id}">${c.valor}</option>`).join('')}
                    </select>
                </div>

                <div class="col-12 col-lg-4 mb-3">
                    <label class="form-label">Monto</label>
                    <input type="text" class="form-control" id="monto" required>
                </div>

                <div class="col-12 col-lg-4 mb-3">
                    <label class="form-label">Unidad de Negocio</label>
                    <select class="form-control" id="udn_id" required onchange="updateProductosByUdnAdd(this.value)">
                        ${udn.map(u => `<option value="${u.id}" ${u.id == selectedUdn ? 'selected' : ''}>${u.valor}</option>`).join('')}
                    </select>
                </div>

                <div class="col-12 col-lg-4 mb-3">
                    <label class="form-label">Tipo de entrega</label>
                    <select class="form-control" id="envio_domicilio" required>
                        <option value="0">Recoger en establecimiento</option>
                        <option value="1">Envío a domicilio</option>
                        <option value="2">No aplica</option>
                    </select>
                </div>
                  <div class="col-12 col-lg-4 mb-3">
                    <label class="form-label">Producto o servicio</label>
                    <select class="form-control" id="producto_id" required multiple>
                        ${pedidos.productosFiltrados.map(r => `<option value="${r.id}">${r.valor}</option>`).join('')}
                    </select>
                </div>

                <div class="col-12 col-lg-4 mb-3">
                    <label class="form-label">Anuncio (opcional)</label>
                    <div class="relative">
                        <button type="button" id="dropdownAnuncioBtn" class="form-control text-left flex items-center justify-between">
                            <span id="selectedAnuncio" class="flex items-center">
                                <span class="text-gray-400">Seleccione un anuncio...</span>
                            </span>
                            <i class="icon-chevron-down text-gray-500"></i>
                        </button>
                        <input type="hidden" id="anuncio_id" value="">
                        <div id="dropdownAnuncioMenu" class="hidden absolute mt-1 w-full bg-white border rounded-lg shadow-lg z-50 max-h-60 overflow-y-auto">
                            <div class="flex items-center px-3 py-2 cursor-pointer hover:bg-gray-100"
                                 data-value=""
                                 data-icon="">
                                <span class="text-gray-400">Sin anuncio</span>
                            </div>
                            ${anunciosOptions}
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4 mb-3">
                    <label class="form-label">Red social</label>
                    <select class="form-control" id="red_social_id" required>
                        <option value="">Seleccione...</option>
                        ${redes_sociales.map(r => `<option value="${r.id}">${r.valor}</option>`).join('')}
                    </select>
                </div>

                <div class="col-12 fw-bold text-lg mb-3 border-b pb-2 mt-3">Información del cliente</div>

                <!-- <div class="col-12 col-lg-6 mb-3">
                    <label class="form-label">Buscar Cliente</label>
                    <select class="form-control" id="cliente_select" style="width: 100%">
                        <option value="">Buscar cliente existente...</option>
                    </select>
                    <input type="hidden" id="cliente_id" value="">
                </div> -->

                <div class="col-12 col-lg-4 mb-3">
                    <label class="form-label">Teléfono</label>
                    <input type="tel" class="form-control" id="cliente_telefono" required>
                </div>

                <div class="col-12 col-lg-4 mb-3">
                    <label class="form-label">Nombre del cliente</label>
                    <input type="text" class="form-control" id="cliente_nombre" required>
                </div>



                <div class="col-12 col-lg-4 mb-3">
                    <label class="form-label">Correo electrónico</label>
                    <input type="email" class="form-control" id="cliente_correo">
                </div>

                <div class="col-12 col-lg-4 mb-3">
                    <label class="form-label">Fecha de cumpleaños</label>
                    <input type="date" class="form-control" id="cliente_cumpleaños">
                </div>
            </form>
        `;

        bootbox.dialog({
            title: '📝 Nuevo Pedido',
            message: formHtml,
            size: 'large',
            buttons: {
                cancel: {
                    label: 'Cancelar',
                    className: 'btn-secondary'
                },
                ok: {
                    label: 'Guardar Pedido',
                    className: 'btn-primary',
                    callback: async () => {
                        const formData = {
                            opc: 'addPedido',
                            fecha_pedido: $('#fecha_pedido').val(),
                            canal_id: $('#canal_id').val(),
                            monto: $('#monto').val(),
                            udn_id: $('#udn_id').val(),
                            envio_domicilio: $('#envio_domicilio').val(),
                            producto_id: $('#producto_id').val(),          // Array de productos
                            anuncio_id: $('#anuncio_id').val() || null,
                            red_social_id: $('#red_social_id').val()
                        };

                        // Si hay un cliente seleccionado, solo enviar el ID
                        if (selectedClienteId) {
                            formData.cliente_id = selectedClienteId;
                        } else {
                            // Si es un cliente nuevo, enviar todos los datos
                            formData.cliente_nombre = $('#cliente_nombre').val();
                            formData.cliente_telefono = $('#cliente_telefono').val();
                            formData.cliente_correo = $('#cliente_correo').val() || null;
                            formData.cliente_cumpleaños = $('#cliente_cumpleaños').val() || null;
                        }

                        const response = await useFetch({
                            url: this._link,
                            data: formData
                        });

                        if (response.status === 200) {
                            alert({
                                icon: "success",
                                title: "Pedido creado",
                                text: response.message,
                                btn1: true,
                                btn1Text: "Aceptar"
                            });
                            pedidos.lsPedidos();
                        } else {
                            alert({
                                icon: "error",
                                text: response.message,
                                btn1: true,
                                btn1Text: "Ok"
                            });
                        }
                        return false;
                    }
                }
            }
        });

        // Inicializar Select2 y otros componentes
        setTimeout(() => {
            // Obtener el contenedor del modal de bootbox
            const modalContainer = $('.bootbox.modal');

            // Autocomplete para nombre de cliente
            $('#cliente_nombre').autocomplete({
                source: clients.map(client => ({
                    label: client.name,
                    value: client.name,
                    phone: client.phone,
                    email: client.email,
                    birthday: client.fecha_cumpleaños
                })),
                minLength: 2,
                appendTo: modalContainer,
                select: function (event, ui) {
                    $('#cliente_telefono').val(ui.item.phone || '');
                    $('#cliente_correo').val(ui.item.email || '');
                    $('#cliente_cumpleaños').val(ui.item.birthday ? ui.item.birthday.split(' ')[0] : '');
                    return true;
                }
            });

            // Autocomplete para teléfono de cliente
            $('#cliente_telefono').autocomplete({
                source: clients.map(client => ({
                    label: `${client.phone} - ${client.name}`,
                    value: client.phone,
                    name: client.name,
                    email: client.email,
                    birthday: client.fecha_cumpleaños
                })),
                minLength: 3,
                appendTo: modalContainer,
                select: function (event, ui) {
                    $('#cliente_nombre').val(ui.item.name || '');
                    $('#cliente_correo').val(ui.item.email || '');
                    $('#cliente_cumpleaños').val(ui.item.birthday ? ui.item.birthday.split(' ')[0] : '');
                    return true;
                }
            });

            // Limpiar campos cuando se borra el nombre
            // $('#cliente_nombre').on('input', function() {
            //     if ($(this).val().trim() === '') {
            //         $('#cliente_telefono').val('');
            //         $('#cliente_correo').val('');
            //         $('#cliente_cumpleaños').val('');
            //     }
            // });

            // Limpiar campos cuando se borra el teléfono
            $('#cliente_telefono').on('input', function () {
                if ($(this).val().trim() === '') {
                    $('#cliente_nombre').val('');
                    $('#cliente_correo').val('');
                    $('#cliente_cumpleaños').val('');
                }
            });




            // Inicializar Select2 para productos (múltiple)
            $('#producto_id').select2({
                placeholder: 'Seleccione productos...',
                allowClear: true,
                width: '100%',
                dropdownParent: modalContainer
            });

            // // Inicializar Select2 para clientes con búsqueda AJAX
            // $('#cliente_select').select2({
            //     placeholder: 'Buscar cliente por nombre o teléfono...',
            //     allowClear: true,
            //     width: '100%',
            //     dropdownParent: modalContainer,
            //     ajax: {
            //         url: api,
            //         type: 'POST',
            //         dataType: 'json',
            //         delay: 250,
            //         data: function (params) {
            //             return {
            //                 opc: 'apiSearchClientes',
            //                 search: params.term || ''

            //             };
            //         },
            //         processResults: function (data) {
            //             return {
            //                 results: data.results || []
            //             };
            //         },
            //         cache: true
            //     },
            //     minimumInputLength: 2
            // });

            // // Cuando se selecciona un cliente, rellenar los campos
            // $('#cliente_select').on('select2:select', async function (e) {
            //     const clienteId = e.params.data.id;
            //     selectedClienteId = clienteId;
            //     $('#cliente_id').val(clienteId);

            //     // Obtener datos completos del cliente
            //     const response = await useFetch({
            //         url: api,
            //         data: { opc: 'getCliente', id: clienteId }
            //     });

            //     if (response.status === 200 && response.data) {
            //         const cliente = response.data;
            //         $('#cliente_nombre').val(cliente.text || '').prop('readonly', true);
            //         $('#cliente_telefono').val(cliente.telefono || '').prop('readonly', true);
            //         $('#cliente_correo').val(cliente.correo || '').prop('readonly', true);
            //         $('#cliente_cumpleaños').val(cliente.fecha_cumpleaños ? cliente.fecha_cumpleaños.split(' ')[0] : '').prop('readonly', true);
            //     }
            // });

            // // Cuando se limpia la selección, habilitar campos para nuevo cliente
            // $('#cliente_select').on('select2:clear', function () {
            //     selectedClienteId = null;
            //     $('#cliente_id').val('');
            //     $('#cliente_nombre').val('').prop('readonly', false);
            //     $('#cliente_telefono').val('').prop('readonly', false);
            //     $('#cliente_correo').val('').prop('readonly', false);
            //     $('#cliente_cumpleaños').val('').prop('readonly', false);
            // });

            // Dropdown de anuncios
            $('#dropdownAnuncioBtn').on('click', function (e) {
                e.preventDefault();
                $('#dropdownAnuncioMenu').toggle();
            });

            $(document).on('click', '#dropdownAnuncioMenu div', function () {
                const value = $(this).data('value');
                const icon = $(this).data('icon');
                const text = $(this).text().trim();

                $('#anuncio_id').val(value);

                if (icon) {
                    $('#selectedAnuncio').html(`<img src="${icon}" class="w-6 h-6 mr-2 rounded object-cover"> ${text}`);
                } else {
                    $('#selectedAnuncio').html(`<span class="text-gray-400">${text}</span>`);
                }

                $('#dropdownAnuncioMenu').hide();
            });

            // Cerrar dropdown al hacer clic fuera
            $(document).on('click', function (e) {
                if (!$(e.target).closest('#dropdownAnuncioBtn, #dropdownAnuncioMenu').length) {
                    $('#dropdownAnuncioMenu').hide();
                }
            });

            // Validación de número en monto
            $('#monto').on('keyup', function () {
                validationInputForNumber('#monto');
            });

            window.updateProductosByUdnAdd = function (udnId) {
                // Actualizar productos
                pedidos.productosFiltrados = productos.filter(p => p.udn_id == udnId);
                const options = pedidos.productosFiltrados.map(r => `<option value="${r.id}">${r.valor}</option>`).join('');
                $('#producto_id').html(options).trigger('change');

                // Actualizar anuncios
                const anunciosFiltrados = anuncios.filter(a => a.udn_id == udnId);
                let anunciosOptions = '<div class="flex items-center px-3 py-2 cursor-pointer hover:bg-gray-100" data-value="" data-icon=""><span class="text-gray-400">Sin anuncio</span></div>';
                anunciosFiltrados.forEach(anuncio => {
                    const imagenUrl = anuncio.imagen ? `https://www.erp-varoch.com/ERP24/${anuncio.imagen}` : 'https://sublimac.com/wp-content/uploads/2017/11/default-placeholder.png';
                    anunciosOptions += `
                        <div class="flex items-center px-3 py-2 cursor-pointer hover:bg-gray-100"
                             data-value="${anuncio.id}"
                             data-icon="${imagenUrl}">
                            <img src="${imagenUrl}" class="w-8 h-8 mr-2 rounded object-cover">
                            ${anuncio.valor}
                        </div>
                    `;
                });
                $('#dropdownAnuncioMenu').html(anunciosOptions);
                // Reiniciar selección de anuncio
                $('#anuncio_id').val('');
                $('#selectedAnuncio').html('<span class="text-gray-400">Seleccione un anuncio...</span>');
            };

        }, 100);
    }

    async editPedido(id) {
        const request = await useFetch({
            url: api,
            data: { opc: "getPedido", id }
        });

        if (request.status !== 200) {
            alert({
                icon: "error",
                text: request.message,
                btn1: true
            });
            return;
        }

        const pedido = request.data;

        // Filtrar productos por unidad de negocio del pedido
        const productosFiltrados = productos.filter(p => p.udn_id == pedido.udn_id);

        // Filtrar anuncios por unidad de negocio del pedido
        const anunciosFiltrados = anuncios.filter(a => a.udn_id == pedido.udn_id);
        let anunciosOptions = '';
        anunciosFiltrados.forEach(anuncio => {
            const imagenUrl = anuncio.imagen ? `https://www.erp-varoch.com/ERP24/${anuncio.imagen}` : 'https://sublimac.com/wp-content/uploads/2017/11/default-placeholder.png';
            anunciosOptions += `
                <div class="flex items-center px-3 py-2 cursor-pointer hover:bg-gray-100"
                     data-value="${anuncio.id}"
                     data-icon="${imagenUrl}">
                    <img src="${imagenUrl}" class="w-8 h-8 mr-2 rounded object-cover">
                    ${anuncio.valor}
                </div>
            `;
        });

        const formHtml = `
            <form id="formPedidoEdit" class="row">
                <div class="col-12 fw-bold text-lg mb-3 border-b pb-2">Datos del pedido</div>

                <div class="col-12 col-lg-4 mb-3">
                    <label class="form-label">Fecha de pedido</label>
                    <input type="date" class="form-control" id="fecha_pedido" value="${pedido.fecha_pedido ? pedido.fecha_pedido.split(' ')[0] : ''}" required>
                </div>

                <div class="col-12 col-lg-4 mb-3">
                    <label class="form-label">Canal</label>
                    <select class="form-control" id="canal_id" required>
                        ${canales.map(c => `<option value="${c.id}" ${c.id == pedido.canal_id ? 'selected' : ''}>${c.valor}</option>`).join('')}
                    </select>
                </div>

                <div class="col-12 col-lg-4 mb-3">
                    <label class="form-label">Monto</label>
                    <input type="text" class="form-control" id="monto" value="${pedido.monto}" required>
                </div>

                <div class="col-12 col-lg-4 mb-3">
                    <label class="form-label">Unidad de Negocio</label>
                    <select class="form-control" id="udn_id_edit" required onchange="updateProductosByUdnEdit()">
                        ${udn.map(u => `<option value="${u.id}" ${u.id == pedido.udn_id ? 'selected' : ''}>${u.valor}</option>`).join('')}
                    </select>
                </div>

                <div class="col-12 col-lg-4 mb-3">
                    <label class="form-label">Tipo de entrega</label>
                    <select class="form-control" id="envio_domicilio" required>
                        <option value="0" ${pedido.envio_domicilio == 0 ? 'selected' : ''}>Recoger en establecimiento</option>
                        <option value="1" ${pedido.envio_domicilio == 1 ? 'selected' : ''}>Envío a domicilio</option>
                        <option value="2" ${pedido.envio_domicilio == 2 ? 'selected' : ''}>No aplica</option>
                    </select>
                </div>

                <div class="col-12 col-lg-4 mb-3">
                    <label class="form-label">Producto o servicio</label>
                    <select class="form-control" id="producto_id" required multiple>
                        ${productosFiltrados.map(p => `<option value="${p.id}" ${pedido.productos && pedido.productos.includes(p.id) ? 'selected' : ''}>${p.valor}</option>`).join('')}
                    </select>
                </div>

                <div class="col-12 col-lg-4 mb-3">
                    <label class="form-label">Anuncio (opcional)</label>
                    <div class="relative">
                        <button type="button" id="dropdownAnuncioBtn" class="form-control text-left flex items-center justify-between">
                            <span id="selectedAnuncio" class="flex items-center">
                                <span class="text-gray-400">Seleccione un anuncio...</span>
                            </span>
                            <i class="icon-chevron-down text-gray-500"></i>
                        </button>
                        <input type="hidden" id="anuncio_id">
                        <div id="dropdownAnuncioMenu" class="hidden absolute mt-1 w-full bg-white border rounded-lg shadow-lg z-50 max-h-60 overflow-y-auto">
                            <div class="flex items-center px-3 py-2 cursor-pointer hover:bg-gray-100" data-value="" data-icon="">
                                <span class="text-gray-400">Sin anuncio</span>
                            </div>
                            ${anunciosOptions}
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4 mb-3">
                    <label class="form-label">Red social</label>
                    <select class="form-control" id="red_social_id" required>
                        ${redes_sociales.map(r => `<option value="${r.id}" ${r.id == pedido.red_social_id ? 'selected' : ''}>${r.valor}</option>`).join('')}
                    </select>
                </div>

                <div class="col-12 fw-bold text-lg mb-3 border-b pb-2 mt-3">Información del cliente</div>

                <div class="col-12 col-lg-6 mb-3">
                    <label class="form-label">Buscar Cliente</label>
                    <select class="form-control" id="cliente_select_edit" style="width: 100%">
                        <option value="">Buscar otro cliente...</option>
                    </select>
                    <input type="hidden" id="cliente_id_edit" value="${pedido.cliente_id}">
                </div>

                <div class="col-12 col-lg-6 mb-3">
                    <label class="form-label">Cliente Actual</label>
                    <input type="text" class="form-control" value="${pedido.cliente_nombre || ''}" readonly>
                    <small class="text-muted">Tel: ${pedido.cliente_telefono || 'N/A'}</small>
                </div>
            </form>
        `;

        bootbox.dialog({
            title: '✏️ Editar Pedido',
            message: formHtml,
            size: 'large',
            buttons: {
                cancel: {
                    label: 'Cancelar',
                    className: 'btn-secondary'
                },
                ok: {
                    label: 'Guardar Cambios',
                    className: 'btn-primary',
                    callback: async () => {
                        const formData = {
                            opc: 'editPedido',
                            id: id,
                            fecha_pedido: $('#fecha_pedido').val(),
                            canal_id: $('#canal_id').val(),
                            monto: $('#monto').val(),
                            udn_id: $('#udn_id_edit').val(),
                            envio_domicilio: $('#envio_domicilio').val(),
                            producto_id: $('#producto_id').val(),
                            anuncio_id: $('#anuncio_id').val(),
                            red_social_id: $('#red_social_id').val(),
                            cliente_id: $('#cliente_id_edit').val()
                        };

                        const response = await useFetch({
                            url: api,
                            data: formData
                        });

                        if (response.status === 200) {
                            alert({
                                icon: "success",
                                text: response.message,
                                btn1: true
                            });
                            pedidos.lsPedidos();
                        } else {
                            alert({
                                icon: response.status === 403 ? "warning" : "error",
                                title: response.status === 403 ? "Acceso denegado" : "Error",
                                text: response.message,
                                btn1: true
                            });
                        }
                        return false;
                    }
                }
            }
        });

        // Inicializar Select2 y dropdown
        setTimeout(() => {
            const modalContainer = $('.bootbox.modal');

            $('#producto_id').select2({
                placeholder: 'Seleccione productos...',
                allowClear: true,
                width: '100%',
                dropdownParent: modalContainer
            });

            // Inicializar Select2 para cambiar cliente
            $('#cliente_select_edit').select2({
                placeholder: 'Buscar otro cliente...',
                allowClear: true,
                width: '100%',
                dropdownParent: modalContainer,
                ajax: {
                    url: api,
                    type: 'POST',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            opc: 'apiSearchClientes',
                            search: params.term || ''
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.results || []
                        };
                    },
                    cache: true
                },
                minimumInputLength: 2
            });

            // Cuando se selecciona otro cliente
            $('#cliente_select_edit').on('select2:select', function (e) {
                const clienteId = e.params.data.id;
                $('#cliente_id_edit').val(clienteId);
            });

            $('#dropdownAnuncioBtn').on('click', function (e) {
                e.preventDefault();
                $('#dropdownAnuncioMenu').toggle();
            });

            $('#dropdownAnuncioMenu div').on('click', function () {
                const value = $(this).data('value');
                const icon = $(this).data('icon');
                const text = $(this).text().trim();

                $('#anuncio_id').val(value);

                if (icon) {
                    $('#selectedAnuncio').html(`<img src="${icon}" class="w-6 h-6 mr-2 rounded object-cover"> ${text}`);
                } else {
                    $('#selectedAnuncio').html(`<span class="text-gray-400">${text}</span>`);
                }

                $('#dropdownAnuncioMenu').hide();
            });

            $('#monto').on('keyup', function () {
                validationInputForNumber('#monto');
            });

            // Establecer el anuncio seleccionado si existe
            if (pedido.anuncio_id) {
                $('#anuncio_id').val(pedido.anuncio_id);
                const anuncioSeleccionado = anuncios.find(a => a.id == pedido.anuncio_id);
                if (anuncioSeleccionado) {
                    const imagenUrl = anuncioSeleccionado.imagen ? `https://www.erp-varoch.com/ERP24/${anuncioSeleccionado.imagen}` : 'https://sublimac.com/wp-content/uploads/2017/11/default-placeholder.png';
                    $('#selectedAnuncio').html(`<img src="${imagenUrl}" class="w-6 h-6 mr-2 rounded object-cover"> ${anuncioSeleccionado.valor}`);
                }
            }

            window.updateProductosByUdnEdit = function () {
                const udnId = $('#udn_id_edit').val();
                // Actualizar productos
                const productosFiltrados = productos.filter(p => p.udn_id == udnId);
                const selected = $('#producto_id').val() || [];
                const options = productosFiltrados.map(p => `<option value="${p.id}" ${selected.includes(p.id.toString()) ? 'selected' : ''}>${p.valor}</option>`).join('');
                $('#producto_id').html(options).trigger('change');

                // Actualizar anuncios
                const anunciosFiltrados = anuncios.filter(a => a.udn_id == udnId);
                let anunciosOptions = '<div class="flex items-center px-3 py-2 cursor-pointer hover:bg-gray-100" data-value="" data-icon=""><span class="text-gray-400">Sin anuncio</span></div>';
                anunciosFiltrados.forEach(anuncio => {
                    const imagenUrl = anuncio.imagen ? `https://www.erp-varoch.com/ERP24/${anuncio.imagen}` : 'https://sublimac.com/wp-content/uploads/2017/11/default-placeholder.png';
                    anunciosOptions += `
                        <div class="flex items-center px-3 py-2 cursor-pointer hover:bg-gray-100"
                             data-value="${anuncio.id}"
                             data-icon="${imagenUrl}">
                            <img src="${imagenUrl}" class="w-8 h-8 mr-2 rounded object-cover">
                            ${anuncio.valor}
                        </div>
                    `;
                });
                $('#dropdownAnuncioMenu').html(anunciosOptions);
                // Reiniciar selección de anuncio
                $('#anuncio_id').val('');
                $('#selectedAnuncio').html('<span class="text-gray-400">Seleccione un anuncio...</span>');
            };

        }, 100);
    }

    verifyTransfer(id) {
        bootbox.confirm({
            title: "💰 Verificar Pago",
            message: "¿Confirmas que el pago de este pedido ha sido verificado?",
            buttons: {
                confirm: {
                    label: 'Sí, verificar',
                    className: 'btn-success'
                },
                cancel: {
                    label: 'Cancelar',
                    className: 'btn-secondary'
                }
            },
            callback: async (result) => {
                if (result) {
                    const response = await useFetch({
                        url: api,
                        data: { opc: "verifyTransfer", id }
                    });

                    if (response.status === 200) {
                        alert({
                            icon: "success",
                            text: response.message,
                            btn1: true
                        });
                        pedidos.lsPedidos();
                    } else {
                        alert({
                            icon: "error",
                            text: response.message,
                            btn1: true
                        });
                    }
                }
            }
        });
    }

    registerArrival(id) {
        bootbox.dialog({
            title: "📍 Registrar Llegada",
            message: `
                <div class="text-center">
                    <p class="mb-4">¿El cliente llegó al establecimiento?</p>
                    <button class="btn btn-success me-2" onclick="pedidos.confirmArrival(${id}, 1)">
                        <i class="icon-check"></i> Sí, llegó
                    </button>
                    <button class="btn btn-danger" onclick="pedidos.confirmArrival(${id}, 0)">
                        <i class="icon-times"></i> No llegó
                    </button>
                </div>
            `,
            buttons: {
                cancel: {
                    label: "Cancelar",
                    className: "btn-secondary"
                }
            }
        });
    }

    async confirmArrival(id, arrived) {
        bootbox.hideAll();

        const response = await useFetch({
            url: api,
            data: { opc: "registerArrival", id, arrived }
        });

        if (response.status === 200) {
            alert({
                icon: "success",
                text: response.message,
                btn1: true
            });
            pedidos.lsPedidos();
        } else {
            alert({
                icon: "error",
                text: response.message,
                btn1: true
            });
        }
    }

    cancelPedido(id) {
        bootbox.confirm({
            title: "❌ Cancelar Pedido",
            message: "¿Estás seguro de que deseas cancelar este pedido? Esta acción no se puede deshacer.",
            buttons: {
                confirm: {
                    label: 'Sí, cancelar',
                    className: 'btn-danger'
                },
                cancel: {
                    label: 'No, mantener',
                    className: 'btn-secondary'
                }
            },
            callback: async (result) => {
                if (result) {
                    const response = await useFetch({
                        url: api,
                        data: { opc: "cancelPedido", id }
                    });

                    if (response.status === 200) {
                        alert({
                            icon: "success",
                            title: "Cancelado",
                            text: response.message,
                            btn1: true
                        });
                        pedidos.lsPedidos();
                    } else {
                        alert({
                            icon: "error",
                            text: response.message,
                            btn1: true
                        });
                    }
                }
            }
        });
    }

}

function validationInputForNumber(selector) {
    const value = $(selector).val();
    const regex = /^\d*\.?\d*$/; // Permite solo números y un punto decimal
    if (!regex.test(value)) {
        $(selector).val(value.slice(0, -1)); // Elimina el último carácter inválido
    }

    // Calcular CPC automáticamente si ambos campos están llenos
    const totalMonto = parseFloat($('#total_monto').val()) || 0;
    const totalClics = parseInt($('#total_clics').val()) || 0;
    if (totalMonto > 0 && totalClics > 0) {
        const cpc = totalMonto / totalClics;
        $('#cpc').val(cpc.toFixed(2));
    } else {
        $('#cpc').val('');
    }
}
