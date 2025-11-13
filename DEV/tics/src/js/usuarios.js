// https://www3.animeflv.net/ver/kamitachi-ni-hirowareta-otoko-2
window.ctrlUser = "ctrl/ctrl-usuarios.php";
window.jsonUDN = window.jsonUDN || {};
window.jsonPerfil = window.jsonPerfil || {};
window.jsonUsers = window.jsonUsers || {};

$(function () {
    listUDN().then(() => {
        tbUser();
    });

    $("#cbUDN").on("change", function () {
        tbUser();
    });

    $("#btnUsuario").on("click", () => {
        modalNuevoUsuario();
    });
});
// FUNCIONES DE CARGA DE INICIO
function listUDN() {
    return new Promise((resolve, reject) => {
        let datos = new FormData();
        datos.append("opc", "lsUDN");
        send_ajax(datos, ctrlUser).then((data) => {
            jsonUDN = data.udn;

            $("#cbUDN").option_select({
                data: data.usr_udn,
                placeholder: "- seleccionar -",
            });
            resolve();
        });
    });
}
function tbUser() {
    let datos = new FormData();
    datos.append("opc", "tbUser");
    datos.append("udn", $("#cbUDN").val());
    send_ajax(datos, ctrlUser, "#tbDatos").then((data) => {
        jsonUsers = data.users;
        $("#tbDatos").html("").create_table(data.table);
        $("#tbUser").table_format({ priority: "1,5" });
    });
}

/*** FUNCIONES GENERALES *****/
function existUser(usser) {
    return jsonUsers.some((u) => u.toUpperCase() === usser.toUpperCase());
}
function showErrorExistUser(IPT) {
    IPT.addClass("is-invalid");
    IPT.parent()
        .next("span.text-danger")
        .removeClass("hide")
        .html("<i class='icon-warning-1'></i> Este usuario ya existe.");
}
function generarKey() {
    const caracteres =
        "0123456789abcdefghijklmnñopqrstuvwxyzABCDEFGHIJKLMNÑOPQRSTUVWXYZ(-_+)/";

    const cadenaAleatoria = Array.from({ length: 6 }, () =>
        caracteres.charAt(Math.floor(Math.random() * caracteres.length))
    ).join("");

    $("#clave").val(cadenaAleatoria);
    $("#clave").prop("type", "text");
    $("#eye").html('<i class="icon-eye-off"></i>');
}
function mostrarKey() {
    const INPUT = $("#clave");

    INPUT.prop("type", INPUT.prop("type") === "text" ? "password" : "text");

    iconEye = INPUT.prop("type") === "text" ? "icon-eye-off" : "icon-eye";
    $("#eye").html(`<i class="${iconEye}"></i>`);
}
function listPerfil() {
    return new Promise((resolve) => {
        function generate_option_select(data) {
            return {
                data,
                select2: data.length > 5,
                father: data.length > 5,
                placeholder: "- Seleccionar -",
            };
        }

        if (jsonPerfil.length > 0) {
            resolve(generate_option_select(jsonPerfil));
        } else {
            let datos = new FormData();
            datos.append("opc", "lsPerfil");
            send_ajax(datos, ctrlUser).then((data) => {
                jsonPerfil = data;
                resolve(generate_option_select(jsonPerfil));
            });
        }
    });
}

