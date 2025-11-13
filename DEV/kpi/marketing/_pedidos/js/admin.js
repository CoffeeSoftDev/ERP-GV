class Admin extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "Admin";
        this.apiAdmin = 'ctrl/ctrl-admin.php';
    }

    render() {
        this.layout();
        this.renderTypes();
        this.lsTypes();

        // clasifications.
        this.renderClasification()
        this.lsClassifications();
     
    }

    layout() {

        this.primaryLayout({
            parent: `container-admin`,
            id: this.PROJECT_NAME,
            card: {
                filterBar: { class: 'w-full  pb-2', id: `filterBar${this.PROJECT_NAME}` },
                container: { class: 'w-full my-2 h-full', id: `container${this.PROJECT_NAME}` }
            }
        });

      

        this.tabLayout({
            parent: `container${this.PROJECT_NAME}`,
            id: `tabsAdmin`,
            class: '',
            type: "short",
            json: [
                {
                    id: "types",
                    tab: "Tipos de Anuncios",
                    class: "mb-1",
                    active: true,
                },
                {
                    id: "classifications",
                    tab: "Clasificaciones",
                },
            ]
        });
    }

    // Tipos de Anuncios

    renderTypes(){

        const container = $("#container-types");
        container.html('<div id="filterbar-types" class="mb-2"></div><div id="table-types"></div>');

        this.createfilterBar({
            parent: "filterbar-types",
            data: [
                {
                    opc: "select",
                    id: "active",
                    class: "col-12 col-md-3",
                    data: [
                        { id: "1", valor: "Activos" },
                        { id: "0", valor: "Inactivos" }
                    ],
                    onchange: 'admin.lsTypes()'
                },
                {
                    opc: "button",
                    class: "col-12 col-md-2",
                    className: 'w-full',
                    id: "btnNewType",
                    text: "Nuevo Tipo",
                    onClick: () => this.addType(),
                },
            ],
        });




    }

    lsTypes() {
       
        this.createTable({
            parent: "table-types",
            idFilterBar: "filterbar-types",
            data: { opc: "lsTypes" },
            coffeesoft: true,
            conf: { datatable: true, pag: 10 },
            attr: {
                id: "tbTypes",
                theme: 'corporativo',
                center: [2]
            },
            success: (data) => {
               
            }
        });
    }

    addType() {
        this.createModalForm({
            id: 'formTypeAdd',
            data: { opc: 'addType' },
            bootbox: {
                title: 'Agregar Tipo de Anuncio',
            },
            json: [
                {
                    opc: "input",
                    id: "nombre",
                    lbl: "Nombre del Tipo",
                    class: "col-12 mb-3",
                    placeholder: "Ej: Video, Publicación, Reel, Historia"
                }
            ],
            success: (response) => {
                if (response.status === 200) {
                    alert({
                        icon: "success",
                        text: response.message,
                        btn1: true,
                        btn1Text: "Aceptar"
                    });
                    this.lsTypes();
                } else {
                    alert({
                        icon: "error",
                        text: response.message,
                        btn1: true,
                        btn1Text: "Ok"
                    });
                }
            }
        });
        this._link = this.apiAdmin;
    }

    async editType(id) {
        const request = await useFetch({
            url: this.apiAdmin,
            data: { opc: "getType", id: id },
        });

        const typeData = request.data;

        this.createModalForm({
            id: 'formTypeEdit',
            data: { opc: 'editType', id: id },
            bootbox: {
                title: 'Editar Tipo de Anuncio',
            },
            autofill: typeData,
            json: [
                {
                    opc: "input",
                    id: "nombre",
                    lbl: "Nombre del Tipo",
                    class: "col-12 mb-3"
                }
            ],
            success: (response) => {
                if (response.status === 200) {
                    alert({
                        icon: "success",
                        text: response.message,
                        btn1: true,
                        btn1Text: "Aceptar"
                    });
                    this.lsTypes();
                } else {
                    alert({
                        icon: "error",
                        text: response.message,
                        btn1: true,
                        btn1Text: "Ok"
                    });
                }
            }
        });
        this._link = this.apiAdmin;
    }

    statusType(id, active) {
        this.swalQuestion({
            opts: {
                title: "¿Cambiar estado del tipo?",
                text: "Esta acción activará o desactivará el tipo de anuncio.",
                icon: "warning",
            },
            data: {
                opc: "statusType",
                active: active === 1 ? 0 : 1,
                id: id,
            },
            methods: {
                send: () => this.lsTypes(),
            },
        });
        this._link = this.apiAdmin;
    }

    // Clasificaciones
    renderClasification(){
        const container = $("#container-classifications");
        container.html('<div id="filterbar-classifications" class="mb-2"></div><div id="table-classifications"></div>');

        this.createfilterBar({
            parent: "filterbar-classifications",
            data: [
                {
                    opc: "select",
                    id: "active",
                    class: "col-12 col-md-3",
                    data: [
                        { id: "1", valor: "Activas" },
                        { id: "0", valor: "Inactivas" }
                    ],
                    onchange: 'admin.lsClassifications()'
                },
                {
                    opc: "button",
                    class: "col-12 col-md-2",
                    className: 'w-full',
                    id: "btnNewClassification",
                    text: "Nueva Clasificación",
                    onClick: () => this.addClassification(),
                },
            ],
        });
    }

    lsClassifications() {
       

        this.createTable({
            parent: "table-classifications",
            idFilterBar: "filterbar-classifications",
            data: { opc: "lsClassifications" },
            coffeesoft: true,
            conf: { datatable: true, pag: 10 },
            attr: {
                id: "tbClassifications",
                theme: 'corporativo',
                center: [2]
            },
            success: (data) => {
                this._link = this.apiAdmin;
            }
        });
    }

    addClassification() {
        this.createModalForm({
            id: 'formClassificationAdd',
            data: { opc: 'addClassification' },
            bootbox: {
                title: 'Agregar Clasificación',
            },
            json: [
                {
                    opc: "input",
                    id: "nombre",
                    lbl: "Nombre de la Clasificación",
                    class: "col-12 mb-3",
                    placeholder: "Ej: Pauta 1, Pauta 2, Video A, Video B"
                }
            ],
            success: (response) => {
                if (response.status === 200) {
                    alert({
                        icon: "success",
                        text: response.message,
                        btn1: true,
                        btn1Text: "Aceptar"
                    });
                    this.lsClassifications();
                } else {
                    alert({
                        icon: "error",
                        text: response.message,
                        btn1: true,
                        btn1Text: "Ok"
                    });
                }
            }
        });
        this._link = this.apiAdmin;
    }

    async editClassification(id) {
        const request = await useFetch({
            url: this.apiAdmin,
            data: { opc: "getClassification", id: id },
        });

        const classificationData = request.data;

        this.createModalForm({
            id: 'formClassificationEdit',
            data: { opc: 'editClassification', id: id },
            bootbox: {
                title: 'Editar Clasificación',
            },
            autofill: classificationData,
            json: [
                {
                    opc: "input",
                    id: "nombre",
                    lbl: "Nombre de la Clasificación",
                    class: "col-12 mb-3"
                }
            ],
            success: (response) => {
                if (response.status === 200) {
                    alert({
                        icon: "success",
                        text: response.message,
                        btn1: true,
                        btn1Text: "Aceptar"
                    });
                    this.lsClassifications();
                } else {
                    alert({
                        icon: "error",
                        text: response.message,
                        btn1: true,
                        btn1Text: "Ok"
                    });
                }
            }
        });
        this._link = this.apiAdmin;
    }

    statusClassification(id, active) {
        this.swalQuestion({
            opts: {
                title: "¿Cambiar estado de la clasificación?",
                text: "Esta acción activará o desactivará la clasificación.",
                icon: "warning",
            },
            data: {
                opc: "statusClassification",
                active: active === 1 ? 0 : 1,
                id: id,
            },
            methods: {
                send: () => this.lsClassifications(),
            },
        });
        this._link = this.apiAdmin;
    }
}
