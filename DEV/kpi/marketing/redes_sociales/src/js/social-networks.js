let api = 'ctrl/ctrl-social-networks.php';
let app, dashboardSocialNetwork, registerSocialNetWork, report, admin, adminMetrics, adminSocialNetWork;

let udn, lsudn, socialNetworks, metrics;

$(async () => {
    const data = await useFetch({ url: api, data: { opc: "init" } });
    udn            = data.udn;
    lsudn          = data.lsudn;
    socialNetworks = data.socialNetworks;
    metrics        = data.metrics;

    app                    = new App(api, "root");
    dashboardSocialNetwork = new DashboardSocialNetwork(api, "root");
    registerSocialNetWork  = new RegisterSocialNetWork(api, "root");
    report                 = new ReportSocialNetwork(api, "root");
    admin                  = new AppAdmin(api, "root");
    adminMetrics           = new AdminMetrics(api, "root");
    adminSocialNetWork     = new AdminSocialNetWork(api, "root");

    app.render();
});

class App extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "SocialNetworks";
    }

    render() {
        this.layout();
        registerSocialNetWork.render()
        report.render();
        dashboardSocialNetwork.render();
        admin.render();
    }

    layout() {
        this.primaryLayout({
            parent: "root",
            id: this.PROJECT_NAME,

            class: "w-full p-2",
            card: {
                filterBar: { class: "w-full", id: "filterBar" + this.PROJECT_NAME },
                container: { class: "w-full h-full", id: "container" + this.PROJECT_NAME },
            },
        });

        this.headerBar({
            parent: `filterBar${this.PROJECT_NAME}`,
            title: "📱 Módulo de Redes Sociales",
            subtitle: "Consulta las métricas de redes sociales por plataforma.",
            onClick: () => app.redirectToHome(),
        });

        this.tabLayout({
            parent: `container${this.PROJECT_NAME}`,
            id: `tabs${this.PROJECT_NAME}`,
            theme: "light",
            class: '',
            type: "short",
            json: [
                {
                    id: "dashboard",
                    tab: "Dashboard",
                    class: "mb-1",
                    onClick: () => dashboardSocialNetwork.renderDashboard()
                },
                {
                    id: "capture",
                    tab: "Captura de información",
                    active: true,
                    onClick: () => registerSocialNetWork.render()
                },
                {
                    id: "report",
                    tab: "Reportes",
                    onClick: () => report.lsAnualReport()
                },
                {
                    id: "admin",
                    tab: "Administrador",
                    onClick: () => admin.render()
                },
            ]
        });

        $('#content-tabs' + this.PROJECT_NAME).removeClass('h-screen');
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

    redirectToHome() {
        const base = window.location.origin + '/ERP24';
        window.location.href = `${base}/kpi/marketing.php`;
    }
}

