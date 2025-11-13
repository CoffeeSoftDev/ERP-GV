const nuevoUsuario = [
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
        option: { data: {}, placeholder: "-seleccionar-" },
    },
    {
        lbl: "Perfil",
        elemento: "select",
        required: true,
        class: "text-uppercase",
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
        id: "iptClave",
        class: "text-start",
        placeholder: "* * * * * *",
        icon: ["<i class='icon-key'></i>", "<i class='icon-eye'></i>"],
        span: [
            {
                class: "pointer",
                onclick: "generarKey('#iptClave', '#eye')",
            },
            {
                class: "pointer",
                id: "eye",
                onclick: "mostrarKey('#iptClave','#eye')",
            },
        ],
    },
    { elemento: "modal_button" },
];
