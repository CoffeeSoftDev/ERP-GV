class Prestamos extends CH {
    #_list;
    constructor(ctrl) {
        super(ctrl);
    }
    get list() {
        return this.#_list;
    }
    set list(valor) {
        this.#_list = valor;
    }

    async simulardorInit() {
        this.lsInit = await this.fnDateUDN("listPrestamosColaboradores");
        const elementos = [
            { lbl: "Unidad de negocio", id: "cbUDN", elemento: "select", option: { data: this.lsInit.udn, placeholder: "-SELECCIONAR-" } },
            { lbl: "Colaborador", id: "cbColaboradores", disabled: true, elemento: "select", option: { data: [], placeholder: "-SELECCIONAR-" } },
            { lbl: "Antigüedad", id: "iptAlta", readonly: true },
            { lbl: "Área / Puesto", id: "iptPuesto", readonly: true },
        ];
        this.container.html("").create_elements(elementos);

        this.udn = "#cbUDN";

        this.udn.on("change", async () => {
            this.list = await this.fnDateUDN({ opc: "lsColaboradores", filtro: 1 });
            $("#cbColaboradores").prop("disabled", false).option_select({ data: this.list, select2: true, placeholder: "-SELECCIONAR-" });
        });

        $("#cbColaboradores").on("change", () => {
            const data = this.list.filter((i) => i.id == $("#cbColaboradores").val())[0];
            const antiguedad = diferenciaFechas(data.alta).s;
            $("#iptAlta").val(antiguedad);
            $("#iptPuesto").val(data.area + " / " + data.puesto);

            this.tbContainer.html("");
            this.createContent(this.tbContainer, [
                { class: "row m-0 p-0 col-12 col-md-6", id: "sumulacionQ" },
                { class: "row m-0 p-0 col-12 col-md-6 line-2", id: "simulacionM" },
                { class: "col-12 line-2", id: "tbDescuentos" },
            ]);

            this.simulacionQuincenal();
        });
    }

    simulacionQuincenal() {
        const data = this.list.filter((i) => i.id == $("#cbColaboradores").val())[0];
        console.log(data);
        const salarioQ = parseFloat(data.sd) * 15;

        const col6 = { div: { class: "col-12 col-md-6 mb-3" } };
        const col12 = { div: { class: "col-12" } };
        const gbIpt = { elemento: "input-group", tipo: "cifra" };

        const elementos = [
            { ...col6, ...gbIpt, lbl: "Cantidad solicitada" },
            { ...col6, ...gbIpt, lbl: "Salario Quincenal", value: salarioQ, readonly: true },
            { ...col6, ...gbIpt, lbl: "Descuento Fonacot" },
            { ...col6, ...gbIpt, lbl: "Descuento Infonavit" },
            { ...col6, ...gbIpt, lbl: "Total descuento quincenal" },
            { ...col6, ...gbIpt, lbl: "Total salario quincenal" },
            { ...col12, label:'==',elemento: "label" },
            { ...col6, ...gbIpt, lbl: "Total salario quincenal" },
            { ...col6, ...gbIpt, lbl: "Total salario quincenal" },
            { ...col6, ...gbIpt, lbl: "Total salario quincenal" },
        ];

        $("#sumulacionQ").create_elements(elementos);
    }

    // async filterBarPrestamos() {
    //     const lsUDN = await fn_ajax({ opc: "listUDN" }, this._ctrl);

    //     this.filterBar.html("").create_elements([{ lbl: "UNIDAD DE NEGOCIO", id: "cbUDN", elemento: "select", option: { data: lsUDN } }]);

    //     this.udn = "#cbUDN";

    //     this.udn.on("change", async () => {
    //         const udn = this.udn.find("option:selected").text();
    //         $(".card-header").html(udn + " - " + sessionStorage.getItem("tab"));
    //     });
    // }

    // async elementosSimulador() {
    //     this.lsInit = await this.fnDateUDN({ opc: "lsColaboradores", filtro: 1 });
    //     const col12 = { div: { class: "col-12 mb-3" } };
    //     const col4 = { div: { class: "col-12 col-md-4 mb-3" } };
    //     return [
    //         { ...col12, lbl: "Unidad de negocio", id: "cbUDN", elemento: "select", option: { data: this.lsInit, select2: true, father: true, placeholder: "- SELECCIONAR -" } },
    //         { ...col12, lbl: "Colaborador", id: "cbColaborador", elemento: "select", option: { data: this.lsInit, select2: true, father: true, placeholder: "- SELECCIONAR -" } },
    //         { ...col12, lbl: "Solicitud", elemento: "input-group", tipo: "cifra" },
    //         { ...col4, lbl: "Intereses", elemento: "input-group", icon: "%", pos: "r", tipo: "cifra", value: 2 },
    //         { ...col4, lbl: "Años", id: "cbYear", elemento: "input-group", icon: "#", pos: "l", tipo: "numero" },
    //         { ...col4, lbl: "Meses", id: "cbMonth", elemento: "input-group", icon: "#", pos: "l", tipo: "numero" },
    //     ];
    // }
    // async filterSimulador() {
    //     this.container.html("").create_elements([{ lbl: "<i class='icon-plus'></i> Simulación", elemento: "button", class: "btn btn-info col-12", onclick: "simulador()" }]);
    // }
    // async simulador() {
    //     this.createModal({
    //         title: "Datos del simulador",
    //         elements: await this.elementosSimulador(),
    //         size: "lg",
    //         fn: () => this.fnSimulador(),
    //     });
    // }
    // fnSimulador() {
    //     $("#cbColaborador").on("change", () => {
    //         const data = this.lsInit.filter((i) => i.id == $("#cbColaborador").val());
    //         console.log(data);
    //     });

    //     $("#cbYear").on("keyup", function () {
    //         const year = $(this).val();
    //         let month = 0;
    //         if ($(this).val()) month = parseInt(year) * 12;
    //         $("#cbMonth").val(month);
    //     });
    //     $("#cbMonth").on("keyup", function () {
    //         const month = $(this).val();
    //         let year = 0;
    //         if ($(this).val()) year = Math.floor(parseInt(month) / 12);
    //         $("#cbYear").val(year);
    //     });
    // }
}