class DashboardSocialNetwork extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "Dashboard";
    }

    render() {
        this.layout();
    }

    async layout() {
        this.dashboardComponent({
            parent: "container-dashboard",
            id: "dashboardComponent",
            title: "📊 Dashboard de Redes Sociales",
            subtitle: "Análisis mensual de métricas por plataforma",
            json: [
                { type: "grafico", id: "containerMonthlyComparative", title: 'Comparitva mensual' },
                { type: "grafico", id: "containerTrendChart", title: "Tendencia de Interacciones" },
                { type: "tabla", id: "containerComparativeTable", title: "Resumen General de Métricas" },
            ]
        });

        this.filterBarDashboard();

        let udn = $('#filterBarDashboard #udn').val();
        let month = $('#filterBarDashboard #mes').val();
        let year = $('#filterBarDashboard #anio').val();

        let data = await useFetch({
            url: api,
            data: {
                opc: "apiDashboardMetrics",
                udn: udn,
                mes: month,
                anio: year,
            },
        });

        this.showCards(data.dashboard);
        this.monthlyComparative({ data: data.monthlyComparative });
        this.trendChart({ data: data.trendData });
        this.comparativeTable({ data: data.comparativeTable });
    }

    renderDashboard() {
        this.layout();
    }

    filterBarDashboard() {
        this.createfilterBar({
            parent: `filterBarDashboard`,
            data: [
                {
                    opc: "select",
                    id: "udn",
                    lbl: "UDN",
                    class: "col-sm-3",
                    data: lsudn,
                    onchange: `dashboardSocialNetwork.renderDashboard()`,
                },
                {
                    opc: "select",
                    id: "mes",
                    lbl: "Mes",
                    class: "col-sm-3",
                    data: moment.months().map((m, i) => ({ id: i + 1, valor: m })),
                    onchange: `dashboardSocialNetwork.renderDashboard()`,
                },
                {
                    opc: "select",
                    id: "anio",
                    lbl: "Año",
                    class: "col-sm-3",
                    data: Array.from({ length: 5 }, (_, i) => {
                        const year = moment().year() - i;
                        return { id: year, valor: year.toString() };
                    }),
                    onchange: `dashboardSocialNetwork.renderDashboard()`,
                },
            ],
        });
        setTimeout(() => {
            // $(`#filterBarDashboard #mes`).val(currentMonth).trigger("change");
        }, 100);
    }

    showCards(data) {
        this.infoCard({
            parent: "cardDashboard",
            theme: "light",
            json: [
                {
                    id: "kpiReach",
                    title: "Total de Alcance",
                    data: {
                        value: data.totalReach,
                        color: "text-[#8CC63F]",
                    },
                },
                {
                    id: "kpiInteractions",
                    title: "Interacciones",
                    data: {
                        value: data.interactions,
                        color: "text-green-800",
                    },
                },
                {
                    title: "Visualizaciones del Mes",
                    data: {
                        value: data.monthViews,
                        color: "text-[#103B60]",
                    },
                },
                {
                    id: "kpiInvestment",
                    title: "Inversión Total",
                    data: {
                        value: data.totalInvestment,
                        color: "text-red-600",
                    },
                },
            ],
        });
    }

    monthlyComparative(options) {
        const defaults = {
            parent: "containerMonthlyComparative",
            id: "chartMonthlyComparative",
            title: "Comparativa Mensual por Red Social",
            class: "p-4",
            data: {},
        };
        const opts = Object.assign({}, defaults, options);

        const container = $("<div>", { class: opts.class });
        const title = $("<h2>", {
            class: "text-lg font-bold mb-3",
            text: opts.title
        });
        const canvas = $("<canvas>", {
            id: opts.id,
            class: "w-full h-[320px]"
        });

        container.append(title, canvas);
        $('#' + opts.parent).html(container);

        const ctx = document.getElementById(opts.id).getContext("2d");
        if (window._monthlyChart) window._monthlyChart.destroy();

        window._monthlyChart = new Chart(ctx, {
            type: "bar",
            data: opts.data,
            options: {
                responsive: true,
                plugins: {
                    legend: { position: "bottom" },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => `${ctx.dataset.label}: ${formatPrice(ctx.parsed.y)}`
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (v) => formatPrice(v)
                        }
                    }
                }
            }
        });
    }

    trendChart(options) {
        const defaults = {
            parent: "containerTrendChart",
            id: "chartTrend",
            title: "Tendencia de Interacciones",
            class: "p-4",
            data: {},
        };
        const opts = Object.assign({}, defaults, options);

        const container = $("<div>", { class: opts.class });
        const title = $("<h2>", {
            class: "text-lg font-bold mb-3",
            text: opts.title
        });
        const canvas = $("<canvas>", {
            id: opts.id,
            class: "w-full h-[320px]"
        });

        container.append(title, canvas);
        $('#' + opts.parent).html(container);

        const ctx = document.getElementById(opts.id).getContext("2d");
        if (window._trendChart) window._trendChart.destroy();

        window._trendChart = new Chart(ctx, {
            type: "line",
            data: opts.data,
            options: {
                responsive: true,
                plugins: {
                    legend: { position: "bottom" },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => `${ctx.dataset.label}: ${formatPrice(ctx.parsed.y)}`
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (v) => formatPrice(v)
                        }
                    }
                }
            }
        });
    }

    comparativeTable(options) {
        const defaults = {
            parent: "containerComparativeTable",
            data: []
        };
        const opts = Object.assign({}, defaults, options);

        const rows = opts.data.map(item => ({
            Plataforma: item.platform,
            Alcance: formatPrice(item.reach),
            Interacciones: formatPrice(item.interactions),
            Seguidores: formatPrice(item.followers),
            Inversión: formatPrice(item.investment),
            ROI: item.roi
        }));

        this.createCoffeTable({
            parent: opts.parent,
            id: "tableComparative",
            title: "📊 Resumen General de Métricas",
            theme: "light",
            data: {
                thead: ["Plataforma", "Alcance", "Interacciones", "Seguidores", "Inversión", "ROI"],
                row: rows
            },
            center: [1, 2, 3, 4, 5],
            right: [5]
        });
    }

    dashboardComponent(options) {
        const defaults = {
            parent: "root",
            id: "dashboardComponent",
            title: "📊 Dashboard",
            subtitle: "Resumen de métricas",
            json: []
        };

        const opts = Object.assign(defaults, options);

        const container = $(`
        <div id="${opts.id}" class="w-full">
            <div class="p-6 border-b border-gray-200">
                <div class="mx-auto">
                    <h1 class="text-2xl font-bold text-[#103B60]">${opts.title}</h1>
                    <p class="text-sm text-gray-600">${opts.subtitle}</p>
                </div>
            </div>

            <div id="filterBarDashboard" class="mx-auto px-4 py-4"></div>

            <section id="cardDashboard" class="mx-auto px-4 py-4"></section>

            <section id="content-${opts.id}" class="mx-auto px-4 py-6 grid gap-6 lg:grid-cols-2"></section>
        </div>`);

        opts.json.forEach(item => {
            let block = $("<div>", {
                id: item.id,
                class: "bg-white p-4 rounded-xl shadow-md border border-gray-200 min-h-[200px]"
            });

            if (item.title) {
                block.prepend(`<h3 class="text-sm font-semibold text-gray-800 mb-3">${item.title}</h3>`);
            }

            $(`#content-${opts.id}`, container).append(block);
        });

        $(`#${opts.parent}`).html(container);
    }

    infoCard(options) {
        const defaults = {
            parent: "root",
            id: "infoCardKPI",
            class: "",
            theme: "light",
            json: [],
        };
        const opts = Object.assign({}, defaults, options);
        const isDark = opts.theme === "dark";
        const cardBase = isDark
            ? "bg-[#1F2A37] text-white rounded-xl shadow"
            : "bg-white text-gray-800 rounded-xl shadow";
        const titleColor = isDark ? "text-gray-300" : "text-gray-600";

        const renderCard = (card, i = "") => {
            const box = $("<div>", {
                id: `${opts.id}_${i}`,
                class: `${cardBase} p-4`
            });
            const title = $("<p>", {
                class: `text-sm ${titleColor}`,
                text: card.title
            });
            const value = $("<p>", {
                id: card.id || "",
                class: `text-2xl font-bold ${card.data?.color || "text-white"}`,
                text: card.data?.value
            });
            box.append(title, value);
            return box;
        };

        const container = $("<div>", {
            id: opts.id,
            class: `grid grid-cols-2 md:grid-cols-4 gap-4 ${opts.class}`
        });

        opts.json.forEach((item, i) => {
            container.append(renderCard(item, i));
        });

        $(`#${opts.parent}`).html(container);
    }
}

