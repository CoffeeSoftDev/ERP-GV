class CH {
    #_udn;
    #_dates;
    #_filtro;
    #_table;
    #_filterBar;
    #_container;
    #_tbContainer;
    #_lsInit;
    #_lsTable;

    constructor(ctrl) {
        this._ctrl = ctrl;
    }
    get udn() {
        return $(this.#_udn);
    }
    set udn(valor) {
        this.#_udn = valor;
    }
    get dates() {
        return $(this.#_dates);
    }
    set dates(valor) {
        this.#_dates = valor;
    }
    get filtro() {
        return $(this.#_filtro);
    }
    set filtro(valor) {
        this.#_filtro = valor;
    }
    get table() {
        return $(this.#_table);
    }
    set table(valor) {
        this.#_table = valor;
    }
    get filterBar(){
        return $(this.#_filterBar);
    }
    set filterBar(valor){
        this.#_filterBar = valor;
    }
    get container() {
        return $(this.#_container);
    }
    set container(valor) {
        this.#_container = valor;
    }
    get tbContainer() {
        return $(this.#_tbContainer);
    }
    set tbContainer(valor) {
        this.#_tbContainer = valor;
    }
    get lsInit() {
        return this.#_lsInit;
    }
    set lsInit(valor) {
        this.#_lsInit = valor;
    }
    get lsTable() {
        return this.#_lsTable;
    }
    set lsTable(valor) {
        this.#_lsTable = valor;
    }
    async fnDateUDN(options, before = "") {
        let dates = {};
        if (this.dates.length > 0) {
            dates = { date1: this.dates.val() };

            if (this.dates.val().length > 10) {
                const lsDates = this.dates.valueDates();
                dates = {
                    date1: lsDates[0],
                    date2: lsDates[1],
                };
            }
        }

        let udn = {};
        if (this.udn.length > 0) udn = { idE: this.udn.val() };

        let filtro = {};
        if (this.filtro.length > 0) filtro = { filtro: this.filtro.val() };

        // Se construye el json con valores
        const datos = { opc: "", ...udn, ...dates, ...filtro };
        // Si options es string se modifica datos.opc
        if (typeof options === "string") datos.opc = options;
        // Convinar datos con options OR {}
        const opt = Object.assign(datos, options ?? {});

        return await fn_ajax(opt, this._ctrl, before);
    }
    rangeIncidencias(dataIncidencias, callback) {
        const picker = this.range_incidencias(dataIncidencias);

        this.dates.daterangepicker(
            {
                startDate: picker.startDate,
                endDate: picker.endDate,
                ranges: picker.ranges,
                showDropdowns: true,
                showCustomRangeLabel: false,
            },
            (start, end) => {
                if (typeof callback === "function") {
                    callback(start, end);
                }
            }
        );
    }
    createContent($parent, options) {
        let defaults = [{ class: "col-12 line-2 mb-3" }];
        let opts = Object.assign(defaults, options);
        opts.forEach((attr) => $parent.append($("<div>", attr)));
        return $parent;
    }
    range_incidencias(calendario) {
        // Objeto para almacenar los rangos de fechas
        let ranges = {};
        const meses = ["", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];

        // Iterar sobre el JSON y construir los rangos de fechas
        calendario.forEach(function (item) {
            let label = "";

            const dateInicio = new Date(item.inicio);
            const mesInicio = dateInicio.getMonth() + 1;
            const dateFin = new Date(item.fin);
            const mesFin = dateFin.getMonth() + 1;

            const period = mesInicio != mesFin ? 1 : 2;

            label = period + "a. " + meses[mesFin];
            ranges[label] = [moment(item.inicio), moment(item.fin)];
        });

        return {
            startDate: moment(calendario[calendario.length - 1].inicio),
            endDate: moment(calendario[calendario.length - 1].fin),
            ranges,
        };
    }
    async createModal(options) {
        const form = createForm();

        let defaults = {
            opc: "php",
            log: false,
            title: "Titulo",
            form: form,
            elements: [{ lbl: "Hola mundo" }],
            size: "medium",
            close: false,
            fn: null,
            ajax: true,
        };

        let opts = Object.assign(defaults, options);

        bootbox.dialog({
            title: opts.title,
            message: opts.message ?? opts.form,
            size: opts.size,
            closeButton: opts.close,
        });

        opts.elements.push({ elemento: "modal_button" });
        form.create_elements(opts.elements);

        if (typeof opts.fn === "function") opts.fn.call(this);

        const waitValidation = () => {
            return new Promise((resolve, reject) => {
                form.validation_form({ opc: opts.opc, ...opts.data }, async (datos) => {
                    if (datos) {
                        if (opts.log == true) for (const x of datos) console.log(x);

                        if (opts.ajax == true) {
                            datos = await send_ajax(datos, this._ctrl);
                            if (datos === true) {
                                alert();
                                closedModal();
                            } else console.error(datos);
                        }

                        resolve(datos);
                    } else reject(new Error("La validación falló"));
                });
            });
        };

        try {
            const datos = await waitValidation();
            return datos;
        } catch (error) {
            console.error("Error en la validación del formulario:", error);
        }
    }
    getValueCell(valor, col) {
        // const table = idTable != "" ? $("#" + idTable).DataTable() : this.table.DataTable();
        let result = 0;
        const table = this.table.DataTable();
        table.rows().every(function () {
            const data = this.data();
            if (Array.isArray(data)) {
                for (let i = 0; i < data.length; i++) {
                    if (data[i] === valor.toString()) {
                        result = data[col];
                        return;
                    }
                }
            }
        });

        return result;
    }
}
