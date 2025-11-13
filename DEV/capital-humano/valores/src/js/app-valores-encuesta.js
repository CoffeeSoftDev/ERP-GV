let ctrl = 'ctrl/ctrl-valores.php';
let ctrl_encuesta = 'ctrl/ctrl-encuesta.php';

let app, encuesta, udn, estados,temporadas;

$(async () => {
    fn_ajax({ opc: "init" }, ctrl).then((data) => {
        udn        = data.udn;
        estados    = data.estados;
        // temporadas = data.temporada;
        app        = new App(ctrl, '');
        encuesta   = new Encuesta(ctrl, '');
        app.render();
    });
});

class App extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
    }

    render() {
        this.layout();
        this.filterBar();
        this.ls();
    }

    layout() {
        this.primaryLayout({
            parent: 'root',
            id: 'Valores'
        });
    }

    filterBar() {
        this.createfilterBar({
            parent: "filterBarValores",
            data: [
                {
                    opc: "input-calendar",
                    class: "col-sm-3",
                    id: "calendar",
                    lbl: "Buscar por fecha: ",
                },

                {
                    opc: "select",
                    class: "col-sm-3",
                    id: "udn",
                    lbl: "Seleccionar udn: ",
                    data: udn,
                    onchange: "encuesta.ls(); encuesta.getSeason();",
                },

                {
                    opc: "select",
                    class: "col-sm-2",
                    id: "estado",
                    lbl: "Seleccionar estados: ",
                    data: estados,
                    onchange: "encuesta.ls()",
                },
                {
                    opc: "select",
                    class: "col-sm-2",
                    id: "id_period",
                    lbl: "Seleccionar temporadas: ",
                    data: [{ id: "0", valor: "TODAS LAS TEMPORADAS" }],
                    onchange: "encuesta.ls()",
                },


                {
                    opc: "btn",
                    class: "col-sm-2",
                    color_btn: "primary",
                    id: "btnNuevaActividad",
                    text: "Nueva encuesta",
                    fn: "encuesta.addSurvey()",
                },
            ],
        });
        // initialized.
        dataPicker({
            parent: "calendar",
            rangepicker: {
                startDate: moment().subtract(2, "month").startOf("month"),
                endDate: moment().endOf("month"),
                showDropdowns: true,
                ranges: {
                    "Mes actual": [moment().startOf("month"), moment().endOf("month")],
                    "Mes anterior": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")],
                    "Primeros 6 meses": [moment().startOf("year"), moment().month(5).endOf("month")],
                    "Últimos 6 meses": [moment().month(6).startOf("month"), moment().endOf("year")]
                }
            },
            onSelect: (start, end) => {
                this.ls();
            },
        });
    }



    // Survey

    async addSurvey() {
        let evaluador = await useFetch({ url: ctrl, data: { opc: "getEvaluators" } });
       

        this.createModalForm({
            bootbox: {
                idFormulario: "formEvaluation",
                title: "INICIAR ENCUESTA",
            },
            closeModal: false,
            json: [
                {
                    opc: "select",
                    id: "id_evaluator",
                    class: "col-12 mb-3",
                    lbl: "Selecciona al evaluador",
                    data: evaluador,
                },
                {
                    opc: "select",
                    id: "id_udn",
                    class: "col-12 mb-3",
                    lbl: "Selecciona UDN para evaluar",
                    onchange: "encuesta.getSeasons()",
                    data: udn,
                },
                {
                    opc: "select",
                    id: "id_period",
                    class: "col-12 mb-3",
                    lbl: "Selecciona el periodo",
                    class: "mt-2",
                   
                    data: [],
                },
            ],
            btnSuccess: {
                className: "w-100",
                text: "Iniciar encuesta ",
                class: 'col-12'
            },
            btnCancel: {
                text: " adios",
                className: "w-full ",
                class: 'd-none'
            },
            autovalidation: true,
            data: {
                opc: "addEvaluation",
            },

            success: (response) => {
                if (response.status == 200) {
                    alert({ icon: "success", text: response.message });
                    $("#formEvaluation button[type='submit']").removeAttr("disabled");
                    $(".bootbox").modal("hide");
                    
                    this.render(response.data);
                    
                } else {
                    alert({ icon: "error", text: response.message, btn1: true, btn1Text: "Ok" });
                    $("#formEvaluation button[type='submit']").removeAttr("disabled");
                }
            },
        });

        // let newudn = udn.slice(1);
        $("#id_evaluator").option_select({ select2: true, father: true });
        $("#id_Format").addClass("text-uppercase");
    }

    delete(id) {
        let tr = $(event.target).closest("tr");
        let title = tr.find("td").eq(1).text();

        this.swalQuestion({
            opts: {
                title: `¿Esta seguro?`,
                html: `Estas apunto de eliminar la encuesta de  <strong>${title} </strong> `,
            },
            data: { opc: "deleteEvaluation", idEvaluation: id },
            methods: {
                request: (response) => {
                    if (response.status == 200) {
                        alert({
                            icon: "success", text: response.message,
                        });
                        this.ls();
                    } else {
                        alert({
                            icon: "error", text: response.message,
                        });
                    }
                },
            },
        });
    }

    ls(options) {

        let rangePicker = getDataRangePicker("calendar");

        this.createTable({
            parent: "containerValores",
            idFilterBar: "filterBarValores",
            data: { opc: "lsEvaluation", fi: rangePicker.fi, ff: rangePicker.ff },
            conf: { datatable: true, pag: 10 },
            coffeesoft: true,
            attr: {
                id: "tableValores",
                striped: true,
                // theme:'corporativo',
                f_size: 12,
                center: [1, 2, 4, 5, 6, 7],
                right: [4],
                extends: true
            },

        });

    }

    // Season
    async getSeason() {
        let seasons = await useFetch({ 
            url: this._link,
            data: { opc: "getSeason", id_udn: $('#udn').val()

        } });

        if (seasons && Array.isArray(seasons)) {
            // Agrega opción 'Seleccionar todas las temporadas' al inicio
            seasons.unshift({ id: "0", valor: "TODAS LAS TEMPORADAS" });

            // Renderiza en el plugin option_select
            $("#id_period").option_select({ select2: false, data: seasons });
        }
    }

    async getSeasons() {
        let seasons = await useFetch({
            url: this._link,
            data: {
                opc: "getSeason",
                id_udn: $('#id_udn').val()
            }
        });

        // Validación y renderizado en formulario modal
        if (seasons && Array.isArray(seasons)) {
            console.log(seasons);
            const $form = $("#frmModal"); // 📝 Formulario dentro del modal
            $form.find("#id_period").option_select({
                select2: false,
                data: seasons
            });
        }
    }



    // Components.
    createButtonGroup(options) {
        const icon_default = 'icon-shop';

        let groups = {

            parent: 'groupButtons',
            cols: 'w-25 ',
            size: 'sm',
            fn: '',
            onClick: '',
            class: '',
            data: [{
                text: 'FRANCES',
                color: 'primary',
                icon: 'icon-shop',
                id: '',

            },
            {
                text: 'PASTELERIA',
                color: 'outline-success',
                icon: 'icon-shop',


            },

            ]

        };


        let configuration = Object.assign(groups, options);

        let divs = $('<div>', { class: 'd-flex overflow-auto ' + configuration.class });


        // Iterate over the group data and create buttons

        if (!configuration.dataEl) {
            configuration.data.forEach((item) => {

                let btn = $('<a>', {
                    class: `btn btn-${configuration.size} btn-${item.color} ${configuration.cols} me-1 d-flex flex-column align-items-center justify-content-center`,
                    id: item.id,
                    click: item.onClick,
                    onclick: item.fn
                });

                if (item.type) {

                    btn = $('<label>', {
                        class: `btn z-index-0 btn-${configuration.size} btn-${item.color} ${configuration.cols} me-1 `,
                        for: item.id,
                        id: item.btnid || 'btnfile'
                    });




                    let ipt_file = $('<input>', {
                        class: 'hide',
                        type: 'file',
                        accept: item.accept ? item.accept : '.xlsx, .xls',
                        id: item.id,
                        onchange: item.fn,
                    });

                    divs.append(ipt_file);

                    // btn.append(counter);
                }




                if (item.icon) {
                    let icon = $('<i>', { class: item.icon + ' d-block' });
                    btn.append(icon);
                }

                if (item.text) {
                    let span = $('<span>', { text: item.text });
                    btn.append(span);
                }

                divs.append(btn);

            });
        } else {


            let classDisabled = configuration.dataEl.disabled ? 'disabled' : '';

            configuration.dataEl.data.forEach((item) => {

                let props = {
                    onclick: configuration.dataEl.onClick + `(${item.id})` || configuration.dataEl.fn + `(${item.id})`
                }

                if (configuration.onClick) {
                    props = {
                        click: configuration.onClick
                    }
                }


                let btn = $('<a>', {
                    class: `btn ${classDisabled} btn-outline-primary ${configuration.cols} d-flex me-1 flex-column w-100 align-items-center justify-content-center`, // Add dynamic color class
                    id: item.id,
                    ...props

                });


                var itemIcon = configuration.dataEl.icon ? configuration.dataEl.icon : '';


                let icon = $('<i>', { class: 'ms-2  d-block ' + (item.icon ? item.icon : itemIcon) });

                let span = $('<span>', { text: item.valor });

                // if(item.id){

                btn.append(icon, span);
                // }else{
                //     btn.append(span);

                // }



                divs.append(btn);
            });


        }


        if (groups.parent) {

            $('#' + groups.parent).html(divs);
        } else {

            return divs;
        }


        const cardPosGroup = document.getElementById(groups.parent);



        // Agregar un evento de clic al contenedor
        cardPosGroup.addEventListener('click', function (event) {



            // // Verificar si el elemento clicado es un botÃ³n
            if (event.target.closest('a')) {
                // Seleccionar todos los botones
                const buttons = cardPosGroup.querySelectorAll('a');

                buttons.forEach(button => {
                    button.classList.remove('active', 'btn-primary', 'text-white');
                    button.classList.add('btn-outline-primary');
                });

                // Agregar las clases de estilo al botÃ³n clicado
                const clickedButton = event.target.closest('a');
                clickedButton.classList.add('active', 'btn-primary', 'text-white');
                clickedButton.classList.remove('btn-outline-primary');

            }
        });
    }
}
