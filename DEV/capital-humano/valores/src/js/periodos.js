
// url api
let api = "ctrl/ctrl-periodos.php";
// instancia
let app;
// vars.
let udn;

$(async () => {
    fn_ajax({ opc: "init" }, api).then((data) => {
        udn = data.udn;
        app = new Periodos(api, "root");
        app.init();
    });
});

class Periodos extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "Periodos";
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
            parent: `root`,
            id: this.PROJECT_NAME,

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
                    lbl: "Filtrar por fecha: ",
                },
                {
                    opc: "select",
                    class: "col-sm-3",
                    id: "udn",
                    lbl: "Seleccionar udn: ",
                    data: udn,
                    onchange: "app.ls()",
                },
                {
                    opc: "select",
                    class: "col-sm-3",
                    id: "status",
                    lbl: "Seleccionar estado: ",
                    data: [
                        { id: 1, valor: "En proceso" },
                        { id: 2, valor: "Finalizado" },
                    ],
                    onchange: "app.ls()",
                },
                {
                    opc: "button",
                    className: "w-100",
                    class: "col-sm-2",
                    color_btn: "primary",
                    id: "btnNuevo",
                    text: "Nuevo",
                    onClick: () => this.add(),
                },
            ],
        });

        dataPicker({
            parent: "calendar" + this.PROJECT_NAME,
            rangepicker: {
                startDate: moment().startOf("year"),
                endDate: moment().month(5).endOf("month"),
                showDropdowns: true,
                ranges: {
                    "Mes actual": [
                        moment().startOf("month"),
                        moment().endOf("month"),
                    ],
                    "Mes anterior": [
                        moment().subtract(1, "month").startOf("month"),
                        moment().subtract(1, "month").endOf("month"),
                    ],
                    "1er semestre": [
                        moment().startOf("year"),
                        moment().month(5).endOf("month"),
                    ],
                    "2do semestre": [
                        moment().month(6).startOf("month"),
                        moment().endOf("year"),
                    ],
                    "Año actual": [
                        moment().startOf("year"),
                        moment().endOf("year"),
                    ],
                    "Personalizar": [
                        moment().startOf("month"),
                        moment().endOf("month"),
                    ],
                },
            },
            onSelect: () => this.ls(),
        });
    }

    ls() {
        let range = getDataRangePicker("calendar" + this.PROJECT_NAME);

        this.createTable({

            parent     : "container" + this.PROJECT_NAME,
            idFilterBar: "filterBar" + this.PROJECT_NAME,
            data       : { opc: "listPeriods", fi: range.fi, ff: range.ff },
            conf       : { datatable: true, pag: 10 },
            coffeesoft: true,
            attr       : {
                id     : "tb" + this.PROJECT_NAME,
                center : [ 3, 4 , 5],
                class  : 'uppercase w-100',
                extends: true,
            },
        });
    }

    add() {
        this.createModalForm({
            id: 'frmModalPeriodos',
            data: { opc: 'addPeriod' },
            bootbox: {
                title: '<strong>Nuevo Periodo</strong>',
            },
            json: [
                { opc: 'select', lbl: 'Seleccionar udn', id: 'id_UDN', class: 'col-12 mb-2', data: udn, required: true },
                { opc: 'input', lbl: 'Fecha de inicio', id: 'date_init', type: 'date', class: 'col-6 mb-2', required: true },
                { opc: 'input', lbl: 'Fecha de fin', id: 'date_end', type: 'date', class: 'col-6 mb-2', required: true },
                { opc: 'input', lbl: 'Nombre del periodo', id: 'name', class: 'col-12', tipo: 'texto', required: true },
            ],
            success: (response) => {
                if (response.status == 200) {
                    alert({ icon: "success", text: response.message,timer:1500 });
                    this.ls();
                } else {
                    alert({
                      icon: "info",
                      text: response.message,
                      timer: 4000,
                    });
                }
            }
        });

        // Esperamos que el DOM del modal esté renderizado
        setTimeout(() => {
            const $fechaInicio = $('#date_init');
            const $fechaFin = $('#date_end');
            const $nombre = $('#name');

            const meses = ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'];

            const updateNombrePeriodo = () => {
                const fi = $fechaInicio.val();
                const ff = $fechaFin.val();
                if (fi && ff) {
                    // Formato esperado: yyyy-mm-dd
                    const [y1, m1] = fi.split('-');
                    const [y2, m2] = ff.split('-');

                    const nombre = `${meses[parseInt(m1) - 1]} ${y1} - ${meses[parseInt(m2) - 1]} ${y2}`;
                    $nombre.val(nombre);
                }
            };

            $fechaInicio.on('change', updateNombrePeriodo);
            $fechaFin.on('change', updateNombrePeriodo);
        }, 100);
    }

    async edit(id) {
        let data = await useFetch({ url: this._link, data: { opc: "getPeriod", id: id } });
        this.createModalForm({
            id: 'frmModalEditPeriodo',
            data: { opc: 'editPeriod', id: id },
            bootbox: {
                title: '<strong>Editar Periodo</strong>'
            },
            autofill: data.periodo,
            json: [
                { opc: 'select', lbl: 'Seleccionar udn', id: 'id_UDN', class: 'col-12 mb-2', data: udn, required: true },
                { opc: 'input', lbl: 'Fecha de inicio', id: 'date_init', type: 'date', class: 'col-6 mb-2', required: true },
                { opc: 'input', lbl: 'Fecha de fin', id: 'date_end', type: 'date', class: 'col-6 mb-2', required: true },
                { opc: 'input', lbl: 'Nombre del periodo', id: 'name', class: 'col-12', tipo: 'texto', required: true },
            ],
            success: (response) => {
                if (response.status == 200) {
                    alert({ icon: "success", text: response.message });
                    this.ls();
                } else {
                    alert({ icon: "error", text: response.message });
                }
            }
        });

        setTimeout(() => {
            const $udn = $('#id_UDN');
            const $fechaInicio = $('#date_init');
            const $fechaFin = $('#date_end');
            const $nombre = $('#name');

            $udn.attr('disabled', true);
            $udn.val(data.data.id_UDN).trigger('change');
            $fechaInicio.val(data.data.date_init);
            $fechaFin.val(data.data.date_end);
            $nombre.val(data.data.name);

            const meses = ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'];

            const updateNombrePeriodo = () => {
                const fi = $fechaInicio.val();
                const ff = $fechaFin.val();
                if (fi && ff) {
                    // Formato esperado: yyyy-mm-dd
                    const [y1, m1] = fi.split('-');
                    const [y2, m2] = ff.split('-');

                    const nombre = `${meses[parseInt(m1) - 1]} ${y1} - ${meses[parseInt(m2) - 1]} ${y2}`;
                    $nombre.val(nombre);
                }
            };

            $fechaInicio.on('change', updateNombrePeriodo);
            $fechaFin.on('change', updateNombrePeriodo);
        }, 100);
    }

    cancel(id) {
        let tr      = $(event.target).closest("tr");
        let udn     = tr.find("td").eq(0).text();
        let periods = tr.find("td").eq(1).text();


        this.swalQuestion({
            opts: {
                title: `¿Estás seguro de eliminar el periodo “${periods}” de ${udn}?`,
                text: 'Una vez eliminado no podras activarla.'
            },
            data: { opc: 'deletePeriod',status:3, id: id },
            methods: {
                request: (res) => {
                    if (res.status == 200) {
                        alert({ icon: "success", text: res.message, timer: 2000 });
                        this.ls();
                    } else {
                        alert({ icon: "error", text: res.message, timer: 2500 });
                    }
                }
            }
        });
    }
}
