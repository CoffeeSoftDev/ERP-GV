let api_dashboard = 'ctrl/ctrl-ingresos-dashboard.php';

class Dashboard extends Templates {

    // components.
    dashboardComponent(options) {
        const defaults = {
            parent: "root",
            id: "dashboardComponent",
            title: "📊 Huubie · Dashboard de Eventos",
            subtitle: "Resumen mensual · Cotizaciones · Pagados · Cancelados",
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

            <div id="filterBar" class="mx-auto px-4 py-3"></div>

            <section id="cardDashboard" class="mx-auto px-4 py-3"></section>

            <section id="content-${opts.id}" class="mx-auto px-4 py-2 grid gap-6 grid-cols-1 md:grid-cols-2 auto-rows-max"></section>
        </div>`);

        const grid = $(`#content-${opts.id}`, container);

        opts.json.forEach(item => {
            if (item.type === "div") {
                const fullRow = $("<div>", {
                    id: item.id,
                    class: "col-span-full w-full"
                });

                if (item.html) {
                    fullRow.html(item.html);
                }

                grid.append(fullRow);
                return;
            }

            const block = $("<div>", {
                id: item.id,
                class: "bg-white p-2 rounded-xl shadow-md border border-gray-200 min-h-[200px] w-full"
            });

            if (item.title) {
                const defaultEmojis = {
                    'grafico': '📊',
                    'tabla': '📁',
                    'doc': '📄',
                    'filterBar': '🔍'
                };

                const emoji = item.emoji || defaultEmojis[item.type] || '';
                const iconHtml = item.icon ? `<i class="${item.icon}"></i> ` : '';
                const titleContent = `${emoji} ${iconHtml}${item.title}`;

                block.prepend(`<h3 class="text-sm font-semibold text-gray-800 mb-3">${titleContent}</h3>`);
            }

            if (item.content && Array.isArray(item.content)) {
                item.content.forEach(contentItem => {
                    const element = $(`<${contentItem.type}>`, {
                        id: contentItem.id || '',
                        class: contentItem.class || '',
                        text: contentItem.text || ''
                    });

                    if (contentItem.attributes) {
                        Object.keys(contentItem.attributes).forEach(attr => {
                            element.attr(attr, contentItem.attributes[attr]);
                        });
                    }

                    if (contentItem.html) {
                        element.html(contentItem.html);
                    }

                    block.append(element);
                });
            }

            grid.append(block);
        });

        $(`#${opts.parent}`).html(container);
    }



    infoCard(options) {
        const defaults = {
            parent: "root",
            id: "infoCardKPI",
            class: "",
            theme: "light", // light | dark
            json: [],
            data: {
                value: "0",
                description: "",
                color: "text-gray-800"
            },
            onClick: () => { }
        };
        const opts = Object.assign({}, defaults, options);
        const isDark = opts.theme === "dark";
        const cardBase = isDark
            ? "bg-[#1F2A37] text-white rounded-xl shadow"
            : "bg-white text-gray-800 rounded-xl shadow";
        const titleColor = isDark ? "text-gray-300" : "text-gray-600";
        const descColor = isDark ? "text-gray-400" : "text-gray-500";
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
                class: `text-xs mt-1 ${card.data?.color || descColor}`,
                text: card.data?.description
            });
            box.append(title, value, description);
            return box;
        };
        const container = $("<div>", {
            id: opts.id,
            class: `grid grid-cols-2 md:grid-cols-4 gap-4 ${opts.class}`
        });
        if (opts.json.length > 0) {
            opts.json.forEach((item, i) => {
                container.append(renderCard(item, i));
            });
        } else {
            container.append(renderCard(opts));
        }
        $(`#${opts.parent}`).html(container);
    }

    linearChart(options) {
        const defaults = {
            parent: "containerLineChart",
            id: "linearChart",
            title: "",
            class: "border p-4 rounded-xl",
            data: {},   // <- puede contener { labels: [], datasets: [], tooltip: [] }
            json: [],
            onShow: () => { },
        };
        const opts = Object.assign({}, defaults, options);
        const container = $("<div>", { class: opts.class });
        const title = $("<h2>", {
            class: "text-lg font-bold mb-2",
            text: opts.title
        });
        const canvasWrapper = $("<div>", {
            class: "w-full",
            css: { height: "300px" }
        });
        const canvas = $("<canvas>", {
            id: opts.id,
            class: "w-full h-full"
        });
        canvasWrapper.append(canvas);
        container.append(title, canvasWrapper);
        $('#' + opts.parent).html(container);

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
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: "bottom" },
                    tooltip: {
                        callbacks: {
                            title: (items) => {
                                const index = items[0].dataIndex;
                                const tooltips = opts.data.tooltip || opts.data.labels;
                                return tooltips[index];
                            },
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

    barChart(options) {
        const defaults = {
            parent: "containerBarChart",
            id: "chartBar",
            title: "Comparativa por Categorías",
            class: "border p-4 rounded-xl",
            labels: [],
            dataA: [], // Año anterior
            dataB: [], // Año actual
            yearA: new Date().getFullYear() - 1, // 2024
            yearB: new Date().getFullYear(),     // 2025
            type: "price"
        };

        const opts = Object.assign({}, defaults, options);
        const isPrice = opts.type === "price";

        // 📦 Crear contenedor
        const container = $("<div>", { class: opts.class });
        const title = $("<h2>", {
            class: "text-lg font-bold mb-2",
            text: opts.title
        });
        const canvasWrapper = $("<div>", {
            class: "w-full",
            css: { height: "400px" }
        });
        const canvas = $("<canvas>", {
            id: opts.id,
            class: "w-full h-full"
        });

        canvasWrapper.append(canvas);
        container.append(title, canvasWrapper);
        $("#" + opts.parent).html(container);

        const ctx = document.getElementById(opts.id).getContext("2d");
        if (!window._barCharts) window._barCharts = {};
        if (window._barCharts[opts.id]) window._barCharts[opts.id].destroy();

        // 🎨 Colores: Azul para período 1 (consulta), Verde para período 2 (comparación)
        const colorPeriodo1 = "#103B60"; // Azul oscuro - Período de consulta
        const colorPeriodo2 = "#8CC63F"; // Verde - Período de comparación

        // 📊 Crear gráfico
        window._barCharts[opts.id] = new Chart(ctx, {
            type: "bar",
            data: {
                labels: opts.labels,
                datasets: [
                    {
                        label: `Año ${opts.yearA}`, // Período 2 (comparación) - dataB
                        data: opts.dataB,
                        backgroundColor: colorPeriodo2
                    },
                    {
                        label: `Año ${opts.yearB}`, // Período 1 (consulta) - dataA
                        data: opts.dataA,
                        backgroundColor: colorPeriodo1
                    },

                ]
            },
            plugins: [{
                id: 'barValueLabels',
                afterDatasetsDraw: function (chart) {
                    const ctx = chart.ctx;
                    ctx.font = "bold 11px sans-serif";
                    ctx.textAlign = "center";
                    ctx.textBaseline = "bottom";

                    chart.data.datasets.forEach(function (dataset, datasetIndex) {
                        const meta = chart.getDatasetMeta(datasetIndex);
                        if (!meta.hidden) {
                            meta.data.forEach(function (bar, index) {
                                const value = dataset.data[index];
                                if (value && value > 0) {
                                    const label = isPrice ? (typeof formatPrice === "function" ? formatPrice(value) : Number(value).toLocaleString()) : Number(value).toLocaleString();
                                    ctx.fillStyle = "#000";
                                    ctx.fillText(label, bar.x, bar.y - 5);
                                }
                            });
                        }
                    });
                }
            }],
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: "bottom",
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            font: {
                                size: 13,
                                weight: "600"
                            },
                            color: "#333"
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: (ctx) =>
                                `${ctx.dataset.label}: ${isPrice && typeof formatPrice === "function" ? formatPrice(ctx.parsed.y) : Number(ctx.parsed.y).toLocaleString()}`
                        }
                    }
                },
                layout: {
                    padding: {
                        top: 30,
                        bottom: 10,
                        left: 10,
                        right: 10
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grace: '5%',
                        ticks: {
                            callback: (v) => isPrice && typeof formatPrice === "function" ? formatPrice(v) : Number(v).toLocaleString(),
                            color: "#333",
                            font: { size: 12 }
                        },
                        grid: { color: "rgba(0,0,0,0.05)" }
                    },
                    x: {
                        ticks: {
                            color: "#333",
                            font: { size: 12 }
                        },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    topDiasMes(options) {
        const defaults = {
            parent: "containerTopDias",
            title: "Mejores Días del Mes",
            subtitle: "",
            data: [] // [{fecha, dia, clientes, total, nota}]
        };

        const opts = Object.assign({}, defaults, options);
        const container = $("<div>", { class: "border p-4 rounded-xl bg-white " });

        // Título
        const header = $("<div>", { class: "mb-3" })
            .append($("<h2>", { class: "text-lg font-bold", text: opts.title }))
            .append($("<p>", { class: "text-sm text-gray-500", text: opts.subtitle }));

        // Lista
        const list = $("<div>", { class: "space-y-3" });

        const colores = [
            { bg: "bg-green-100", circle: "bg-green-500 text-white" },
            { bg: "bg-blue-100", circle: "bg-blue-500 text-white" },
            { bg: "bg-purple-100", circle: "bg-purple-500 text-white" },
            { bg: "bg-orange-100", circle: "bg-orange-500 text-white" },
            { bg: "bg-gray-100", circle: "bg-gray-600 text-white" }
        ];

        opts.data.forEach((item, i) => {
            const rank = i + 1;
            const palette = colores[i] || { bg: "bg-white", circle: "bg-gray-300 text-black" };

            // Fila con fondo dinámico
            const row = $("<div>", {
                class: `flex items-center gap-3 p-3 rounded-lg ${palette.bg}`
            });

            // Número ranking con círculo dinámico
            row.append(
                $("<span>", {
                    class: `flex items-center justify-center w-8 h-8 rounded-full font-bold ${palette.circle}`,
                    text: rank
                })
            );

            // Datos
            const content = $("<div>", { class: "flex-1" });
            content.append(
                $("<div>", { class: "flex justify-between" })
                    .append($("<span>", { class: "font-semibold", text: `${item.dia}, ${item.fecha}` }))
                    .append($("<span>", { class: "font-bold", text: formatPrice(item.total) }))
            );
            content.append(
                $("<div>", { class: "text-sm text-gray-600 flex justify-between" })
                    .append($("<span>", { text: `${item.clientes} clientes` }))
                    .append($("<span>", { class: "italic", text: item.nota }))
            );

            row.append(content);
            list.append(row);
        });

        container.append(header, list);
        $("#" + opts.parent).html(container);
    }

    topDiasSemana(options) {
        const defaults = {
            parent: "containerTopDiasSemana",
            title: "📊 Ranking por Promedio Semanal",
            subtitle: "",
            data: [] // [{dia, promedio, veces}]
        };

        const opts = Object.assign({}, defaults, options);
        const container = $("<div>", { class: "border p-4 rounded-xl bg-white " });

        // Título
        const header = $("<div>", { class: "mb-3" })
            .append($("<h2>", { class: "text-lg font-bold", text: opts.title }))
            .append($("<p>", { class: "text-sm text-gray-500", text: opts.subtitle }));

        // Lista
        const list = $("<div>", { class: "space-y-3" });

        const colores = [
            { bg: "bg-green-100", circle: "bg-green-500 text-white" },
            { bg: "bg-blue-100", circle: "bg-blue-500 text-white" },
            { bg: "bg-purple-100", circle: "bg-purple-500 text-white" },
            { bg: "bg-orange-100", circle: "bg-orange-500 text-white" },
            { bg: "bg-pink-100", circle: "bg-pink-500 text-white" },
            { bg: "bg-yellow-100", circle: "bg-yellow-600 text-white" },
            { bg: "bg-gray-100", circle: "bg-gray-600 text-white" }
        ];

        opts.data.forEach((item, i) => {
            const rank = i + 1;
            const palette = colores[i] || { bg: "bg-white", circle: "bg-gray-300 text-black" };

            // Fila con fondo dinámico
            const row = $("<div>", {
                class: `flex items-center gap-3 p-3 rounded-lg ${palette.bg}`
            });

            // Número ranking con círculo dinámico
            row.append(
                $("<span>", {
                    class: `flex items-center justify-center w-8 h-8 rounded-full font-bold ${palette.circle}`,
                    text: rank
                })
            );

            // Datos
            const content = $("<div>", { class: "flex-1" });
            content.append(
                $("<div>", { class: "flex justify-between" })
                    .append($("<span>", { class: "font-semibold", text: item.dia }))
                    .append($("<span>", { class: "font-bold", text: formatPrice(item.promedio) }))
            );
            content.append(
                $("<div>", { class: "text-sm text-gray-600 flex justify-between" })
                    .append($("<span>", { text: `${item.veces} días con ${item.clientes} clientes` }))
                    .append($("<span>", { class: "italic", text: rank === 1 ? "⭐ Mejor día" : "" }))
            );

            row.append(content);
            list.append(row);
        });

        container.append(header, list);
        $("#" + opts.parent).html(container);
    }

    handleCategoryChange(idudn) {
        // Filtrar las clasificaciones que coincidan con el idudn
        let lsclasificacion = clasificacion.filter((item) => item.udn == idudn);

        // Generar options HTML para el select
        const optionsHtml = lsclasificacion.map(item =>
            `<option value="${item.id}">${item.valor}</option>`
        ).join('');

        // Actualizar el select con las opciones
        $('#filterBarProductMargen #category').html(optionsHtml);
    }

    renderSelectCategory(options) {
        const defaults = {
            parent: "category",
            udn: null,
            data: [],
            placeholder: "Seleccionar categoría",
            includeAll: false,
            onChange: null
        };

        const opts = Object.assign({}, defaults, options);

        if (!opts.udn) {
            console.warn('UDN no proporcionado para renderSelectCategory');
            return;
        }

        const filteredData = opts.data.filter((item) => item.udn == opts.udn);

        let optionsHtml = '';

        if (opts.includeAll) {
            optionsHtml += `<option value="0">${opts.placeholder}</option>`;
        }

        optionsHtml += filteredData.map(item =>
            `<option value="${item.id}">${item.valor}</option>`
        ).join('');

        $(`#${opts.parent}`).html(optionsHtml);

        if (opts.onChange && typeof opts.onChange === 'function') {
            $(`#${opts.parent}`).off('change').on('change', opts.onChange);
        }
    }


}

class FinanceDashboard extends Dashboard {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "order";
    }

    render() {
        this.layout();
    }

    layout() {

        this.dashboardComponent({
            parent: "container-dashboard",
            id: "dashboardComponent",
            title: "📊 Dashboard de Ventas",
            subtitle: "Análisis comparativo de ventas entre dos períodos",
            json: [
                { type: "div", id: "CategoryDashboard" },

                // Graficos de cheque promedio.
                {
                    type: "grafico", id: "linearChequePromedio", title: "",
                    content: [
                        { class: "border px-3 py-2 rounded", type: "div", id: "filterBarChequePromedio" },
                        { class: " mt-2", type: "div", id: "barChequePromedio" },
                    ]
                },

                {
                    type: "grafico", id: "dailyAverageCheck", title: "",
                    content: [
                        { class: "border px-3 py-2 rounded", type: "div", id: "filterBarDailyAverageCheck" },
                        { class: " mt-2", type: "div", id: "containerDailyAverageCheck" },
                    ]
                },

                { type: "grafico", id: "rankingChequePromedio", title: "Ranking Cheque Promedio" },
                { type: "grafico", id: "containerChequePro" },

                {
                    type: "grafico", id: "clientesPorSemana", title: "Clientes",
                    content: [
                        { class: " mt-2", type: "div", id: "containerClientesSemana" },
                    ]
                },


                {
                    type: "grafico", id: "barProductMargen1", title: "",
                    content: [
                        { class: "border px-3 py-2 rounded", type: "div", id: "filterBarProductMargen" },
                        { class: " mt-2", type: "div", id: "barProductMargen" },
                    ]
                },
                { type: "grafico", id: "ventasDiasSemana", title: "Ventas por Día de la Semana" },
                { type: "grafico", id: "Tendencia", title: "Tendencia de Ventas" },


            ]
        });

        this.filterBar();

        this.createfilterBar({
            parent: `filterBarProductMargen`,
            data: [
                {
                    opc: "select",
                    id: "category",
                    lbl: "Categorias",
                    class: "col-sm-4",
                    // data:,
                    onchange: `dashboard.comparativaByCategory()`,
                },

            ],
        });

        this.tabLayout({
            parent: `CategoryDashboard`,
            id: `tabs${this.PROJECT_NAME}`,
            theme: "light",
            class: 'mt-2',
            type: "button",
            renderContainer: false,
            json: [
                {
                    id: "graphigs-sales",
                    tab: "Graficas de venta",
                    class: "mb-1",
                    active: true,
                    onClick: () => this.showGraphicsCategory('sales')
                },
                {
                    id: "graphigs-daily",
                    tab: "Graficas de Cheque Promedio",
                    onClick: () => this.showGraphicsCategory('daily')
                },
            ]

        });


        setTimeout(() => {
            this.renderDashboard();
            this.showGraphicsCategory('sales');
        }, 500);



    }

    filterBar() {
        this.createfilterBar({
            parent: `filterBar`,
            data: [
                {
                    opc: "select",
                    id: "udn",
                    lbl: "UDN",
                    class: "col-sm-3",
                    data: lsudn,
                    onchange: `dashboard.renderDashboard()`,
                },
                {
                    opc: "div",
                    id: "containerPeriodo1",
                    lbl: "Consultar con:",
                    class: "col-lg-3 col-sm-4",
                    html: `
                        <input 
                            type="month" 
                            id="periodo1" 
                            class="form-control"
                            style="width: 100%; min-width: 100%; display: block;"
                            onchange="dashboard.renderDashboard()"
                        />
                    `
                },
                {
                    opc: "div",
                    id: "containerPeriodo2",
                    lbl: "Comparar con:",
                    class: "col-lg-3 col-sm-4 ",
                    html: `
                        <input 
                            type="month" 
                            id="periodo2" 
                            class="form-control"
                            onchange="dashboard.renderDashboard()"
                        />
                    `
                },
            ],
        });

        const currentYear = moment().year();
        const currentMonth = moment().month() + 1;
        const lastYear = currentYear - 1;

        const periodo1 = `${currentYear}-${String(currentMonth).padStart(2, '0')}`;
        const periodo2 = `${lastYear}-${String(currentMonth).padStart(2, '0')}`;

        $('#containerPeriodo1').removeClass('col-lg-3 col-sm-4');
        $('#containerPeriodo2').removeClass('col-lg-3 col-sm-4');

        setTimeout(() => {
            $(`#filterBar #periodo1`).val(periodo1);
            $(`#filterBar #periodo2`).val(periodo2);
        }, 100);
    }

    async renderDashboard() {

        this.handleCategoryChange($('#idFilterBar #udn').val());


        let udn           = $('#filterBar #udn').val();
        let periodo1      = $('#filterBar #periodo1').val();
        let [anio1, mes1] = periodo1.split('-');
        let periodo2      = $('#filterBar #periodo2').val();
        let [anio2, mes2] = periodo2.split('-');

        let mkt = await useFetch({
            url: api_dashboard,
            data: {
                opc: "apiPromediosDiarios",
                udn: udn,
                anio1: anio1,
                mes1: mes1,
                anio2: anio2,
                mes2: mes2,
            },
        });

        let ventas = await useFetch({
            url: api,
            data: {
                opc: "apiPromediosDiarios",
                udn: udn,
                anio1: anio1,
                mes1: mes1,
                anio2: anio2,
                mes2: mes2,
            },
        });



        // init Cards.

        this.showCards(mkt.dashboard);

        // Graficos cheque promedio.

        this.layoutDailyAverageCheck();
        this.layoutChequePromedio();

        // Graficos ventas.

        this.chequeComparativo({
            data: mkt.barras.dataset,
            anioA: mkt.barras.anioA,
            anioB: mkt.barras.anioB,
        });

        // Grafico Linear.
        this.comparativaIngresosDiarios({ data: ventas.linear });

       
        this.ventasPorDiaSemana(ventas.barDays);


        this.topDiasSemana({
            parent: "Tendencia",
            title: "📊 Ranking por Promedio Semanal",
            subtitle: "Promedio de ventas por día de la semana en el mes seleccionado",
            data: ventas.topWeek
        });

        this.topChequePromedioSemanal({
            parent: "rankingChequePromedio",
            title: "💰 Ranking Cheque Promedio Semanal",
            subtitle: "Promedio de cheque por día de la semana en el mes seleccionado",
            data: mkt.topWeekCheque || []
        });

        this.renderClientesPorSemana();


    }

    showGraphicsCategory(category) {
        if (category === 'sales') {
            // Mostrar gráficos de ventas
            $('#containerChequePro').show();
            $('#barProductMargen1').show();
            $('#ventasDiasSemana').show();
            $('#Tendencia').show();
            // Ocultar gráficos de cheque promedio
            $('#clientesPorSemana').hide();
            $('#linearChequePromedio').hide();
            $('#dailyAverageCheck').hide();
            $('#rankingChequePromedio').hide();
          
        } else if (category === 'daily') {
            // Mostrar gráficos de cheque promedio
            $('#linearChequePromedio').show();
            $('#dailyAverageCheck').show();
            $('#rankingChequePromedio').show();
            $('#clientesPorSemana').show();

            // Ocultar gráficos de ventas
            $('#containerChequePro').hide();
            $('#barProductMargen1').hide();
            $('#ventasDiasSemana').hide();
            $('#Tendencia').hide();
        }
    }

    // Cards.
    showCards(data) {
        this.infoCard({
            parent: "cardDashboard",
            theme: "light",
            json: [
                {
                    id: "kpiDia",
                    title: data.ventaDia.titulo,
                    data: {
                        value: data.ventaDia.valor,
                        description: data.ventaDia.fecha,
                        color: data.ventaDia.color,
                    },
                },
                {
                    id: "kpiMes",
                    title: data.ventaMes.titulo,
                    data: {
                        value: data.ventaMes.valor,
                        description: `${this.getTrendIcon(data.ventaMes.tendencia)} ${data.ventaMes.mensaje}`,
                        color: data.ventaMes.color,
                    },
                },
                {
                    title: data.clientes.titulo,
                    data: {
                        value: data.clientes.valor,
                        description: `${this.getTrendIcon(data.clientes.tendencia)} ${data.clientes.mensaje}`,
                        color: data.clientes.color,
                    },
                },
                {
                    id: "kpiCheque",
                    title: data.chequePromedio.titulo,
                    data: {
                        value: data.chequePromedio.valor,
                        description: `${this.getTrendIcon(data.chequePromedio.tendencia)} ${data.chequePromedio.mensaje}`,
                        color: data.chequePromedio.color,
                    },
                },
            ],
        });
    }

    getTrendIcon(tendencia) {
        switch (tendencia) {
            case 'up':
                return '↑';
            case 'down':
                return '↓';
            default:
                return '→';
        }
    }

    // Daily.

    layoutDailyAverageCheck() {

        $('#filterBarDailyAverageCheck').empty();

        this.createfilterBar({
            parent: `filterBarDailyAverageCheck`,
            data: [
                {
                    opc: "select",
                    id: "category",
                    lbl: "Categorias",
                    class: "col-sm-4",
                    onchange: `dashboard.renderDailyAverageCheck()`,
                },

            ],
        });

        this.renderSelectCategory({
            parent: 'filterBarDailyAverageCheck #category',
            udn: $('#idFilterBar #udn').val(),
            data: clasificacion,
            includeAll: false
        });

        this.renderDailyAverageCheck();

    }

    async renderDailyAverageCheck() {

        let udn = $('#filterBar #udn').val();
        let category = $('#category option:selected').text();
        let date = this.getFilterDate();

        const meses = moment.months();
        const nombreMes1 = meses[parseInt(date.month1) - 1];
        const nombreMes2 = meses[parseInt(date.month2) - 1];

        let mkt = await useFetch({
            url: api_dashboard,
            data: {

                opc: "getDailyCheck",
                udn: udn,
                category: category,
                anio1: date.year1,
                mes1: date.month1,
                anio2: date.year2,
                mes2: date.month2,
            },
        });

        this.barChart({

            parent: "containerDailyAverageCheck",
            id: "chartDailyCheck",

            title: `📊 Cheque Promedio Diario - ${nombreMes1} ${date.year1} vs ${nombreMes2} ${date.year2}`,
            labels: mkt.labels,
            dataA: mkt.dataB,
            dataB: mkt.dataA,
            yearA: mkt.yearA,
            yearB: mkt.yearB,
            type: "price"
        });
    }

    // Daily Average 

    layoutChequePromedio() {

        $('#filterBarChequePromedio').empty();

        this.createfilterBar({
            parent: `filterBarChequePromedio`,
            data: [
                {
                    opc: "select",
                    id: "category",
                    lbl: "Categoria",
                    class: "col-sm-4",
                    onchange: `dashboard.renderChequePromedioCategory()`,
                },
                {
                    opc: "select",
                    id: "range",
                    lbl: "Rango de consulta",
                    class: "col-sm-4",
                    data: [
                        { id: '3', valor: ' 3 meses' },
                        { id: '6', valor: ' 6 meses' },
                        { id: '9', valor: ' 9 meses' },
                    ],
                    onchange: `dashboard.renderChequePromedioCategory()`,
                },

            ],
        });

        this.renderSelectCategory({
            parent: 'filterBarChequePromedio #category',
            udn: $('#idFilterBar #udn').val(),
            data: clasificacion,
            includeAll: false
        });

        this.renderChequePromedioCategory()


    }

    async renderChequePromedioCategory() {

        let udn = $('#filterBar #udn').val();
        let category = $('#filterBarChequePromedio #category option:selected').text();
        let date = this.getFilterDate();

        const meses = moment.months();
        const nombreMes1 = meses[parseInt(date.month1) - 1];
        const nombreMes2 = meses[parseInt(date.month2) - 1];

        let mkt = await useFetch({
            url: api_dashboard,
            data: {
                opc: "getPromediosDiariosRange",
                udn: udn,
                concepto: category,
                mes: 10,
                anio: 2025,
                rango: $('#filterBarChequePromedio #range').val()

            },
        });



        this.barChart({
            parent: 'barChequePromedio',
            id: 'chartAnual',
            ...mkt.dataset
        });

    }

    // graphigs.
    chequeComparativo(options) {
        const defaults = {
            parent: "containerChequePro",
            id: "chart",
            title: "Comparativa por Categorías",
            class: "border p-4 rounded-xl",
            data: {},
            json: [],
            anioA: new Date().getFullYear(),
            anioB: new Date().getFullYear() - 1,
            onShow: () => { },
        };
        const opts = Object.assign({}, defaults, options);
        const isPrice = opts.type === "price";

        const periodo1 = $('#filterBar #periodo1').val();
        const [anio1, mesNum1] = periodo1.split('-');
        const mes1 = moment().month(parseInt(mesNum1) - 1).format('MMMM');

        const periodo2 = $('#filterBar #periodo2').val();
        const [anio2, mesNum2] = periodo2.split('-');
        const mes2 = moment().month(parseInt(mesNum2) - 1).format('MMMM');

        const container = $("<div>", { class: opts.class });
        const title = $("<h2>", {
            class: "text-lg font-bold mb-2",
            text: `Comparativa: ${mes1} ${anio1} vs ${mes2} ${anio2}`
        });
        const canvasWrapper = $("<div>", {
            class: "w-full",
            css: { height: "400px" }
        });
        const canvas = $("<canvas>", {
            id: opts.id,
            class: "w-full h-full"
        });
        canvasWrapper.append(canvas);
        container.append(title, canvasWrapper);

        $('#' + opts.parent).html(container);

        const ctx = document.getElementById(opts.id).getContext("2d");
        if (window._chq) window._chq.destroy();
        window._chq = new Chart(ctx, {
            type: "bar",
            data: {
                labels: opts.data.labels,
                datasets: [
                    {
                        label: `${mes2} ${anio2}`,
                        data: opts.data.B,
                        backgroundColor: "#8CC63F"
                    },
                    {
                        label: `${mes1} ${anio1}`,
                        data: opts.data.A,
                        backgroundColor: "#103B60"
                    },

                ]
            },
            plugins: [{
                id: 'barValueLabelsComparativa',
                afterDatasetsDraw: function (chart) {
                    const ctx = chart.ctx;
                    ctx.font = "bold 11px sans-serif";
                    ctx.textAlign = "center";
                    ctx.textBaseline = "bottom";

                    chart.data.datasets.forEach(function (dataset, datasetIndex) {
                        const meta = chart.getDatasetMeta(datasetIndex);
                        if (!meta.hidden) {
                            meta.data.forEach(function (bar, index) {
                                const value = dataset.data[index];
                                if (value && value > 0) {
                                    const label = typeof formatPrice === "function" ? formatPrice(value) : value;
                                    ctx.fillStyle = "#000";
                                    ctx.fillText(label, bar.x, bar.y - 5);
                                }
                            });
                        }
                    });
                }
            }],
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        top: 30,
                        bottom: 10,
                        left: 10,
                        right: 10
                    }
                },
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
                        grace: '5%',
                        ticks: {
                            callback: (v) => formatPrice(v)
                        }
                    }
                }
            }
        });
    }

    comparativaIngresosDiarios(options) {
        let periodo1 = $('#filterBar #periodo1').val();
        let [anio1, mesNum1] = periodo1.split('-');
        let mes1 = moment().month(parseInt(mesNum1) - 1).format('MMMM');

        let periodo2 = $('#filterBar #periodo2').val();
        let [anio2, mesNum2] = periodo2.split('-');
        let mes2 = moment().month(parseInt(mesNum2) - 1).format('MMMM');

        this.linearChart({
            parent: "barProductMargen",
            id: "chartLine",
            title: `📈 Comparativa de ventas :  ${mes2} ${anio2}  vs ${mes1} ${anio1} `,
            data: options.data
        });

    }

    ventasPorDiaSemana(data) {
        this.barChart({
            parent: 'ventasDiasSemana',
            title: 'Ventas por Día de Semana',
            ...data

        })
    }

    async comparativaByCategory() {
        let udn = $('#filterBar #udn').val();
        let periodo1 = $('#filterBar #periodo1').val();
        let [anio1, mes1] = periodo1.split('-');
        let periodo2 = $('#filterBar #periodo2').val();
        let [anio2, mes2] = periodo2.split('-');

        let name_category = $('#filterBarProductMargen #category option:selected').text();

        let mkt = await useFetch({
            url: api,
            data: {
                opc: "comparativaByCategory",
                udn: udn,
                category: name_category ,
                anio1: anio1,
                mes1: mes1,
                anio2: anio2,
                mes2: mes2,
            },
        });


        this.linearChart({
            parent: "barProductMargen",
            id: "barProductMargewn",
            title: `📈 Comparativa por Categoría · ${name_category}`,
            data: mkt
        });

    }

    // Top cheque promedio.
    topChequePromedioSemanal(options) {
        const defaults = {
            parent: "containerTopChequePromedio",
            title: "💰 Ranking Cheque Promedio Semanal",
            subtitle: "",
            data: [] // [{dia, cheque_promedio, veces, clientes}]
        };

        const opts = Object.assign({}, defaults, options);
        const container = $("<div>", { class: "border p-4 rounded-xl bg-white " });

        const header = $("<div>", { class: "mb-3" })
            .append($("<h2>", { class: "text-lg font-bold", text: opts.title }))
            .append($("<p>", { class: "text-sm text-gray-500", text: opts.subtitle }));

        const list = $("<div>", { class: "space-y-3" });

        const colores = [
            { bg: "bg-green-100", circle: "bg-green-500 text-white" },
            { bg: "bg-blue-100", circle: "bg-blue-500 text-white" },
            { bg: "bg-purple-100", circle: "bg-purple-500 text-white" },
            { bg: "bg-orange-100", circle: "bg-orange-500 text-white" },
            { bg: "bg-pink-100", circle: "bg-pink-500 text-white" },
            { bg: "bg-yellow-100", circle: "bg-yellow-600 text-white" },
            { bg: "bg-gray-100", circle: "bg-gray-600 text-white" }
        ];

        opts.data.forEach((item, i) => {
            const rank = i + 1;
            const palette = colores[i] || { bg: "bg-white", circle: "bg-gray-300 text-black" };

            const row = $("<div>", {
                class: `flex items-center gap-3 p-3 rounded-lg ${palette.bg}`
            });

            row.append(
                $("<span>", {
                    class: `flex items-center justify-center w-8 h-8 rounded-full font-bold ${palette.circle}`,
                    text: rank
                })
            );

            const content = $("<div>", { class: "flex-1" });
            content.append(
                $("<div>", { class: "flex justify-between" })
                    .append($("<span>", { class: "font-semibold", text: item.dia }))
                    .append($("<span>", { class: "font-bold text-green-600", text: formatPrice(item.cheque_promedio) }))
            );
            content.append(
                $("<div>", { class: "text-sm text-gray-600 flex justify-between" })
                    .append($("<span>", { text: `${item.veces} días con ${item.clientes} clientes` }))
                    .append($("<span>", { class: "italic", text: rank === 1 ? "💎 Mayor cheque" : "" }))
            );

            row.append(content);
            list.append(row);
        });

        container.append(header, list);
        $("#" + opts.parent).html(container);
    }

    async renderClientesPorSemana() {

        let udn = $('#filterBar #udn').val();
        let date = this.getFilterDate();

        const meses = moment.months();
        const nombreMes1 = meses[parseInt(date.month1) - 1];
        const nombreMes2 = meses[parseInt(date.month2) - 1];

        let mkt = await useFetch({
            url: api_dashboard,
            data: {
                opc: "getClientesPorSemana",
                udn: udn,
                anio1: date.year1,
                mes1: date.month1,
                anio2: date.year2,
                mes2: date.month2,
            },
        });

        this.barChart({
            parent: "containerClientesSemana",
            id: "chartClientesSemana",
            title: `👥 Total de Clientes por Día de la Semana - ${nombreMes1} ${date.year1} vs ${nombreMes2} ${date.year2}`,
            labels: mkt.labels,
            dataA: mkt.dataB,
            dataB: mkt.dataA,
            yearA: mkt.yearA,
            yearB: mkt.yearB,
            type: "number"
        });
    }



    // auxiliar.

    getFilterDate() {
        let periodo1 = $('#filterBar #periodo1').val();
        let [year1, month1] = periodo1.split('-');

        let periodo2 = $('#filterBar #periodo2').val();
        let [year2, month2] = periodo2.split('-');

        return {
            year1,
            month1,
            year2,
            month2
        };
    }
}