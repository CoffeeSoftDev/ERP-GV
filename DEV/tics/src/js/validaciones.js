// Llamada al plugin con una función de devolución de llamada
window.ctrlValidaciones = window.ctrlValidaciones || "ctrl/ctrl-validaciones.php";

$(function () {
    const datos_array = [
        { id: 1, valor: "one" },
        { id: 2, valor: "two" },
        { id: 3, valor: "three" },
    ];
    const FORM = $("#formDatos");
    FORM.create_elements([
        { lbl: "Texto *", type: "text", tipo: "letra", required: true },
        { lbl: "Hora ", type: "time" },
        { lbl: "Fecha *", type: "date", required: true },
        {
            lbl: "Texto y número *",
            type: "text",
            tipo: "alfanumerico",
            required: true,
        },
        { lbl: "Número *", type: "number", tipo: "cifra", required: true },
        {
            lbl: "Porcentaje",
            elemento: "input-group",
            tipo: "cifra",
            icon: "%",
            pos: "right",
        },
        {
            lbl: "Correo electrónico",
            elemento: "input-group",
            icon: "@,<i class='icon-ok'></i>",
            tipo: "correo",
            span: [{}, { class: "btn btn-danger", onclick: "alert('Soy un botón')" }],
        },
        { lbl: "Select", elemento: "select", option: { data: datos_array } },
        {
            lbl: "Select2",
            elemento: "select",
            option: { data: datos_array, select2: true },
        },
        {
            lbl: "Select Group",
            elemento: "select-group",
            option: { data: datos_array, select2: true, group: true },
            span: {
                class: "btn btn-success",
                onClick: "alert({title:'Soy un select-button'});",
            },
        },
        {
            lbl: "Select2 multiple",
            elemento: "select",
            multiple: "multiple",
            option: { data: datos_array, select2: true },
        },
        {
            lbl: "Select2 multiple group",
            elemento: "select-group",
            multiple: "multiple",
            option: { data: datos_array, select2: true, group: true },
            span: {
                class: "btn btn-info",
                onClick: "alert({title:'Soy un select-button'});",
            },
        },
        { elemento: "submit", lbl: "Validar" },
    ]).then(function () {
        FORM.validation_form({ opc: "prueba" }, function (datos) {
            send_ajax(datos, ctrlValidaciones).then((data) => {
                if (data === true) alert();
                else console.log(data);
            });
        });
    });

    $("#contenedor").create_elements([
        {
            lbl: "Texto *",
            id: "iptTexto",
            maxlength: "3",
            type: "text",
            tipo: "cifra",
            required: true,
        },
        { lbl: "Fecha", type: "date", required: true },
        { lbl: "Hora", type: "time", required: true },
        {
            lbl: "cbSelect",
            elemento: "select",
            option: { data: datos_array },
            required: true,
        },
        {
            elemento: "textarea",
            lbl: "Textarea",
            div: { class: "col-12 col-sm-12 col-md-12 col-lg-12" },
            required: true,
            tipo: "",
        },
        {
            elemento: "button",
            lbl: "Enviar Datos",
            option: { data: { id: 1, valor: "Uno" } },
            onClick: "enviar_datos();",
            div: { class: "col-12 col-sm-12 col-md-12 col-lg-12" },
        },
    ]);

    $("#btnModal").on("click", () => {
        alert();
    });
});

function enviar_datos() {
    $("#contenedor").validar_contenedor({ tipo: "text", var1: "var1", var2: "var2" }, (datos) => {
        // for (const x of datos) console.log(x);
        console.log(datos);
    });
}
