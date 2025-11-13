class Calificacion extends Templates {

    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "Tabulation";
        this.Periods = "";
    }

    init() {
        this.render();
    }

    render(options) {

        this.layout();
        this.filterBar();
        this.Periods = options.title;
        this.ls();
    }

    layout() {
        this.primaryLayout({
            parent: "root",
            id: this.PROJECT_NAME,
            class:'space-y-2',
           
        });
    }

    filterBar() {
        this.createfilterBar({
            parent: "filterBar" + this.PROJECT_NAME,
            data: [
                {
                    opc      : "button",
                    id       : "btnExit",
                    class    : "col-sm-2",
                    className: 'w-100',
                    color_btn: "outline-dark",
                    icon     : ' icon-left-5',
                    text     : "Regresar",
                    onClick  : () => this.getout()
                },
                {
                    opc      : "button",
                    id       : "btnFinish",
                    class    : "col-sm-2",
                    className: 'w-100',
                    color_btn: "success",
                    text     : "Finalizar ",
                    onClick  : () => this.finishedTabulation()
                },
                {
                    opc: "button",
                    id: "btnFinish",
                    class: "col-sm-2",
                    className: 'w-100',
                    color_btn: "primary",
                    text: "Consultar",
                    onClick: () => this.viewRegistros()
                }
            ]
        });
    }

    getout(){
        alert({icon:'question',title:`¿Deseas salir de la tabulación?`}).then((result)=>{
            if(result.isConfirmed)
                    app.render();
        });
    }

    ls() {

         
        this.createTable({

            parent     : "container" + this.PROJECT_NAME,
            idFilterBar: "filterBar" + this.PROJECT_NAME,
            data       : { opc: "list", id: idTabulation },
            conf       : { datatable: true, pag: 10 },
            coffeesoft : true,

            attr       : {

                id      : "tb" + this.PROJECT_NAME,
                center  : [2, 3, 4, 5, 6, 7],
                title: `Lista de empleados  (${this.Periods})`,
                subtitle: 'Calificaciones de los empleados en los 4 rubros de evaluación',
                theme   : 'corporativo',
                right   : [7],
                extends : true,
            }
        });

    }

    viewRegistros() {

        this.createTable({
          parent: "container" + this.PROJECT_NAME,
          idFilterBar: "filterBar" + this.PROJECT_NAME,
            data: { opc: "lsTabulationMap", id_period: 19 },
          conf: { datatable: true, pag: 10 },
          coffeesoft: true,
          attr: {
            id: "tb" + this.PROJECT_NAME,
            center: [1, 3, 4, 5, 6, 7],
            theme:'corporativo',
            extends: true,
          },
        });

    }

    calcAvg() {
        const fields = ['te', 'ps', 'ap', 'pr'];
        let total = 0;
        let count = 0;
        let showWarning = false;

        // Limpiar mensaje anterior
        $('#warning-msg').remove();

        fields.forEach(id => {
            const valRaw = $(`#${id}`).val();
            const val = parseFloat(valRaw);

            if (!isNaN(val)) {
                if (val >= 1 && val <= 5) {
                    total += val;
                    count++;
                } else if (val > 5) {
                    showWarning = true;
                }
            }
        });

        const avg = count ? (total / count).toFixed(2) : '';
        $('#calf').val(avg);

        // Mostrar mensaje solo si hay error
        if (showWarning) {
            $('#calf').parent().append(`
            <div id="warning-msg" class="text-danger mt-2 text-sm">
                ⚠️ Los valores deben estar entre 1 y 5. Corrige los campos señalados.
            </div>
        `);
        }
    }

    async editTabulation(id) {

        let request = await useFetch({
            url: this._link,
            data: { opc: 'getTabulation', id: id }
        });


        this.createModalForm({
            id     : 'formModalEdit',
            data   : { opc: 'edit', id: id },

            bootbox: {
                title: ''
            },

            autofill: request.data,
            json: [
                {
                    opc  : 'label',
                    html : `<strong>CALIFICAR A</strong> ${request.employed || ' '}`,
                    class: 'col-12 text-lg'
                },
                {
                    opc  : 'label',
                    html : `Departamento: ${request.dpto || ' '}`,
                    class: 'col-12 text-sm text-gray-600 mb-3'
                },
                {
                    opc     : 'input',
                    lbl     : 'Trabajo en equipo',
                    id      : 'te',
                    class   : 'col-6 mb-2',
                    tipo    : 'cifra',
                    required: true,
                    onkeyup : 'calificacion.calcAvg()'
                },
                {
                    opc     : 'input',
                    lbl     : 'Pasión por el servicio',
                    id      : 'ps',
                    class   : 'col-6 mb-2',
                    tipo    : 'cifra',
                    required: true,
                    onkeyup : 'calificacion.calcAvg()'
                },
                {
                    opc     : 'input',
                    lbl     : 'Actitud positiva',
                    id      : 'ap',
                    class   : 'col-6 mb-2',
                    tipo    : 'cifra',
                    required: true,
                    onkeyup : 'calificacion.calcAvg()'
                },
                {
                    opc     : 'input',
                    lbl     : 'Profesionalismo',
                    id      : 'pr',
                    class   : 'col-6 mb-2',
                    tipo    : 'cifra',
                    required: true,
                    onkeyup : 'calificacion.calcAvg()'

                },
                {
                    opc     : 'input-group',
                    id      : 'calf',
                    lbl     : 'Calificación final',
                    icon    : 'icon-pencil',
                    tipo    : 'cifra',
                    class   : 'col-12 mb-2',
                    required: false,
                    disabled: true
                }
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

    finishedTabulation(){
        this.swalQuestion({
            opts: {
                title: `¿ Deseas finalizar la tabulación ? ${this.Periods}`,
                text: 'Al terminar la tabulación se desactivara el periodo seleccionado.',
            },
            data: {
                opc  : 'close',
                stado: 2,
                id   : idTabulation

            },
            methods: {
                request: (response) => {

                if (response.status == 200) {
                    alert({ icon: "success", text: response.message });
                    app.render();
                } else {
                    alert({ icon: "error", text: response.message });
                }

                   
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
}
