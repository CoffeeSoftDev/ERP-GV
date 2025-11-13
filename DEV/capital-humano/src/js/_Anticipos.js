class Anticipos extends CH {
    constructor(ctrl) {
        super(ctrl);
    }

    async filterAnticipos() {
        this.lsInit = await fn_ajax({ opc: "initAdvance" }, this._ctrl);
        const hideUDN = Object.keys(this.lsInit.udn).length == 1 ? "hide" : "";

        this.container
            .html("")
            .create_elements([
                { lbl: "Unidad de negocio", id: "cbUDN", elemento: "select", option: { data: this.lsInit.udn }, div: { class: hideUDN + " mb-3 col-12 col-sm-6 col-md-4 col-lg-3" } },
                { lbl: "Filtro de incidencias", id: "iptDate", elemento: "input-group", icon: '<i class="icon-calendar"></i>', pos: "r" },
                ...this.lsInit.habil,
            ]);

        this.udn = "#cbUDN";
        this.dates = "#iptDate";

        this.rangeIncidencias(this.lsInit.calendario, () => this.tbAnticipos());
        this.udn.off("change").on("change", () => this.tbAnticipos());
    }
    // FORMULARIO NUEVO ANTICIPO
    async elementsAdvance() {
        const col12 = { div: { class: "col-12 mb-3" } };
        const col6 = { div: { class: "col-6 mb-3" } };
        const cifra = { elemento: "input-group", tipo: "cifra" };
        const percent = { elemento: "input-group", icon: '<i class="icon-percent"></i>', pos: "r", tipo: "cifra" };
        const disabled = { disabled: true };

        this.lsInit = await this.fnDateUDN("lsColaboradoresxAnticipo");
        console.log(this.lsInit);
        
        const elements = [
            { lbl: "idE", name: "id_UDN", value: this.udn.val(), div: { class: "hide" } },
            { ...col12, lbl: "Colaborador", name: "Empleado_Anticipo", elemento: "select", option: { data: this.lsInit, placeholder: "- Seleccionar -", select2: true, father: true }, required: true },
            { ...col6, ...cifra, ...disabled, id: "sd", lbl: "Salario diario" },
            { ...col6, ...percent, ...disabled, id: "porcentaje", lbl: "Porcentaje permitido" },
            { ...col6, ...cifra, ...disabled, class: "bg-disabled1", id: "acumulado", lbl: "Acumulado del período" },
            { ...col6, ...cifra, ...disabled, id: "permitido", lbl: "Anticipo permitido" },
            { ...col12, ...cifra, ...disabled, id: "anticipo", name: "Saldo", lbl: "Anticipo solicitado", required: true },
        ];

        return elements;
    }
    async advance() {
        const datos = await this.createModal({
            elements: await this.elementsAdvance(),
            size: "large",
            title: "Nuevo anticipo / " + this.udn.find("option:selected").text(),
            fn: this.validationElements,
            opc: "newAdvance",
            ajax: false,
        });

        datos.append("Saldo_Acumulado", $("#acumulado").val().replace(",", ""));
        this.saveNewAdvance(datos);
    }
    validationElements() {
        $('button[type="submit"]').prop("disabled", false);

        $("#colaborador").on("change", () => {
            const info = this.lsInit.filter((e) => e.id == $("#colaborador").val())[0];
            const alta = diasTranscurridos(info.alta);
            if (alta > 30) {
                $("#sd").val(info.sd);
                $("#porcentaje").val(info.pa);
                $("#acumulado").val(number_format(info.acumulado, 2, ".", ","));
                $("#acumulado").attr("acumulado", info.acumulado);

                const salario = parseFloat(info.sd) * 15;
                const permitido = salario * (parseFloat(info.pa) / 100);
                const disponible = permitido - parseFloat(info.acumulado);

                $("#permitido").val(number_format(disponible, 2, ".", ","));
                $("#permitido").attr("permitido", disponible);
                $("#anticipo").removeAttr("disabled");
                $("#colaborador").siblings("span.text-danger").remove();
            } else {
                $("#sd").val("");
                $("#porcentaje").val("");
                $("#acumulado").val("");
                $("#permitido").val("");
                $("#anticipo").prop("disabled", true);
                $("#colaborador").siblings("span.text-danger").remove();
                $("#colaborador")
                    .parent()
                    .append($("<span>", { class: "text-danger fw-bold mt-3 form-text", html: '<i class="icon-attention-1"></i> El colaborador debe tener por lo menos 1 mes de antigüedad.' }));
            }
        });

        $("#anticipo").on("keyup", () => {
            const valor = $("#anticipo").val() != "" ? parseFloat($("#anticipo").val()) : 0;
            const acumulado = parseFloat($("#acumulado").attr("acumulado"));
            const permitido = parseFloat($("#permitido").attr("permitido"));
            const nuevoAcumulado = acumulado + valor;
            const nuevoPermitido = permitido - valor;

            $("#acumulado").val(number_format(nuevoAcumulado, 2, ".", ","));
            $("#permitido").val(number_format(nuevoPermitido, 2, ".", ","));

            if (nuevoPermitido < 0) {
                $("#permitido").addClass("text-danger fw-bold");
                $('button[type="submit"]').prop("disabled", true);
                $("#anticipo").parent().next("span.text-danger").remove();
                $("#anticipo")
                    .parent()
                    .parent()
                    .append($("<span>", { class: "text-danger fw-bold mt-3 form-text", html: '<i class="icon-attention-1"></i> Cantidad no permitida' }));
            } else {
                $("#permitido").removeClass("text-danger fw-bold");
                $('button[type="submit"]').prop("disabled", false);
                $("#anticipo").parent().next("span.text-danger").remove();
            }
        });
    }
    async saveNewAdvance(datos) {
        const data = await send_ajax(datos, this._ctrl);
        if (data == true) {
            alert();
            $(".bootbox-close-button").click();
            this.tbAnticipos();
        } else if (data == "false") alert({ icon: "error", title: "No puedes hacer anticipos.", text: "Estas fuera del rango de fechas permitidas.", btn1: true });
        else console.error(data);
    }
    // TABLA DE ANTICIPOS
    async tbAnticipos() {
        this.lsTable = await this.fnDateUDN("tbAnticipos", this.tbContainer);
        this.tbContainer.html("").create_table(this.structureTbAnticipo());
        this.table.table_format({ info: false, paging: false, ordering: false, searching: false });
    }
    structureTbAnticipo() {
        let tbody = [];
        let thead = "Colaborador,Saldo";
        const perfilesAceptados = this.lsInit.permisos;
        

        thead += perfilesAceptados.includes(getCookies()["IDP"]) ? ",opciones" : "";

        const extra = perfilesAceptados.includes(getCookies()["IDP"]) ? [{ html: "" }] : [];

        const icon = '<i class="icon-right-dir"></i>';
        let button = $("<button>", { class: "btn btn-sm btn-outline-info", html: '<i class="icon-print"></i>' });
        let total = 0;
        this.lsTable.forEach((tr) => {
            tbody.push([{ html: icon + tr.name, class: "fw-bold pointer", onclick: "showData('ant" + tr.id + "')" }, { html: format_number(tr.acumulado), class: "text-end fw-bold" }, ...extra]);

            tr.anticipos.forEach((tr2) => {
                button.attr("onclick", "formato_ancitipos(" + tr2.id + ")");

                total += parseFloat(tr2.saldo);

                const extra2 = perfilesAceptados.includes(getCookies()["IDP"]) ? [{ html: button.prop("outerHTML"), class: "text-center" }] : [];

                tbody.push([{ tr: { class: "hide ant" + tr.id } }, { html: tr2.fecha, class: "fst-italic" }, { html: format_number(tr2.saldo), class: "text-end" }, ...extra2]);
            });
        });

        let tfoot = "";
        if (Object.keys(this.lsTable).length > 0) tfoot = [{ html: "Total" }, { html: format_number(total), class: "text-end" }, ...extra];

        this.table = "#tbAnticipo";

        return {
            table: { id: "tbAnticipo" },
            thead,
            tbody,
            tfoot,
        };
    }
}