/****** FUNCIONES PARA MODAL NUEVO USUARIO *******/
function crear_formulario() {
    // CREAMOS CONTENEDOR DE FORMULARIO
    let form = $("<form>", {
        id: "formNuevoUsuario",
        novalidate: true,
    });

    //Creamos los elementos del formulario
    form.html("").create_elements2([
        {
            lbl: "UDN",
            elemento: "select",
            class: "text-uppercase",
            required: true,
            option: { data: jsonUDN, placeholder: "- Seleccionar -" },
        },
        {
            lbl: "Colaborador",
            elemento: "select",
            class: "text-uppercase",
            required: true,
            disabled: true,
            option: { data: {}, placeholder: "- Seleccionar -" },
        },
        {
            lbl: "Perfil",
            elemento: "select",
            required: true,
            class: "text-uppercase",
            option: { data: {}, placeholder: "- Seleccionar -" },
        },
        {
            lbl: "Usuario",
            elemento: "input-group",
            required: true,
            icon: "<i class='icon-user'></i>",
        },
        {
            lbl: "Contraseña",
            elemento: "input-group",
            type: "password",
            required: true,
            id: "clave",
            class: "text-start",
            placeholder: "* * * * * *",
            icon: ["<i class='icon-key'></i>", "<i class='icon-eye'></i>"],
            span: [
                {
                    class: "pointer",
                    onclick: "generarKey()",
                },
                {
                    class: "pointer",
                    id: "eye",
                    onclick: "mostrarKey()",
                },
            ],
        },
        { elemento: "modal_button" },
    ]);

    return form;
}
function lsColaboradores(udn) {
    return new Promise((resolve) => {
        let datos = new FormData();
        datos.append("opc", "lsColaboradores");
        datos.append("udn", udn);
        send_ajax(datos, ctrlUser).then((data) => {
            resolve(data);
        });
    });
}
function showErrorCreatioUser() {
    alert({
        icon: "error",
        title: "No se ha podido crear el usuario.",
        text: "Revisa tu conexión a internet y actualiza la página. Si esto no funciona comunicate con soporte.",
        btn1: true,
    });
}
function showSuccessCreationUser(data) {
    return new Promise((resolve, reject) => {
        if (data.whatsapp === true || data.correo === true) {
            text = "Las credenciales de acceso del usuario se han enviado por ";
            text +=
                (data.whatsapp ? "whatsapp" : "") +
                (data.whatsapp && data.correo ? " y " : "") +
                (data.correo ? "correo" : "");
            text += ".";

            alert({
                icon: "success",
                title: "Felicidades se ha creado un nuevo usuario",
                text,
                btn1: true,
            }).then((result) => {
                resolve(result);
            });
        } else {
            alert({
                icon: "warning",
                title: "Se ha creado un nuevo usuario.",
                text: "No se ha encontrado ningún correo ó teléfono para enviar los datos.",
                btn1: true,
            }).then((result) => {
                resolve(result);
            });
        }
    });
}
// MODAL NUEVO USUARIO
function modalNuevoUsuario() {
    form = crear_formulario();
    // Creamos el modal
    bootbox.dialog({
        title: `NUEVO USUARIO`,
        message: form,
        onShown: function () {
            // Cargamos la lista de perfiles
            listPerfil().then((data) => {
                $("#perfil").option_select(data);
            });

            $("#udn").on("change", function () {
                lsColaboradores($(this).val()).then((data) => {
                    const optionSelectConfig = {
                        data,
                        placeholder:
                            data.length > 0
                                ? "- SELECCIONAR -"
                                : "- NO HAY DATOS -",
                        select2: data.length > 5,
                        father: data.length > 5,
                    };

                    $("#colaborador")
                        .prop("disabled", data.length === 0)
                        .option_select(optionSelectConfig);
                });
            });

            form.validation_form({ opc: "newUser" }, (datos) =>
                crear_usuario(datos)
            );
        },
    });
}
function crear_usuario(datos) {
    const iptUser = $("#usuario");
    if (existUser(iptUser.val())) {
        showErrorExistUser(iptUser);
    } else {
        $('button [type="submit"]').attr("disabled", "disabled");
        send_ajax(
            datos,
            ctrlUser,
            "Espera un momento, estamos procesando los datos."
        ).then((data) => {
            
            console.log(data);
            if (data === false) {
                showErrorCreatioUser();
            } else {
                showSuccessCreationUser(data).then((result) => {
                    if (result.isConfirmed) {
                        tbUser();
                        bootbox.hideAll();
                    }
                });
            }
        });
    }
}

