let api = 'ctrl/ctrl-cuentamayor.php';
let app, subAccount, purchaseType, paymentMethod;
let lsudn;

$(async () => {
    const data = await useFetch({ url: api, data: { opc: "init" } });
    lsudn = data.udn;

    app = new App(api, "root");
    subAccount = new SubAccount(api, "root");
    purchaseType = new PurchaseType(api, "root");
    paymentMethod = new PaymentMethod(api, "root");

    app.render();
});

class App extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "cuentamayor";
    }

    render() {
        this.layout();
        this.filterBar();
        this.lsCuentaMayor();
    }

    layout() {
        this.primaryLayout({
            parent: "root",
            id: this.PROJECT_NAME,
            class: 'w-full',
            card: {
                filterBar: { class: 'w-full border-b pb-2', id: `filterBar${this.PROJECT_NAME}` },
                container: { class: 'w-full my-2 h-full', id: `container${this.PROJECT_NAME}` }
            }
        });

        this.tabLayout({
            parent: `container${this.PROJECT_NAME}`,
            id: `tabs${this.PROJECT_NAME}`,
            theme: "light",
            type: "short",
            json: [
                {
                    id: "cuentamayor",
                    tab: "Cuenta de mayor",
                    class: "mb-1",
                    active: true,
                    onClick: () => this.lsCuentaMayor()
                },
                {
                    id: "subcuenta",
                    tab: "Subcuenta de mayor",
                    onClick: () => subAccount.lsSubcuenta()
                },
                {
                    id: "tipocompra",
                    tab: "Tipos de compra",
                    onClick: () => purchaseType.lsTipoCompra()
                },
                {
                    id: "formapago",
                    tab: "Formas de pago",
                    onClick: () => paymentMethod.lsFormaPago()
                }
            ]
        });

        $(`#container${this.PROJECT_NAME}`).prepend(`
            <div class="px-4 pt-3 pb-3">
                <h2 class="text-2xl font-semibold">📊 Módulo de Cuentas de Mayor</h2>
                <p class="text-gray-400">Gestiona cuentas de mayor, subcuentas, tipos de compra y formas de pago.</p>
            </div>
        `);
    }

    filterBar() {
        const container = $(`#container-cuentamayor`);
        container.html('<div id="filterbar-cuentamayor" class="mb-2"></div><div id="tabla-cuentamayor"></div>');

        this.createfilterBar({
            parent: "filterbar-cuentamayor",
            data: [
                {
                    opc: "select",
                    id: "udn",
                    lbl: "Unidad de negocio",
                    class: "col-12 col-md-3",
                    data: lsudn,
                    onchange: 'app.lsCuentaMayor()'
                },
                {
                    opc: "button",
                    class: "col-12 col-md-3",
                    id: "btnNuevaCuenta",
                    text: "Agregar nueva cuenta de mayor",
                    onClick: () => this.addCuentaMayor()
                }
            ]
        });
    }

    lsCuentaMayor() {
        this.createTable({
            parent: "tabla-cuentamayor",
            idFilterBar: "filterbar-cuentamayor",
            data: { opc: "lsCuentaMayor" },
            coffeesoft: true,
            conf: { datatable: true, pag: 10 },
            attr: {
                id: "tbCuentaMayor",
                theme: 'corporativo',
                title: 'Lista de Cuentas de Mayor',
                subtitle: 'Cuentas registradas en el sistema',
                center: [1],
                right: [2]
            }
        });
    }

    addCuentaMayor() {
        this.createModalForm({
            id: 'formCuentaMayorAdd',
            data: { opc: 'addCuentaMayor' },
            bootbox: {
                title: 'Nueva Cuenta de Mayor'
            },
            json: this.jsonCuentaMayor(),
            success: (response) => {
                if (response.status === 200) {
                    alert({ icon: "success", text: response.message });
                    this.lsCuentaMayor();
                } else {
                    alert({ icon: "info", title: "Oops!...", text: response.message, btn1: true, btn1Text: "Ok" });
                }
            }
        });
    }

    async editCuentaMayor(id) {
        const request = await useFetch({
            url: this._link,
            data: {
                opc: "getCuentaMayor",
                id: id
            }
        });

        const cuenta = request.data;

        this.createModalForm({
            id: 'formCuentaMayorEdit',
            data: { opc: 'editCuentaMayor', id: cuenta.id },
            bootbox: {
                title: 'Editar Cuenta de Mayor'
            },
            autofill: cuenta,
            json: this.jsonCuentaMayor(),
            success: (response) => {
                if (response.status === 200) {
                    alert({ icon: "success", text: response.message });
                    this.lsCuentaMayor();
                } else {
                    alert({ icon: "info", title: "Oops!...", text: response.message });
                }
            }
        });
    }

    statusCuentaMayor(id, active) {
        const message = active === 1 
            ? "La cuenta mayor ya no estará disponible, pero seguirá reflejándose en los registros contables."
            : "La cuenta mayor ya estará disponible, para la captura de información.";

        this.swalQuestion({
            opts: {
                title: active === 1 ? "¿Desactivar cuenta de mayor?" : "¿Activar cuenta de mayor?",
                text: message,
                icon: "warning"
            },
            data: {
                opc: "statusCuentaMayor",
                active: active === 1 ? 0 : 1,
                id: id
            },
            methods: {
                send: (response) => {
                    if (response.status === 200) {
                        alert({ icon: "success", text: response.message });
                        this.lsCuentaMayor();
                    } else {
                        alert({ icon: "info", title: "Oops!...", text: response.message });
                    }
                }
            }
        });
    }

    jsonCuentaMayor() {
        const udnValue = $('#filterbar-cuentamayor #udn').val();
        return [
            {
                opc: "select",
                id: "udn_id",
                lbl: "Unidad de negocio",
                class: "col-12 mb-3",
                data: lsudn,
                disabled: true,
                value: udnValue
            },
            {
                opc: "input",
                id: "name",
                lbl: "Nombre de la cuenta mayor",
                class: "col-12 mb-3",
                required: true
            },
            {
                opc: "textarea",
                id: "description",
                lbl: "Descripción",
                class: "col-12 mb-3",
                rows: 3
            }
        ];
    }
}

