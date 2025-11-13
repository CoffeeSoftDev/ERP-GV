let ctrl = 'ctrl/ctrl-valores-matriz.php';
let app, matrix;
let udn, estados ;


$( async() => {
    fn_ajax({ opc: "init" }, ctrl).then((data) => {
        udn     = data.udn;
        estados = data.estados;
        matrix = new Matrix(ctrl, '');
        matrix.render();
    });
});

class App extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
    }

    layout() {
        this.primaryLayout({
            parent: 'root',
            id    : 'Matriz'
        });
    }

    filterBar() {
        this.createfilterBar({
            parent: "filterBarMatriz",

            data: [
                {
                    opc     : "select",
                    class   : "col-sm-3",
                    id      : "udn",
                    lbl     : "Seleccionar udn: ",
                    data    : udn,
                    onchange: "matrix.ls()",
                },
                {
                    opc  : "input-calendar",
                    class: "col-sm-3",
                    id   : "calendar",
                    lbl  : "Buscar por fecha: ",
                },
                {
                    opc      : "btn",
                    class    : "col-sm-3",
                    color_btn: "primary",
                    id       : "btnNuevaActividad",
                    text     : "Nueva Matriz",
                    fn       : "matrix.matrixModal()",
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
                    "Últimos 3 meses": [
                        moment().subtract(2, "month").startOf("month"),
                        moment().endOf("month")
                    ],
                    "Mes actual": [
                        moment().startOf("month"),
                        moment().endOf("month")
                    ],
                    "Mes anterior": [
                        moment().subtract(1, "month").startOf("month"),
                        moment().subtract(1, "month").endOf("month")
                    ]
                },
            },
            onSelect: (start, end) => {
                this.ls();
            },
        });
    }

    ls(options) {
        let rangePicker = getDataRangePicker("calendar");
        this.createTable({
            parent     : "containerMatriz",
            idFilterBar: "filterBarMatriz",
            data       : { opc: "lsMatrizEvaluacion", fi: rangePicker.fi, ff: rangePicker.ff },
            conf       : { datatable: true, pag: 10 ,fn_datatable: 'datable_export_excel',},
            coffeesoft  : true,
            attr: {

                id      : "tableMatriz",
                // theme   : 'corporativo',
                center  : [1, 2],
                extends : true,
            },
        });
    }



    // cancelMatriz(id) { // plantilla de sweet_alert
    //     let tr = $(event.target).closest("tr");
    //     let title = tr.find("td").eq(2).text();
    //     this.swalQuestion({
    //         opts: { title: ` ${title} ?` },
    //         data: {
    //             opc: 'example',
    //             idList: id
    //         },
    //         methods: {
    //             request: (data) => {
    //                 if (data.status == true) {
    //                     alert({ title: 'Se ha enviado correctamente', timer: 500 });
    //                 } else {
    //                     alert('Verificar con soporte');
    //                 }
    //             }
    //         }
    //     });
    // }
    // editMatriz() {
    // }
}

