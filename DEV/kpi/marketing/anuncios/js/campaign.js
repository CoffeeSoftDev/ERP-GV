let api = 'ctrl/ctrl-campaign.php';
let api_admin = 'ctrl/ctrl-admin.php';
let app, campaign, dashboard, summary, history, admin;

let udn, red_social, tipo_anuncio, clasificacion;

$(async () => {
    let dataInit = await useFetch({ url: api, data: { opc: "init" } });
    udn = dataInit.udn;
    red_social = dataInit.red_social;
    tipo_anuncio = dataInit.tipo_anuncio;
    clasificacion = dataInit.clasificacion;

    app = new App(api, "root");
    campaign = new Campaign(api, "root");
    dashboard = new CampaignDashboard(api, "root");
    summary = new CampaignSummary(api, "root");
    history = new AnnualHistory(api, "root");
    admin = new Admin(api_admin, "root");

    app.render();
});

class App extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "Campaigns";
    }

    render() {
        this.layout();
        dashboard.render();
    }

    layout() {
        this.primaryLayout({
            parent: "root",
            id: this.PROJECT_NAME,
            class: "w-full",
            card: {
                filterBar: { class: "w-full", id: "filterBarCampaigns" },
                container: { class: "w-full h-full", id: "containerCampaigns" },
            },
        });

        this.headerBar({
            parent: `filterBarCampaigns`,
            title: "Módulo de Anuncios y Campañas ✨",
            subtitle: "Administra campañas, anuncios y métricas con facilidad.",
            onClick: () => app.redirectToHome(),
        });

        this.tabLayout({
            parent: `containerCampaigns`,
            id: `tabs${this.PROJECT_NAME}`,
            theme: "light",
            class: '',
            type: "short",
            json: [
                {
                    id: "dashboard",
                    tab: "Dashboard",
                    class: "mb-1",
                    active: true,
                    onClick: () => dashboard.render()
                },
                {
                    id: "campaigns",
                    tab: "Anuncios",

                    onClick: () => campaign.render()
                },
                {
                    id: "summary",
                    tab: "Resumen de Campaña",
                    onClick: () => summary.render()
                },
                {
                    id: "history",
                    tab: "Historial Anual",

                    onClick: () => history.render()
                },
                {
                    id: "admin",
                    tab: "Administrador",
                    onClick: () => admin.render()
                },
            ]
        });
        history.render()
        $('#content-tabsCampaigns').removeClass('h-screen');
    }

    redirectToHome() {
        const base = window.location.origin + '/ERP24';
        window.location.href = `${base}/kpi/marketing.php`;
    }

    headerBar(options) {
        const defaults = {
            parent: "root",
            title: "Título por defecto",
            subtitle: "Subtítulo por defecto",
            icon: "icon-home",
            textBtn: "Inicio",
            classBtn: "border-1 border-blue-700 text-blue-600 hover:bg-blue-700 hover:text-white transition-colors duration-200",
            onClick: null,
        };

        const opts = Object.assign({}, defaults, options);

        const container = $("<div>", {
            class: "relative flex justify-center items-center px-2 pt-3 pb-3"
        });

        // 🔵 Botón alineado a la izquierda (posición absoluta)
        const leftSection = $("<div>", {
            class: "absolute left-0"
        }).append(
            $("<button>", {
                class: `${opts.classBtn} font-semibold px-4 py-2 rounded transition flex items-center`,
                html: `<i class="${opts.icon} mr-2"></i>${opts.textBtn}`,
                click: () => typeof opts.onClick === "function" && opts.onClick()
            })
        );

        // 📜 Texto centrado
        const centerSection = $("<div>", {
            class: "text-center"
        }).append(
            $("<h2>", {
                class: "text-2xl font-bold",
                text: opts.title
            }),
            $("<p>", {
                class: "text-gray-400",
                text: opts.subtitle
            })
        );

        container.append(leftSection, centerSection);
        $(`#${opts.parent}`).html(container);
    }

}