class SubAccount extends App {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "subcuenta";
    }

    lsSubcuenta() {
        const container = $(`#container-subcuenta`);
        container.html('<div id="filterbar-subcuenta" class="mb-2"></div><div id="tabla-subcuenta"></div>');

        this.filterBarSubcuenta();

        this.createTable({
            parent: "tabla-subcuenta",
            idFilterBar: "filterbar-subcuenta",
            data: { opc: "lsSubcuenta" },
            coffeesoft: true,
            conf: { datatable: true, pag: 10 },
            attr: {
                id: "tbSubcuenta",
                theme: 'corporativo',
                title: 'Lista de Subcuentas',
                subtitle: 'Subcuentas registradas en el sistema'
            }
        });
    }

    filterBarSubcuenta() {
        this.createfilterBar({
            parent: "filterbar-subcuenta",
            data: [
                {
                    opc: "select",
                    id: "udn",
                    lbl: "Unidad de negocio",
                    class: "col-12 col-md-3",
                    data: lsudn,
                    onchange: 'subAccount.lsSubcuenta()'
                },
                {
                    opc: "button",
                    class: "col-12 col-md-3",
                    id: "btnNuevaSubcuenta",
                    text: "Agregar subcuenta",
                    onClick: () => this.addSubcuenta()
                }
            ]
        });
    }

    addSubcuenta() {
        this.createModalForm({
            id: 'formSubcuentaAdd',
            data: { opc: 'addSubcuenta' },
            bootbox: {
                title: 'Nueva Subcuenta'
            },
            json: this.jsonSubcuenta(),
            success: (response) => {
                if (response.status === 200) {
                    alert({ icon: "success", text: response.message });
                    this.lsSubcuenta();
                } else {
                    alert({ icon: "info", title: "Oops!...", text: response.message, btn1: true, btn1Text: "Ok" });
                }
            }
        });
    }

    async editSubcuenta(id) {
        const request = await useFetch({
            url: this._link,
            data: {
                opc: "getSubcuenta",
                id: id
            }
        });

        const subcuenta = request.data;

        this.createModalForm({
            id: 'formSubcuentaEdit',
            data: { opc: 'editSubcuenta', id: subcuenta.id },
            bootbox: {
                title: 'Editar Subcuenta'
            },
            autofill: subcuenta,
            json: this.jsonSubcuenta(),
            success: (response) => {
                if (response.status === 200) {
                    alert({ icon: "success", text: response.message });
                    this.lsSubcuenta();
                } else {
                    alert({ icon: "info", title: "Oops!...", text: response.message });
                }
            }
        });
    }

    statusSubcuenta(id, active) {
        const message = active === 1 
            ? "La subcuenta ya no estará disponible, pero seguirá reflejándose en los registros contables."
            : "La subcuenta ya estará disponible, para la captura de información.";

        this.swalQuestion({
            opts: {
                title: active === 1 ? "¿Desactivar subcuenta?" : "¿Activar subcuenta?",
                text: message,
                icon: "warning"
            },
            data: {
                opc: "statusSubcuenta",
                active: active === 1 ? 0 : 1,
                id: id
            },
            methods: {
                send: (response) => {
                    if (response.status === 200) {
                        alert({ icon: "success", text: response.message });
                        this.lsSubcuenta();
                    } else {
                        alert({ icon: "info", title: "Oops!...", text: response.message });
                    }
                }
            }
        });
    }

    jsonSubcuenta() {
        return [
            {
                opc: "input",
                id: "name",
                lbl: "Nombre de la subcuenta",
                class: "col-12 mb-3",
                required: true
            }
        ];
    }
}