class RegisterSocialNetWork extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "Capture";
        this.historyData = [];
        this.filteredData = [];
        this.selectedMonth = null;
    }

    render() {
        this.layout();
        this.filterBar();
        this.layoutCaptureForm()
    }

    layout() {
        this.primaryLayout({
            parent: `container-capture`,
            id: this.PROJECT_NAME,
            card: {
                filterBar: { class: 'w-full pb-3 ', id: `filterBar${this.PROJECT_NAME}` },
                container: { class: 'w-full h-full', id: `container${this.PROJECT_NAME}` }
            }
        });
    }

    filterBar() {
        this.createfilterBar({
            parent: `filterBar${this.PROJECT_NAME}`,
            data: [
                {
                    opc: "select",
                    id: "udn",
                    lbl: "Unidad de Negocio",
                    class: "col-sm-2",
                    data: lsudn,
                    onchange: `registerSocialNetWork.reloadCaptureAndHistory()`,
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
                    onchange: `registerSocialNetWork.reloadCaptureAndHistory()`,
                },
            ],
        });
    }


    // Capture Layout.

    layoutCaptureForm() {
        const udnName = $('#filterBarCapture #udn option:selected').text();
        const year = $('#filterBarCapture #anio').val();

        $(`#container${this.PROJECT_NAME}`).html(`
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 ">
                <!-- Formulario de Captura -->
                <div class="bg-white rounded-lg border p-6">
                    <div class="mb-4">
                        <div class="flex items-center gap-2 mb-2">
                            <i class="icon-edit text-blue-600 text-xl"></i>
                            <h3 class="text-lg font-semibold text-gray-800">Capturar Métricas de ${udnName}  (${year}) </h3>
                        </div>
                    </div>

                    <div id="capture-filters" class=" grid grid-cols-2 gap-3 border rounded p-3 mb-4"></div>
                    <div id="metrics-inputs" class="grid grid-cols-2 gap-3 border rounded p-3 ">
                        <div class="col-span-2 flex flex-col items-center justify-center py-8 text-gray-500">
                            <i class="icon-info text-3xl mb-2"></i>
                            <p class="text-sm text-center">Selecciona una red social para cargar sus métricas disponibles</p>
                        </div>
                    </div>
                    
                    <button id="btnSaveCapture" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-2 rounded-lg mt-4 flex items-center justify-center gap-2">
                       
                        Guardar Métricas
                    </button>
                </div>

                <!-- Historial de Métricas -->
                <div class="bg-white rounded-lg border p-6">
                    <div class="mb-2">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <i class="icon-clock text-green-600 text-xl"></i>
                                <h3 id="history-title" class="text-lg font-semibold text-gray-800">Historial de Métricas de ${udnName}  (${year})</h3>
                            </div>
                            <div id="metrics-counter" class="text-sm text-gray-500 font-medium">-- métricas</div>
                        </div>
                    </div>
                    
                    <!-- Filtro de fecha -->
                    <div id="history-filter" class="mb-2"></div>
                    
                    <!-- Área desplazable para el historial -->
                    <div id="history-container" class="space-y-3" style="min-height: 400px; max-height: 600px; overflow-y: auto;"></div>
                </div>
            </div>
        `);

        this.createCaptureFilters();
        this.renderHistoryMetrics();
    }

    createCaptureFilters() {
        const container = $('#capture-filters');
        container.html(`
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-2">Red Social</label>
                <select id="captureNetwork" class="form-select w-full" onchange="registerSocialNetWork.loadMetrics()">
                    <option value="">Seleccionar...</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de la Métrica</label>
                <input type="date" id="captureDate" class="form-control w-full" value="${moment().format('YYYY-MM-DD')}">
            </div>
        `);

        // Llenar select de redes sociales
        socialNetworks.forEach(network => {
            $('#captureNetwork').append(`<option value="${network.id}">${network.valor}</option>`);
        });
    }

    createHistoryFilter() {
        const container = $('#history-filter');
        const months = [
            'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ];

        container.html(`
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-2">Filtrar por mes</label>
                <select id="monthFilter" class="form-select w-full" onchange="registerSocialNetWork.filterHistoryByMonth()">
                    <option value="">Todas</option>
                    ${months.map((month, index) =>
            `<option value="${index + 1}">${month}</option>`
        ).join('')}
                </select>
            </div>
        `);
    }

    filterHistoryByMonth() {
        const selectedMonth = $('#monthFilter').val();
        this.selectedMonth = selectedMonth;

        if (!selectedMonth) {
            this.filteredData = [...this.historyData];
        } else {
            this.filteredData = this.historyData.filter(item => {
                const itemDate = new Date(item.date);
                const itemMonth = itemDate.getMonth() + 1;
                return itemMonth == selectedMonth;
            });
        }

        this.renderFilteredHistory();
        $('#metrics-counter').text(`${this.filteredData.length} métricas`);
    }


    // show Capture.

    async saveCapture() {
        const networkId = $('#captureNetwork').val();
        const captureDate = $('#captureDate').val();
        const udn = $('#filterBarCapture #udn').val();

        if (!networkId || !captureDate) {
            alert({ icon: "warning", text: "Por favor selecciona una red social y fecha" });
            return;
        }

        const date = moment(captureDate);
        const month = date.month() + 1;
        const year = date.year();



        const metrics = [];
        $('.metric-input').each(function () {
            const value = $(this).val();
            if (value) {
                metrics.push({
                    metric_id: $(this).data('metric-id'),
                    value: parseFloat(value)
                });
            }
        });

        if (metrics.length === 0) {
            alert({ icon: "warning", text: "Por favor ingrese al menos una métrica" });
            return;
        }

        let dateCapture = $('#capture-filters #captureDate').val();
        const response = await useFetch({
            url: api,
            data: {
                opc: "addCapture",
                social_network_id: networkId,
                month: month,
                year: year,
                udn: udn,
                fecha_creacion: dateCapture,
                metrics: JSON.stringify(metrics)
            }
        });

        if (response.status === 200) {
            alert({ icon: "success", text: response.message });
            $('.metric-input').val('');
            this.renderHistoryMetrics();
        } else {
            alert({ icon: "error", text: response.message });
        }
    }

    async loadMetrics() {
        const networkId = $('#capture-filters #captureNetwork').val();

        if (!networkId) return;

        const data = await useFetch({
            url: api,
            data: {
                opc: "getMetricsByNetwork",
                social_network_id: networkId
            }
        });

        this.renderMetricsInputs(data.metrics);
    }

    renderMetricsInputs(metrics) {
        const container = $('#metrics-inputs');
        container.empty();

        if (!metrics || metrics.length === 0) {
            container.html('<p class="col-span-2 text-gray-500 text-center py-4">Selecciona una red social para ver sus métricas</p>');
            return;
        }

        metrics.forEach(metric => {
            const input = $(`
                <div class="mb-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">${metric.name}</label>
                    <input type="number" 
                           class="form-control w-full metric-input" 
                           data-metric-id="${metric.id}"
                           data-metric-name="${metric.name}"
                           placeholder="0"
                           step="1">
                </div>
            `);
            container.append(input);
        });

        $('#btnSaveCapture').off('click').on('click', () => this.saveCapture());
    }

    async editCapture(id) {
        const response = await useFetch({
            url: api,
            data: {
                opc: "getCapture",
                id: id
            }
        });

        if (response.status !== 200) {
            alert({ 
                icon: "error", 
                text: "No se pudo cargar la información de la métrica",
                btn1: true,
                btn1Text: "Ok"
            });
            return;
        }

        const capture = response.data;

        const modalContent = `
            <div id="editCaptureModal">
                <!-- Información de la red social -->
                <div class="mb-4">
                    <div class="flex items-center gap-3 bg-gray-50 p-3 rounded-lg border">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: ${capture.social_network_color}20;">
                            <i class="${capture.social_network_icon}" style="color: ${capture.social_network_color}; font-size: 20px;"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">${capture.social_network_name}</p>
                            <p class="text-xs text-gray-500">Fecha: ${capture.date}</p>
                        </div>
                    </div>
                </div>

                <!-- Inputs de métricas -->
                <div id="edit-metrics-inputs" class="grid grid-cols-2 gap-3 border rounded p-3"></div>
            </div>
        `;

        const modal = bootbox.dialog({
            title: '<i class="icon-edit text-blue-600"></i> Editar Métricas',
            message: modalContent,
            size: 'large',
            closeButton: true,
            buttons: {
                cancel: {
                    label: '<i class="icon-close"></i> Cancelar',
                    className: 'btn-secondary',
                    callback: function() {
                        return true;
                    }
                },
                confirm: {
                    label: '<i class="icon-check"></i> Actualizar Métricas',
                    className: 'btn-primary',
                    callback: function() {
                        registerSocialNetWork.updateCaptureFromModal(id);
                        return false;
                    }
                }
            }
        });

        modal.on('shown.bs.modal', function() {
            const metricsContainer = $('#edit-metrics-inputs');
            capture.metrics.forEach(metric => {
                const input = $(`
                    <div class="mb-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">${metric.name}</label>
                        <input type="number" 
                               class="form-control w-full metric-input-edit" 
                               data-metric-id="${metric.metric_id}"
                               data-historial-metric-id="${metric.historial_metric_id}"
                               value="${metric.value}"
                               placeholder="0"
                               step="1"
                               min="0">
                    </div>
                `);
                metricsContainer.append(input);
            });
        });
    }

    async updateCaptureFromModal(id) {
        const metrics = [];
        let hasEmptyValues = false;

        $('.metric-input-edit').each(function () {
            const value = $(this).val();
            
            if (value && value.trim() !== '') {
                metrics.push({
                    historial_metric_id: $(this).data('historial-metric-id'),
                    metric_id: $(this).data('metric-id'),
                    value: parseFloat(value)
                });
            } else {
                hasEmptyValues = true;
            }
        });

        if (metrics.length === 0) {
            alert({ 
                icon: "warning", 
                text: "Por favor ingrese al menos una métrica con valor",
                btn1: true,
                btn1Text: "Ok"
            });
            return;
        }

        const response = await useFetch({
            url: api,
            data: {
                opc: "updateCaptureMetrics",
                id: id,
                metrics: JSON.stringify(metrics)
            }
        });

        if (response.status === 200) {
            bootbox.hideAll();
            alert({ 
                icon: "success", 
                text: response.message || "Métricas actualizadas correctamente",
                btn1: true,
                btn1Text: "Ok"
            });
            this.renderHistoryMetrics();
        } else {
            alert({ 
                icon: "error", 
                text: response.message || "Error al actualizar las métricas",
                btn1: true,
                btn1Text: "Ok"
            });
        }
    }



    // History.

    async renderHistoryMetrics() {

        // Crear filtro de fecha
        this.createHistoryFilter();

        const container = $('#history-container');
        container.html('<p class="text-gray-500 text-center">Cargando historial...</p>');

        const udn = $('#filterBarCapture #udn').val();
        const year = $('#filterBarCapture #anio').val();

        const response = await useFetch({
            url: api,
            data: {
                opc: "apiGetHistoryMetrics",
                udn: udn,
                year: year
            }
        });

        console.log('renderHistory',response)

        if (response.status !== 200) {
            container.html('<p class="text-red-500 text-center">Error al cargar el historial</p>');
            $('#metrics-counter').text('Sin métricas');
            return;
        }

        const history = response.data;

        if (!history || history.length === 0) {
            container.html('<p class="text-gray-500 text-center">No hay registros en el historial</p>');
            this.historyData = [];
            this.filteredData = [];
            $('#metrics-counter').text('Sin métricas');
            return;
        }

        // Guardar datos originales y filtrados
        this.historyData = history;
        this.filteredData = [...history];

        // Aplicar filtro si hay uno seleccionado
        if (this.selectedMonth) {
            $('#monthFilter').val(this.selectedMonth);
            this.filterHistoryByMonth();
        } else {
            this.renderFilteredHistory();
            $('#metrics-counter').text(this.filteredData.length);
        }
    }

    renderFilteredHistory() {
        const container = $('#history-container');
        container.empty();

        if (!this.filteredData || this.filteredData.length === 0) {
            container.html('<p class="text-gray-500 text-center py-8">No hay métricas para el período seleccionado</p>');
            return;
        }

        this.filteredData.forEach(item => {
            const card = $(`
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: ${item.color}20;">
                                <i class="${item.icon}" style="color: ${item.color}; font-size: 20px;"></i>
                            </div>
                            <span class="font-semibold text-gray-800">${item.network}</span>
                        </div>
                        <span class="text-sm text-gray-500">${item.date}</span>
                    </div>
                    <div class="space-y-2 mb-3">
                        ${item.metrics.map(m => `
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-600">${m.name}:</span>
                                <span class="font-medium">${m.value}</span>
                            </div>
                        `).join('')}
                    </div>
                    <div class="flex gap-2">
                        <button class="w-full bg-blue-600 text-white hover:bg-blue-700 py-2 px-3 rounded text-sm font-medium transition flex items-center justify-center gap-1" onclick="registerSocialNetWork.editHistory(${item.id})">
                            <i class="icon-edit"></i> Editar Métrica
                        </button>
                    </div>
                </div>
            `);
            container.append(card);
        });
    }

    editHistory(id) {
        this.editCapture(id);
    }

    reloadCaptureAndHistory() {
        this.layoutCaptureForm();
    }
}