class Campaign extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "Campaign";
        this.idCampaign = null;
    }

    async render() {
        this.layout();
        this.filterBar();
        this.lsCampaigns();

        let dataInit = await useFetch({ url: api, data: { opc: "init" } });

        red_social = dataInit.red_social;
        tipo_anuncio = dataInit.tipo_anuncio;
        clasificacion = dataInit.clasificacion;
    }

    layout() {
        this.primaryLayout({
            parent: `container-campaigns`,
            id: this.PROJECT_NAME,
            card: {
                filterBar: { class: 'w-full border-b pb-2', id: `filterBar${this.PROJECT_NAME}` },
                container: { class: 'w-full my-2 h-full', id: `container${this.PROJECT_NAME}` }
            }
        });
    }

    filterBar() {
        this.createfilterBar({
            parent: `filterBar${this.PROJECT_NAME}`,
            data: [
                {
                    opc: "select",
                    id: "udn_id",
                    lbl: "Unidad de Negocio",
                    class: "col-sm-3",
                    data: udn,
                    onchange: `campaign.lsCampaigns()`,
                },
                {
                    opc: "select",
                    id: "red_social_id",
                    lbl: "Red Social",
                    class: "col-sm-3",
                    data: red_social,
                    onchange: `campaign.lsCampaigns()`,
                },
                {
                    opc: "button",
                    class: "col-sm-3",
                    id: "btnNewCampaign",
                    text: "<i class='icon-plus'></i>Nueva Campaña",
                    onClick: () => this.addCampaign(),
                },
            ],
        });
    }

    lsCampaigns() {
        // 🎨 Contenedor principal
        $(`#container${this.PROJECT_NAME}`).html(`
            <div class="px-2 pt-2 pb-2">
                <h2 class="text-2xl font-semibold">📢 Anuncios de la Campaña</h2>
                <p>Gestiona anuncios de las campañas por red social o unidad de negocio</p>
            </div>
            <div id="container-table-announcements"></div>
        `);

        // 🔹 Generar tabla filtrada
        this.createTable({
            parent: "container-table-announcements",
            idFilterBar: `filterBarCampaign`,
            data: {
                opc: 'lsAnnouncements',
            },
            coffeesoft: true,
            conf: { datatable: true, pag: 15 },
            attr: {
                id: "tbAnnouncements",
                theme: 'corporativo',
                center: [0, 3, 4]
            },
        });
    }

    async addCampaign() {
        const result = await alert({
            icon: "question",
            title: "¿Deseas crear una nueva campaña?",
            text: "Nota: Las campañas por lo general se crean cada MES.",
            btn1: true,
            btn1Text: "Crear Campaña",
            btn2: true,
            btn2Text: "Cancelar",
        });

        if (result.isConfirmed) {
            const request = await useFetch({
                url: this._link,
                data: { opc: "addCampaign" },
            });
            campaign.idCampaign = request.data.id;

            bootbox.dialog({
                title: "📢 Nueva Campaña",
                size: "extra-large",
                closeButton: true,
                message: `
                    <div class="p-2">
                        <h2 class="text-xl font-semibold mb-4">CAMPAÑA DEL MES</h2>

                        <div class="grid grid-cols-3 gap-3 mb-4">
                            <div>
                                <label class="block text-sm font-medium">Estrategia</label>
                                <input type="text" class="form-control w-full border rounded px-2 py-1" id="modal_estrategia">
                            </div>
                            <div>
                                <label class="block text-sm font-medium">Unidad de negocio</label>
                                <select class="form-control w-full border rounded px-2 py-1" id="modal_udn_id"></select>
                            </div>
                            <div class="flex items-end gap-2">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium">Red social</label>
                                    <select class="form-control w-full border rounded px-2 py-1" id="modal_red_social_id"></select>
                                </div>
                                <button class="bg-blue-600 text-white px-3 py-2 rounded font-bold hover:bg-blue-700" id="btnAddAds" title="Agregar Anuncio">
                                    +
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-6" id="containerAds">
                            ${campaign._createAdForm(1)}
                            ${campaign._createAdForm(2)}
                        </div>
                    </div>
                `,
            });

            // 🧩 Poblar selects
            $('#modal_red_social_id').append(new Option("Selecciona una red", 0, true, true)).prop("disabled", false);
            $('#modal_udn_id').append(new Option("Selecciona una udn", 0, true, true)).prop("disabled", false);

            red_social.forEach(rs => {
                $('#modal_red_social_id').append(new Option(rs.valor, rs.id));
            });

            udn.forEach(u => {
                $('#modal_udn_id').append(new Option(u.valor, u.id));
            });
            // 🎯 Activar funcionalidad de imagen e inserción dinámica
            campaign._initImageUpload();
            campaign._initAddAds();
            campaign._initSaveAds();
            campaign._initAutoEditCampaign();
        }
    }

    _createAdForm(index) {
        // Generar opciones dinámicas
        const optionsTipo = tipo_anuncio.map(t => `<option value="${t.id}">${t.valor}</option>`).join("");
        const optionsClas = clasificacion.map(c => `<option value="${c.id}">${c.valor}</option>`).join("");

        return `
        <div class="border rounded-lg p-4 shadow-sm relative" id="adForm_${index}">
            <label class="block text-sm font-medium">Nombre del anuncio</label>
            <input type="text" class="form-control w-full border rounded px-2 py-1 mb-2" id="ad_name_${index}">

            <div class="grid grid-cols-2 gap-2 mb-2">
                <div>
                    <label class="block text-sm font-medium">Fecha inicio</label>
                    <input type="date" class="form-control w-full border rounded px-2 py-1" id="ad_start_${index}">
                </div>
                <div>
                    <label class="block text-sm font-medium">Fecha fin</label>
                    <input type="date" class="form-control w-full border rounded px-2 py-1" id="ad_end_${index}">
                </div>
            </div>

            <label class="block text-sm font-medium">Tipo de anuncio</label>
            <select class="form-control w-full border rounded px-2 py-1 mb-2" id="ad_type_${index}">
                <option value="">Selecciona</option>
                ${optionsTipo}
            </select>

            <label class="block text-sm font-medium">Clasificación</label>
            <select class="form-control w-full border rounded px-2 py-1 mb-2" id="ad_class_${index}">
                <option value="">Selecciona</option>
                ${optionsClas}
            </select>

            <label class="block text-sm font-medium">Imagen</label>
            <div 
                class="border border-dashed rounded-lg relative flex justify-center items-center mb-3 overflow-hidden group"
                id="imageContainer_${index}"
                style="height: 180px; background-color: #f9fafb;"
            >
                <input 
                    type="file" 
                    accept="image/*" 
                    class="absolute inset-0 opacity-0 cursor-pointer z-10" 
                    id="inputImage_${index}"
                >
                <i class="icon-upload text-4xl text-gray-400 group-hover:text-blue-600 transition" id="uploadIcon_${index}"></i>
                <img 
                    id="previewImage_${index}" 
                    class="hidden absolute inset-0 w-full h-full object-contain p-2 rounded-lg bg-white" 
                />
            </div>

            <button 
                id="btnSaveAd_${index}" 
                class="w-full bg-blue-200 hover:bg-blue-300 text-blue-800 font-semibold py-2 rounded">
                Guardar
            </button>
        </div>
        `;
    }

    _initImageUpload(selector = "[id^='inputImage_']") {
        $(selector).off("change").on("change", function (e) {
            const input = e.target;
            const file = input.files[0];
            const index = input.id.split("_")[1];
            const preview = $(`#previewImage_${index}`);
            const icon = $(`#uploadIcon_${index}`);

            if (file) {
                const reader = new FileReader();
                reader.onload = (ev) => {
                    preview.attr("src", ev.target.result).removeClass("hidden");
                    icon.addClass("hidden");
                };
                reader.readAsDataURL(file);
            }
        });
    }

    _initAddAds() {
        const self = this;

        $("#btnAddAds").off("click").on("click", function () {
            const $container = $("#containerAds");
            const count = $container.children().length + 1;

            // Crear nuevo bloque con selects ya poblados
            const newAd = self._createAdForm(count);
            $container.append(newAd);

            // Inicializar solo los eventos necesarios
            self._initImageUpload(`#inputImage_${count}`);
        });
    }

    _initSaveAds() {
        const self = this;

        // Delegar eventos a todos los botones de Guardar
        $(document).off("click", "[id^='btnSaveAd_']").on("click", "[id^='btnSaveAd_']", async function () {
            const index = this.id.split("_")[1];
            const formData = new FormData();

            // 📦 Campos básicos
            formData.append("opc", "addAnnouncement");
            formData.append("nombre", $(`#ad_name_${index}`).val());
            formData.append("fecha_inicio", $(`#ad_start_${index}`).val());
            formData.append("fecha_fin", $(`#ad_end_${index}`).val());
            formData.append("tipo_id", $(`#ad_type_${index}`).val());
            formData.append("clasificacion_id", $(`#ad_class_${index}`).val());
            formData.append("campaña_id", self.idCampaign); // 🔑 llave foránea

            // 🖼️ Imagen
            const file = $(`#inputImage_${index}`)[0].files[0];
            if (file) formData.append("image", file);

            try {
                const request = await fetch(self._link, { method: "POST", body: formData });
                const response = await request.json();

                // Backend retorna: { status, message, data }
                if (response.status == 200) {
                    alert(response.message);
                    self._convertToEditMode(index, response.data.id);
                    self._initUpdateAds();
                    self._initDeleteAds();
                    campaign.lsCampaigns();
                } else {
                    alert(response.message || "Error al guardar el anuncio.");
                }
            } catch (error) {
                alert("Error al enviar los datos al servidor.");
            }
        });
    }

    _initUpdateAds() {
        const self = this;

        $(document).off("click", "[id^='btnUpdateAd_']").on("click", "[id^='btnUpdateAd_']", async function () {
            const index = this.id.split("_")[1];
            const idAd = $(this).data("id");

            const formData = new FormData();
            formData.append("opc", "editAnnouncement");
            formData.append("id", idAd);
            formData.append("nombre", $(`#ad_name_${index}`).val());
            formData.append("fecha_inicio", $(`#ad_start_${index}`).val());
            formData.append("fecha_fin", $(`#ad_end_${index}`).val());
            formData.append("tipo_id", $(`#ad_type_${index}`).val());
            formData.append("clasificacion_id", $(`#ad_class_${index}`).val());
            formData.append("campaña_id", self.idCampaign);

            // 🖼️ Solo enviar si el usuario cambió la imagen
            const inputFile = $(`#inputImage_${index}`)[0];
            const file = inputFile?.files[0];
            const currentSrc = $(`#previewImage_${index}`).attr("src");

            if (file) {
                formData.append("image", file);
            } else if (currentSrc && !currentSrc.startsWith("blob:")) {
                // Solo enviamos la ruta actual si no es un preview temporal (blob)
                formData.append("imagen_actual", currentSrc);
            }

            try {
                const response = await fetch(self._link, { method: "POST", body: formData })
                    .then(r => r.json());

                if (response.status == 200) {
                    alert(response.message);
                    if (response.data?.image) {
                        $(`#previewImage_${index}`).attr("src", response.data.image);
                    }
                    campaign.lsCampaigns();
                } else {
                    alert(response.message || "Error al actualizar el anuncio.");
                }
            } catch (error) {
                alert("Error en la comunicación con el servidor.");
            }
        });
    }

    _initDeleteAds() {
        const self = this;

        $(document).off("click", "[id^='btnDeleteAd_']").on("click", "[id^='btnDeleteAd_']", async function () {
            const index = this.id.split("_")[1];
            const idAd = $(this).data("id");

            const confirm = await Swal.fire({
                icon: "warning",
                title: "¿Eliminar anuncio?",
                text: "Esta acción no se puede deshacer.",
                showCancelButton: true,
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar",
                confirmButtonColor: "#d33"
            });

            if (!confirm.isConfirmed) return;

            const formData = new FormData();
            formData.append("opc", "removeAnnouncement");
            formData.append("id", idAd);

            try {
                const request = await fetch(self._link, { method: "POST", body: formData });
                const response = await request.json();

                if (response.status === 200) {
                    Swal.fire({
                        icon: "success",
                        title: "Eliminado",
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });

                    // 🧹 Quitar del DOM
                    $(`#adForm_${index}`).fadeOut(300, function () {
                        $(this).remove();
                    });
                    campaign.lsCampaigns();
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: response.message
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Error en la comunicación con el servidor."
                });
            }
        });
    }

    _convertToEditMode(index, idAd) {
        const $form = $(`#adForm_${index}`);

        // Actualizar el botón
        $(`#btnSaveAd_${index}`).replaceWith(`
            <div class="flex gap-2 mt-2">
                <button id="btnUpdateAd_${index}" data-id="${idAd}" class="w-1/2 bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 rounded">
                    Actualizar
                </button>
                <button id="btnDeleteAd_${index}" data-id="${idAd}" class="w-1/2 bg-red-200 hover:bg-red-300 text-red-700 font-semibold py-2 rounded">
                    Eliminar
                </button>
            </div>
        `);

        // Eventos de actualización y eliminación
        campaign._initUpdateAds(index);
        campaign._initDeleteAds(index);
    }

    _initAutoEditCampaign() {
        const self = this;

        // 🧠 Helper para enviar cambios
        const updateField = async (field, value) => {
            if (!self.idCampaign) return alert("ID de campaña no definido.");

            try {
                const request = await useFetch({
                    url: self._link,
                    data: {
                        opc: "editCampaign",
                        [field]: value,
                        id: self.idCampaign
                    }
                });

                if (request.status != 200) {
                    alert(`❌ Error al actualizar ${field}: ${request.message}`);
                }
            } catch (err) {
                alert(`❌ Error en la comunicación al actualizar ${field}.`);
            }
        };

        // 🎯 Eventos automáticos de edición
        $(document)
            .off("blur", "#modal_estrategia")
            .on("blur", "#modal_estrategia", function () {
                const value = $(this).val().trim();
                if (value) updateField("estrategia", value);
            });

        $(document)
            .off("change", "#modal_udn_id")
            .on("change", "#modal_udn_id", function () {
                const value = $(this).val();
                if (value) updateField("udn_id", value);
            });

        $(document)
            .off("change", "#modal_red_social_id")
            .on("change", "#modal_red_social_id", function () {
                const value = $(this).val();
                if (value) updateField("red_social_id", value);
            });
    }

    async editCampaign(id) {
        try {
            // 🧩 1. Obtener datos de la campaña y sus anuncios
            const request = await useFetch({
                url: this._link,
                data: { opc: "getCampaign", id }
            });

            const data = request.data;
            if (!data) return alert("No se encontraron datos de la campaña.");

            campaign.idCampaign = data.campaña.id;

            // 🧩 2. Crear modal (mismo layout que addCampaign)
            bootbox.dialog({
                title: `📢 Editar Campaña — ${data.campaña.nombre || "Sin nombre"}`,
                size: "extra-large",
                closeButton: true,
                message: `
                <div class="p-2">
                    <h2 class="text-xl font-semibold mb-4">Editar Campaña</h2>

                    <div class="grid grid-cols-3 gap-3 mb-4">
                        <div>
                            <label class="block text-sm font-medium">Estrategia</label>
                            <input type="text" class="form-control w-full border rounded px-2 py-1" 
                                id="modal_estrategia" 
                                value="${data.campaña.estrategia || ""}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Unidad de negocio</label>
                            <select class="form-control w-full border rounded px-2 py-1" id="modal_udn_id"></select>
                        </div>
                        <div class="flex items-end gap-2">
                            <div class="flex-1">
                                <label class="block text-sm font-medium">Red social</label>
                                <select class="form-control w-full border rounded px-2 py-1" id="modal_red_social_id"></select>
                            </div>
                            <button class="bg-blue-600 text-white px-3 py-2 rounded font-bold hover:bg-blue-700" 
                                id="btnAddAds" 
                                title="Agregar Anuncio">+</button>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6" id="containerAds"></div>
                </div>
            `
            });

            // 🧩 3. Poblar selects con opción inicial
            $('#modal_red_social_id').append(new Option("Selecciona una red", 0, true, true));
            $('#modal_udn_id').append(new Option("Selecciona una udn", 0, true, true));

            red_social.forEach(rs => $('#modal_red_social_id').append(new Option(rs.valor, rs.id)));
            udn.forEach(u => $('#modal_udn_id').append(new Option(u.valor, u.id)));

            // Asignar valores actuales
            $("#modal_red_social_id").val(data.campaña.red_social_id);
            $("#modal_udn_id").val(data.campaña.udn_id);


            // 🧩 4. Renderizar anuncios existentes
            const $container = $("#containerAds");
            if (data.anuncios.length > 0) {
                data.anuncios.forEach((ad, i) => {
                    $container.append(campaign._createAdForm(i + 1));
                    $(`#ad_name_${i + 1}`).val(ad.nombre);
                    $(`#ad_start_${i + 1}`).val(ad.fecha_inicio);
                    $(`#ad_end_${i + 1}`).val(ad.fecha_fin);
                    $(`#ad_type_${i + 1}`).val(ad.tipo_id);
                    $(`#ad_class_${i + 1}`).val(ad.clasificacion_id);

                    if (ad.imagen != null && ad.imagen !== "") {
                        $(`#previewImage_${i + 1}`)
                            .attr("src", ad.imagen)
                            .removeClass("hidden");
                        $(`#uploadIcon_${i + 1}`).addClass("hidden");
                    } else {
                        $(`#previewImage_${i + 1}`).addClass("hidden");
                        $(`#uploadIcon_${i + 1}`).removeClass("hidden");
                    }

                    // Convertir botón a modo edición (Actualizar y Eliminar)
                    $(`#btnSaveAd_${i + 1}`).replaceWith(`
                        <div class="flex gap-2 mt-2">
                            <button id="btnUpdateAd_${i + 1}" data-id="${ad.id}" class="w-1/2 bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 rounded">
                                Actualizar
                            </button>
                            <button id="btnDeleteAd_${i + 1}" data-id="${ad.id}" class="w-1/2 bg-red-200 hover:bg-red-300 text-red-700 font-semibold py-2 rounded">
                                Eliminar
                            </button>
                        </div>
                    `);
                    // Activar eventos de actualización y eliminación para este anuncio
                    campaign._initUpdateAds(i + 1);
                    campaign._initDeleteAds(i + 1);
                });
            } else {
                // Si no hay anuncios, crear al menos 2 vacíos
                $container.append(campaign._createAdForm(1));
                $container.append(campaign._createAdForm(2));
            }

            // 🧩 5. Activar módulos funcionales
            campaign._initImageUpload();
            campaign._initAddAds();
            campaign._initUpdateAds();
            campaign._initSaveAds();
            campaign._initAutoEditCampaign();

        } catch (error) {
            alert("Error al cargar los datos de la campaña.");
        }
    }

    async captureResults(idAd) {
        const request = await useFetch({
            url: this._link,
            data: { opc: "getAnnouncement", id: idAd },
        });

        const announcementData = request.data;

        let resultDate = announcementData.fecha_resultado;
        let fechita = null;
        if (resultDate == null) {
            // Devuelve "YYYY-MM-DD HH:MM:SS" en la zona local
            fechita = new Date().toLocaleString('sv-SE', { hour12: false }).replace('T', ' ');

        } else {
            fechita = resultDate;
        }

        this.createModalForm({
            id: 'formCaptureResults',
            data: { opc: 'captureResults', fecha_resultado: fechita, id: idAd },
            bootbox: { title: '📊 Capturar Resultados del Anuncio' },
            autofill: announcementData,
            json: [
                {
                    opc: "label",
                    text: `Anuncio: ${announcementData.nombre}`,
                    class: "col-12 text-lg font-bold mb-3"
                },
                {
                    opc: "input",
                    id: "total_monto",
                    lbl: "Inversión Total ($)",
                    tipo: "number",
                    class: "col-12 mb-3",
                    onkeyup: "campaign.validationInputForNumber('#total_monto')"
                },
                {
                    opc: "input",
                    id: "total_clics",
                    lbl: "Total de Clics",
                    tipo: "number",
                    class: "col-12 mb-3",
                    onkeyup: "campaign.validationInputForNumber('#total_clics')"
                },
                {
                    opc: "input",
                    id: "cpc",
                    lbl: "CPC (Costo por Clic)",
                    tipo: "cifra",
                    class: "col-12 mb-3",
                },
                {
                    opc: "label",
                    id: "warning_update",
                    text: "", // aquí se rellena dinámicamente
                    class: "col-12 text-center font-semibold mt-2"
                }
            ],
            success: (response) => {
                if (response.status === 200) {
                    let cpc = 0;
                    let totalMonto = parseFloat($('#total_monto').val());
                    let totalClics = parseInt($('#total_clics').val());

                    if (totalClics > 0) {
                        cpc = totalMonto / totalClics;
                    }

                    alert({
                        icon: "success",
                        title: "Resultados Capturados",
                        text: `${response.message}\nCPC Calculado: $${cpc.toFixed(2)}`,
                        btn1: true,
                        btn1Text: "Aceptar"
                    });

                    campaign.lsCampaigns();
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

        // 🧩 Deshabilitar CPC
        $('#cpc').prop('disabled', true);

        // 🧠 Si ya hay datos capturados, calcular CPC y mostrar mensaje
        if (announcementData.total_monto > 0 && announcementData.total_clics > 0 && announcementData.fecha_resultado) {
            const cpc = announcementData.total_monto / announcementData.total_clics;
            $('#cpc').val(cpc.toFixed(2));

            const hoy = new Date();
            const fechaResultado = new Date(announcementData.fecha_resultado);
            const diffDays = Math.floor((hoy - fechaResultado) / (1000 * 60 * 60 * 24));

            // 🧮 Cálculo de margen
            const margen = 2 - diffDays;
            const $warning = $('#warning_update');

            if (margen >= 0) {
                if (margen == 0) {
                    // 🟠 Último día
                    $warning.html(`<span class="text-red-500">⚠️ Hoy es el último día para actualizar en caso de error!</span>`);
                } else {
                    // 🔴 Dentro del margen
                    $warning.html(`<span class="text-red-600">⚠️ Te quedan ${margen} día${margen !== 1 ? 's' : ''} para actualizar en caso de error!</span>`);
                }
            } else {
                // ⛔ Expirado
                $warning.html(`<span class="text-gray-500">⛔ El periodo de actualización ha expirado.</span>`);
                $('#total_monto, #total_clics').prop('disabled', true);
            }
        }
    }


    validationInputForNumber(selector) {
        const value = $(selector).val();
        const regex = /^\d*\.?\d*$/; // Permite solo números y un punto decimal 
        if (!regex.test(value)) {
            $(selector).val(value.slice(0, -1)); // Elimina el último carácter inválido
        }

        // Calcular CPC automáticamente si ambos campos están llenos
        const totalMonto = parseFloat($('#total_monto').val()) || 0;
        const totalClics = parseInt($('#total_clics').val()) || 0;
        if (totalMonto > 0 && totalClics > 0) {
            const cpc = totalMonto / totalClics;
            $('#cpc').val(cpc.toFixed(2));
        } else {
            $('#cpc').val('');
        }
    }

    async viewCampaign(id) {
        try {
            // 🧩 1. Obtener datos de la campaña y sus anuncios
            const request = await useFetch({
                url: this._link,
                data: { opc: "getCampaign", id }
            });

            const data = request.data;
            if (!data) return alert("No se encontraron datos de la campaña.");

            campaign.idCampaign = data.campaña.id;

            // 🧩 2. Crear modal en modo lectura
            bootbox.dialog({
                title: `👁️ Ver Campaña — ${data.campaña.nombre || "Sin nombre"}`,
                size: "extra-large",
                closeButton: true,
                message: `
                    <div class="p-2">
                        <h2 class="text-xl font-semibold mb-4">Detalles de la Campaña</h2>

                        <div class="grid grid-cols-3 gap-3 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Estrategia</label>
                                <p class="text-base font-semibold text-gray-800 border rounded px-2 py-1 bg-gray-50">
                                    ${data.campaña.estrategia || "Sin estrategia"}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500">Unidad de negocio</label>
                                <p class="text-base font-semibold text-gray-800 border rounded px-2 py-1 bg-gray-50">
                                    ${udn.find(u => u.id == data.campaña.udn_id)?.valor || "Sin UDN"}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500">Red social</label>
                                <p class="text-base font-semibold text-gray-800 border rounded px-2 py-1 bg-gray-50">
                                    ${red_social.find(rs => rs.id == data.campaña.red_social_id)?.valor || "Sin red social"}
                                </p>
                            </div>
                        </div>

                        <h3 class="text-lg font-semibold mb-2 border-b pb-1">Anuncios de la Campaña</h3>
                        <div class="grid grid-cols-2 gap-6" id="containerAdsView">
                            ${data.anuncios.length > 0
                        ? data.anuncios.map((ad, i) => campaign._createAdView(ad, i + 1)).join("")
                        : `<p class='text-gray-500 italic'>No hay anuncios registrados en esta campaña.</p>`}
                        </div>
                    </div>
                `
            });
        } catch (error) {
            alert("Error al cargar los datos de la campaña.");
        }
    }

    _createAdView(ad, index) {
        const tipo = tipo_anuncio.find(t => t.id == ad.tipo_id)?.valor || "Sin tipo";
        const clas = clasificacion.find(c => c.id == ad.clasificacion_id)?.valor || "Sin clasificación";

        return `
            <div class="border rounded-lg p-4 shadow-sm relative bg-gray-50" id="adView_${index}">
                <h4 class="text-base font-semibold mb-3 flex items-center gap-2">
                    <i class="icon-bullhorn text-blue-600"></i> 
                    Anuncio ${index}: ${ad.nombre || "Sin nombre"}
                </h4>

                <div class="grid grid-cols-2 gap-3 mb-2">
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Fecha inicio</label>
                        <p class="text-sm font-semibold text-gray-800 border rounded px-2 py-1 bg-white">
                            ${ad.fecha_inicio || "—"}
                        </p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Fecha fin</label>
                        <p class="text-sm font-semibold text-gray-800 border rounded px-2 py-1 bg-white">
                            ${ad.fecha_fin || "—"}
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 mb-2">
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Tipo</label>
                        <p class="text-sm font-semibold text-gray-800 border rounded px-2 py-1 bg-white">${tipo}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Clasificación</label>
                        <p class="text-sm font-semibold text-gray-800 border rounded px-2 py-1 bg-white">${clas}</p>
                    </div>
                </div>

                <label class="block text-xs font-medium text-gray-500 mb-1">Imagen</label>
                <div class="border rounded-lg overflow-hidden bg-white flex justify-center items-center" style="height:180px;">
                    ${ad.imagen
                ? `<img src="${ad.imagen}" class="w-full h-full object-contain p-2 rounded-lg" />`
                : `<div class="flex items-center justify-center w-full h-full text-gray-400">
                            <i class="icon-image text-4xl"></i> Sin imagen
                        </div>`
            }
                </div>
            </div>
        `;
    }
}