class PurchaseType extends App {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "tipocompra";
    }

    lsTipoCompra() {
        const container = $(`#container-tipocompra`);
        container.html('<div id="filterbar-tipocompra" class="mb-2"></div><div id="tabla-tipocompra"></div>');

        this.filterBarTipoCompra();

        this.createTable({
            parent: "tabla-tipocompra",
            idFilterBar: "filterbar-tipocompra",
            data: { opc: "lsTipoCompra" },
            coffeesoft: true,
            conf: { datatable: true, pag: 10 },
            attr: {
                id: "tbTipoCompra",
                theme: 'corporativo',
                title: 'Lista de Tipos de Compra',
                subtitle: 'Tipos de compra registrados en el sistema'
            }
        });
    }

    filterBarTipoCompra() {
        this.createfilterBar({
            parent: "filterbar-tipocompra",
            data: [
                {
                    opc: "select",
                    id: "udn",
                    lbl: "Unidad de negocio",
                    class: "col-12 col-md-3",
                    data: lsudn,
                    onchange: 'purchaseType.lsTipoCompra()'
                },
                {
                    opc: "button",
                    class: "col-12 col-md-3",
                    id: "btnNuevoTipo",
                    text: "Agregar tipo de compra",
                    onClick: () => this.addTipoCompra()
                }
            ]
        });
    }

    addTipoCompra() {
        this.createModalForm({
            id: 'formTipoCompraAdd',
            data: { opc: 'addTipoCompra' },
            bootbox: {
                title: 'Nuevo Tipo de Compra'
            },
            json: this.jsonTipoCompra(),
            success: (response) => {
                if (response.status === 200) {
                    alert({ icon: "success", text: response.message });
                    this.lsTipoCompra();
                } else {
                    alert({ icon: "info", title: "Oops!...", text: response.message, btn1: true, btn1Text: "Ok" });
                }
            }
        });
    }

    async editTipoCompra(id) {
        const request = await useFetch({
            url: this._link,
            data: {
                opc: "getTipoCompra",
                id: id
            }
        });

        const tipo = request.data;

        this.createModalForm({
            id: 'formTipoCompraEdit',
            data: { opc: 'editTipoCompra', id: tipo.id },
            bootbox: {
                title: 'Editar Tipo de Compra'
            },
            autofill: tipo,
            json: this.jsonTipoCompra(),
            success: (response) => {
                if (response.status === 200) {
                    alert({ icon: "success", text: response.message });
                    this.lsTipoCompra();
                } else {
                    alert({ icon: "info", title: "Oops!...", text: response.message });
                }
            }
        });
    }

    statusTipoCompra(id, active) {
        const message = active === 1 
            ? "El tipo de compra ya no estará disponible, pero seguirá reflejándose en los registros contables."
            : "El tipo de compra ya estará disponible, para la captura de información.";

        this.swalQuestion({
            opts: {
                title: active === 1 ? "¿Desactivar tipo de compra?" : "¿Activar tipo de compra?",
                text: message,
                icon: "warning"
            },
            data: {
                opc: "statusTipoCompra",
                active: active === 1 ? 0 : 1,
                id: id
            },
            methods: {
                send: (response) => {
                    if (response.status === 200) {
                        alert({ icon: "success", text: response.message });
                        this.lsTipoCompra();
                    } else {
                        alert({ icon: "info", title: "Oops!...", text: response.message });
                    }
                }
            }
        });
    }

    jsonTipoCompra() {
        const udnValue = $('#filterbar-tipocompra #udn').val();
        return [
            {
                opc: "select",
                id: "udn_id",
                lbl: "Unidad de negocio",
                class: "col-12 mb-3",
                data: lsudn,
                disabled: true,
                value: udnValue
            },
            {
                opc: "input",
                id: "nombre",
                lbl: "Nombre del tipo de compra",
                class: "col-12 mb-3",
                required: true
            },
            {
                opc: "textarea",
                id: "descripcion",
                lbl: "Descripción",
                class: "col-12 mb-3",
                rows: 3
            }
        ];
    }
}

