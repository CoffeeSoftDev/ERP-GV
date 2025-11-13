let api_costsys = 'https://erp-varoch.com/ERP24/costsys/ctrl/ctrl-menu.php';
let api_costo = 'https://erp-varoch.com/ERP24/costsys/ctrl/ctrl-costo-potencial.php';
let api_kpi_costsys = 'ctrl/ctrl-kpi-costsys.php';


let api = 'https://erp-varoch.com/ERP24/kpi/marketing/ventas/ctrl/ctrl-ingresos.php';
let udn, lsudn, clasification, mkt;
let app, costsys, dashboard;

$(async () => {
    const data = await useFetch({ url: api, data: { opc: "init" } });
    udn = data.udn;
    lsudn = data.lsudn;
    clasification = data.clasification;

    // ** KPI Costsys **

    app       = new App(api_costsys, 'root');
    costsys   = new Costsys(api_costsys, 'root');
    dashboard = new AnalyticsCostsys(api_kpi_costsys, 'root');

    app.render()

});


class App extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "orders";
    }


    render() {
        this.layout();
        dashboard.render();
        costsys.renderCostoPotencial()
        costsys.renderDesplazamiento()
        costsys.renderVentas()
    }

    layout() {
        this.primaryLayout({
            parent: "root",
            id: this.PROJECT_NAME,
            class: "w-full",
            card: {
                filterBar: { class: "w-full", id: "filterCostsys" },
                container: { class: "w-full h-full", id: "containerCostsys" },
            },
        });

        this.tabLayout({
            parent: `containerCostsys`,
            id: "tabsCostsSys",
            type: "short",
            json: [
                { id: "Dashboard", tab: "Dashboard", active: true, onClick: () => { dashboard.renderDashboard() } },
                { id: "costoPotencial", tab: "Costo Potencial" },
                { id: "desplazamiento", tab: "Desplazamiento Mensual" },
                { id: "ventas", tab: "Ventas Mensual" }
            ]
        });

        // $('#content-tabsCostsSys').removeClass('h-screen');

        this.headerBar({
            parent: `filterCostsys`,
            title: "📊 Panel CostSys",
            subtitle: "Consulta y visualiza ventas, costos y desplazamientos.",
            onClick: () => app.redirectToHome(),
        });

  


        $('#content-tabsVentas').removeClass('h-screen');
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

class Costsys extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "costsys";
    }
    

    renderCostoPotencial(){
        this.layout()
        this.filterBarCostoPotencial()
        this.lsCostoPotencial()
    }

    layout() {
        this.primaryLayout({
            parent: `container-costoPotencial`,
            id: this.PROJECT_NAME,
            card: {
                filterBar: { class: 'w-full   ', id: `filterBarCostoPotencial` },
                container: { class: 'w-full mt-2', id: `containerCostoPotencial` }
            }
        });
    }

    filterBarCostoPotencial() {
        this.createfilterBar({
            parent: `filterBarCostoPotencial`,
            data: [
                {
                    opc: "select",
                    id: "UDNs",
                    lbl: "Sucursal",
                    class: "col-sm-2",
                    data: udn
                    // data: [
                    //     { id: 5, valor: 'Sonoras Meat' }
                    // ]
                },
                {
                    opc: "select",
                    id: "Clasificacion",
                    lbl: "Categoria",
                    class: "col-sm-2",
                    data: [
                        { id: 2, valor: "Cortes" },
                        { id: 1, valor: "Bebidas" },
                        { id: 3, valor: "Guarniciones" },
                        { id: 24, valor: "Aditamentos para asar" },
                    ]
                },
                {
                    opc: "select",
                    id: "Mes",
                    lbl: "mes",
                    class: "col-sm-3",
                    data: Array.from({ length: 12 }, (_, i) => {
                        const month = moment().month(i); // i = 0 to 11
                        return {
                            id: month.format("MM"),
                            valor: month.format("MMMM") // "Enero", "Febrero", ...
                        };
                    }),
                },

                {
                    opc: "select",
                    id: "Anio",
                    lbl: "año",
                    class: "col-sm-3",
                    data: Array.from({ length: 2 }, (_, i) => {
                        const year = moment().year() - i;
                        return { id: year, valor: year.toString() };
                    }),
                },



                {
                    opc: "button",
                    id: "btnBuscar",
                    class: "col-sm-2",
                    text: "Buscar",
                    className: "btn-primary w-100",
                    onClick: () => this.lsCostoPotencial()
                }
            ]
        });

        // render
        setTimeout(() => {
            $("#filterBarCostoPotencial #Mes").val(moment().format("MM"));
            $("#filterBarCostoPotencial #Anio").val(moment().year());
        }, 50);
    }

    lsCostoPotencial() {

        this.createTable({
            parent: `containerCostoPotencial`,
            idFilterBar: `filterBarCostoPotencial`,
            data: {
                opc: "TypeReport",
                type: 1,
                name_month: $('#Mes option:selected').text()

            },
            conf: { datatable: true, pag: 120 },
            coffeesoft: true,
            attr: {
                id: `tb${this.PROJECT_NAME}`,
                theme: "corporativo",
                title: 'Sistema de costos',
                right: [3, 4, 5, 6, 7, 8, 9],
                color_group: 'bg-gray-300',
                center: [1],
                extends: true,
                collapse: true
            },
        });
    }

    // tab Desplazamiento por Mes

    renderDesplazamiento(){
        this.layoutDesplazamiento()
        this.filterBarDesplazamiento()
        this.lsDesplazamiento()
    }

    layoutDesplazamiento() {

        this.primaryLayout({
            parent: `container-desplazamiento`,
            id: this.PROJECT_NAME,
            card: {
                filterBar: { class: 'w-full   ', id: `filterBarDesplazamiento` },
                container: { class: 'w-full ', id: `containerDesplazamiento` }
            }
        });
    }

    filterBarDesplazamiento() {
        this.createfilterBar({
            parent: `filterBarDesplazamiento`,
            data: [
                {
                    opc: "select",
                    id: "UDNs",
                    lbl: "UDN",
                    class: "col-sm-3",
                    data: [{ id: 4, valor: 'Baos' }]
                },
                {
                    opc: "select",
                    id: "Clasificacion",
                    lbl: "Clasificación",
                    class: "col-sm-3",
                    data: [
                        { id: 13, valor: "ALIMENTOS" },
                        { id: "BEBIDAS", valor: "BEBIDAS" }
                    ]
                },
                {
                    opc: "select",
                    id: "Anio",
                    lbl: "Año",
                    class: "col-sm-2",
                    data: Array.from({ length: 2 }, (_, i) => {
                        const year = moment().year() - i;
                        return { id: year, valor: year.toString() };
                    }),
                },
                {
                    opc: "select",
                    id: "Mes",
                    lbl: "Mes",
                    class: "col-sm-2",
                    data: Array.from({ length: 12 }, (_, i) => {
                        const month = moment().month(i); // i = 0 to 11
                        return {
                            id: month.format("MM"),
                            valor: month.format("MMMM") // "Enero", "Febrero", ...
                        };
                    }),
                },
                {
                    opc: "button",
                    id: "btnBuscar",
                    class: "col-sm-2",
                    text: "Buscar",
                    className: "btn-primary w-100",
                    onClick: () => this.lsDesplazamiento()
                }
            ]
        });

        setTimeout(() => {
            $("#filterBarDesplazamiento #Mes").val(moment().format("MM"));
            $("#filterBarDesplazamiento #Anio").val(moment().year());
        }, 50);
    }

    lsDesplazamiento() {

        this.createTable({
            parent: `containerDesplazamiento`,
            idFilterBar: `filterBarDesplazamiento`,
            data: {
                opc: "TypeReport",
                type: 2,
                name_month: $('#Mes option:selected').text()

            },
            conf: { datatable: false, pag: 10 },
            coffeesoft: true,
            attr: {
                id: `tb${this.PROJECT_NAME}`,
                theme: "corporativo",
                title: 'Histórico de desplazamiento por producto',
                subtitle: 'Visualización detallada de los movimientos mensuales por producto, organizados por categoría y período de tiempo para análisis comparativo',
                right: [3, 4, 5, 6, 7, 8, 9],
                color_group: 'bg-gray-300',
                center: [1],
                extends: true,
                collapse: true
            },
        });
    }

    // tab Ventas por mes

    renderVentas(){
        this.layoutVentas()
        this.filterBarVentas()
        this.lsVentas()
    }

    layoutVentas() {
        this.primaryLayout({
            parent: `container-ventas`,
            id: this.PROJECT_NAME,
            card: {
                filterBar: { class: 'w-full   ', id: `filterBarVentas` },
                container: { class: 'w-full mt-2', id: `containerVentas` }
            }
        });
    }

    filterBarVentas() {
        this.createfilterBar({
            parent: `filterBarVentas`,
            data: [
                {
                    opc: "select",
                    id: "UDNs",
                    lbl: "UDN",
                    class: "col-sm-3",
                    data: [{ id: 4, valor: 'Baos' }]
                },
                {
                    opc: "select",
                    id: "Clasificacion",
                    lbl: "Clasificación",
                    class: "col-sm-3",
                    data: [
                        { id: 13, valor: "ALIMENTOS" },
                        { id: "BEBIDAS", valor: "BEBIDAS" }
                    ]
                },
                {
                    opc: "select",
                    id: "Anio",
                    lbl: "Año",
                    class: "col-sm-2",
                    data: Array.from({ length: 2 }, (_, i) => {
                        const year = moment().year() - i;
                        return { id: year, valor: year.toString() };
                    }),
                },
                {
                    opc: "select",
                    id: "Mes",
                    lbl: "Mes",
                    class: "col-sm-2",
                    data: Array.from({ length: 12 }, (_, i) => {
                        const month = moment().month(i); // i = 0 to 11
                        return {
                            id: month.format("MM"),
                            valor: month.format("MMMM") // "Enero", "Febrero", ...
                        };
                    }),
                },
                {
                    opc: "button",
                    id: "btnBuscar",
                    class: "col-sm-2",
                    text: "Buscar",
                    className: "btn-primary w-100",
                    onClick: () => this.lsVentas()
                }
            ]
        });

        setTimeout(() => {
            $("#filterBarVentas #Mes").val(moment().format("MM"));
            $("#filterBarVentas #Anio").val(moment().year());
        }, 50);
    }

    lsVentas() {

        this.createTable({
            parent: `containerVentas`,
            idFilterBar: `filterBarVentas`,
            data: {
                opc: "TypeReport",
                type: 3,
                name_month: $('#Mes option:selected').text()

            },
            conf: { datatable: false, pag: 10 },
            coffeesoft: true,
            attr: {
                id: `tb${this.PROJECT_NAME}`,
                theme: "corporativo",
                title: 'Histórico de ventas por producto',
                subtitle: 'Visualización detallada de los ventas mensuales por producto, organizados por categoría y período de tiempo para análisis comparativo',
                right: [3, 4, 5, 6, 7, 8, 9],
                color_group: 'bg-gray-300',
                center: [1],
                extends: true,
                collapse: true
            },
        });


    }

}