/********** FUNCIONES PARA EDITAR EL USUARIO ******************/
// MODAL EDITAR USUARIOS
function crear_formulario_editar() {
    // CREAMOS CONTENEDOR DE FORMULARIO
    let form = $("<form>", {
        id: "formNuevoUsuario",
        novalidate: true,
    });

    //Creamos los elementos del formulario
    form.html("").create_elements2([
        {
            lbl: "Perfil",
            elemento: "select",
            required: true,
            class: "text-uppercase",
            option: { data: {}, placeholder: "- Seleccionar -" },
        },
        {
            lbl: "Usuario",
            elemento: "input-group",
            required: true,
            icon: "<i class='icon-user'></i>",
        },
        {
            lbl: "Contraseña",
            elemento: "input-group",
            type: "password",
            id: "clave",
            class: "text-start",
            placeholder: "* * * * * *",
            icon: ["<i class='icon-key'></i>", "<i class='icon-eye'></i>"],
            span: [
                {
                    class: "pointer",
                    onclick: "generarKey()",
                },
                {
                    class: "pointer",
                    id: "eye",
                    onclick: "mostrarKey()",
                },
            ],
        },
        { elemento: "modal_button" },
    ]);

    return form;
}
function showAlertUpdateUser() {
    alert({
        icon: "warning",
        title: "No se ha detectado ningún cambio.",
        text: "¿Esta seguro que desea actualizar este usuario?",
        btn1: true,
    });
}
function showErrorUpdateUser() {
    alert({
        icon: "error",
        title: "No se ha podido crear el usuario.",
        text: "Revisa tu conexión a internet y actualiza la página. Si esto no funciona comunicate con soporte.",
        btn1: true,
    });
}
function showSuccessUpdateUser(data) {
    return new Promise((resolve, reject) => {
        if (data.whatsapp === true || data.correo === true) {
            text = "";
            if (data.whatsapp === true)
                text = "Se ha enviado por whatsapp los datos del usuario";
            if (data.correo === true)
                text += ", también se han envidado por correo.";

            alert({
                icon: "success",
                title: "Felicidades los datos se modificadaron correctamente.",
                text,
                btn1: true,
            }).then((result) => {
                resolve(result);
            });
        } else {
            alert({
                icon: "warning",
                title: "Se ha modificado las credenciales del usuario.",
                text: "No se ha encontrado ningún correo ó teléfono para enviar los datos.",
                btn1: true,
            }).then((result) => {
                resolve(result);
            });
        }
    });
}
function modalEditarUsuario(idU, usuario, idP) {
    form = crear_formulario_editar();

    swal_question(
        `¿DESEA EDITAR LOS DATOS DEL USUARIO "${usuario.toUpperCase()}"?`
    ).then((result) => {
        if (result.isConfirmed) {
            bootbox.dialog({
                title: `EDITAR USUARIO "${usuario.toUpperCase()}"`,
                message: form,
                onShown: function () {
                    $("#usuario").val(usuario);

                    // Cargamos la lista de perfiles
                    listPerfil().then((data) => {
                        $("#perfil").option_select(data);

                        $('#perfil option[value="' + idP + '"]').prop(
                            "selected",
                            true
                        );
                    });

                    form.validation_form(
                        { opc: "updateUser", id: idU, user: usuario, idP: idP },
                        (datos) => {
                            const iptUser = $("#usuario");
                            if (
                                existUser(iptUser.val()) &&
                                iptUser.val() != usuario
                            ) {
                                showErrorExistUser(iptUser);
                            } else if (
                                iptUser.val().toUpperCase() ==
                                    usuario.toUpperCase() &&
                                idP == $("#perfil").val() &&
                                !$("#clave").val()
                            ) {
                                showAlertUpdateUser();
                            } else {
                                $('button [type="submit"]').attr(
                                    "disabled",
                                    "disabled"
                                );
                                send_ajax(datos, ctrlUser,'Espera estamos realizando los cambios.').then((data) => {
                                    showSuccessUpdateUser(data).then(
                                        (result) => {
                                            if (result.isConfirmed) {
                                                tbUser();
                                                bootbox.hideAll();
                                            }
                                        }
                                    );
                                });
                            }
                        }
                    );
                },
            });
        }
    });
}

// ESTADO DE USUARIOS
function userToggle(id) {
    const BTN = $("#btnToggle" + id);
    const ESTADO = BTN.attr("estado");
    let estado = 1;
    let question = "¿DESEA ACTIVAR ESTE USUARIO?";
    let iconToggle = '<i class="icon-toggle-on"></i>';

    if (ESTADO == 1) {
        estado = 0;
        question = "¿DESEA DESACTIVAR ESTE USUARIO?";
        iconToggle = '<i class="icon-toggle-off"></i>';
    }

    swal_question(question).then((result) => {
        if (result.isConfirmed) {
            let datos = new FormData();
            datos.append("opc", "statusUser");
            datos.append("id", id);
            datos.append("estado", estado);
            send_ajax(datos, ctrlUser).then((data) => {
                if (data === true) {
                    BTN.html(iconToggle);
                    BTN.attr("estado", estado);
                }
            });
        }
    });
}

