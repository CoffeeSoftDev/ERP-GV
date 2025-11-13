class SalesCalendar extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "SalesCalendar";
        this._ventasData = [];
        this._extremes = {
            ventas: {},
            cheque: {},
            clientes: {}
        };
        this._maxVentaGlobal = 0;
    }

    render() {
        this.layout();
    }

    layout() {
        this.primaryLayout({
            parent: `container-calendar`,
            id: this.PROJECT_NAME,
            card: {
                filterBar: { class: 'w-full border-b pb-2', id: `filterBar` },
                container: { class: 'w-full my-2 h-full', id: `container${this.PROJECT_NAME}` }
            }
        });

        let cookies = getCookies();

        let buttonIA = ``;
        if (cookies.IDP == 2) {
            buttonIA = ` 
            <button id="askAIButton" onclick="calendar.openAIModal()"
                class="ask-ai-btn mt-4 flex items-center gap-2 px-5 py-2.5 rounded-lg 
                    text-white font-medium text-sm shadow-md hover:shadow-lg">
                🤖
                <span>Consultar con IA</span>
            </button>
        `;
        } 

     


        $(`#container${this.PROJECT_NAME}`).html(`
        <!-- 🧭 Encabezado flexible -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 px-3 pt-3 pb-2 flex-wrap">
            <!-- 📅 Título -->
            <div class="flex flex-col">
                <h2 class="text-2xl font-semibold text-[#103B60] leading-tight">
                    📅 Calendario de Ventas
                </h2>
                <p class="text-gray-500 text-sm">
                    Visualiza las ventas de las últimas 5 semanas
                </p>
            </div>

            <!-- 🧭 Barra de filtros y botón IA -->
            <div id="filterBar${this.PROJECT_NAME}" class="w-full sm:w-auto flex flex-col sm:flex-row items-center justify-end gap-3 mt-3 sm:mt-0">
                <!-- 🤖 Botón Consultar con IA -->
               ${buttonIA}
            </div>
        </div>
        <!-- 📆 Contenedor del calendario -->
        <div id="calendario-container" class="px-2">
        </div>
    `);

        this.renderCalendar();
        this.filterBar();
    }

    parseMarkdown(text) {
        let html = text;

        html = html.replace(/```(\w+)?\n([\s\S]*?)```/g, '<pre class="bg-gray-900 text-white p-3 rounded-lg overflow-x-auto my-2"><code>$2</code></pre>');
        html = html.replace(/`([^`]+)`/g, '<code class="bg-gray-100 px-1 py-0.5 rounded text-xs font-mono text-gray-800">$1</code>');
        html = html.replace(/\*\*\*(.*?)\*\*\*/g, '<strong><em>$1</em></strong>');
        html = html.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        html = html.replace(/\*(.*?)\*/g, '<em>$1</em>');
        html = html.replace(/~~(.*?)~~/g, '<del>$1</del>');
        html = html.replace(/^#### (.*$)/gim, '<h4 class="text-sm font-bold mt-2 mb-1 text-gray-800">$1</h4>');
        html = html.replace(/^### (.*$)/gim, '<h3 class="text-base font-bold mt-3 mb-2 text-gray-800">$1</h3>');
        html = html.replace(/^## (.*$)/gim, '<h2 class="text-lg font-bold mt-3 mb-2 text-gray-800">$1</h2>');
        html = html.replace(/^# (.*$)/gim, '<h1 class="text-xl font-bold mt-4 mb-2 text-gray-800">$1</h1>');

        const lines = html.split('\n');
        let inList = false;
        let listType = '';
        let result = [];

        for (let i = 0; i < lines.length; i++) {
            const line = lines[i];

            if (/^[\-\*\+] (.*)/.test(line)) {
                if (!inList) {
                    result.push('<ul class="my-2 space-y-1">');
                    inList = true;
                    listType = 'ul';
                }
                result.push(line.replace(/^[\-\*\+] (.*)/, '<li class="ml-4">$1</li>'));
            } else if (/^\d+\. (.*)/.test(line)) {
                if (!inList) {
                    result.push('<ol class="my-2 space-y-1 list-decimal">');
                    inList = true;
                    listType = 'ol';
                }
                result.push(line.replace(/^\d+\. (.*)/, '<li class="ml-4">$1</li>'));
            } else {
                if (inList) {
                    result.push(`</${listType}>`);
                    inList = false;
                }
                result.push(line);
            }
        }

        if (inList) {
            result.push(`</${listType}>`);
        }

        html = result.join('\n');
        html = html.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" class="text-blue-600 hover:underline" target="_blank">$1</a>');
        html = html.replace(/\n\n+/g, '</p><p class="mb-2">');
        html = html.replace(/\n/g, '<br>');

        if (!html.startsWith('<')) {
            html = '<p class="mb-2">' + html + '</p>';
        }

        return `<div class="markdown-content">${html}</div>`;
    }

    async openAIModal() {
        const chatMessages = [];

        bootbox.dialog({
            title: `
                <div class="flex items-center gap-2">
                    <span class="text-2xl">🤖</span>
                    <span class="font-bold text-[#103B60]">CoffeeIA - Asistente de Ventas</span>
                </div>
            `,
            size: 'extra-large',
            closeButton: true,
            message: `
                <style>
                    .markdown-content h1, .markdown-content h2, .markdown-content h3 {
                        margin-top: 0.75rem;
                        margin-bottom: 0.5rem;
                    }
                    .markdown-content ul, .markdown-content ol {
                        margin: 0.5rem 0;
                        padding-left: 1.5rem;
                    }
                    .markdown-content li {
                        margin: 0.25rem 0;
                    }
                    .markdown-content code {
                        background-color: #f3f4f6;
                        padding: 0.125rem 0.25rem;
                        border-radius: 0.25rem;
                        font-size: 0.875rem;
                        font-family: monospace;
                    }
                    .markdown-content pre {
                        background-color: #1f2937;
                        color: white;
                        padding: 0.75rem;
                        border-radius: 0.5rem;
                        overflow-x: auto;
                        margin: 0.5rem 0;
                    }
                    .markdown-content pre code {
                        background-color: transparent;
                        padding: 0;
                        color: inherit;
                    }
                    .markdown-content strong {
                        font-weight: 600;
                        color: #1f2937;
                    }
                    .markdown-content em {
                        font-style: italic;
                    }
                    .markdown-content a {
                        color: #2563eb;
                        text-decoration: underline;
                    }
                    .markdown-content a:hover {
                        color: #1d4ed8;
                    }
                    .markdown-content p {
                        margin-bottom: 0.5rem;
                    }
                    #chatContainer::-webkit-scrollbar {
                        width: 8px;
                    }
                    #chatContainer::-webkit-scrollbar-track {
                        background: #f1f1f1;
                        border-radius: 10px;
                    }
                    #chatContainer::-webkit-scrollbar-thumb {
                        background: #888;
                        border-radius: 10px;
                    }
                    #chatContainer::-webkit-scrollbar-thumb:hover {
                        background: #555;
                    }
                </style>
                <div class="flex flex-col h-[500px]">
                    <!-- Chat Container -->
                    <div id="chatContainer" class="flex-1 overflow-y-auto p-4 bg-gray-50 rounded-lg mb-4 space-y-3">
                        <div class="flex items-start gap-2">
                            <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white flex-shrink-0">
                                🤖
                            </div>
                            <div class="bg-white rounded-lg p-3 shadow-sm max-w-[80%]">
                                <p class="text-sm text-gray-800 mb-3">
                                    ¡Hola! Soy CoffeeIA ☕. Puedo ayudarte a analizar las ventas del calendario. ¿Qué te gustaría saber?
                                </p>
                                <div class="space-y-2" id="quickOptions">
                                    <button class="quick-option w-full text-left px-4 py-2 bg-gray-50 hover:bg-gray-100 rounded-lg text-sm text-gray-700 transition-colors duration-200 border border-gray-200">
                                        Los mejores 5 días del calendario
                                    </button>
                                    <button class="quick-option w-full text-left px-4 py-2 bg-gray-50 hover:bg-gray-100 rounded-lg text-sm text-gray-700 transition-colors duration-200 border border-gray-200">
                                        Haz un análisis de ventas
                                    </button>
                                    <button class="quick-option w-full text-left px-4 py-2 bg-gray-50 hover:bg-gray-100 rounded-lg text-sm text-gray-700 transition-colors duration-200 border border-gray-200">
                                        Haz una estrategia de ventas
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Input Container -->
                    <div class="flex items-center gap-2">
                        <input 
                            type="text" 
                            id="chatInput" 
                            placeholder="Escribe tu pregunta aquí..."
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 
                                focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                        <button 
                            id="sendButton"
                            class="flex items-center justify-center bg-[#1A73E8] hover:bg-[#1669C1] text-white 
                                rounded-lg w-14 h-11 transition-colors duration-300 shadow-sm"
                        >
                        <i class="icon-paper-plane text-white"></i>
                        </button>
                    </div>
                </div>

            `,
            onShown: () => {
                const chatInput = $('#chatInput');
                const sendButton = $('#sendButton');
                const chatContainer = $('#chatContainer');

                const sendMessage = async () => {
                    const message = chatInput.val().trim();
                    if (!message) return;

                    chatInput.val('');
                    chatInput.prop('disabled', true);
                    sendButton.prop('disabled', true);

                    const userMessageHtml = `
                        <div class="flex items-start gap-2 justify-end">
                            <div class="bg-blue-600 text-white rounded-lg p-3 shadow-sm max-w-[80%]">
                                <p class="text-sm">${message}</p>
                            </div>
                            <div class="w-8 h-8 rounded-full bg-gray-400 flex items-center justify-center text-white flex-shrink-0">
                                👤
                            </div>
                        </div>
                    `;
                    chatContainer.append(userMessageHtml);
                    chatContainer.scrollTop(chatContainer[0].scrollHeight);

                    chatMessages.push({ role: "user", content: message });

                    const loadingHtml = `
                        <div class="flex items-start gap-2" id="loadingMessage">
                            <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white flex-shrink-0">
                                🤖
                            </div>
                            <div class="bg-white rounded-lg p-3 shadow-sm">
                                <div class="flex gap-1">
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                </div>
                            </div>
                        </div>
                    `;
                    chatContainer.append(loadingHtml);
                    chatContainer.scrollTop(chatContainer[0].scrollHeight);

                    try {
                        const ventasValidas = this._ventasData.filter(d => !d.isEmpty && d.total > 0);

                        const totalVentas = ventasValidas.reduce((sum, d) => sum + parseFloat(d.total || 0), 0);
                        const totalClientes = ventasValidas.reduce((sum, d) => sum + parseInt(d.clientes || 0), 0);
                        const promedioVentaDiaria = ventasValidas.length > 0 ? (totalVentas / ventasValidas.length).toFixed(2) : 0;

                        const mejorDia = ventasValidas.reduce((max, d) =>
                            parseFloat(d.total || 0) > parseFloat(max.total || 0) ? d : max
                            , ventasValidas[0] || {});

                        const peorDia = ventasValidas.reduce((min, d) =>
                            parseFloat(d.total || 0) < parseFloat(min.total || 0) ? d : min
                            , ventasValidas[0] || {});

                        const ventasPorDiaSemana = {};
                        ventasValidas.forEach(d => {
                            const dia = moment(d.fecha).format('dddd');
                            if (!ventasPorDiaSemana[dia]) {
                                ventasPorDiaSemana[dia] = { total: 0, count: 0, clientes: 0 };
                            }
                            ventasPorDiaSemana[dia].total += parseFloat(d.total || 0);
                            ventasPorDiaSemana[dia].count += 1;
                            ventasPorDiaSemana[dia].clientes += parseInt(d.clientes || 0);
                        });

                        const ventasDetalladas = ventasValidas.map(d =>
                            `📅 ${d.fecha} (${moment(d.fecha).format('dddd')}): 💰 ${d.totalFormateado} | 👥 ${d.clientes} clientes | 🍽️ CP: ${d.chequePromedio}`
                        ).join('\n');

                        const resumenDiasSemana = Object.entries(ventasPorDiaSemana)
                            .map(([dia, data]) => {
                                const promedio = (data.total / data.count).toFixed(2);
                                return `${dia}: Promedio $${promedio} (${data.count} días, ${data.clientes} clientes totales)`;
                            })
                            .join('\n');

                        let udn_id = $(`#filterBar${this.PROJECT_NAME} #udn`).val();
                        const udnNombre = $(`#filterBar${this.PROJECT_NAME} #udn option:selected`).text();

                        const empresasDescripcion = {
                            '4': 'Es un restaurante de Mar y Tierra a las afueras de la ciudad, cuenta con una hermosa vista al mar (cerca de los barcos de la Marina) y ofrecen un estilo tropical contemporáneo, con influencias de diseño costero y rústico natural.',
                            '1': 'Es un motel 4 estrellas, un lugar romántico y exclusivo de la ciudad, diseñado para ofrecer a sus huéspedes comodidad, discreción y elegancia en cada detalle. Estilo de decoración lujo moderno, toque romántico y elementos naturales.',
                            '5': 'Es un boutique de cortes finos que vende carne y es restaurante. Su estilo es industrial moderno con toques rústicos.',
                            '6': 'Es una pastelería y panadería de calidad. Su estilo es rústico artesanal con toques vintage y colonial.'
                        };

                        let descripcionEmpresa = '';
                        if (!udn_id) {
                            descripcionEmpresa = 'Empresa del grupo con enfoque en servicio de calidad y experiencia del cliente.';
                        } else if (empresasDescripcion[udn_id]) {
                            descripcionEmpresa = empresasDescripcion[udn_id];
                        } else {
                            descripcionEmpresa = 'Es una unidad de negocio con enfoque en servicio de calidad y experiencia del cliente.';
                        }

                        const promptSystem = `
                            Eres CoffeeIA 🤖, un asistente experto en análisis de ventas y datos.
                            
                            📊 RESUMEN GENERAL:
                            - Total de días con ventas: ${ventasValidas.length}
                            - Venta total acumulada: $${totalVentas.toFixed(2)}
                            - Promedio de venta diaria: $${promedioVentaDiaria}
                            - Total de clientes atendidos: ${totalClientes}
                            - Mejor día: ${mejorDia.fecha} con ${mejorDia.totalFormateado}
                            - Día más bajo: ${peorDia.fecha} con ${peorDia.totalFormateado}
                            
                            🏢 UNIDAD DE NEGOCIO:
                            - UDN Seleccionada: ${udnNombre}
                            - Período: ${ventasValidas.length > 0 ? ventasValidas[0].fecha : 'N/A'} al ${ventasValidas.length > 0 ? ventasValidas[ventasValidas.length - 1].fecha : 'N/A'}
                            
                            📝 DESCRIPCIÓN DE LA EMPRESA:
                            ${descripcionEmpresa}
                            
                            📈 PROMEDIO POR DÍA DE LA SEMANA:
                            ${resumenDiasSemana}
                            
                            📅 DATOS DETALLADOS POR DÍA:
                            ${ventasDetalladas}
                            
                            🎯 INSTRUCCIONES:
                            - SIEMPRE menciona que los datos corresponden a "${udnNombre}" cuando sea relevante
                            - Considera el contexto y estilo de la empresa al hacer recomendaciones estratégicas
                            - Si te piden estrategias de ventas, adapta tus sugerencias al tipo de negocio y su estilo
                            - Responde de manera clara, concisa y profesional
                            - Usa emojis cuando sea apropiado para hacer la conversación más amigable
                            - Proporciona insights y análisis cuando te pregunten sobre tendencias
                            - Puedes hacer comparaciones entre días, semanas o períodos
                            - Si te preguntan por un día específico, busca en los datos detallados
                            - Puedes calcular porcentajes, promedios y hacer análisis comparativos
                            - Sé proactivo sugiriendo análisis relevantes cuando sea apropiado
                            - Cuando menciones cifras monetarias, usa el formato con $ y comas (ej: $1,500.00)
                        `;

                        const response = await fetch("../../../../DEV/conf/_Complements.php", {
                            method: "POST",
                            headers: { "Content-Type": "application/json" },
                            body: JSON.stringify({
                                model: "gpt-4.1",
                                temperature: 0.7,
                                messages: [
                                    { role: "system", content: promptSystem },
                                    ...chatMessages
                                ]
                            })
                        });

                        if (!response.ok) throw new Error(`HTTP ${response.status}`);

                        const result = await response.json();

                        $('#loadingMessage').remove();

                        if (result?.status === 200) {
                            const aiResponse = result.response;
                            chatMessages.push({ role: "assistant", content: aiResponse });

                            const formattedResponse = this.parseMarkdown(aiResponse);

                            const aiMessageHtml = `
                                <div class="flex items-start gap-2">
                                    <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white flex-shrink-0">
                                        🤖
                                    </div>
                                    <div class="bg-white rounded-lg p-3 shadow-sm max-w-[80%] prose prose-sm">
                                        <div class="text-sm text-gray-800">${formattedResponse}</div>
                                    </div>
                                </div>
                            `;
                            chatContainer.append(aiMessageHtml);
                        } else {
                            const errorHtml = `
                                <div class="flex items-start gap-2">
                                    <div class="w-8 h-8 rounded-full bg-red-600 flex items-center justify-center text-white flex-shrink-0">
                                        ⚠️
                                    </div>
                                    <div class="bg-red-50 rounded-lg p-3 shadow-sm max-w-[80%]">
                                        <p class="text-sm text-red-800">Lo siento, no pude procesar tu pregunta. Intenta nuevamente.</p>
                                    </div>
                                </div>
                            `;
                            chatContainer.append(errorHtml);
                        }
                    } catch (error) {
                        console.error("Error al conectar con CoffeeIA:", error);
                        $('#loadingMessage').remove();

                        const errorHtml = `
                            <div class="flex items-start gap-2">
                                <div class="w-8 h-8 rounded-full bg-red-600 flex items-center justify-center text-white flex-shrink-0">
                                    ⚠️
                                </div>
                                <div class="bg-red-50 rounded-lg p-3 shadow-sm max-w-[80%]">
                                    <p class="text-sm text-red-800">Error de conexión. Por favor, intenta nuevamente.</p>
                                </div>
                            </div>
                        `;
                        chatContainer.append(errorHtml);
                    } finally {
                        chatContainer.scrollTop(chatContainer[0].scrollHeight);
                        chatInput.prop('disabled', false);
                        sendButton.prop('disabled', false);
                        chatInput.focus();
                    }
                };

                sendButton.on('click', sendMessage);
                chatInput.on('keypress', (e) => {
                    if (e.which === 13) sendMessage();
                });

                $('.quick-option').on('click', function () {
                    const optionText = $(this).text();
                    chatInput.val(optionText);
                    // $('#quickOptions').fadeOut(300);
                    sendMessage();
                });

                chatInput.focus();
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
                    lbl: "UDN",
                    class: "w-full",
                    data: lsudn,
                    onchange: `calendar.renderCalendar()`
                }
            ]
        });
    }

    async renderCalendar() {
        const udn = $(`#filterBar${this.PROJECT_NAME} #udn`).val();
        const data = await useFetch({
            url: this._link,
            data: { opc: 'getCalendarioVentas', udn: udn }
        });

        // 🧮 Acumulamos todos los días
        this._ventasData = [];
        (data.semanas || []).forEach(sem => {
            sem.dias.forEach(dia => this._ventasData.push(dia));
        });

        // 📊 Calcular máximos/mínimos por día de la semana
        this._extremes = this.getWeeklyExtremes(this._ventasData);

        // 💰 Calcular la venta global más alta
        const valores = this._ventasData.map(d => parseFloat(d.total) || 0).filter(v => v > 0);
        this._maxVentaGlobal = valores.length ? Math.max(...valores) : 0;

        // 📅 Renderizar calendario
        this.calendarioVentas({
            parent: 'calendario-container',
            id: 'calendarioVentas',
            json: data.semanas || [],
            onDayClick: this.showDayDetail.bind(this)
        });
    }

    calendarioVentas(options) {
        const defaults = {
            parent: "calendario-container",
            id: "calendarioVentas",
            class: "w-full",
            data: {},
            json: [],
            onDayClick: () => { },
            onWeekClick: () => { }
        };

        const opts = Object.assign({}, defaults, options);

        const container = $("<div>", {
            id: opts.id,
            class: opts.class
        });

        const calendarContainer = $("<div>", {
            class: "bg-white rounded-lg shadow-md p-4 border border-gray-200"
        });

        const diasHeader = $("<div>", {
            class: "grid grid-cols-7 gap-2 mb-4"
        });

        const diasSemana = ["Lun", "Mar", "Mié", "Jue", "Vie", "Sáb", "Dom"];
        diasSemana.forEach(dia => {
            diasHeader.append(
                $("<div>", {
                    class: "text-center text-sm font-semibold text-gray-600 py-2",
                    text: dia
                })
            );
        });

        const diasContainer = $("<div>", {
            class: "grid grid-cols-7 gap-2"
        });

        if (opts.json && opts.json.length > 0) {
            const todosDias = [];
            opts.json.forEach((semana) => {
                semana.dias.forEach(dia => {
                    todosDias.push(dia);
                    this._ventasData.push(dia);
                });
            });

            todosDias.sort((a, b) => {
                return moment(a.fecha, 'YYYY-MM-DD') - moment(b.fecha, 'YYYY-MM-DD');
            });

            const semanas = this.agruparPorSemanas(todosDias);

            semanas.forEach(semana => {
                semana.forEach(dia => {
                    if (dia) {
                        const diaCard = this.renderDia(dia, opts);
                        diasContainer.append(diaCard);
                    } else {
                        diasContainer.append($("<div>", { class: "p-4" }));
                    }
                });
            });
        }

        calendarContainer.append(diasHeader, diasContainer);
        container.append(calendarContainer);
        $(`#${opts.parent}`).html(container);
    }

    // 📈 Calcula máximos/mínimos por día de la semana
    getWeeklyExtremes(data) {
        const dias = ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"];
        const extremes = { ventas: {}, cheque: {}, clientes: {} };

        dias.forEach(d => {
            const diasSemana = data.filter(el => moment(el.fecha).format('dddd') === d);

            const ventas = diasSemana.map(el => parseFloat(el.total) || 0).filter(v => v > 0);
            const cheque = diasSemana.map(el => parseFloat((el.chequePromedio || "").replace(/[^\d.]/g, "")) || 0);
            const clientes = diasSemana.map(el => parseInt(el.clientes) || 0);

            extremes.ventas[d] = { max: Math.max(...ventas, 0), min: Math.min(...ventas, ...ventas.length ? [Infinity] : [0]) };
            extremes.cheque[d] = { max: Math.max(...cheque, 0), min: Math.min(...cheque, ...cheque.length ? [Infinity] : [0]) };
            extremes.clientes[d] = { max: Math.max(...clientes, 0), min: Math.min(...clientes, ...clientes.length ? [Infinity] : [0]) };
        });

        return extremes;
    }

    agruparPorSemanas(dias) {
        if (!dias || dias.length === 0) return [];

        const semanas = [];
        let semanaActual = new Array(7).fill(null);
        let primerDia = moment(dias[0].fecha, 'YYYY-MM-DD');
        let inicioSemana = primerDia.clone().startOf('isoWeek');

        dias.forEach(dia => {
            const fecha = moment(dia.fecha, 'YYYY-MM-DD');
            const diaSemana = fecha.isoWeekday() - 1;
            const semanaDia = fecha.clone().startOf('isoWeek');

            if (!semanaDia.isSame(inicioSemana, 'day')) {
                const semanaCompleta = this.completarSemana(semanaActual, inicioSemana);
                semanas.push(semanaCompleta);
                semanaActual = new Array(7).fill(null);
                inicioSemana = semanaDia;
            }

            semanaActual[diaSemana] = dia;
        });

        if (semanaActual.some(d => d !== null)) {
            const semanaCompleta = this.completarSemana(semanaActual, inicioSemana);
            semanas.push(semanaCompleta);
        }

        return semanas;
    }

    completarSemana(semana, inicioSemana) {
        return semana.map((dia, index) => {
            if (dia) return dia;

            const fecha = inicioSemana.clone().add(index, 'days');
            return {
                dia: fecha.format('DD'),
                mes: fecha.format('MM'),
                mesAbreviado: fecha.format('MMM').toLowerCase(),
                fecha: fecha.format('YYYY-MM-DD'),
                diaSemana: fecha.format('dddd'),
                total: 0,
                totalFormateado: '-',
                clientes: 0,
                chequePromedio: '-',
                isEmpty: true
            };
        });
    }

    // 🎨 Aplica color según máximo o mínimo
    getExtremeClass(value, { max, min }) {
        if (value === max && value > 0)
            return "text-green-600 font-extrabold";
        if (value === min && value > 0)
            return "text-red-500 font-bold ";
        return "text-[#103B60]";
    }

    // 📊 Detalle modal de día
    showDayDetail(dia) {
        bootbox.dialog({
            title: `<p class="text-2xl">📊 Ventas del ${dia.dia} de ${dia.mesAbreviado}</p>`,
            size: 'large',
            message: `
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-center mt-3">
                    <!-- 💰 Total -->
                    <div class="bg-gradient-to-br from-[#EAF3FA] to-[#C9E3F8] rounded-2xl p-4 shadow-[0_4px_16px_rgba(0,0,0,0.06)] hover:shadow-[0_8px_24px_rgba(16,59,96,0.12)] hover:-translate-y-1 transition-all duration-300">
                        <div class="flex flex-col items-center">
                            <div class="text-3xl mb-2 text-[#103B60]">💰</div>
                            <h3 class="text-xs uppercase tracking-wide font-semibold text-[#335D84] mb-1">Total</h3>
                            <p class="text-2xl font-bold text-[#103B60] mt-1">${dia.totalFormateado}</p>
                        </div>
                    </div>
                    <!-- 👥 Clientes -->
                    <div class="bg-gradient-to-br from-[#E8F8ED] to-[#C9F3D7] rounded-2xl p-4 shadow-[0_4px_16px_rgba(0,0,0,0.06)] hover:shadow-[0_8px_24px_rgba(140,198,63,0.18)] hover:-translate-y-1 transition-all duration-300">
                        <div class="flex flex-col items-center">
                            <div class="text-3xl mb-2 text-[#639C3F]">👥</div>
                            <h3 class="text-xs uppercase tracking-wide font-semibold text-[#6A9E56] mb-1">Clientes</h3>
                            <p class="text-2xl font-bold text-[#447733] mt-1">${dia.clientes}</p>
                        </div>
                    </div>
                    <!-- 🍽️ Cheque Promedio -->
                    <div class="bg-gradient-to-br from-[#FFF9E8] to-[#FCEFC2] rounded-2xl p-4 shadow-[0_4px_16px_rgba(0,0,0,0.06)] hover:shadow-[0_8px_24px_rgba(255,200,80,0.18)] hover:-translate-y-1 transition-all duration-300">
                        <div class="flex flex-col items-center">
                            <div class="text-3xl mb-2 text-[#C79A00]">🍽️</div>
                            <h3 class="text-xs uppercase tracking-wide font-semibold text-[#B58900] mb-1">Cheque Promedio</h3>
                            <p class="text-2xl font-bold text-[#9A7400] mt-1">${dia.chequePromedio}</p>
                        </div>
                    </div>
                </div>
            `,
            buttons: {
                ok: { label: 'Cerrar', className: 'btn-primary' }
            }
        });
    }

    // 🧾 Renderización diaria con ribbon dorado si es el mejor día
    renderDia(dia, opts) {
        const isEmpty = dia.isEmpty || dia.total === 0;
        const diaSemana = moment(dia.fecha).format('dddd');
        const extremes = this._extremes;
        const total = parseFloat(dia.total) || 0;
        const cheque = parseFloat((dia.chequePromedio || "").replace(/[^\d.]/g, "")) || 0;
        const clientes = parseInt(dia.clientes) || 0;

        const totalClass = this.getExtremeClass(total, extremes.ventas[diaSemana]);
        const chequeClass = this.getExtremeClass(cheque, extremes.cheque[diaSemana]);
        const clientesClass = this.getExtremeClass(clientes, extremes.clientes[diaSemana]);

        const estrella = (clientes === extremes.clientes[diaSemana].max && clientes > 0) ? "⭐" : "";
        const trofeoCheque = (cheque === extremes.cheque[diaSemana].max && cheque > 0) ? "🏆" : "";

        // 💰 Es el mejor día global de ventas
        const esTopVentaGlobal = total === this._maxVentaGlobal && total > 0;

        // 🧱 Card base
        const card = $("<div>", {
            class: `relative rounded-2xl p-4 transition-all duration-300 ease-out cursor-pointer 
                    ${isEmpty ? 'bg-gray-50 text-gray-400' : 'bg-white shadow hover:shadow-lg'}`,
            click: isEmpty ? null : () => opts?.onDayClick?.(dia)
        });

        // ✨ Ribbon dorado (45°)
        if (esTopVentaGlobal) {
            const ribbon = $(`
                <div class="absolute top-0 right-0 w-24 h-24 overflow-hidden pointer-events-none">
                    <div class="ribbon-gold text-white text-[10px] font-bold rotate-45 absolute top-4 right-[-32px] text-center shadow-md">
                        MEJOR VENTA
                    </div>
                </div>
            `);
            card.append(ribbon);
        }

        // 🧭 Contenido
        card.append(`
            <div class="flex flex-col items-center text-center select-none">
                <div class="text-sm font-medium text-gray-500 mb-1">${dia.dia}/${dia.mes}</div>
                <div class="text-lg font-bold ${totalClass}">${dia.totalFormateado}</div>
                <div class="text-xs mt-1 ${clientesClass}">${estrella} ${dia.clientes} clientes</div>
                <div class="text-xs italic ${chequeClass}">${trofeoCheque} CP: ${dia.chequePromedio}</div>
            </div>
        `);

        // 🟩 Línea decorativa inferior
        card.append(`
            <div class="absolute bottom-0 left-0 w-0 h-1 bg-gradient-to-r from-[#8CC63F] to-[#A7E056]
                 rounded-tr-xl rounded-bl-xl group-hover:w-full transition-all duration-300 ease-out"></div>
        `);

        return card;
    }
}
