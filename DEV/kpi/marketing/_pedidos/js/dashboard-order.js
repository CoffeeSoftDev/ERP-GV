let apis = 'ctrl/ctrl-dashboard-order.php';
// let app, salesDashboard;
let udn, lsudn;

$(async () => {
    // Agregar estilos CSS para controlar el tamaño de los gráficos
    const style = $(`
        <style>
            .chart-container {
                position: relative !important;
                height: 250px !important;
                width: 100% !important;
                overflow: hidden !important;
            }
            
            .chart-container canvas {
                max-height: 250px !important;
                width: 100% !important;
            }
            
            #content-dashboardComponent > div {
                height: fit-content !important;
                max-height: 400px !important;
            }
            
            .bg-white.rounded-xl.shadow-md {
                overflow: hidden !important;
            }
        </style>
    `);
    $('head').append(style);

    const data = await useFetch({ url: api, data: { opc: "init" } });
    udn = data.udn;
    lsudn = data.udn;

    // app = new App(api, "root");
    
});

class AppS extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "dashboardOrder";
    }

    render() {
        this.layout();
        salesDashboard.render();
    }

    layout() {
        this.primaryLayout({
            parent: "root",
            id: this.PROJECT_NAME,
            class: "w-full",
            card: {
                filterBar: { class: "w-full", id: "filterBarDashboard" },
                container: { class: "w-full h-full", id: "containerDashboard" },
            },
        });

        this.headerBar({
            parent: `filterBarDashboard`,
            title: "📊 Dashboard de Ventas",
            subtitle: "Análisis anual, por periodo y por hora.",
            onClick: () => app.render(),
        });
    }

    headerBar(options) {
        const defaults = {
            parent: "root",
            title: "Dashboard de Ventas",
            subtitle: "Análisis de métricas comerciales",
            icon: "icon-home",
            textBtn: "Actualizar",
            classBtn: "bg-blue-600 hover:bg-blue-700",
            onClick: null,
        };

        const opts = Object.assign({}, defaults, options);

        const container = $("<div>", {
            class: "flex justify-between items-center px-2 pt-3 pb-3"
        });

        const leftSection = $("<div>").append(
            $("<h2>", {
                class: "text-2xl font-semibold",
                text: opts.title
            }),
            $("<p>", {
                class: "text-gray-400",
                text: opts.subtitle
            })
        );

        const rightSection = $("<div>").append(
            $("<button>", {
                class: `${opts.classBtn} text-white font-semibold px-4 py-2 rounded transition flex items-center`,
                html: `<i class="${opts.icon} mr-2"></i>${opts.textBtn}`,
                click: () => {
                    if (typeof opts.onClick === "function") {
                        opts.onClick();
                    }
                }
            })
        );

        container.append(leftSection, rightSection);
        $(`#${opts.parent}`).html(container);
    }
}