$.fn.create_elements2 = function (options) {
    return new Promise((resolve, reject) => {
        let contenedor = $(this);

        let defaults = [{ lbl: "Input", type: "text" }];

        let opts = options === undefined ? defaults : options;

        // Hacemos un recorrido por todos los elementos que se van a crear
        opts.forEach((dom) => {
            let vacio = "&nbsp;"; //Creamos un elemento que asigna espacios en blanco

            // Creamos el div contenedor
            let div = $("<div>", { class: "col mb-3" });

            // Obtenemos el texto del label "&nbsp;"
            let value = dom.lbl !== undefined ? dom.lbl : "#";
            delete dom.lbl;
            let texto = value
                .toLowerCase()
                .replace(/[^a-zA-ZÀ-ÖØ-öø-ÿ\s]+/g, "")
                .replace(/ /g, "-");
            let id = dom.id === undefined ? texto : dom.id;
            delete dom.id;
            let forLbl = id;

            // Creamos una etiqueta label para casi todos los elementos del formulario
            let label = $("<label>", {
                for: forLbl,
                class: "fw-bold mb-1",
                html: value,
            });

            // Span de error
            let spanError = $("<span>", {
                class: "text-danger form-text hide",
                html: '<i class=""></i> Este campo es requerido *',
            });

            //Crearemos input por defecto
            if (dom.elemento === undefined || dom.elemento === "input") {
                let text_align = "";
                let placeholder = value;

                if (
                    dom.type === "number" ||
                    dom.tipo === "numero" ||
                    dom.tipo === "cifra"
                ) {
                    text_align = "text-end";
                    placeholder = "0.00";
                }

                let control = "form-control";
                if (
                    dom.type != undefined &&
                    dom.type.toLowerCase() == "submit"
                ) {
                    control = "btn btn-primary col-12";
                    label.html(vacio);
                }

                let attr = $.extend(
                    {
                        class: control + text_align,
                        id: id,
                        name: id,
                        placeholder: placeholder,
                        type: "text",
                    },
                    dom
                );

                let input = $("<input>", attr);
                if (dom.type !== "checkbox") {
                    div.append(label, input, spanError);
                } else {
                    div.append(input, label, spanError);
                }

                if (
                    dom.type !== "submit" &&
                    dom.type !== "radio" &&
                    dom.type !== "checkbox"
                ) {
                    if (!input.hasClass("form-control"))
                        input.addClass("form-control");
                } else if (dom.type === "submit") {
                    if (!input.hasClass("btn")) input.addClass("btn");
                    if (!input.hasClass("col-12")) input.addClass("col-12");
                }

                if (!input.hasClass(text_align)) input.addClass(text_align);
            } else {
                if (dom.elemento == "textarea") {
                    delete dom.elemento; //Eliminamos el elemento del json
                    attr = $.extend(
                        {
                            name: id,
                            id: id,
                            row: "5",
                            class: "form-control resize",
                            placeholder: value,
                        },
                        dom
                    ); // Se convinan atributos con los que aun existen dentro del json

                    let textarea = $("<textarea>", attr); // Se crea el input
                    if (!textarea.hasClass("form-control"))
                        textarea.addClass("form-control");
                    if (!textarea.hasClass("resize"))
                        textarea.addClass("resize");
                    div.append(label, textarea, spanError); // Se agrega el label y el input al [div] padre
                }
                if (dom.elemento == "select") {
                    delete dom.elemento; //Eliminamos el elemento del json

                    // Atributos para el plugin option_select
                    let option_select = {
                        data: {},
                        placeholder: "- Seleccionar -",
                    };

                    if (dom.option !== undefined) {
                        option_select = $.extend({}, option_select, dom.option);
                        delete dom.option;
                    } else if (dom.options !== undefined) {
                        option_select = $.extend(
                            {},
                            option_select,
                            dom.options
                        );
                        delete dom.options;
                    } else if (dom.option_select !== undefined) {
                        option_select = $.extend(
                            {},
                            option_select,
                            dom.option_select
                        );
                        delete dom.option_select;
                    }

                    // Atributos para el select
                    let attr = $.extend(
                        {
                            name: id,
                            id: id,
                            class: "form-select",
                        },
                        dom
                    ); // Se convinan los atributos restantes que no fueron eliminados

                    let select = $("<select>", attr); // Se crea el boton
                    if (!select.hasClass("form-select"))
                        select.addClass("form-select");
                    div.append(label, select, spanError); // Se agrega el label y el boton al (div) padre
                    select.option_select(option_select); // Se hace uso del plugin "option_select" despues de agregarlo al DOM
                }
                if (dom.elemento == "input-group") {
                    delete dom.elemento; //Eliminamos el elemento del json
                    // Creamos el contenedor del input-group
                    let divgb = $("<div>", { class: "input-group" });

                    // Trabajamos con arreglos debido a que pueden existir 2 al mismo tiempo
                    // Asignamos el icono del span
                    let icon = [];
                    if (dom.icon !== undefined) {
                        if (Array.isArray(dom.icon)) icon = dom.icon;
                        else icon = dom.icon.split(",");
                        delete dom.icon;
                    } else icon = ['<i class="icon-dollar"></i>'];

                    // Se hace un recorrido en caso que sean mas de 1 icono
                    let span = [];
                    icon.forEach((icon, index) => {
                        if (index < 2) {
                            dom.span = Array.isArray(dom.span)
                                ? dom.span
                                : [dom.span];

                            // Asignamos clases adicionales a los atributos del span
                            let clase_extra = "";
                            if (dom.span[index] && dom.span[index].class) {
                                //comprobamos la existencia de clases adicionales
                                clase_extra = dom.span[index].class;
                                delete dom.span[index].class; //Eliminamos su existencia para evitar duplicidad
                            }

                            // Se creando atributos por defecto del span
                            let spanAttr = $.extend(
                                {
                                    class: `input-group-text ${clase_extra}`,
                                    html: icon,
                                    id: `sgp_${id}${index}`,
                                },
                                dom.span[index]
                            );

                            delete dom.span[index]; //Se eliminan los atributos del span para no afectar el input

                            //Se crea el span
                            span.push($("<span>", spanAttr));
                        }
                    });
                    delete dom.span;

                    let text_align = "";
                    let tipo = "";
                    let type = "text";
                    let clase = "form-control";
                    if (dom.tipo === "cifra" || dom.tipo === "numero") {
                        tipo = dom.tipo === undefined ? "cifra" : dom.tipo;
                        type = "number";
                        text_align = "text-end";
                        value = "0.00";
                        clase += ` ${text_align} ${
                            dom.class === undefined ? "" : dom.class
                        }`;
                    }

                    delete dom.tipo;
                    delete dom.class;

                    let attr = $.extend(
                        {
                            type: type,
                            name: id,
                            id: id,
                            class: clase,
                            placeholder: value,
                        },
                        dom
                    );

                    let input = $("<input>", attr).removeAttr("pos");
                    if (span.length == 2) divgb.append(span[0], input, span[1]);
                    else {
                        if (dom.pos !== undefined) {
                            if (["r", "right", "d", "der"].includes(dom.pos))
                                divgb.append(input, span);
                            else if (
                                ["l", "left", "i", "izq"].includes(dom.pos)
                            )
                                divgb.append(span, input);
                            else divgb.append(span, input);
                        } else {
                            divgb.append(span, input);
                        }
                    }
                    delete dom.pos;

                    div.append(label, divgb, spanError);
                }
                if (dom.elemento == "select-group") {
                    delete dom.elemento; //Eliminamos el elemento del json
                    // Creamos el contenedor del input-group
                    let divcb = $("<div>", { class: "input-group" });

                    // trabajamos con arreglos debido a que pueden existir 2 al mismo tiempo
                    //Asignamos el icono del span
                    let iconcb = [];
                    if (dom.icon !== undefined) {
                        if (Array.isArray(dom.icon)) iconcb = dom.icon;
                        else iconcb = dom.icon.split(",");
                        delete dom.icon;
                    } else iconcb = ['<i class="icon-plus"></i>'];

                    // Se hace un recorrido en caso que sean mas de 1 icono
                    let spancb = [];
                    iconcb.forEach((icon, index) => {
                        if (index < 2) {
                            dom.span = Array.isArray(dom.span)
                                ? dom.span
                                : [dom.span];

                            // Asignamos clases adicionales a los atributos del span
                            let clase_extra = "btn-success";
                            if (dom.span[index] && dom.span[index].class) {
                                //comprobamos la existencia de clases adicionales
                                clase_extra = dom.span[index].class;
                                delete dom.span[index].class; //Eliminamos su existencia para evitar duplicidad
                            }

                            // Se creando atributos por defecto del span
                            let spanAttr = $.extend(
                                {
                                    class: `input-group-text btn ${clase_extra}`,
                                    html: icon,
                                    id: `sgp_${id}${index}`,
                                },
                                dom.span[index]
                            );

                            delete dom.span[index]; //Se eliminan los atributos del span para no afectar el input

                            //Se crea el span
                            spancb.push($("<span>", spanAttr));
                        }
                    });
                    delete dom.span;

                    let cbclase = "form-select ";
                    cbclase += dom.class === undefined ? "" : dom.class;

                    delete dom.tipo;
                    delete dom.class;

                    let attr = $.extend(
                        {
                            name: id,
                            id: id,
                            class: cbclase,
                        },
                        dom
                    );

                    // Atributos para el plugin option_select
                    let option_selectgb = {
                        data: {},
                        placeholder: "- Seleccionar -",
                    };

                    if (dom.option !== undefined) {
                        option_selectgb = $.extend(
                            {},
                            option_selectgb,
                            dom.option
                        );
                    } else if (dom.options !== undefined) {
                        option_selectgb = $.extend(
                            {},
                            option_selectgb,
                            dom.options
                        );
                    } else if (dom.option_select !== undefined) {
                        option_selectgb = $.extend(
                            {},
                            option_selectgb,
                            dom.option_select
                        );
                    }
                    delete dom.option_select;

                    let selectgb = $("<select>", attr).removeAttr("pos");
                    if (!selectgb.hasClass("form-select"))
                        selectgb.addClass("form-select");

                    if (spancb.length == 2)
                        divcb.append(spancb[0], selectgb, spancb[1]);
                    else {
                        if (dom.pos !== undefined) {
                            if (["r", "right", "d", "der"].includes(dom.pos))
                                divcb.append(selectgb, spancb[0]);
                            else if (
                                ["l", "left", "i", "izq"].includes(dom.pos)
                            )
                                divcb.append(spancb[0], selectgb);
                            else divcb.append(spancb[0], selectgb);
                        } else {
                            divcb.append(spancb[0], selectgb);
                        }
                    }
                    delete dom.pos;
                    div.append(label, divcb);
                    selectgb.option_select(option_selectgb);
                }
                if (dom.elemento == "modal_button") {
                    let labeles = ["Guardar", "Cancelar"];
                    if (value !== "#")
                        labeles = Array.isArray(value)
                            ? value
                            : value.split(","); //Creamos un array con el texto que contiene el boton

                    div.attr("class", ""); //Limpiamos las clases del div
                    div.addClass(
                        "col-12  d-flex flex-wrap justify-content-around"
                    );

                    let attrBtn1 = {
                        class: "btn btn-primary col-5",
                        text: labeles[0].trim(),
                        type: "submit",
                    };

                    let attrBtn2 = {
                        class: "btn btn-outline-danger bootbox-close-button col-5",
                        text: labeles[1].trim(),
                        type: "submit",
                    };

                    attrBtn1 = $.extend(attrBtn1, dom.btn1);
                    attrBtn2 = $.extend(attrBtn2, dom.btn2);

                    let btnConfirm = $("<button>", attrBtn1);
                    let btnCancel = $("<button>", attrBtn2);

                    div.append(btnConfirm, btnCancel);
                }
            }

            contenedor.append(div);
        });
    });
};
