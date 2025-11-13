let app, calificacion;
let udn, periods, idTabulation;

let api = "ctrl/ctrl-tabulacion.php";
let api_calificacion = "ctrl/ctrl-tabulacion-calificaciones.php";
let api_concentrado = "ctrl/ctrl-tabulacion-concentrado.php";

$(function () {
    fn_ajax({ opc: "init" }, api).then((request) => {
        udn = request.udn;
        periods = request.periods;
        app = new App(api, "root");
        calificacion = new Calificacion(api_calificacion, "root");
        concentrado = new Concentrado(api_concentrado, "root");
        app.init();
    });
});

class App extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "tabulacion";
    }

    init() {
        this.render();
    }

    render() {
        this.layout();
        this.filterBar();
        this.ls();
    }

    layout() {
        this.primaryLayout({
            parent: "root",
            id: this.PROJECT_NAME,
            class: "h-full",
        });
    }

    filterBar() {
        this.createfilterBar({
            parent: "filterBar" + this.PROJECT_NAME,
            data: [
                {
                    opc: "input-calendar",
                    class: "col-sm-3",
                    id: "calendar" + this.PROJECT_NAME,
                    lbl: "Buscar por fecha:",
                },
                {
                    opc: "select",
                    class: "col-sm-3",
                    id: "udn",
                    lbl: "Seleccionar UDN:",
                    data: udn,
                    onchange: "app.ls()",
                },
                {
                    opc: "select",
                    class: "col-sm-3",
                    id: "status",
                    lbl: "Seleccionar estado:",
                    data: [
                        { id: 0, valor: "-- Todos los estados --" },
                        { id: 1, valor: "Activo" },
                        { id: 2, valor: "Finalizado" },
                    ],
                    onchange: "app.ls()",
                },
                {
                    opc: "button",
                    class: "col-sm-3",
                    color_btn: "primary",
                    className: "w-100",
                    id: "btnNuevoTabulacion",
                    text: "Nueva tabulación",
                    onClick: () => {
                        this.addTabulation();
                    },
                },
            ],
        });

        const mesActual = new Date().getMonth() + 1;
        let startDate =
            mesActual <= 6
                ? moment().startOf("year")
                : moment().startOf("year").add(6, "months");

        dataPicker({
            parent: "calendar" + this.PROJECT_NAME,
            rangepicker: {
                startDate,
                endDate: moment(),
                showDropdowns: true,
                ranges: {
                    "1er semestre": [
                        moment().startOf("year"),
                        moment().startOf("year").add(5, "months"),
                    ],
                    "2do semestre": [
                        moment().startOf("year").add(6, "months"),
                        moment().endOf("year"),
                    ],
                    "Año actual": [moment().startOf("year"), moment().endOf("year")],
                    "Año anterior": [
                        moment().subtract(1, "year").startOf("year"),
                        moment().subtract(1, "year").endOf("year"),
                    ],
                },
            },
            onSelect: (start, end) => {
                this.ls();
            },
        });
    }

    ls() {
        let rangePicker = getDataRangePicker("calendar" + this.PROJECT_NAME);

        this.createTable({
            parent: "container" + this.PROJECT_NAME,
            idFilterBar: "filterBar" + this.PROJECT_NAME,
            data: {
                opc: "list",
                fi: rangePicker.fi,
                ff: rangePicker.ff,

                udn: $("#udn").val(),
                status: $("#status").val(),
            },
            coffeesoft: true,

            conf: { datatable: true, pag: 10 },
            attr: {
                id: "tb" + this.PROJECT_NAME,
                theme: 'corporativo',

                center: [1, 2, 3, 4, 5],
                right: [2],
                f_size: 12,
                extends: true,
            },
        });
    }

    lsTabulationEvaluados() {
        let rangePicker = getDataRangePicker("calendar" + this.PROJECT_NAME);

        this.createTable({
            parent: "container" + this.PROJECT_NAME,
            idFilterBar: "filterBar" + this.PROJECT_NAME,
            data: {
                opc: "list",
                fi: rangePicker.fi,
                ff: rangePicker.ff,

                udn: $("#udn").val(),
                status: $("#status").val(),
            },
            coffeesoft: true,

            conf: { datatable: true, pag: 10 },
            attr: {
                id     : "tb" + this.PROJECT_NAME,
                theme  : 'corporativo',
                center : [1, 2, 3, 4, 5],
                right  : [2],
                f_size : 12,
                extends: true,
            },
        });
    }

    addTabulation() {
        this.createModalForm({
            id: "formModal",
            data: { opc: "addTabulacion" },

            bootbox: { title: "<strong>Nueva tabulación</strong>" },

            json: [
                {
                    opc: "select",
                    lbl: "Seleccionar udn:",
                    id: "id_UDN",
                    class: "col-12",
                    data: udn,
                    onchange: "app.getPeriods()",
                },

                {
                    opc: "select",
                    lbl: "Seleccionar período:",
                    id: "id_period",
                    class: "col-12",
                    data: [],
                },
            ],

            success: (response) => {
                if (response.status == 200) {
                    alert({
                        icon: "success",
                        text: response.message,
                        timer: 1500,
                    });
                    idTabulation = response.id;
                    calificacion.render({
                        title: $("#id_period").find(":selected").text(),
                    });
                    $(this).closest(".bootbox").modal("hide");

                  
                } else {
                    alert({
                        icon: "warning",
                        text: response.message,
                        timer: 5000,
                    });
                }
            },
        });

        // Initialized. solo el boton btnSuccess debe estar deshabilitado
        const modal =$("#formModal").closest(".bootbox");
        const btnSuccess = modal.find("#btnSuccess");
        btnSuccess.attr("disabled", "disabled");
    }

    async getPeriods() {
        let request = await useFetch({
            url: this._link,
            data: { opc: "getPeriods", udn: $("#id_UDN").val() },
        });

        // Actualizar el select de períodos
        $("#id_period").option_select({ data: request.data });

        const hasData = request.data && request.data.length > 0;

        // Controlar solo el botón btnSuccess
        const modal = $("#formModal").closest(".bootbox");
        const btnSuccess = modal.find("#btnSuccess");

        if (hasData) {
            btnSuccess.removeAttr("disabled");
        } else {
            btnSuccess.attr("disabled", "disabled");
        }
    }

    // Confirmar reanudar
    reanudar(id, udn) {
        let tr = $(event.target).closest("tr");
        let title = tr.find("td").eq(0).text();
        let periods = tr.find("td").eq(1).text();

        alert({
            icon: "question",
            text: `Periodo asignado : ${periods}`,
            title: `¿Deseas reanudar la tabulación de ${title} ?`,
        }).then((result) => {
            if (result.isConfirmed) {
                idTabulation = id;
                calificacion.render({
                    title: periods,
                });
            }
        });
    }

    // Confirmar revisión
    revisar(id) {
        let tr = $(event.target).closest("tr");
        let udn     = tr.find("td").eq(0).text();
        let periods = tr.find("td").eq(1).text();

        alert({
            icon: "question",
            text: `Periodo asignado : ${periods}`,
            title: `¿Deseas ver el concentrado de la tabulación de ${udn} ?`,
        }).then((result) => {
            if (result.isConfirmed) {
                idTabulation = id;
                concentrado.render({
                    title: periods,
                    udn: udn
                });
            }
        });
    }
}
