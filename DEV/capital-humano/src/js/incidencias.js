window.ctrlCH = "ctrl/ctrl-ch.php";
const incidencias = new Incidencias(ctrlCH);
incidencias.container = "#filterBar";
incidencias.tbContainer = "#tbDatos";

$(async () => {
    incidencias.term = "#nomenglatura";
    $("#btnNomenglatura").on("click", () => $("#nomenglatura").toggleClass("hide"));
    $("#btnLock").on("click", () => incidencias.lockOpen());
    $("#btnLockOff").on("click", () => incidencias.lockClosed());

    await incidencias.filterIncidencias();
    await incidencias.terminologia();
    await incidencias.tbIncidencias();
});

const tbIncidencias = () => incidencias.tbIncidencias();
const closedInc = (opc) => incidencias.closedInc(opc);
const updateInc = (id, date) => incidencias.updateInc(id, date);
const adicionalInc = (id) => incidencias.adicionalInc(id);
const deleteExtraInc = (id1,id2) => incidencias.deleteExtraInc(id1,id2);
$.fn.fixed_inc = function (fixed) {
    let col = [];
    for (let i = 0; i < fixed; i++) col.push(i);

    $(this).table_format({
        ordering: true,
        searching: true,
        responsive: false,
        paging: false,
        info: false,
        keys: true,
        autoWidth: false,
        collapse:true,
        columnDefs: [
            { targets: "_all", width: "80px" }, // Ancho mínimo para todas las columnas
            { targets: col, width: "150px", className: "bg-aliceblue" }, // Ancho fijo para las columnas fijas
        ],
        fixedColumns: {
            left: fixed,
        },
        scrollY: "500px",
        scrollX: true,
        scrollCollapse: true,
    });

    // Ahora obtener la instancia de DataTable directamente del elemento
    const table = $(this).DataTable();

    table.on("key-focus", function (e, datatable, cell) {
        var input = $(cell.node()).find(":input.cell-inc");
        if (input.length > 0) input.focus();
    });
};