class ReportSocialNetwork extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "report";
    }

    render() {
        this.layout();
        this.filterBar();
        this.lsMonthlyComparative();
    }

    layout() {
        this.primaryLayout({
            parent: `container-report`,
            id: this.PROJECT_NAME,
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
                    id: "udn",
                    lbl: "UDN",
                    class: "col-sm-3",
                    data: lsudn,
                    onchange: `report.updateView()`,
                },
                {
                    opc: "select",
                    id: "social_network_id",
                    lbl: "Red Social",
                    class: "col-sm-3",
                    data: socialNetworks,
                    onchange: `report.updateView()`,
                },
                {
                    opc: "select",
                    id: "year",
                    lbl: "Año",
                    class: "col-sm-2",
                    data: Array.from({ length: 5 }, (_, i) => {
                        const year = moment().year() - i;
                        return { id: year, valor: year.toString() };
                    }),
                    onchange: `report.updateView()`,
                },
                {
                    opc: "select",
                    id: "reportType",
                    lbl: "Tipo de Reporte",
                    class: "col-sm-4",
                    data: [
                        { id: "1", valor: "Concentrado Anual" },
                        { id: "2", valor: "Comparativa Mensual" },
                        { id: "3", valor: "Comparativa Anual" },
                    ],
                    onchange: `report.updateView()`,
                },
            ],
        });
    }

    updateView() {
        const reportType = $('#filterBarreport #reportType').val();

        switch (reportType) {
            case "1":
                this.lsAnualReport();
                break;
            case "2":
                this.lsMonthlyComparative();
                break;
            case "3":
                this.lsAnnualComparative();
                break;

        }

    }

    // Concentrado anual.

    lsAnualReport() {
        const year = $('#filterBarreport #year').val();
        const networkId = $('#filterBarreport #social_network_id').val();
        const networkName = $('#filterBarreport #social_network_id option:selected').text();

        const title = networkId ? `📊 Concentrado Anual - ${networkName}` : '📊 Concentrado Anual';

        this.createTable({
            parent: `container${this.PROJECT_NAME}`,
            idFilterBar: `filterBar${this.PROJECT_NAME}`,
            data: { opc: 'apiAnnualReport' },
            coffeesoft: true,
            conf: { datatable: false, pag: 15 },
            attr: {
                title: title,
                subtitle: `Resumen de métricas por mes del año ${year}`,
                id: "tbAnnualReport",
                theme: 'corporativo',
                center: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13],
                striped: true
            },
        });
    }

    lsMonthlyComparative() {
        const year = $('#filterBarreport #year').val();
        const networkId = $('#filterBarreport #socialNetwork').val();
        const networkName = $('#filterBarreport #socialNetwork option:selected').text();

        const title = networkId ? `📊 Comparativa Mensual - ${networkName}` : '📊 Comparativa Mensual';

        this.createTable({
            parent: `container${this.PROJECT_NAME}`,
            idFilterBar: `filterBar${this.PROJECT_NAME}`,
            data: { opc: 'apiMonthlyComparative' },
            coffeesoft: true,
            conf: { datatable: false, pag: 15 },
            attr: {
                title: title,
                subtitle: `Comparación mes a mes del año ${year}`,
                id: "tbMonthlyComparative",
                theme: 'corporativo',
                center: [1, 2, 3],
                right: [4],
                striped: true
            },
        });
    }

    lsAnnualComparative() {

        const year = $('#filterBarreport #year').val();
        const networkId = $('#filterBarreport #socialNetwork').val();
        const networkName = $('#filterBarreport #socialNetwork option:selected').text();

        const title = networkId ? `📊 Comparativa Anual - ${networkName}` : '📊 Comparativa Anual';

        this.createTable({
            parent: `container${this.PROJECT_NAME}`,
            idFilterBar: `filterBar${this.PROJECT_NAME}`,
            data: { opc: 'apiAnnualComparative' },
            coffeesoft: true,
            conf: { datatable: false, pag: 15 },
            attr: {
                title: title,
                subtitle: `Comparación año ${year} vs ${year - 1}`,
                id: "tbAnnualComparative",
                theme: 'corporativo',
                center: [1, 2, 3],
                right: [4],
                striped: true
            },
        });
    }



}

