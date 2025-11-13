let app;
const apiApp = 'ctrl/ctrl-marketing.php';
$(async () => {

    let cookies = getCookies();

    if(cookies.IDU == 75){
        const base = window.location.origin + '/ERP24';
        window.location.href = `${base}/kpi/marketing/pedidos/index.php`; 
    } else {
        app = new App(apiApp, "root");
        app.init();
    }
});

class App extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "orders";
    }

    init() {
        this.render();
    }

    render() {
        this.renderModules();

    }

    // 🔷 Panel de módulos
    async renderModules() {
        let dataPermissions = await useFetch({ url: apiApp, data: { opc: "getPermissions" } });

        $("#root").empty();

        this.moduleCard({
            parent: "root",
            theme: "light",
            title: "Panel principal",
            subtitle: "Selecciona un módulo para comenzar 🪄",
            json: [
                {
                    titulo: "Modulo de Ventas",
                    descripcion: "Consulta las métricas de ventas",
                    icon: "icon icon-dollar",
                    color: "bg-green-200",
                    textColor: "text-green-600",
                    borderColor: "border-green-600",
                    onClick: () => this.redirectToModules('/kpi/marketing/ventas/index.php'),
                },
                {
                    titulo: "Modulo de Costsys",
                    descripcion: "Accede a reportes de costos",
                    icon: "icon-food",
                    color: "bg-orange-200",
                    textColor: "text-orange-600",
                    borderColor: "orange-600",
                    onClick: () => this.redirectToModules('/kpi/marketing/costsys/index.php'),
                },
                {
                    titulo: "Modulo de Pedidos",
                    descripcion: "Revisa actividad de pedidos",
                    icon: "icon-motorcycle",
                    color: "bg-yellow-200",
                    textColor: "text-yellow-600",
                    onClick: () => this.redirectToModules('/kpi/marketing/pedidos/index.php'),

                },
                {
                    titulo: "Módulo de Clientes",
                    descripcion: "Gestiona la información de clientes",
                    icon: "icon-users",
                    color: "bg-blue-200",
                    textColor: "text-blue-600",
                    onClick: () => this.redirectToModules('/kpi/marketing/clientes/index.php'),
                },

                {
                    titulo: "Modulo Redes Sociales",
                    descripcion: "Revisa actividad social",
                    icon: "icon-instagram",
                    color: "bg-pink-200",
                    textColor: "text-pink-600",
                    onClick: () => this.redirectToModules('/kpi/marketing/redes_sociales/index.php'),
                },
                {
                    titulo: "Módulo de Campañas",
                    descripcion: "Revisa campañas y anuncios",
                    icon: "icon-megaphone",
                    color: "bg-red-200",
                    textColor: "text-red-600",
                    onClick: () => this.redirectToModules('/kpi/marketing/anuncios/index.php'),
                },
            ]
        });
    }

    // PASAR A ERP24 EN SU MOMENTO
    redirectToModules(url) {
        const base = window.location.origin + '/ERP24';
        window.location.href = `${base}${url}`;
    }
    
    // 🔧 COMPONENTES
    headerBar(options) {
        const defaults = {
            parent: "root",
            title: "Default Title",
            subtitle: "Default subtitle",
            onClick: null,
        };

        const opts = Object.assign({}, defaults, options);

        const container = $(`
            <div class="flex justify-between items-center px-2 pt-3 pb-3">
                <div>
                    <h2 class="text-2xl font-semibold">${opts.title}</h2>
                    <p class="text-gray-400">${opts.subtitle}</p>
                </div>
                <div>
                    <button class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded transition flex items-center">
                        <i class="icon-home mr-2"></i>Inicio
                    </button>
                </div>
            </div>
        `);

        container.find("button").on("click", () => {
            if (typeof opts.onClick === "function") {
                opts.onClick();
            }
        });

        $(`#${opts.parent}`).append(container);
    }

    moduleCard(options) {
        const defaults = {
            parent: "cardInicioContainer",
            title: "",
            subtitle: "",
            theme: "light",
            json: [],
        };

        const opts = Object.assign({}, defaults, options);
        const isDark = opts.theme === "dark";

        const colors = {
            cardBg: isDark ? "bg-[#2C3544]" : "",
            titleColor: isDark ? "text-white" : "text-gray-800",
            subtitleColor: isDark ? "text-gray-400" : "text-gray-600",
            badgeColor: isDark ? "bg-blue-800 text-white" : "bg-blue-100 text-blue-800"
        };

        const titleContainer = $("<div>", { class: "w-full px-4 mt-2 mb-2" });
        const title = $("<h1>", { class: "text-2xl font-bold text-gray-900 mb-2", text: opts.title });
        const subtitle = $("<p>", { class: colors.subtitleColor + " ", text: opts.subtitle });
        titleContainer.append(title, subtitle);

        const container = $("<div>", {
            class: "w-full grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 p-4",
        });


        opts.json.forEach((item) => {
            let iconContent = item.icon
                ? `<div class="w-14 h-14 flex items-center justify-center ${item.color} ${item.textColor} rounded-lg text-xl mb-4 group-hover:bg-opacity-80 transition-all"><i class="${item.icon}"></i></div>`
                : item.imagen
                    ? `<img class="w-14 h-14 rounded-lg mb-3" src="${item.imagen}" alt="${item.titulo}">`
                    : "";

            const badge = item.badge
                ? `<span class="px-2 py-0.5 rounded-full text-xs font-medium ${colors.badgeColor}">${item.badge}</span>`
                : "";

            const card = $(`

                       <div class="group relative h-[250px] ${colors.cardBg} rounded-xl shadow-md
                        overflow-hidden p-4 flex flex-col justify-between cursor-pointer
                        border border-transparent  hover:scale-[1.05] hover:border-blue-700
                       transition-transform duration-300 ease-in-out transform font-[Poppins]">

                        <div class="flex justify-between items-start">
                            ${iconContent}
                            ${badge}
                        </div>
                        <div class="flex-grow flex flex-col justify-center ">
                            <h2 class="text-lg font-bold ${colors.titleColor}">${item.titulo}</h2>
                            ${item.descripcion ? `<p class="${colors.subtitleColor} text-sm mt-1">${item.descripcion}</p>` : ""}
                        </div>
                        <div class="mt-4 flex items-center ${item.textColor} text-[12px]">
                            <span>Acceder</span>
                            <i class="icon-right-1 ml-2 text-xs transition-transform group-hover:translate-x-2"></i>
                        </div>
                    </div>
                `).click(function () {


                // Ejecutar acciones
                if (item.enlace) window.location.href = item.enlace;
                if (item.href) window.location.href = item.href;
                if (item.onClick) item.onClick();
            });

            container.append(card);

        });

        const div = $('<div>', {
            class: 'lg:px-8 mt-5'
        });
        div.append(titleContainer, container);

        $(`#${opts.parent}`).empty().append(div);
    }
}
