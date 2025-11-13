class CampaignSummary extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "Summary";
        this.apiSummary = 'ctrl/ctrl-summary.php';
    }

    render() {
        this.layout();
        this.filterBar();
        this.lsSummary();
    }

    layout() {
        this.primaryLayout({
            parent: `container-summary`,
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
                    onchange: `summary.lsSummary()`,
                },
                {
                    opc: "select",
                    id: "red_social_id",
                    lbl: "Red Social",
                    class: "col-sm-3",
                    data: red_social,
                    onchange: `summary.lsSummary()`,
                },
                {
                    opc: "select",
                    id: "mes",
                    lbl: "Mes",
                    class: "col-sm-3",
                    data: moment.months().map((m, i) => ({ id: i + 1, valor: m })),
                    onchange: `summary.lsSummary()`,
                },
                {
                    opc: "select",
                    id: "año",
                    lbl: "Año",
                    class: "col-sm-3",
                    data: Array.from({ length: 5 }, (_, i) => {
                        const year = moment().year() - i;
                        return { id: year, valor: year.toString() };
                    }),
                    onchange: `summary.lsSummary()`,
                },
            ],
        });

        const currentMonth = moment().month() + 1;
        setTimeout(() => {
            $(`#filterBar${this.PROJECT_NAME} #mes`).val(currentMonth).trigger("change");
        }, 100);
    }

    async lsSummary() {
        const nombreMes = $(`#filterBar${this.PROJECT_NAME} #mes option:selected`).text();
        const año = $(`#filterBar${this.PROJECT_NAME} #año`).val();

        $(`#container${this.PROJECT_NAME}`).html(`
            <div class="px-2 pt-2 pb-2">
                <h2 class="text-2xl font-semibold">📋 Resumen de Campaña - ${nombreMes} ${año}</h2>
                <p class="text-gray-400">Desglose detallado de campañas y anuncios del mes</p>
            </div>
            <div id="container-table-summary"></div>
        `);

        const tempLink = this._link;
        this._link = this.apiSummary;


        const response = await useFetch({
            url: this._link,
            data: {
                opc: "lsSummary",
                udn_id: $(`#filterBar${this.PROJECT_NAME} #udn_id`).val(),
                red_social_id: $(`#filterBar${this.PROJECT_NAME} #red_social_id`).val(),
                mes: $(`#filterBar${this.PROJECT_NAME} #mes`).val(),
                año: $(`#filterBar${this.PROJECT_NAME} #año`).val(),
            }
        });


        const grouped = response.grouped;
        const totals = response.totals[0];

        let rowCampañas = '<tr><th class="bg-blue-100 border border-gray-300 rounded-tl-xl"></th>';
        let rowAnuncios = '<tr><th class="text-left px-3 py-2 bg-blue-100 border border-gray-300 font-medium">Anuncios</th>';
        let rowClasif = '<tr><th class="text-left px-3 py-2 bg-blue-50 border border-gray-300 font-medium">Clasificación</th>';
        let rowClics = '<tr><th class="text-left px-3 py-2 bg-white border border-gray-300 font-medium">Resultados (clics)</th>';
        let rowInversion = '<tr><th class="text-left px-3 py-2 bg-white border border-gray-300 font-medium">Inversión</th>';
        let rowCPC = '<tr><th class="text-left px-3 py-2 bg-white border border-gray-300 font-medium">CPC</th>';
        let rowTotalCampaña = '<tr><th class="font-bold px-3 py-2 bg-blue-50 border border-gray-300">Total de resultados x campaña</th>';

        grouped.forEach(c => {
            rowCampañas += `<th class="text-center bg-blue-100 font-semibold border border-gray-300" colspan="${c.rows.length}">${c.group}</th>`;

            c.rows.forEach(r => {
                rowAnuncios += `<th class="text-center bg-blue-100 border border-gray-300 font-semibold">${r.Anuncio}</th>`;
                rowClasif += `<td class="text-center bg-blue-50 border border-gray-300 italic">${r.Clasificación}</td>`;
                rowClics += `<td class="text-right bg-white border border-gray-300 px-1">${r["Resultados (clics)"]}</td>`;
                rowInversion += `<td class="text-right bg-white border border-gray-300  px-1">${r["Inversión"]}</td>`;
                rowCPC += `<td class="text-right bg-white border border-gray-300  px-1">${r["CPC"]}</td>`;
            });

            rowTotalCampaña += `<td class="text-center font-semibold bg-blue-50 border border-gray-300" colspan="${c.rows.length}">Total ${c.group}: <span class='font-bold'>${c.footer["Resultados (clics)"]}</span></td>`;
        });

        rowCampañas += `
                <th class="text-center bg-blue-100 font-semibold border border-gray-300" rowspan="2">Tipo de resultados</th>
                <th class="text-center bg-blue-100 font-semibold border border-gray-300 rounded-tr-xl" rowspan="2">Resultados</th>
                </tr>`;

        rowClasif += `<td class="text-center bg-blue-50 border border-gray-300"></td>
              <td class="text-center bg-blue-50 border border-gray-300"></td></tr>`;

        rowClics += `
             <td class="text-center bg-gray-50 border border-gray-300 font-semibold">Total de resultados (clics)</td>
             <td class="text-right bg-gray-50 font-bold border border-gray-300 px-1">${totals.total_clics}</td>
             </tr>`;

        rowInversion += `
                 <td class="text-center bg-gray-50 border border-gray-300 font-semibold">Costo total</td>
                 <td class="text-right bg-gray-50 font-bold border border-gray-300 px-1">${evaluar(totals.total_monto)}</td></tr>`;

        rowCPC += `
           <td class="text-center bg-gray-50 border border-gray-300 font-semibold">CPC promedio</td>
           <td class="text-right bg-gray-50 font-bold border border-gray-300 px-1">${evaluar(totals.promedio_cpc)}</td>
           </tr>`;

        rowTotalCampaña += `<td class="bg-blue-50 border border-gray-300"></td>
                    <td class="bg-blue-50 border border-gray-300"></td></tr>`;


        $('#container-table-summary').html(`
            <div class="overflow-auto rounded-xl border border-gray-300 shadow-md mt-6">
                <table class="min-w-full text-sm text-gray-800 border-collapse rounded-xl overflow-hidden">
                    <thead class="rounded-t-xl">
                        ${rowCampañas}
                        ${rowAnuncios}
                    </thead>
                    <tbody>
                        ${rowClasif}
                        ${rowClics}
                        ${rowInversion}
                        ${rowCPC}
                        ${rowTotalCampaña}
                    </tbody>
                </table>
            </div>
        `);
    }
}

function evaluar(valor) {
    const number = parseFloat(valor) || 0;
    return number.toLocaleString('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 2
    });
}
