class Dashboard extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "Dashboard";
    }

    render() {
        this.layout();
        this.filterBar();
        this.loadMetrics();
    }

    layout() {
        this.primaryLayout({
            parent: 'root',
            id: this.PROJECT_NAME,
            class: 'w-full min-h-screen bg-[#0f172a]',
            card: {
                filterBar: { class: 'w-full border-b border-gray-700 pb-4', id: `filterBar${this.PROJECT_NAME}` },
                container: { class: 'w-full my-4', id: `container${this.PROJECT_NAME}` }
            }
        });

        $(`#${this.PROJECT_NAME}`).prepend(`
            <div class="px-6 pt-6 pb-4">
                <h1 class="text-3xl font-bold text-white">📊 Dashboard de Pedidos</h1>
                <p class="text-gray-400 mt-2">Análisis y métricas del sistema de administración de pedidos</p>
            </div>
        `);

        $(`#container${this.PROJECT_NAME}`).html(`
            <div class="px-6">
                <div id="kpiCards" class="mb-6"></div>
                <div id="chartsContainer" class="grid grid-cols-1 lg:grid-cols-2 gap-6"></div>
            </div>
        `);
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
                    onchange: `dashboard.loadMetrics()`
                },
                {
                    opc: "select",
                    id: "mes",
                    lbl: "Mes",
                    class: "col-sm-3",
                    data: moment.months().map((m, i) => ({ id: i + 1, valor: m })),
                    onchange: `dashboard.loadMetrics()`
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
                    onchange: `dashboard.loadMetrics()`
                }
            ]
        });

        const currentMonth = moment().month() + 1;
        setTimeout(() => {
            $(`#filterBar${this.PROJECT_NAME} #mes`).val(currentMonth).trigger("change");
        }, 100);
    }

    async loadMetrics() {
        const udn = $(`#filterBar${this.PROJECT_NAME} #udn`).val();
        const mes = $(`#filterBar${this.PROJECT_NAME} #mes`).val();
        const anio = $(`#filterBar${this.PROJECT_NAME} #anio`).val();

        const response = await useFetch({
            url: this._link,
            data: {
                opc: "apiDashboardMetrics",
                udn: udn,
                mes: mes,
                anio: anio
            }
        });

        this.showKPIs(response.dashboard);
        this.renderCharts(response.chartData);
    }

    showKPIs(data) {
        this.infoCard({
            parent: "kpiCards",
            theme: "dark",
            json: [
                {
                    id: "kpiTotalPedidos",
                    title: "Total de Pedidos",
                    data: {
                        value: data.totalPedidos,
                        description: "Pedidos del mes",
                        color: "text-blue-400"
                    }
                },
                {
                    id: "kpiIngresos",
                    title: "Ingresos Totales",
                    data: {
                        value: data.ingresosTotales,
                        description: "Ventas del mes",
                        color: "text-green-400"
                    }
                },
                {
                    id: "kpiCheque",
                    title: "Cheque Promedio",
                    data: {
                        value: data.chequePromedio,
                        description: "Promedio por pedido",
                        color: "text-purple-400"
                    }
                },
                {
                    id: "kpiPagos",
                    title: "Pagos Verificados",
                    data: {
                        value: data.pagosVerificados,
                        description: "Transferencias confirmadas",
                        color: "text-yellow-400"
                    }
                }
            ]
        });
    }

    renderCharts(chartData) {
        $('#chartsContainer').html(`
            <div class="bg-gray-800 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4">📊 Ventas por Canal</h3>
                <canvas id="chartVentasCanal" class="w-full" height="300"></canvas>
            </div>
            <div class="bg-gray-800 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4">📈 Resumen del Mes</h3>
                <div class="text-gray-300 space-y-3">
                    <div class="flex justify-between items-center p-3 bg-gray-700 rounded-lg">
                        <span>Canales Activos</span>
                        <span class="font-bold text-green-400">${chartData.labels.length}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-700 rounded-lg">
                        <span>Canal con Mayor Venta</span>
                        <span class="font-bold text-blue-400">${chartData.labels[0] || 'N/A'}</span>
                    </div>
                    <div class="text-sm text-gray-400 mt-4">
                        <i class="fas fa-info-circle"></i> Los datos se actualizan en tiempo real
                    </div>
                </div>
            </div>
        `);

        const ctx = document.getElementById('chartVentasCanal').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        titleColor: '#fff',
                        bodyColor: '#d1d5db',
                        borderColor: '#374151',
                        borderWidth: 1
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#9ca3af',
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        },
                        grid: {
                            color: '#374151'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#9ca3af'
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    infoCard(options) {
        const defaults = {
            parent: "root",
            id: "infoCardKPI",
            class: "",
            theme: "dark",
            json: []
        };
        const opts = Object.assign({}, defaults, options);
        const isDark = opts.theme === "dark";
        const cardBase = isDark
            ? "bg-gray-800 text-white rounded-xl shadow-lg"
            : "bg-white text-gray-800 rounded-xl shadow";
        const titleColor = isDark ? "text-gray-300" : "text-gray-600";
        const descColor = isDark ? "text-gray-400" : "text-gray-500";

        const renderCard = (card, i = "") => {
            const box = $("<div>", {
                id: `${opts.id}_${i}`,
                class: `${cardBase} p-6 hover:shadow-xl transition-shadow duration-300`
            });
            const title = $("<p>", {
                class: `text-sm ${titleColor} mb-2`,
                text: card.title
            });
            const value = $("<p>", {
                id: card.id || "",
                class: `text-3xl font-bold ${card.data?.color || "text-white"} mb-1`,
                text: card.data?.value
            });
            const description = $("<p>", {
                class: `text-xs ${descColor}`,
                text: card.data?.description
            });
            box.append(title, value, description);
            return box;
        };

        const container = $("<div>", {
            id: opts.id,
            class: `grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 ${opts.class}`
        });

        opts.json.forEach((item, i) => {
            container.append(renderCard(item, i));
        });

        $(`#${opts.parent}`).html(container);
    }
}
