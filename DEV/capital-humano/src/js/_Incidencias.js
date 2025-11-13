class Incidencias extends CH {
    #_term;
    constructor(ctrl) {
        super(ctrl);
    }
    get term() {
        return $(this.#_term);
    }
    set term(valor) {
        this.#_term = valor;
    }
    async filterIncidencias() {
        this.lsInit = await fn_ajax({ opc: "terminologia" }, this._ctrl);

        if (this.lsInit.permiso == true && getCookies()["IDE"] == 8) {
            $("#btnLock").removeClass("hide");
            const uniqueUdns = new Set(this.lsInit.open.map((item) => item.udn)).size;
            if (uniqueUdns != 0)
                $("#btnLockOff")
                    .removeClass("hide")
                    .html('<i class="icon-lock"></i><sup class="text-danger fw-bold">[' + uniqueUdns + "]</sup>");
        }
        const hideUDN = Object.keys(this.lsInit.udn).length == 1 ? "hide" : "";

        this.container.html("").create_elements([
            { lbl: "Unidad de negocio", id: "cbUDN", elemento: "select", option: { data: this.lsInit.udn }, div: { class: hideUDN + " mb-3 col-12 col-sm-6 col-md-4 col-lg-3" } },
            {
                lbl: "Información",
                id: "cbFiltro",
                elemento: "select",
                option: {
                    data: [
                        { id: "1", valor: "Incidencias" },
                        { id: "2", valor: "Concentrado" },
                    ],
                },
            },
            { lbl: "Filtro de incidencias", readonly: true, id: "iptDate", elemento: "input-group", icon: '<i class="icon-calendar"></i>', pos: "r" },
        ]);

        this.udn = "#cbUDN";
        this.dates = "#iptDate";
        this.filtro = "#cbFiltro";
        this.dates.addClass("text-center");

        if (getCookies()["IDE"] == 8 && getCookies()["IDP"] != 8 && getCookies()["IDP"] != 9 && getCookies()["IDP"] != 28 && getCookies()["IDP"] != 15 && getCookies()["IDP"] != 10 && this.filtro.val() == 1) {
            this.udn.val(8).change().prop("disabled", true);
        }

        // const perfiles_informacion = [2, 6, 7, 8, 9, 10];
        // if (!perfiles_informacion.includes(getCookies()["IDP"])) {
        //     this.filtro.parent().addClass("hide");
        // }

        if (sessionStorage.getItem("udn")) this.udn.val(sessionStorage.getItem("udn")).change();

        this.rangeIncidencias(this.lsInit.calendario, () => this.tbIncidencias());
        this.udn.off("change").on("change", () => this.tbIncidencias());

        this.filtro.off("change").on("change", () => {
            console.log("IDE", getCookies()["IDE"]);
            console.log("IDP", getCookies()["IDP"]);
            console.log("VAL", this.filtro.val());

            if (getCookies()["IDE"] == 8 && getCookies()["IDP"] != 8 && getCookies()["IDP"] != 9 && getCookies()["IDP"] != 15 && getCookies()["IDP"] != 28 && this.filtro.val() == 1) {
                this.udn.val(8).change().prop("disabled", true);
            } else if (getCookies()["IDE"] == 8 && getCookies()["IDP"] != 8 && getCookies()["IDP"] != 9 && getCookies()["IDP"] != 15 && getCookies()["IDP"] != 28 && this.filtro.val() == 2) {
                this.udn.prop("disabled", false);
                this.tbIncidencias();
            } else {
                this.tbIncidencias();
            }
        });
    }
    async terminologia() {
        this.lsInit.terminologia.forEach((t) => {
            if (t.valor != "")
                this.term.append(
                    $("<div>")
                        .html(`[ ${t.valor} ] ${t.terminologia}`) // Agrega el contenido HTML
                        .css({
                            color: "#" + t.color,
                            "background-color": "#" + t.bg,
                        })
                );
        });
    }
    async tbIncidencias() {
        sessionStorage.setItem("udn", this.udn.val());
        this.lsTable = await this.fnDateUDN({ opc: "lsIncidencias", filtro2: this.filtro.val() }, this.tbContainer);

        this.tbContainer.html("").create_table(this.structureTBIncidencias());
        this.table.fixed_inc(2);
    }
    structureTBIncidencias() {
        let tbody = [];
        const lsCol = this.lsTable.colaboradores;
        const buttons = $("<button>", { class: "btn btn-sm btn-outline-info", html: "" });
        const input = $("<input>", { type: "text", class: "cell-inc text-center text-uppercase", maxlength: 2, tipo: "texto" });

        lsCol.forEach((tr) => {
            buttons.attr("onclick", "adicionalInc(" + tr.id + ")");

            if (tr.inc_extra == true) {
                buttons.html('<i class="icon-eye"></i>');
                buttons.removeClass("btn-outline-info");
                buttons.addClass("btn-success");
            } else {
                buttons.removeClass("btn-success");
                buttons.addClass("btn-outline-info");
                buttons.html('<i class="icon-pencil"></i>');
            }

            const sdGerente = getCookies()["IDP"] == 8 || getCookies()["IDP"] == 9 || getCookies()["IDP"] == 15 || getCookies()["IDP"] == 28 ? [{ html: format_number(tr.sd), class: "text-end" }] : [];

            if (this.filtro.val() == 1) {
                let inc = [];
                tr.incidencias.forEach((i) => {
                    if (i.id == null && i.open == false) inc.push({ html: "", class: "text-center" });
                    else {
                        const term = this.lsInit.terminologia.filter((t) => t.id == i.id)[0];
                        if (i.open == false) inc.push({ html: term.valor, class: "text-center", style: "color:#" + term.color + " !important; background-color:#" + term.bg + " !important;" });
                        else {
                            const valor = term == undefined ? "" : term.valor;
                            input
                                .css("background-color", "var(--primary1)")
                                .prop({ id: "cell_" + tr.id + "_" + i.date })
                                .attr({ onBlur: "updateInc(" + tr.id + ",'" + i.date + "')", valor: valor, value: valor, fecha: i.date, ident: tr.id });

                            inc.push({ html: input.prop("outerHTML"), class: "text-center" });
                        }
                    }
                });

                tbody.push([{ html: tr.area + "<br>" + tr.puesto }, { html: tr.valor }, ...sdGerente, { html: buttons.prop("outerHTML"), class: "text-center" }, ...inc]);
            } else {
                let horaExtra = tr.hraExtra[0] != null ? tr.hraExtra[0] + " hrs. / " + format_number(tr.hraExtra[1]) : "-";

                let bitacora = [];
                this.lsInit.terminologia.forEach((i) => {
                    if (i.valor != "") {
                        const style = { style: "color:#" + i.color + " !important; background-color:#" + i.bg + " !important;" };
                        const exito = tr.bitacora.find((b) => b.id == i.id);
                        if (exito) bitacora.push({ html: exito.cant, class: "text-center", ...style });
                        else bitacora.push({ html: "", class: "text-center", ...style });
                    }
                });

                tbody.push([
                    { html: tr.area + "<br>" + tr.puesto },
                    { html: tr.valor },
                    ...sdGerente,
                    { html: format_number(tr.anticipo), class: "text-end" },
                    { html: horaExtra, class: "text-end" },
                    { html: format_number(tr.comp), class: "text-end" },
                    { html: format_number(tr.bono), class: "text-end" },
                    { html: format_number(tr.infonavit), class: "text-end" },
                    { html: format_number(tr.fonacot), class: "text-end" },
                    { html: format_number(tr.prestamo), class: "text-end" },
                    ...bitacora,
                ]);
            }
        });

        this.table = "#tbIncidencias";

        let valoresInc = "";
        this.lsInit.terminologia.forEach((i) => {
            if (i.valor != "") valoresInc += "," + i.valor;
        });

        let thFiltro = this.filtro.val() == 1 ? "Adicionales," + this.lsTable.thDates : "Anticipos,Horas Extras,Complementos,Bonos,Infonavit,Fonacot,Préstamos" + valoresInc;

        const optGerente = getCookies()["IDP"] == 8 || getCookies()["IDP"] == 9 || getCookies()["IDP"] == 15 ||  getCookies()["IDP"] == 28 ? ",S. Diario," + thFiltro : "," + thFiltro;

        return {
            table: { id: "tbIncidencias" },
            thead: "Departamento, Colaborador" + optGerente,
            tbody,
        };
    }
    updateInc(id, date) {
        let input = $("#cell_" + id + "_" + date);
        let valor = input.val().toUpperCase();
        let valor2 = input.attr("valor").toUpperCase();

        if (valor !== valor2) {
            const term = this.lsInit.terminologia.filter((t) => t.valor == valor)[0];
            if (term != undefined) {
                // VAMOS HACER UN JSON CON LAS VARIABLES CON SU VALORES PARA USAR UTIL->SQL
                const jsonSQL = {
                    id_Empleado: id,
                    id_Terminologia: term.id,
                    Fecha_Incidencia: date,
                };

                fn_ajax({ opc: "saveIncidencia", ...jsonSQL }, this._ctrl).then((data) => {
                    if (data === true) {
                        input.next("span").remove();
                        input.css({ width: "85%" });
                        input.after('<span class="d-inline-block align-top text-center"><sup><i class="icon-ok text-success"></i></sup></span>');
                        input.next("span").css({ width: "15%", height: "50px" });
                        input.attr("valor", valor);
                    } else console.error(data);
                });
            } else {
                input.val(valor2);
                alert({ icon: "warning", title: "El término [" + valor + "] no existe." });
            }
        }
    }
    elementsLockOpenModal() {
        const col12 = { div: { class: "col-12 mb-3" } };

        return [
            { ...col12, lbl: "Unidad de negocio", name: "idE", required: true, elemento: "select", option: { data: this.lsInit.udn, placeholder: "- SELECCIONAR -" } },
            { ...col12, lbl: "Rango de fechas", name: "dates", readonly: true, id: "datesInc", elemento: "input-group", icon: '<i class="icon-calendar"></i>', pos: "r" },
            { ...col12, lbl: "Motivo de apertura", name: "motivo", elemento: "textarea", required: true },
        ];
    }
    async lockOpen() {
        const datos = await this.createModal({
            elements: this.elementsLockOpenModal(),
            title: "APERTURA DE INCIDENCIAS",
            opc: "bitacoraInc",
            fn: () => {
                $("#datesInc").addClass("text-center").daterangepicker();
            },
            ajax: true,
        });

        if (datos == true) {
            this.lsInit.open = await this.fnDateUDN("historyIncidencias");
            const uniqueUdns = new Set(this.lsInit.open.map((item) => item.udn)).size;
            if (uniqueUdns != 0)
                $("#btnLockOff")
                    .removeClass("hide")
                    .html('<i class="icon-lock"></i><sup class="text-danger fw-bold">[' + uniqueUdns + "]</sup>");
        }
    }
    tbLockClosed() {
        let tbody = [];
        let udn = "";

        const button = $("<button>", {
            class: "btn btn-sm btn-outline-primary",
            html: '<i class="icon-lock"></i>',
        });

        this.lsInit.open.forEach((tr) => {
            if (udn != tr.udn) {
                udn = tr.udn;

                button.attr({ id: udn, onclick: "closedInc(" + udn + ")" });
                tbody.push([
                    { html: '<i class="icon-right-dir"></i> ' + this.lsInit.udn.filter((i) => i.id == udn)[0]["valor"], class: "fw-bold pointer", onclick: "showData('tr" + udn + "')" },
                    { html: button.prop("outerHTML"), class: "text-center" },
                ]);

                button.attr({ id: tr.id, onclick: "closedInc(" + tr.id + ")" });
                tbody.push([{ tr: { class: "hide tr" + udn } }, { html: tr.fecha, class: "text-end" }, { html: button.prop("outerHTML"), class: "text-center" }]);
            } else {
                button.attr({ id: tr.id, onclick: "closedInc(" + tr.id + ")" });
                tbody.push([{ tr: { class: "hide tr" + udn } }, { html: tr.fecha, class: "text-end" }, { html: button.prop("outerHTML"), class: "text-center" }]);
            }
        });

        button.attr({ id: "all", onclick: "closedInc(0)" });

        $("#tbModalLockInc")
            .html("")
            .create_table({
                table: { id: "tbLock" },
                thead: "UDN,Bloquear " + button.prop("outerHTML"),
                tbody,
            });
    }
    lockClosed() {
        const uniqueUdns = new Set(this.lsInit.open.map((item) => item.udn)).size;
        if (uniqueUdns > 0) {
            bootbox.dialog({
                title: "INCIDENCIAS ABIERTAS",
                message: '<div class="col-12" id="tbModalLockInc"></div>',
                size: "large",
                closeButton: true,
            });

            this.tbLockClosed();
        } else alert({ icon: "warning", title: "No hay empresas abiertas", timer: 1500 });
    }
    async closedInc(id) {
        const data = await fn_ajax({ opc: "closedInc", id }, this._ctrl);
        if (data === true) {
            this.lsInit.open = await this.fnDateUDN("historyIncidencias");
            const uniqueUdns = new Set(this.lsInit.open.map((item) => item.udn)).size;
            if (uniqueUdns == 0) {
                $("#btnLockOff").addClass("hide");
                closedModal();
            } else {
                $("#btnLockOff").html('<i class="icon-lock"></i><sup class="text-danger fw-bold">[' + uniqueUdns + "]</sup>");
                this.tbLockClosed();
            }
            alert();
        } else console.error(data);
    }
    formAdicionalModal(id) {
        const col12 = { div: { class: "col-12 mb-3" } };
        const col8 = { div: { class: "col-12 col-lg-8 mb-3" } };
        const col6 = { div: { class: "col-12 col-lg-6 mb-3" } };
        const col4 = { div: { class: "col-12 col-lg-4 mb-3" } };
        const colaborador = this.lsTable.colaboradores.filter((i) => i.id == id)[0].valor;
        const dates = this.dates.valueDates();

        const elements = [
            { ...col8, lbl: "Colaborador", value: colaborador, disabled: true },
            { ...col4, lbl: "Fecha de la incidencia", name: "Fecha_Inc_Extra", min: dates[0], max: dates[1], value: fecha_actual(), id: "dateEvent", class: "text-center", type: "date" },
            { ...col4, lbl: "Horas extras", name: "horaExtra", elemento: "input-group", tipo: "numero", icon: '<i class="icon-hash"></i>' },
            { ...col4, lbl: "Bonos", name: "Bono", elemento: "input-group", tipo: "cifra" },
            { ...col4, lbl: "Complementos", name: "Complemento", elemento: "input-group", tipo: "cifra" },
            { ...col12, lbl: "Observaciones", name: "Observaciones", required: true, elemento: "textarea" },
            { elemento: "modal_button" },
        ];

        const form = createForm({ id: "formAdicionalInc" });
        form.create_elements(elements);

        $("#formAdicional").append(form);
    }
    async tbAdicionalModal(id) {
        let tbody = [];
        const button = $("<button>", { class: "btn btn-sm btn-outline-danger", html: '<i class="icon-cancel"></i>' });
        const lsMov = await this.fnDateUDN({ opc: "lsMovAdicional", id });
        lsMov.forEach((tr) => {
            button.attr("onclick", "deleteExtraInc(" + tr.id + "," + id + ");");
            tbody.push([
                { html: tr.fecha, class: "text-center" },
                { html: tr.hraExtra, class: "text-end" },
                { html: format_number(tr.bono), class: "text-end" },
                { html: format_number(tr.comp), class: "text-end" },
                { html: tr.obs },
                { html: button.prop("outerHTML"), class: "text-center" },
            ]);
        });

        const table = {
            table: { id: "movAdicional" },
            thead: "Fecha,Hra. Extra, Bono, Complemento, Observacion, Cancelar",
            tbody,
        };

        $("#tbAdicionalForm").html("").create_table(table);
    }
    async deleteExtraInc(idExtra, id) {
        const data = await fn_ajax({ opc: "deleteExtraInc", idExtra: idExtra }, this._ctrl);
        if (data === true) {
            this.tbAdicionalModal(id);
            alert();
        } else console.error(data);
    }
    tbCreditoAdicionalModal(id) {
        let tbody = [];

        const table = {
            table: { id: "credAdicional" },
            thead: "Crédito,Folio,No. Pago,Monto Q.",
            tbody,
        };

        $("#creditoAdicional").html("").create_table(table);
    }
    containerAdicionalModal(id) {
        const dates = this.dates.valueDates();
        const date1 = new Date(dates[0] + " 00:00:00");
        const date2 = new Date(dates[1] + " 00:00:00");
        const dateNow = new Date();

        const container = $("<div>", { class: "row" });
        container.simple_json_nav([
            {
                tab: "Formulario",
                class: "text-dark",
                active: true,
                contenedor: [
                    { class: "col-12 pt-2", id: "formAdicional" },
                    { class: "col-12 mt-3", id: "tbAdicionalForm" },
                ],
            },
        ]);
        return container;
    }
    adicionalInc(id) {
        this.createModal({
            message: this.containerAdicionalModal(id),
            title: "INFORMACION ADICIONAL DE INCIDENCIAS",
            opc: "adicionalInc",
            size: "xl",
            close: true,
            data: { id },
            fn: () => {
                this.tbAdicionalModal(id);
                this.formAdicionalModal(id);
                this.tbCreditoAdicionalModal(id);
            },
            ajax: true,
        });

        if ($("#formAdicionalInc")) {
            $("#formAdicionalInc").validation_form({ opc: "adicionalInc", id_Empleado: id }, async (datos) => {
                const data = await send_ajax(datos, this._ctrl);
                if (data === true) {
                    alert();
                    this.tbAdicionalModal(id);
                    this.tbIncidencias();
                    $("#formAdicionalInc")[0].reset();
                } else console.error(data);
            });
        }
    }
}
