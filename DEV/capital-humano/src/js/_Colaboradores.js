class Colaboradores extends CH {
    constructor(ctrl) {
        super(ctrl);
    }
    async filterBar() {
        const jsFiltro = [
            { id: "1", valor: "ACTIVOS" },
            { id: "0", valor: "INACTIVOS" },
        ];

        this.lsInit = await fn_ajax({ opc: "listUDN" }, this._ctrl);

        const hideUDN = Object.keys(this.lsInit).length == 1 ? "hide" : "";

        const elements = [
            { lbl: "Unidad de negocio", id: "cbUDN", elemento: "select", option: { data: this.lsInit }, div: { class: hideUDN + " mb-3 col-12 col-sm-6 col-md-4 col-lg-3" } },
            { lbl: "Filtro de colaboradores", id: "cbFiltro", elemento: "select", option: { data: jsFiltro }, div: { class: hideUDN + " mb-3 col-12 col-sm-6 col-md-4 col-lg-3" } },
            { lbl: '<i class="icon-birthday"></i> Cumpleaños', class: "btn btn-info col-12", elemento: "button", onclick: "birthday()" },
            { lbl: '<i class="icon-plus"></i> Colaborador', id: "btnNuevoColaborador", elemento: "button" },
        ];
        this.container.html("").create_elements(elements);

        this.udn = "#cbUDN";
        this.filtro = "#cbFiltro";

        if (Object.keys(this.lsInit).length == 1) $("#btnNuevoColaborador").parent().remove();
        if (getCookies()["IDP"] != 8 && getCookies()["IDP"] != 9) $("#btnNuevoColaborador").parent().remove();
    }
    async tbColaboradores() {
        this.lsTable = await this.fnDateUDN("lsColaboradores", this.tbContainer);
        this.tbContainer.html("").create_table(this.tbStructure());
        const limit = getCookies()["IDP"] == 8 || getCookies()["IDP"] == 9 ? 8 : 5;
        this.table.table_format({ pageLength: 15, priority: [limit, 3, 2], order: [[1, "asc"]] });
        const table = this.table.DataTable();
        table.column(0).visible(false);
    }
    tbStructure() {
        let tbody = [];

        this.lsTable.forEach((tr) => {
            let phone = tr.telefono ?? '<i class="icon-alert text-danger"></i>';
            const antiguedad = diferenciaFechas(tr.alta);

            let button = create_dropdown([
                { icon: "icon-pencil", text: "Editar", fn: "edit(" + tr.id + ")" },
                { icon: "icon-down-circled", text: "Dar de baja", fn: "low(" + tr.id + ")" },
            ]);

            if ($("#cbFiltro").val() == "0") {
                button = "<button class='btn btn-sm btn-outline-info' onClick='active(" + tr.id + ")'><i class='icon-up-circled'></i>Dar de alta</button>";
            }

            // console.log($('#cbFiltro').val());

            const btnOpts =
                getCookies()["IDP"] == 8 || getCookies()["IDP"] == 9
                    ? [
                          { html: format_number(tr.sd), class: "text-end" },
                          { html: format_number(tr.sf), class: "text-end" },
                          { html: button, class: "text-center" },
                      ]
                    : [];

            tbody.push([
                { html: tr.id },
                { html: tr.area + " / " + tr.puesto },
                { html: tr.valor },
                { html: format_phone(phone), class: "text-center" },
                { html: format_date(tr.alta), class: "text-center" },
                { html: antiguedad.s, class: "text-center" },
                ...btnOpts,
            ]);
        });

        const opt = getCookies()["IDP"] == 8 || getCookies()["IDP"] == 9 ? ",S. Diario,S. Fiscal,opciones" : "";

        this.table = "#tbColaboradores";
        return {
            table: { id: "tbColaboradores" },
            thead: "#,Departamento/Puesto,Colaborador,Teléfono,F.Alta,Antigüedad" + opt,
            tbody,
        };
    }
    elementsLowModal(id) {
        const now = fecha_actual();
        const colaborador = this.getValueCell(id, 2);

        const col12 = { div: { class: "col-12 mb-3" } };
        const elements = [
            {
                ...col12,
                lbl: "Unidad de negocio",
                name: "UDN_AB",
                elemento: "select",
                class: "bg-disabled1",
                readonly: true,
                option: { data: [{ id: this.udn.val(), valor: this.udn.find("option:selected").text() }] },
            },
            { div: { class: "col12 mb3 hide" }, lbl: "folio", name: "AB_Empleados", value: id },
            { div: { class: "col12 mb3 hide" }, lbl: "stado", name: "Estado_ab", value: 0 },
            { ...col12, lbl: "Colaborador", value: colaborador, value: colaborador, disabled: true },
            { ...col12, lbl: "Fecha de baja", name: "Fecha_ab", elemento: "input", type: "date", value: now, class: "text-center", required: true },
            { ...col12, lbl: "Finiquito", name: "finiquito", elemento: "input-group", tipo: "cifra", class: "text-end", required: true },
            { ...col12, lbl: "Motivo de la baja", name: "Observacion_ab", elemento: "textarea", rows: 10, required: true },
        ];

        return elements;
    }
    async modalLow(id) {
        const datos = await this.createModal({ title: "BAJA DE COLABORADOR", size: "large", opc: "lowCollaborator", elements: this.elementsLowModal(id) });

        if (datos === true) {
            alert();
            closedModal();
            this.tbColaboradores();
        }
    }
    elementsActiveModal(id) {
        const now = fecha_actual();
        const colaborador = this.getValueCell(id, 2);

        const col12 = { div: { class: "col-12 mb-3" } };
        const elements = [
            {
                ...col12,
                lbl: "Unidad de negocio",
                name: "UDN_AB",
                elemento: "select",
                class: "bg-disabled1",
                readonly: true,
                option: { data: [{ id: this.udn.val(), valor: this.udn.find("option:selected").text() }] },
            },
            { div: { class: "col12 mb3 hide" }, lbl: "folio", name: "AB_Empleados", value: id },
            { div: { class: "col12 mb3 hide" }, lbl: "stado", name: "Estado_ab", value: 1 },
            { ...col12, lbl: "Colaborador", value: colaborador, value: colaborador, disabled: true },
            { ...col12, lbl: "Fecha de alta", name: "Fecha_ab", elemento: "input", type: "date", value: now, class: "text-center", required: true },
            { ...col12, lbl: "Motivo de la alta", name: "Observacion_ab", elemento: "textarea", required: true },
        ];

        return elements;
    }
    async modalActive(id) {
        const datos = await this.createModal({ title: "ALTA DE COLABORADOR", opc: "lowCollaborator", elements: this.elementsActiveModal(id) });

        if (datos === true) {
            alert();
            closedModal();
            this.tbColaboradores();
        }
    }
    editCollaborator(id) {
        sessionStorage.setItem("colaborador", id);
        redireccion("capital-humano/editar-colaborador.php");
    }
    async birthday() {
        const meses = [0, "ENERO", "FEBRERO", "MARZO", "ABRIL", "MAYO", "JUNIO", "JULIO", "AGOSTO", "SEPTIEMBRE", "OCTUBRE", "NOVIEMBRE", "DICIEMBRE"];
        const mes = fecha_actual().split("-")[1];
        const icon = '<i class="icon-birthday"></i>';

        let optionMes = [];
        for (let i = 1; i < 13; i++) optionMes.push({ id: i, valor: meses[i] });

        const father = $("<div>", { class: "row" });
        const filter = $("<div>", { class: "col-12 mb-0 pb-0 d-flex justify-content-end" });
        filter.create_elements([{ lbl: "Mes", id: "mesBirthday", elemento: "select", option: { data: optionMes } }]);
        father.html("").append(filter);
        father.append(await this.lsBirthday(mes));

        this.createModal({
            title: icon + " CUMPLEAÑEROS DE " + meses[parseInt(mes)],
            message: father,
            size: "large",
            fn: () => {
                $("#tbBirthday").table_format({ info: false, searching: false, paging: false });
                $("#mesBirthday").val(mes).change();
                $("#mesBirthday").on("change", async () => {
                    $("#tableBirthday").html(await this.lsBirthday($("#mesBirthday").val()));
                    $("#tbBirthday").table_format({ info: false, searching: false, paging: false });
                });
            },
            close: true,
        });
    }
    async lsBirthday(mes) {
        let tbody = [];
        const data = await fn_ajax({ opc: "lsBirthday", mes }, this._ctrl);

        if (getCookies()["IDE"] != 8) {
            const list = data.filter((col) => col.udn2 == getCookies()["IDE"]);
            list.forEach((tr) => tbody.push([{ html: tr.fecha, class: "text-center" }, { html: tr.colaborador }, { html: tr.udn, class: "text-center" }]));
        } else data.forEach((tr) => tbody.push([{ html: tr.fecha, class: "text-center" }, { html: tr.colaborador }, { html: tr.udn, class: "text-center" }]));

        const container = $("<div>", { class: "col-12 mt-0 pt-0", id: "tableBirthday" });
        container.create_table({
            table: { id: "tbBirthday" },
            thead: "Cumpleaños,Colaborador,UDN",
            tbody,
        });
        return container.prop("outerHTML");
    }
}