class PaymentMethod extends App {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "formapago";
    }

    lsFormaPago() {
        const container = $(`#container-formapago`);
        container.html('<div id="filterbar-formapago" class="mb-2"></div><div id="tabla-formapago"></div>');

        this.filterBarFormaPago();

        this.createTable({
            parent: "tabla-formapago",
            idFilterBar: "filterbar-formapago",
            data: { opc: "lsFormaPago" },
            coffeesoft: true,
            conf: { datatable: true, pag: 10 },
            attr: {
                id: "tbFormaPago",
                theme: 'corporativo',
                title: 'Lista de Formas de Pago',
                subtitle: 'Formas de pago registradas en el sistema'
            }
        });
    }

    filterBarFormaPago() {
        this.createfilterBar({
            parent: "filterbar-formapago",
            data: [
                {
                    opc: "select",
                    id: "udn",
                    lbl: "Unidad de negocio",
                    class: "col-12 col-md-3",
                    data: lsudn,
                    onchange: 'paymentMethod.lsFormaPago()'
                },
                {
                    opc: "button",
                    class: "col-12 col-md-3",
                    id: "btnNuevaForma",
                    text: "Agregar forma de pago",
                    onClick: () => this.addFormaPago()
                }
            ]
        });
    }

    addFormaPago() {
        this.createModalForm({
            id: 'formFormaPagoAdd',
            data: { opc: 'addFormaPago' },
            bootbox: {
                title: 'Nueva Forma de Pago'
            },
            json: this.jsonFormaPago(),
            success: (response) => {
                if (response.status === 200) {
                    alert({ icon: "success", text: response.message });
                    this.lsFormaPago();
                } else {
                    alert({ icon: "info", title: "Oops!...", text: response.message, btn1: true, btn1Text: "Ok" });
                }
            }
        });
    }

    async editFormaPago(id) {
        const request = await useFetch({
            url: this._link,
            data: {
                opc: "getFormaPago",
                id: id
            }
        });

        const forma = request.data;

        this.createModalForm({
            id: 'formFormaPagoEdit',
            data: { opc: 'editFormaPago', id: forma.id },
            bootbox: {
                title: 'Editar Forma de Pago'
            },
            autofill: forma,
            json: this.jsonFormaPago(),
            success: (response) => {
                if (response.status === 200) {
                    alert({ icon: "success", text: response.message });
                    this.lsFormaPago();
                } else {
                    alert({ icon: "info", title: "Oops!...", text: response.message });
                }
            }
        });
    }

    statusFormaPago(id, active) {
        const message = active === 1 
            ? "La forma de pago ya no estará disponible, pero seguirá reflejándose en los registros contables."
            : "La forma de pago ya estará disponible, para la captura de información.";

        this.swalQuestion({
            opts: {
                title: active === 1 ? "¿Desactivar forma de pago?" : "¿Activar forma de pago?",
                text: message,
                icon: "warning"
            },
            data: {
                opc: "statusFormaPago",
                active: active === 1 ? 0 : 1,
                id: id
            },
            methods: {
                send: (response) => {
                    if (response.status === 200) {
                        alert({ icon: "success", text: response.message });
                        this.lsFormaPago();
                    } else {
                        alert({ icon: "info", title: "Oops!...", text: response.message });
                    }
                }
            }
        });
    }

    jsonFormaPago() {
        const udnValue = $('#filterbar-formapago #udn').val();
        return [
            {
                opc: "select",
                id: "udn_id",
                lbl: "Unidad de negocio",
                class: "col-12 mb-3",
                data: lsudn,
                disabled: true,
                value: udnValue
            },
            {
                opc: "input",
                id: "nombre",
                lbl: "Nombre de la forma de pago",
                class: "col-12 mb-3",
                required: true
            },
            {
                opc: "textarea",
                id: "descripcion",
                lbl: "Descripción",
                class: "col-12 mb-3",
                rows: 3
            }
        ];
    }
}
