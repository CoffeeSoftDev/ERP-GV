class Admin extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "Admin";
        this.currentReportType = "pedidos";
    }


    render() {
        this.layout();

        channel.render();
        product.render();
    }

    layout() {

        this.tabLayout({
            parent: `container-admin`,
            id: `tabs${this.PROJECT_NAME}`,
            theme: "light",
            class: "h-full",
            type: "button",
            json: [
                {
                    id: "channel",
                    tab: "Canal de ventas",
                    active: true,
                    onClick: () => channel.lsCanales()
                },
                {
                    id: "products",
                    tab: "Productos",
                    onClick: () => product.lsProductos()
                },

                // {
                //     id: "migration",
                //     tab: "Subir registros",
                //
                //     // onClick: () => product.lsProductos()
                // },


            ]
        });

        setTimeout(() => {
            $('#content-tabsAdmin').addClass('h-full flex flex-col');
        }, 100);

    }


}

class AdminChannel extends Admin {

    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "AdminChannel";
        this.currentReportType = "pedidos";
    }

    render() {
        this.layout();
        this.filterBar();
        this.lsCanales();
    }

    layout() {

        this.primaryLayout({
            parent: "container-channel",
            id: this.PROJECT_NAME,
            class: "h-full flex flex-col",
            card: {
                filterBar: { class: "w-full ", id: "filterBar" + this.PROJECT_NAME },
                container: { class: "w-full flex-1 overflow-auto", id: "container" + this.PROJECT_NAME }
            }
        });


    }

    filterBar() {

        this.createfilterBar({
            parent: `filterBar${this.PROJECT_NAME}`,
            data: [
                {
                    opc: "select",
                    id: "active",
                    lbl: "Estado",
                    class: "col-sm-2",
                    data: [
                        { id: "1", valor: "Activo" },
                        { id: "0", valor: "Inactivo" }
                    ],
                    onchange: `channel.lsCanales()`
                },
                {
                    opc: "button",
                    class: "col-sm-3",
                    id: "btnNuevoCanal",
                    text: "Nuevo Canal",
                    onClick: () => this.addCanal()
                }
            ]
        });


    }

    lsCanales() {
        this.createTable({
            parent: "container" + this.PROJECT_NAME,
            idFilterBar: "filterBarAdminChannel",
            data: { opc: "lsCanales" },
            coffeesoft: true,
            conf: { datatable: true, pag: 15 },
            attr: {
                id: "tbCanales",
                theme: 'corporativo',
                center: [2, 3]
            }
        });
    }

    addCanal() {
        this.createModalForm({
            id: 'formCanalAdd',
            data: { opc: 'addCanal' },
            bootbox: {
                title: 'Agregar Canal',
            },
            json: this.jsonCanal(),
            success: (response) => {
                if (response.status === 200) {
                    alert({
                        icon: "success",
                        text: response.message,
                        btn1: true
                    });
                    this.lsCanales();
                } else {
                    alert({
                        icon: response.status === 409 ? "warning" : "error",
                        title: response.status === 409 ? "Canal duplicado" : "Error",
                        text: response.message,
                        btn1: true
                    });
                }
            }
        });
    }

    async editCanal(id) {
        const request = await useFetch({
            url: this._link,
            data: { opc: "getCanal", id }
        });

        if (request.status !== 200) {
            alert({
                icon: "error",
                text: request.message,
                btn1: true
            });
            return;
        }

        this.createModalForm({
            id: 'formCanalEdit',
            data: { opc: 'editCanal', id },
            bootbox: {
                title: 'Editar Canal',
            },
            autofill: request.data,
            json: this.jsonCanal(),
            success: (response) => {
                if (response.status === 200) {
                    alert({
                        icon: "success",
                        text: response.message,
                        btn1: true
                    });
                    this.lsCanales();
                } else {
                    alert({
                        icon: "error",
                        text: response.message,
                        btn1: true
                    });
                }
            }
        });
    }

    statusCanal(id, active) {
        const accion = active === 1 ? "desactivar" : "activar";

        this.swalQuestion({
            opts: {
                title: `¿${accion.charAt(0).toUpperCase() + accion.slice(1)} canal?`,
                text: `Esta acción ${accion === "desactivar" ? "ocultará" : "mostrará"} el canal.`,
                icon: "warning"
            },
            data: { opc: "statusCanal", active: active === 1 ? 0 : 1, id },
            methods: {
                send: (response) => {
                    if (response.status === 200) {
                        alert({
                            icon: "success",
                            text: response.message,
                            btn1: true
                        });
                        this.lsCanales();
                    }
                }
            }
        });
    }

    jsonCanal() {
        return [
            {
                opc: "input",
                id: "nombre",
                lbl: "Nombre del Canal",
                class: "col-12 mb-3"
            }
        ];
    }



}