class AnalyticsCostsys extends Templates {

    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "costsys";
    }

    render() {
        this.layoutDashboard();
        this.filterBarDashboard();
        // this.renderDashboard()
    }

    layoutDashboard() {

        this.primaryLayout({
            parent: `container-Dashboard`,
            id: 'dashboard',
            card: {
                filterBar: { class: 'w-full ', id: `container-filterBar` },
                container: { class: 'w-full   h-full mt-2  ', id: `container-dashboard` }
            }
        });

        $("#container-filterBar").prepend(`
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-[#103B60]">Dashboard de Ventas</h1>
                    <p class="text-sm text-gray-600 mt-1 ">Análisis mensual de productos vendidos y con margen</p>
                </div>
            </div>

            <div class="w-full mt-2" id="filterBarDashboard"></div>

        `);



    }

    filterBarDashboard() {

        this.createfilterBar({
            parent: `filterBarDashboard`,
            data: [
                {
                    opc: "select",
                    id: "UDNs",
                    lbl: "Sucursal",
                    class: "col-sm-3",
                    onchange: `dashboard.lsClasificacion()`,
                    data: udn,
                    // data: [{ id: 5, valor: 'Sonoras Meat' }]
                },
                {
                    opc: "select",
                    id: "Clasificacion",
                    lbl: "Categoria",
                    class: "col-sm-3",
                    onchange: `dashboard.renderDashboard()`,
                    // data: clasification
                    // data: [
                    //     { id: 2, valor: "Cortes" },
                    //     { id: 1, valor: "Bebidas" },
                    //     { id: 3, valor: "Guarniciones" },
                    //     { id: 24, valor: "Aditamentos para asar" },
                    // ]
                },
                {
                    opc: "select",
                    id: "Mes",
                    lbl: "mes",
                    class: "col-sm-3",
                    onchange: `dashboard.renderDashboard()`,
                    data: Array.from({ length: 12 }, (_, i) => {
                        const month = moment().month(i); // i = 0 to 11
                        return {
                            id: month.format("MM"),
                            valor: month.format("MMMM") // "Enero", "Febrero", ...
                        };
                    }),
                },

                {
                    opc: "select",
                    id: "Anio",
                    lbl: "año",
                    class: "col-sm-3",
                    onchange: `dashboard.renderDashboard()`,
                    data: Array.from({ length: 2 }, (_, i) => {
                        const year = moment().year() - i;
                        return { id: year, valor: year.toString() };
                    }),
                },


            ],
        });

        const currentMonth = moment().month() + 1; // Mes actual (1-12)

        setTimeout(() => {
            $(`#filterBarDashboard #mes`).val(currentMonth).trigger("change");
            this.lsClasificacion()

        }, 1000);


    }

    lsClasificacion() {
        let clasificacion = clasification.filter(
            (json) => json.udn === $("#UDNs").val()
        );
        $("#Clasificacion").option_select({ data: clasificacion });
        this.renderDashboard()
    }


    // Dashboard
    async renderDashboard() {

        let udn = $('#filterBarDashboard #UDNs').val();
        let month = $('#filterBarDashboard #Mes').val();
        let year = $('#filterBarDashboard #Anio').val();
        let clasificacion = $('#filterBarDashboard #Clasificacion').val();



        let mkt = await useFetch({
            url: api_costsys,
            data: {
                opc: "apiCostoPotencial",
                UDNs: udn,
                Mes: month,
                Anio: year,
                Clasificacion: clasificacion,
                type: 1
            },
        });



        $("#container-dashboard").html(`

            <div class=" font-sans  ">

                <!-- KPIs -->
                <div class="my-3" id="containerKpi"></div>

                <!-- Resto del dashboard (gráficas, tabla, etc.) -->


                <div class="grid lg:grid-cols-2 gap-4 mb-2">

                    <div class="bg-white rounded-lg shadow-sm p-3 max-h-[90vh]" id="barProductSales"></div>
                    <div class="bg-white rounded-lg shadow-sm p-3 max-h-[90vh]" id="barProductMargen"></div>

                    <div class="bg-white rounded-lg shadow-sm p-3 max-h-[90vh]" id="containerPromotionSales"></div>
                    <div class="bg-white rounded-lg shadow-sm p-3 max-h-[90vh]" id="containerPromotionMargen"></div>

                </div>

                <div class="bg-white p-3 w-full rounded-xl shadow overflow-auto" id="containerAttentionProducts"></div>
            <div>
        `);



        //cards
        this.showCards(mkt.tablero);

        // Graficas
        this.barProductSales(mkt.chartIngreso)
        this.barProductMargen(mkt.chartMargen)

        this.lsPromotionBySales(mkt.topIngreso)
        this.lsPromotionByMargin(mkt.topMargen)
        this.lsAttentionProducts(mkt.productosSinVenta)

    }

    // Cards.

    showCards(data) {
        this.infoCard({
            parent: "containerKpi",
            theme: "light",
            json: [
                {
                    id: "kpiDia",
                    title: "Total de ingresos",
                    data: {
                        value: formatPrice(data.ventaEstimadaReal),
                        description: "",
                        color: "text-green-700",
                    },
                },

                {
                    title: "Margen de contribución",
                    data: {
                        value: formatPrice(data.mcEstimadoReal),
                        description: "",
                        color: "text-blue-700",
                    },
                },
                {
                    title: "Costo de producción",
                    data: {
                        value: formatPrice(data.costoEstimadoReal),
                        description: "",
                        color: "text-orange-700",
                    },
                },
                {
                    id: "kpiCheque",
                    title: "Productos sin venta",
                    data: {
                        value: data.productosSinVenta,
                        description: "",
                        color: "text-red-700",
                    },
                },
            ],
        });

    }

    // Graficas.
    barProductSales(data) {

        // this.primaryLayout({
        //     parent: `barProductSales`,
        //     id: this.PROJECT_NAME,
        //     card: {
        //         filterBar: { class: 'w-full   ', id: `filterBarProductSales` },
        //         container: { class: 'w-full ', id: `containerProductSales` }
        //     }
        // });

        // this.createfilterBar({
        //     parent: 'filterBarProductSales',
        //     data: [
        //         {
        //             opc: "select",
        //             id: "category",
        //             class: "col-md-6",
        //             lbl: "Categoria",
        //             data: [
        //                 { id: "2025", valor: "2025" },
        //                 { id: "2024", valor: "2024" }
        //             ],
        //             onchange: 'analitycs_costsys.barProductSales()'
        //         }
        //     ]
        // });

        this.barChart({
            parent: "barProductSales",
            id: "chartIngresos",
            title: "TOP 10 - Productos más vendidos",

            data: data
        });

    }

    barProductMargen(data) {

        // this.primaryLayout({
        //     parent: `barProductMargen`,
        //     id: this.PROJECT_NAME,
        //     card: {
        //         filterBar: { class: 'w-full line  ', id: `filterBaProductMargen` },
        //         container: { class: 'w-full ', id: `containerProductMargen` }
        //     }
        // });

        // this.createfilterBar({
        //     parent: 'filterBaProductMargen',
        //     data: [
        //         {
        //             opc: "select",
        //             id: "category",
        //             class: "col-md-6",
        //             lbl: "Categoria",
        //             data: [
        //                 { id: "2025", valor: "2025" },
        //                 { id: "2024", valor: "2024" }
        //             ],
        //             onchange: 'analitycs_costsys.barProductSales()'
        //         }
        //     ]
        // });


        this.barChart({
            parent: "barProductMargen",
            id: "chartMargen",
            title: "TOP 10 - Productos con Mayor Ganancia",
            type: "dollar",
            data: data
        });
    }


    // Lista de estrategias.

    lsPromotionBySales(data) {
        this.createCoffeTable({
            parent: "containerPromotionSales",
            id: "tbPromotion",
            theme: "corporativo",
            title: "💰 Productos con mayor ingreso",
            data: data,
            center: [1, 4],
            right: [3]
        });

        simple_data_table('#tbPromotion', 10);

    }

    lsPromotionByMargin(data) {
        this.createCoffeTable({
            parent: "containerPromotionMargen",
            id: "tblPromotionMargen",
            title: "🎯 Productos a Promocionar por Margen",
            theme: "corporativo",
            center: [1, 4],
            right: [3],
            data: data
        });

        // 🔹 Insertar span al inicio del contenedor
        $("#containerPromotionMargen").prepend(`
            <span id="avgMargen" class="block text-sm text-gray-600 text-end mb-1">
                <strong>  Promedio MC </strong> : ${formatPrice(data.promedio) ?? ''}
            </span>
            <span id="avgDesplazamiento" class="block text-sm text-gray-600 text-end mb-1">
                <strong>  Promedio Desplazamiento </strong> : ${data.desplazamiento ?? ''}
            </span>
        `);

        simple_data_table('#tblPromotionMargen', 10);

        $('.table-responsive').children('div').removeClass('py-3');
    }

    lsAttentionProducts(data) {

        this.createCoffeTable({
            parent: "containerAttentionProducts",
            id: "tblProductosAtencion",
            title: "⚠️ Productos que Requieren Atención (No se venden)",
            theme: "corporativo",
            data: data
        });

        simple_data_table('#tblProductosAtencion', 15);
    }

    // Components.
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


    barChart(options) {
        const defaults = {
            parent: "containerChequePro",
            id: "chart",
            title: "",
            class: "border p-3 rounded-xl",
            data: {},
            json: [],
            type: "number", // 💲 Nuevo: modo de formato ("number" o "dollar")
            onShow: () => { },
        };

        const opts = Object.assign({}, defaults, options);

        // 📦 Contenedor principal
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

        // 🧹 Limpieza de gráficos previos
        if (!window._charts) window._charts = {};
        if (window._charts[opts.id]) {
            window._charts[opts.id].destroy();
        }

        // 🧮 Limpieza y formateo de datos
        if (opts.data && opts.data.datasets) {
            opts.data.datasets.forEach(dataset => {
                dataset.data = dataset.data.map(value => {
                    // 🔹 Elimina símbolos no numéricos excepto punto o signo negativo
                    if (typeof value === "string") {
                        value = value.replace(/[^0-9.-]/g, "");
                    }

                    const num = parseFloat(value);
                    return isNaN(num) ? 0 : parseFloat(num.toFixed(2));
                });
            });
        }

        // 💵 Función de formateo según tipo
        const formatValue = (v) => {
            if (opts.type === "dollar") {
                return `$${v.toFixed(2)}`;
            } else {
                return v.toFixed(2);
            }
        };

        // 📊 Render del gráfico
        window._charts[opts.id] = new Chart(ctx, {
            type: "bar",
            data: opts.data,
            options: {
                responsive: true,
                aspectRatio: 3,
                animation: { onComplete: function () { } },
                plugins: {
                    legend: { position: "bottom" },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => {
                                const val = ctx.parsed.y || 0;
                                return `${ctx.dataset.label}: ${formatValue(val)}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (v) => formatValue(v)
                        }
                    }
                }
            }
        });
    }




    // Json temporal.


    getIngresosData() {
        return {
            labels: [
                "Pastel de temporada",
                "Pastel de cacahuate",
                "Tarta a la vizcaína",
                "Huevos al gusto",
                "Tiramisú",
                "Brownie artesanal",
                "Cheesecake de frutos rojos",
                "Ensalada gourmet",
                "Croissant relleno",
                "Pan de plátano"
            ],
            datasets: [{
                label: "Ingresos",
                data: [34000, 5000, 5000, 2000, 2000, 4200, 8800, 3100, 5600, 2700],
                backgroundColor: [
                    "#8CC63F", // verde acción GV
                    "#103B60", // azul oscuro GV
                    "#1E88E5", // azul medio
                    "#26A69A", // turquesa
                    "#F0B200", // dorado
                    "#FF7043", // naranja suave
                    "#AB47BC", // morado
                    "#29B6F6", // celeste
                    "#66BB6A", // verde medio
                    "#EF5350"  // rojo suave
                ],
                borderRadius: 6
            }]
        };
    }

    getMargenData() {
        return {
            labels: [
                "Croque Madame",
                "Croque Monsieur",
                "Sándwich Queso Panela",
                "Desayuno Huevos al gusto",
                "Yogurt con mermelada"
            ],
            datasets: [{
                label: "Margen",
                data: [180, 160, 130, 80, 40],
                backgroundColor: "#1E88E5", // azul medio para diferenciar
                borderRadius: 6
            }]
        };
    }



}