class SalesDashboard extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "salesDashboard";
    }

    render() {
        this.layout();
    }

    async layout() {
        this.dashboardComponent({
            parent: "container-dashboard",
            id: "dashboardComponent",
            title: "📊 Dashboard de Ventas",
            subtitle: "Análisis anual, por periodo y por hora.",
            json: [
                { type: "grafico", id: "lineChartContainer", title: "Pedidos por mes del año 2025" },
                { type: "grafico", id: "barChartContainer", title: "Ventas por Canal" },
                { type: "tabla", id: "channelRankingContainer", title: "Canales con Mejor Rendimiento" },
                { type: "tabla", id: "monthlyPerformanceContainer", title: "Rendimiento mensual" },
            ]
        });

        this.filterBarDashboard();

        let udn = $('#filterBarDashboard #udn').val();
        let month = $('#filterBarDashboard #mes').val();
        let year = $('#filterBarDashboard #anio').val();
        
        let mkt = await useFetch({
            url: api,
            data: {
                opc: "apiPromediosDiarios",
                udn: udn,
                mes: month,
                anio: year,
            },
        });

        this.showCards(mkt.dashboard);
        this.renderLineChart(mkt.lineChart);
        this.renderBarChart(mkt.barChart);
        this.renderChannelRanking(mkt.channelRanking);
        this.renderMonthlyPerformance(mkt.monthlyPerformance);
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
                    onchange: `salesDashboard.layout()`,
                },
                {
                    opc: "select",
                    id: "mes",
                    lbl: "Mes",
                    class: "col-sm-3",
                    data: moment.months().map((m, i) => ({ id: i + 1, valor: m })),
                    onchange: `salesDashboard.layout()`,
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
                    onchange: `salesDashboard.layout()`,
                },
            ],
        });
        
        const currentMonth = moment().month() + 1;
        setTimeout(() => {
            $(`#filterBarDashboard #mes`).val(currentMonth);
        }, 100);
    }

    showCards(data) {
        this.infoCard({
            parent: "cardDashboard",
            theme: "light",
            json: [
                {
                    id: "kpiIngresos",
                    title: "Ingresos Totales",
                    data: {
                        value: data.ingresosTotales,
                        description: data.variacionIngresos,
                        color: "text-[#8CC63F]",
                    },
                },
                {
                    id: "kpiPedidos",
                    title: "Total de Pedidos",
                    data: {
                        value: data.totalPedidos,
                        description: data.rangeFechas,
                        color: "text-[#103B60]",
                    },
                },
                {
                    id: "kpiPromedio",
                    title: "Valor Promedio",
                    data: {
                        value: data.valorPromedio,
                        description: "por pedido",
                        color: "text-blue-600",
                    },
                },
                {
                    id: "kpiCanal",
                    title: "Canal Principal",
                    data: {
                        value: data.canalPrincipal,
                        description: data.porcentajeCanal,
                        color: "text-green-700",
                    },
                },
            ],
        });
    }



    renderLineChart(data) {
        const container = $("<div>", { class: "p-4" });
        const title = $("<h3>", {
            class: "text-lg font-semibold mb-4 text-gray-800",
            text: `Pedidos por mes del año ${new Date().getFullYear()}`
        });
        const canvasContainer = $("<div>", {
            class: "chart-container"
        });
        const canvas = $("<canvas>", {
            id: "lineChart",
            class: "w-full h-full"
        });

        canvasContainer.append(canvas);
        container.append(title, canvasContainer);
        $("#lineChartContainer").html(container);

        const ctx = document.getElementById("lineChart").getContext("2d");
        if (window._lineChart) window._lineChart.destroy();

        window._lineChart = new Chart(ctx, {
            type: "line",
            data: {
                labels: data.labels,
                datasets: [{
                    label: "Total de Pedidos",
                    data: data.values,
                    borderColor: "#103B60",
                    backgroundColor: "rgba(16, 59, 96, 0.1)",
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: "#103B60",
                    pointBorderColor: "#fff",
                    pointBorderWidth: 2,
                    pointRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: "rgba(0, 0, 0, 0.8)",
                        titleColor: "#fff",
                        bodyColor: "#fff",
                        callbacks: {
                            label: (context) => `${context.parsed.y} pedidos`
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: "rgba(0, 0, 0, 0.1)"
                        },
                        ticks: {
                            color: "#6B7280"
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: "#6B7280"
                        }
                    }
                }
            }
        });
    }

    renderBarChart(data) {
        const container = $("<div>", { class: "p-4" });
        const title = $("<h3>", {
            class: "text-lg font-semibold mb-4 text-gray-800",
            text: "Ventas por Canal"
        });
        const canvasContainer = $("<div>", {
            class: "chart-container"
        });
        const canvas = $("<canvas>", {
            id: "barChart",
            class: "w-full h-full"
        });

        canvasContainer.append(canvas);
        container.append(title, canvasContainer);
        $("#barChartContainer").html(container);

        const ctx = document.getElementById("barChart").getContext("2d");
        if (window._barChart) window._barChart.destroy();

        const channelColors = {
            'WhatsApp': '#25D366',
            'Meep': '#FF6B35',
            'Ecommerce': '#007BFF',
            'Facebook': '#1877F2',
            'Llamada': '#6C757D',
            'Uber': '#000000',
            'Otro': '#9E9E9E'
        };

        const datasets = data.channels.map(channel => ({
            label: channel.name,
            data: channel.data,
            backgroundColor: channelColors[channel.name] || '#9E9E9E',
            borderRadius: 4,
            borderSkipped: false
        }));

        window._barChart = new Chart(ctx, {
            type: "bar",
            data: {
                labels: data.months,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: "bottom",
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            boxWidth: 12
                        }
                    },
                    tooltip: {
                        backgroundColor: "rgba(0, 0, 0, 0.8)",
                        titleColor: "#fff",
                        bodyColor: "#fff",
                        callbacks: {
                            label: (context) => `${context.dataset.label}: ${this.formatPrice(context.parsed.y)}`
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        stacked: false,
                        grid: {
                            color: "rgba(0, 0, 0, 0.1)"
                        },
                        ticks: {
                            color: "#6B7280",
                            callback: (value) => this.formatPrice(value)
                        }
                    },
                    x: {
                        stacked: false,
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: "#6B7280"
                        }
                    }
                }
            }
        });
    }

    renderChannelRanking(data) {
        const container = $("<div>", { class: "p-4" });
        const title = $("<h3>", {
            class: "text-lg font-semibold mb-4 text-gray-800",
            text: "Canales con Mejor Rendimiento"
        });

        const list = $("<div>", { class: "space-y-3" });

        const channelIcons = {
            'WhatsApp': '💬',
            'Meep': '🛍️',
            'Ecommerce': '🌐',
            'Facebook': '📘',
            'Llamada': '📞',
            'Uber': '🚗',
            'Otro': '📋'
        };

        const channelColors = {
            'WhatsApp': 'bg-green-50 border-green-200',
            'Meep': 'bg-orange-50 border-orange-200',
            'Ecommerce': 'bg-blue-50 border-blue-200',
            'Facebook': 'bg-blue-50 border-blue-200',
            'Llamada': 'bg-gray-50 border-gray-200',
            'Uber': 'bg-gray-50 border-gray-200',
            'Otro': 'bg-gray-50 border-gray-200'
        };

        data.forEach((channel, index) => {
            const row = $("<div>", {
                class: `flex items-center justify-between p-3 rounded-lg border ${channelColors[channel.name] || 'bg-gray-50 border-gray-200'}`
            });

            const leftSection = $("<div>", { class: "flex items-center gap-3" });
            
            const icon = $("<span>", {
                class: "text-2xl",
                text: channelIcons[channel.name] || '📋'
            });

            const info = $("<div>");
            info.append($("<div>", {
                class: "font-semibold text-gray-800",
                text: channel.name
            }));
            info.append($("<div>", {
                class: "text-sm text-gray-600",
                text: `${channel.orders} pedidos`
            }));

            leftSection.append(icon, info);

            const rightSection = $("<div>", { class: "text-right" });
            rightSection.append($("<div>", {
                class: "font-bold text-lg text-gray-800",
                text: this.formatPrice(channel.total)
            }));
            rightSection.append($("<div>", {
                class: "text-sm text-gray-600",
                text: `${channel.percentage}%`
            }));

            row.append(leftSection, rightSection);
            list.append(row);
        });

        container.append(title, list);
        $("#channelRankingContainer").html(container);
    }

    renderMonthlyPerformance(data) {
        const container = $("<div>", { class: "p-4" });
        const title = $("<h3>", {
            class: "text-lg font-semibold mb-4 text-gray-800",
            text: "📊 Rendimiento mensual"
        });

        const table = $("<div>", { class: "overflow-x-auto" });
        const tableElement = $("<table>", { class: "w-full border-collapse" });

        // Header
        const thead = $("<thead>");
        const headerRow = $("<tr>", { class: "bg-[#103B60] text-white" });
        headerRow.append(
            $("<th>", { class: "px-4 py-3 text-left font-semibold", text: "Mes" }),
            $("<th>", { class: "px-4 py-3 text-center font-semibold", text: "Pedidos" }),
            $("<th>", { class: "px-4 py-3 text-right font-semibold", text: "Ventas" }),
            $("<th>", { class: "px-4 py-3 text-center font-semibold", text: "Crecimiento" })
        );
        thead.append(headerRow);

        // Body
        const tbody = $("<tbody>");
        data.forEach((month, index) => {
            const row = $("<tr>", { 
                class: index % 2 === 0 ? "bg-gray-50" : "bg-white"
            });

            const growthClass = month.growth >= 0 ? "text-green-600" : "text-red-600";
            const growthIcon = month.growth >= 0 ? "↑" : "↓";
            const growthText = `${growthIcon} ${Math.abs(month.growth)}%`;

            row.append(
                $("<td>", { class: "px-4 py-3 font-medium", text: month.name }),
                $("<td>", { class: "px-4 py-3 text-center", text: month.orders }),
                $("<td>", { class: "px-4 py-3 text-right font-semibold", text: this.formatPrice(month.sales) }),
                $("<td>", { 
                    class: `px-4 py-3 text-center font-semibold ${growthClass}`, 
                    text: growthText 
                })
            );
            tbody.append(row);
        });

        tableElement.append(thead, tbody);
        table.append(tableElement);
        container.append(title, table);
        $("#monthlyPerformanceContainer").html(container);
    }

    dashboardComponent(options) {
        const defaults = {
            parent: "root",
            id: "dashboardComponent",
            title: "📊 Dashboard de Ventas",
            subtitle: "Análisis mensual de productos vendidos y con margen",
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
                class: "bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden"
            });

            if (item.title) {
                const emoji = '📊';
                const titleContent = `${emoji} ${item.title}`;
                block.prepend(`<h3 class="text-sm font-semibold text-gray-800 mb-3 px-4 pt-4">${titleContent}</h3>`);
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
            json: []
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
            
            const description = $("<p>", {
                class: `text-xs mt-1 ${card.data?.description?.includes('↑') ? 'text-green-600' : card.data?.description?.includes('↓') ? 'text-red-600' : 'text-gray-500'}`,
                text: card.data?.description || ""
            });
            
            box.append(title, value, description);
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

    linearChart(options) {
        const defaults = {
            parent: "containerLineChart",
            id: "linearChart",
            title: "",
            class: "rounded-xl",
            data: {}
        };
        
        const opts = Object.assign({}, defaults, options);
        const container = $("<div>", { class: opts.class });
        const title = $("<h2>", {
            class: "text-lg font-bold mb-2",
            text: opts.title
        });
        const canvas = $("<canvas>", {
            id: opts.id,
            class: "w-full h-[150px]"
        });
        
        container.append(title, canvas);
        $('#' + opts.parent).append(container);

        const ctx = document.getElementById(opts.id).getContext("2d");
        if (!window._charts) window._charts = {};
        if (window._charts[opts.id]) {
            window._charts[opts.id].destroy();
        }

        window._charts[opts.id] = new Chart(ctx, {
            type: "line",
            data: opts.data,
            options: {
                responsive: true,
                aspectRatio: 3,
                plugins: {
                    legend: { position: "bottom" },
                    tooltip: {
                        callbacks: {
                            title: (items) => {
                                const index = items[0].dataIndex;
                                const tooltips = opts.data.tooltip || opts.data.labels;
                                return tooltips[index];
                            },
                            label: (ctx) => `${ctx.dataset.label}: ${this.formatPrice(ctx.parsed.y)}`
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (v) => this.formatPrice(v)
                        }
                    }
                }
            }
        });
    }

    formatPrice(amount) {
        return '$' + Number(amount).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }
}