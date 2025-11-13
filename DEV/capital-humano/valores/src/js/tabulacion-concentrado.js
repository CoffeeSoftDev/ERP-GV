  class Concentrado extends Templates {

    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "Concentrado";
        this.Periods = "";
        this.UDN = "";  
    }

    init() {
        this.render();
    }

    render(options) {
        this.Periods = options.title;
        this.UDN = options.udn;
        this.layout();
        this.filterBar();
       
        // this.ls();
    }

    layout() {

        this.CreateTab({
            parent: 'root',
            json: [
                { id: "tabulation", tab: "Tabulación", icon: "icon-doc", active: true, onClick: () => { this.ls() } },
                { id: "concentrado", tab: "Concentrado de tabulación", icon: "", onClick: () => {   this.lsConcentrado()  } },
            ]
        });



        this.ls();

    }

    filterBar() {
        this.createfilterBar({
            parent: "filterBar" + this.PROJECT_NAME,
            data: [
                {
                    opc: "button",
                    id: "btnExit",
                    class: "col-lg-3 col-sm-3",
                    className: 'w-100',
                    color_btn: "outline-dark",
                    icon: ' icon-left-5',
                    text: "Volver a tabulaciones",
                    onClick: () => app.render()
                },
               
            ]
        });
    }

    ls() {

        this.createTable({
            parent     : "container-tabulation",

            idFilterBar: "filterBar" + this.PROJECT_NAME,
            data       : { opc: "list", id: idTabulation },
            conf       : { datatable: true, pag: 10 },
            coffeesoft: true,
            attr: {

                id     : "tbTabulations" + this.PROJECT_NAME,
                title: `Lista de empleados  (${this.Periods})`,
                subtitle: 'Calificaciones de los empleados en los 4 rubros de evaluación',
                center : [1, 2, 3, 4, 5, 6, 7],
                right  : [7],
                extends: true
            }
        });

    }

    lsConcentrado() {

        this.createTable({
            parent: "container-concentrado" ,
            idFilterBar: "filterBar" + this.PROJECT_NAME,
            data: { opc: "listConcentrado", id: idTabulation },
            conf: { datatable: true, pag: 10 },
            coffeesoft: true,

            attr: {
                id: "tbConcentrado" + this.PROJECT_NAME,
                center: [ 2, 3, 4, 5, 6, 7],
                title: `Evaluación de desempeño`,

                right: [7],
                extends: true,
                
            }
        });

        $("#container-concentrado").append(`
        <div class="mt-6">
            <h4 class="text-sm font-semibold mb-2 text-gray-700">Leyenda de desempeño:</h4>
            <table class="table-auto text-center w-full border border-gray-300 text-xs">
            <thead>
                <tr>
                <th class="bg-red-600 text-white px-2 py-1 border border-gray-300">BD</th>
                <th class="bg-amber-400 text-black px-2 py-1 border border-gray-300">DA</th>
                <th class="bg-yellow-300 text-black px-2 py-1 border border-gray-300">DE</th>
                <th class="bg-blue-300 text-black px-2 py-1 border border-gray-300">AD</th>
                <th class="bg-green-400 text-white px-2 py-1 border border-gray-300">DEX</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                <td class="px-2 py-1 border border-gray-300">1 – 4</td>
                <td class="px-2 py-1 border border-gray-300">4.01 – 4.17</td>
                <td class="px-2 py-1 border border-gray-300">4.18 – 4.50</td>
                <td class="px-2 py-1 border border-gray-300">4.51 – 4.67</td>
                <td class="px-2 py-1 border border-gray-300">4.68 – 5</td>
                </tr>
            </tbody>
            </table>
        </div>
        `);

    }

    async edit(id) {

        let request = await useFetch({
            url: this._link,
            data: { opc: 'get', id: id }
        });

        this.createModalForm({
            id: 'formModalEdit',
            data: { opc: 'edit', id: id },
            bootbox: {
                title: '<strong>Editar Calificación</strong>'
            },
            autofill: request.data,  // Datos obtenidos para autocompletar
            json: [
                { opc: 'label', text: `Evaluar a ${request.data.nombre || 'Colaborador'}`, class: 'col-12 text-lg  mb-2' },
                { opc: 'input', lbl: 'Trabajo en Equipo', id: 'te', class: 'col-6 mb-2', tipo: 'cifra', required: true },
                { opc: 'input', lbl: 'Pasión por el Servicio', id: 'ps', class: 'col-6 mb-2', tipo: 'cifra', required: true },
                { opc: 'input', lbl: 'Actitud Positiva', id: 'ap', class: 'col-6 mb-2', tipo: 'cifra', required: true },
                { opc: 'input', lbl: 'Liderazgo', id: 'pr', class: 'col-6 mb-2', tipo: 'cifra', required: true },
                {
                    opc: 'input-group',
                    id: 'calf',
                    icon: 'icon-user',
                    class: 'col-12 mb-2',
                    label: 'Calificación',
                    icon: 'icon-pencil',
                    tipo: 'cifra',
                    required: true
                },
            ],
            success: (response) => {
                if (response.status == 200) {
                    alert({ icon: "success", text: response.message });
                    this.ls(); // recargar la tabla
                } else {
                    alert({ icon: "error", text: response.message });
                }
            }
        });

    }

    getCalificationOptions() {
        return [
            { id: 1, valor: "1 - Muy Malo" },
            { id: 2, valor: "2 - Malo" },
            { id: 3, valor: "3 - Bueno" },
            { id: 4, valor: "4 - Muy Bueno" },
            { id: 5, valor: "5 - Excelente" },
        ];
    }

    CreateTab(options) {
        const defaults = {
            parent: "root",
            id: "tabComponent",
            class: "bg-gray-100 rounded-lg flex p-1 w-full overflow-hidden",
            renderContainer: true,
            json: [
                { id: "default", tab: "Tab 1", icon: "", active: true, onClick: () => { } },
            ]
        };

        const opts = Object.assign({}, defaults, options);

        const container = $("<div>", {
            id: opts.id,
            class: opts.class
        });

        opts.json.forEach(tab => {
            const isActive = tab.active || false;

            const tabButton = $("<button>", {
                id: `tab-${tab.id}`,
                html: tab.icon ? `<i class='${tab.icon} mr-2'></i>${tab.tab}` : tab.tab,
                class: `flex-1 py-2 text-sm font-semibold text-center rounded-lg transition duration-150 ease-in-out
                ${isActive ? "bg-white text-black" : "text-gray-500 hover:bg-white"}`,
                click: () => {
                    // reset all tabs
                    $(`#${opts.id} button`).removeClass("bg-white text-black").addClass("text-gray-500");
                    tabButton.addClass("bg-white text-black").removeClass("text-gray-500");

                    // manejar renderizado
                    if (opts.renderContainer) {
                        $(`#content-${opts.id} > div`).addClass("hidden");
                        $(`#container-${tab.id}`).removeClass("hidden");
                    }

                    if (typeof tab.onClick === "function") tab.onClick(tab.id);
                }
            });

            container.append(tabButton);
        });

        $(`#${opts.parent}`).html(container);

        // Crear contenedor de contenido si se activa renderContainer
        if (opts.renderContainer) {
            const contentContainer = $("<div>", {
                id: `content-${opts.id}`,
                class: "mt-2"
            });

            opts.json.forEach(tab => {
                const contentView = $("<div>", {
                    id: `container-${tab.id}`,
                    class: `hidden border p-3 h-full rounded-lg`,
                    html: tab.content || ""
                });
                contentContainer.append(contentView);
            });

            $(`#${opts.parent}`).append(contentContainer);

            // mostrar el tab activo
            const activeTab = opts.json.find(t => t.active);
            if (activeTab) {
                $(`#container-${activeTab.id}`).removeClass("hidden");
            }
        }
    }
}