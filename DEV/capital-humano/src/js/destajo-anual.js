window.ctrlCH = "ctrl/ctrl-ch.php";
$(() => {
    $(".datepicker").daterangepicker(
        {
            startDate: moment().startOf("year"),
            endDate: moment(),
            showDropdowns: true,
            ranges: {
                "Año actual": [moment().startOf("year"), moment()],
                "Año anterior": [moment().startOf("year").subtract(1, "year"), moment().endOf("year").subtract(1, "year")],
            },
        },
        () => tbDestajoAnual()
    );

    $(".datepicker")
        .next("span")
        .on("click", function () {
            $(".datepicker").click();
        });

    tbDestajoAnual();
});

async function tbDestajoAnual() {
    $("#tbDatos")
        .html("")
        .create_table(await structureTbDetajoAnual());

    $("#tbDestajo").table_format({ ordering: false, paging: false,priority:[8,1,0] });
}

async function structureTbDetajoAnual() {
    let tbody = [];
    const icon = '<i class="icon-right-dir"></i>';
    const dates = $("#iptDate").valueDates();
    const data = await fn_ajax({ opc: "destajoAnual", date1: dates[0], date2: dates[1] }, ctrlCH);

    data.forEach((tr) => {
        tbody.push([
            { tr: { class: "bg-warning" } },
            { html: tr.area, class: "fw-bold" },
            { html: format_number(tr.promedio), class: "text-end fw-bold" },
            { html: format_number(tr.destajo), class: "text-end fw-bold" },
            { html: format_number(tr.bono), class: "text-end fw-bold" },
            { html: format_number(tr.fonacot), class: "text-end fw-bold" },
            { html: format_number(tr.infonavit), class: "text-end fw-bold" },
            { html: format_number(tr.perdida), class: "text-end fw-bold" },
            { html: format_number(tr.prestamo), class: "text-end fw-bold" },
            { html: format_number(tr.total), class: "text-end fw-bold" },
        ]);

        tr.colaboradores.forEach((tr2) => {
            tbody.push([
                { html: icon + tr2.colaborador, class: "pointer", onclick: "showData('c" + tr2.id + "')" },
                { html: format_number(tr2.promedio), class: "text-end" },
                { html: format_number(tr2.destajo), class: "text-end" },
                { html: format_number(tr2.bono), class: "text-end" },
                { html: format_number(tr2.fonacot), class: "text-end" },
                { html: format_number(tr2.infonavit), class: "text-end" },
                { html: format_number(tr2.perdida), class: "text-end" },
                { html: format_number(tr2.prestamo), class: "text-end" },
                { html: format_number(tr2.total), class: "text-end" },
            ]);

            tr2.meses.forEach((tr3) => {
                tbody.push([
                    { tr: { class: "hide c" + tr2.id } },
                    { html: tr3.mes, class: "" },
                    { html: format_number(tr3.promedio), class: "text-end" },
                    { html: format_number(tr3.destajo), class: "text-end" },
                    { html: format_number(tr3.bono), class: "text-end" },
                    { html: format_number(tr3.fonacot), class: "text-end" },
                    { html: format_number(tr3.infonavit), class: "text-end" },
                    { html: format_number(tr3.perdida), class: "text-end" },
                    { html: format_number(tr3.prestamo), class: "text-end" },
                    { html: format_number(0), class: "text-end" },
                ]);
            });
        });
    });

    return {
        table: { id: "tbDestajo" },
        thead: "Colaboradores,Promedio,Destajo,Dias extras,Fonacot,Infonavit,Perdida material,Préstamos,Total",
        tbody,
    };
}