class AdminProducts extends Admin {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "AdminProducts";
    }

    render() {
        this.layout();
        this.filterBar();
        this.lsProductos();
    }

    layout() {
        this.primaryLayout({
            parent: `container-products`,
            id: this.PROJECT_NAME,
            class: 'h-full flex flex-col',
            card: {
                filterBar: { class: 'w-full border-b pb-2', id: `filterBar${this.PROJECT_NAME}` },
                container: { class: 'w-full flex-1 overflow-auto', id: `container${this.PROJECT_NAME}` }
            }
        });

        $(`#container${this.PROJECT_NAME}`).prepend(`
            <div class="px-4 pt-3 pb-3">
                <h2 class="text-2xl font-semibold">📦 Administrador de Productos</h2>
                <p class="text-gray-400">Gestiona los productos asociados a cada unidad de negocio.</p>
            </div>
        `);
    }

    filterBar() {
        this.createfilterBar({
            parent: `filterBar${this.PROJECT_NAME}`,
            data: [
                {
                    opc: "select",
                    id: "udn",
                    lbl: "Unidad de Negocio",
                    class: "col-12 col-md-2",
                    data: lsudn,
                    onchange: 'product.lsProductos()'
                },
                {
                    opc: "select",
                    id: "estado-productos",
                    lbl: "Estado",
                    class: "col-12 col-md-2",
                    data: [
                        { id: "1", valor: "Activo" },
                        { id: "0", valor: "Inactivo" }
                    ],
                    onchange: 'product.lsProductos()'
                },
                {
                    opc: "button",
                    class: "col-12 col-md-3",
                    id: "btnNuevoProducto",
                    text: "Nuevo Producto",
                    onClick: () => this.addProducto(),
                },
            ],
        });
    }

    lsProductos() {

        this.createTable({
            parent: `container${this.PROJECT_NAME}`,
            idFilterBar: `filterBar${this.PROJECT_NAME}`,
            data: {
                opc: "lsProductos",
                'estado-productos': $(`#filterBar${this.PROJECT_NAME} #estado-productos`).val() || 1,
                udn: $(`#filterBar${this.PROJECT_NAME} #udn`).val() || null
            },
            coffeesoft: true,
            conf: { datatable: true, pag: 15 },
            attr: {
                id: `tbProductos`,
                theme: 'corporativo',

                center: [2, 4],
                right: [5]
            },
        });
    }

    addProducto() {
        this.createModalForm({
            id: 'formProductoAdd',
            data: { opc: 'addProducto' },
            bootbox: {
                title: 'Agregar Producto',
            },
            json: this.jsonProducto(),
            success: (response) => {
                if (response.status === 200) {
                    alert({ icon: "success", text: response.message });
                    this.lsProductos();
                } else {
                    alert({
                        icon: response.status === 409 ? "warning" : "error",
                        title: "Oops!...",
                        text: response.message,
                        btn1: true,
                        btn1Text: "Ok"
                    });
                }
            }
        });
    }

    async editProducto(id) {
        const request = await useFetch({
            url: this._link,
            data: {
                opc: "getProducto",
                id: id,
            },
        });

        if (request.status !== 200) {
            alert({
                icon: "error",
                text: request.message,
                btn1: true,
                btn1Text: "Ok"
            });
            return;
        }

        const producto = request.data;

        this.createModalForm({
            id: 'formProductoEdit',
            data: { opc: 'editProducto', id: producto.id },
            bootbox: {
                title: 'Editar Producto',
            },
            autofill: producto,
            json: this.jsonProducto(),
            success: (response) => {
                if (response.status === 200) {
                    alert({ icon: "success", text: response.message });
                    this.lsProductos();
                } else {
                    alert({
                        icon: "error",
                        title: "Oops!...",
                        text: response.message,
                        btn1: true,
                        btn1Text: "Ok"
                    });
                }
            }
        });
    }

    statusProducto(id, active) {
        this.swalQuestion({
            opts: {
                title: "¿Desea cambiar el estado del Producto?",
                text: "Esta acción ocultará o reactivará el producto.",
                icon: "warning",
            },
            data: {
                opc: "statusProducto",
                active: active === 1 ? 0 : 1,
                id: id,
            },
            methods: {
                send: (response) => {
                    if (response.status === 200) {
                        alert({ icon: "success", text: response.message });
                        this.lsProductos();
                    } else {
                        alert({
                            icon: "error",
                            text: response.message,
                            btn1: true,
                            btn1Text: "Ok"
                        });
                    }
                },
            },
        });
    }

    jsonProducto() {
        return [
            {
                opc: "input",
                id: "nombre",
                lbl: "Nombre del Producto",
                class: "col-12 mb-3",
                required: true
            },
            {
                opc: "textarea",
                id: "descripcion",
                lbl: "Descripción",
                class: "col-12 mb-3",
                rows: 3
            },
            {
                opc: "select",
                id: "udn_id",
                lbl: "Unidad de Negocio",
                class: "col-12 mb-3",
                data: lsudn,
                text: "valor",
                value: "id",
                required: true
            }
        ];
    }
}

class Migration extends Admin {

    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "Migration";
    }

    render() {
        this.layout();
        this.filterBar();
        // this.lsProductos();
    }

    layout() {
        this.primaryLayout({
            parent: `container-migration`,
            id: this.PROJECT_NAME,
            class: 'h-full flex flex-col',
            card: {
                filterBar: { class: 'w-full  pb-2', id: `filterBar${this.PROJECT_NAME}` },
                container: { class: 'w-full flex-1 overflow-auto', id: `container${this.PROJECT_NAME}` }
            }
        });


    }

    filterBar() {

        this.createfilterBar({
            parent: `filterBar${this.PROJECT_NAME}`,
            data: [
                {
                    opc: "select",
                    id: "udn",
                    lbl: "Unidad de Negocio",
                    class: "col-12 col-md-2",
                    data: lsudn,
                    onchange: 'migration.ls()'
                },
                {
                    opc: "input-file",
                    text: "Subir archivo",
                    id: "btnSubir",
                    color_btn: " bg-orange-400 hover:bg-orange-600 text-white",
                    fn: "migration.fileUpload()",
                    class: "col-12 col-sm-6 col-lg-2",
                },

            ]
        });


    }

}

