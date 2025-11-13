let idE ;
class Encuesta extends App {
    
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.Evaluador = '';
    }

    init() {
        this.render();
    }

    render(options) {
        console.log('render',options);


        this.questionLayout(options);
        // initials.
        this.createEndEvaluationButtons(options);
        this.groupCard(options);
    }

    // Layout

    questionLayout(options) {
        let opts = {
            id: `content-${this.PROJECT_NAME}`,
            parent: 'root',
            class: "row px-3 py-2",
            contenedor: [
                {
                    type: "div",
                    class: "line",
                    id: `content-header-${this.PROJECT_NAME}`,
                },

                {
                    type: "div",
                    class: "flex flex-col gap-4 p-2 border border-gray-200 rounded-lg",
                    id: `content-questions-${this.PROJECT_NAME}`,
                    children: [
                        { class: "w-full p-2 rounded-lg ", id: "groups" },
                        { class: "w-full p-2 rounded-lg ", id: "questions" },
                    ] 
                },

                {
                    type: "div",
                    class: "col-12 text-end py-4 line ",
                    id: `content-footer-${this.PROJECT_NAME}`,
                },
            ],
        };



        this.createPlantilla({
            data: opts,
            parent: opts.parent,
            design: false,
        });

        // initials.
        $('#content-header-' + this.PROJECT_NAME).append(`
        <div class="flex flex-col line">
          <!-- Título principal -->
            <h3 class="font-bold text-blue-950 uppercase text-center mb-2 text-lg">
            Sistema de Evaluación de Resultados - Valores
            </h3>
            
            <!-- Descripción o subtítulo -->
            <p class="text-gray-700 text-sm mb-2">
                En grupo VAROCH buscamos mejorar nuestros procesos y desempeño.
                Por favor evalúa a las siguientes personas de acuerdo con la siguiente escala y coloca el número correspondiente:
            </p>

            <span class="text-gray-800 font-bold  text-xs "> 5= Totalmente de acuerdo,  4= De acuerdo, 3= Ni de acuerdo ni en desacuerdo, 2= En desacuerdo, 1= Totalmente en desacuerdo</span>
        </div> `);


        
    }

    createEndEvaluationButtons(options) {

      
        let buttons = [

            {
                opc: 'button',
                color: "outline-secondary fw-bold",
                icon: "icon-check",
                text: "Salir de la evaluación",
                onClick: () => {

                    alert({
                        icon: "question",
                        title: "¿Desea regresar?",
                        // text: "Si regresa puede que algunos cambios no se guarden.",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            app.render();

                        }
                    });


                },
            },
            {
                opc: 'button',
                color: "primary fw-bold",
                icon: "icon-check",
                text: "Terminar evaluación",
                onClick: () => {

                    this._link = ctrl_encuesta;

                    // 📜 **Seleccionar todos los botones dentro del `groupCard`**
                    const buttons = document.querySelectorAll("#groupCard a");

                    // 📜 **Filtrar los botones que NO tienen la clase `btn-success` (evaluaciones incompletas)**
                    let incompletos = [];
                    buttons.forEach(button => {
                        if (!button.classList.contains("btn-success")) {
                            incompletos.push(button.innerText.trim()); // Captura el texto del botón
                        }
                    });

                    // 📜 **Si todas las evaluaciones están completas**
                    if (incompletos.length === 0) {
                        this.swalQuestion({
                            opts: {
                                icon: "question",
                                title: "¿Deseas terminar la evaluación?",
                                text: "Al finalizar se guardarán los cambios de la encuesta.",
                                confirmButtonText: "Sí, terminar",
                                cancelButtonText: "Cancelar"
                            },
                            data: {
                                opc: 'endEvaluation',
                                id_status: 2,
                                idEvaluation: options.id,
                                udn: options.udn,
                                idEmployed: options.idEmployed
                            },
                            methods: {
                                request: (data) => {
                                    this._link = ctrl;
                                    
                                    app.render();
                                    alert({text:' Gracias por realizar la encuesta'});
                                }
                            }
                        });
                    } else {
                        // 📜 **Mostrar mensaje indicando qué evaluaciones faltan**
                        let mensaje = "No se puede finalizar la evaluación. Faltan las siguientes evaluaciones:\n\n";
                        mensaje += incompletos.join("\n");

                        Swal.fire({
                            icon: "warning",
                            title: "Evaluaciones Incompletas",
                            text: mensaje
                        });
                    }

                },
            },
        ];

        this.createButtonGroup({
            data: buttons,
            class: "justify-content-end gap-2 pt-2",
            parent: "content-footer-" + this.PROJECT_NAME,
            size: "sm",
            cols: "w-25 p-2",
        });
    }

    async onShowQuestionnaire(idEmployed, idUDN, idEvaluation) {

        let data = await useFetch({
            url: ctrl_encuesta,
            data: { opc: 'getGroup', id_evaluator: idEmployed, id_udn: idUDN, idEvaluation: idEvaluation }
        });

        this.render({
            evaluators: data.evaluators,
            id: idEvaluation,
            idEmployed: idEmployed,
            udn: idUDN
        });

    }

    async initEvaluation(options, id) {

        let questions = await useFetch({ url: ctrl_encuesta, data: { opc: "getQuestionnaire", id_evaluated: id, id_evaluation: options.id } });
        var nombre    = document.getElementById(id).querySelector("h6").innerText;

        this.createEvaluation({
            parent: 'questions',
            data: questions,

            questions: {
                id: options.id,
                data: [],
                json: questions
            },

            info: {
                user: nombre,
                id: id,

            }

        });

    }

    groupCard(options) {

  
        this.createGroups({
            parent: "groups",
            title: "Evaluados",
            data: options.evaluators,
            onclick: (id) => {
                this.initEvaluation(options, id);
            }
        });
    }


    // Components.


    createEvaluation(options) {
        let defaults = {
            parent: 'questionnaireContainer',
            questions: {
                id: 0,
                data: [],
                json: []
            },
            info: {
                user: '',
                puesto: 'somx',
                id: 0
            },
            options: ['1', '2', '3', '4', '5']
        };

        let opts = Object.assign({}, defaults, options);

        let container = $('<div>', { class: 'questionnaire bg-gray-200 p-3 rounded-lg shadow-sm overflow-auto', id: opts.parent, style: 'max-height: 600px;' });

        let mainTitle = $('<div>', { class: 'p-2 text-[18px] rounded-lg' }).append(
            $('<span>', { class: '', html: `<strong>  Nombre:</strong> ${opts.info.user}  ` }),
            // $('<span>', { class: 'mb-1', html: `<strong>Puesto:</strong> ${opts.info.puesto}` })
        );

        let titleElement = $('<h4>', { class: 'text-uppercase mb-2', html: mainTitle });
        let subTitle = $('<h6>', {
            class: 'text-muted mx-1 px-2 mb-2', html: ''
        });


        container.append(titleElement,
            // subTitle
        );

        opts.questions.json.forEach(section => {


            let id_valor = section.id_valor;

            let sectionContainer = $('<div>', { class: 'mb-4 p-3 bg-white rounded-md shadow-sm' });

            let header = $('<h6>', { class: 'fw-bold text-uppercase mb-2', text: section.title });
            sectionContainer.append(header);

            section.questions.forEach(question => {

                let id_question = question.id;

                let questionContainer = $('<div>', { class: 'mb-3' });
                let questionText = $('<p>', { class: 'text-muted font-semibold mb-1', text: question.text });
                let buttonGroup = $('<div>', { class: 'relative flex grid grid-cols-5 gap-5' });


                // 📜 **Guardar la respuesta si ya fue respondida, si no, dejar vacío**
                let answeredValue = question.data.length > 0 ? question.data[0].answered : "";


                opts.options.forEach(opt => {

                    let button = $('<button>', {
                        class: 'btn btn-outline-secondary rounded-2 px-1 py-1 shadow-sm',
                        text: opt,
                        id: question.id,
                        click: function () {

                            useFetch({
                                url: ctrl_encuesta,
                                data: {
                                    opc: 'addSurvey',
                                    id_evaluation: opts.questions.id,
                                    id_evaluated: opts.info.id,
                                    id_question: id_question,

                                    answered: opt
                                },
                                success: ()=>{

                                    const resultado = countRequest();
                                    updateRequest( opts.info.id, resultado.respondidas);

                                }
                            });

                       

                            $(this).siblings().removeClass('active btn-primary text-white').addClass('btn-outline-secondary');
                            $(this).addClass('active btn-primary text-white').removeClass('btn-outline-secondary');
                        }
                    });

                    // 🔵 **Si la pregunta ya fue respondida, seleccionar el botón correspondiente**
                    if (opt === answeredValue) {
                        button.addClass('active btn-primary text-white').removeClass('btn-outline-secondary');
                    }

                    buttonGroup.append(button);
                });

                questionContainer.append(questionText, buttonGroup);
                sectionContainer.append(questionContainer);
            });

            container.append(sectionContainer);
        });

        $('#' + opts.parent).html(container);
    }

    createGroups(options) {
        let defaults = {
            parent: "groupButtons",
            cols: "w-25 ",
            size: "sm",
            type: "group",
            colors: "bg-primary",
            description: "",
            titleGroup: "Tiempo",
            subtitleGroup: "hrs",
            class:'',
            id:'groupCard',
            data: [],
            styleCard: {
                group: { class: "category-card mb-3" }
            }
        };

        let opts = Object.assign(defaults, options);
        let container = $('<div>', { id: opts.id,class: 'flex gap-3 overflow-auto ' + opts.class });
        let divs = $('#' + opts.parent);
        divs.empty();

        // 📜 **Agregar título y descripción**
        if (opts.title) {
            divs.append(
                $('<label>', { class: 'uppercase font-bold text-muted mb-2', text: opts.title }),
                $('<p>', { class: 'mb-0', text: opts.description })
            );
        }

        divs.append(container);

        // 📜 **Generar los elementos del grupo**
        if (opts.data.length) {
            opts.data.forEach((El) => {
                let class_answered_group = (El.items && El.result && El.items === El.result) ? 'btn-success' : 'btn-outline-primary';
                let btn = $('<a>', {
                    class: `btn btn-${opts.size}  ${opts.cols} flex  p-3 flex-col align-items-center justify-content-center ${class_answered_group}`,
                    id: El.id,
                    click: () => opts.onclick(El.id)
                });

                if (El.icon) {
                    let icon = $('<i>', { class: El.icon + ' d-block' });
                    btn.append(icon);
                }

                btn.append(
                    $('<h6>', { class: 'text-uppercase fw-bold', text: El.valor })
                );

                if (El.items !== undefined && El.result !== undefined) {
                    btn.append(
                        $('<span>', { html: `Preguntas: ${El.result} / ${El.items}` })
                    );
                }

                container.append(btn);
            });
        } else {
            container.append('No hay grupos definidos.');
        }

        // 📌 **Manejar selección de botón activo**
        const cardPosGroup = document.getElementById(opts.parent);
        if (!cardPosGroup) return;

        cardPosGroup.addEventListener('click', function (event) {
            if (event.target.closest('a')) {
                const buttons = cardPosGroup.querySelectorAll('a');
                buttons.forEach(button => {
                    button.classList.remove('active', 'btn-primary', 'text-white');
                    button.classList.add('btn-outline-primary');
                });

                const clickedButton = event.target.closest('a');
                clickedButton.classList.add('active', 'btn-primary', 'text-white');
                clickedButton.classList.remove('btn-outline-primary');
            }
        });
    }
}


function countRequest() {
    // 📜 **Seleccionar todas las preguntas**
    const preguntas = document.querySelectorAll("#questions .mb-3");
    let totalPreguntas = preguntas.length;
    let preguntasRespondidas = 0;

    preguntas.forEach(pregunta => {
        // 📜 **Buscar si algún botón dentro de la pregunta tiene la clase 'active'**
        const botonesActivos = pregunta.querySelectorAll("button.active");
        if (botonesActivos.length > 0) {
            preguntasRespondidas++;
        }
    });

    return {
        total: totalPreguntas,
        respondidas: preguntasRespondidas,
        pendientes: totalPreguntas - preguntasRespondidas
    };
}

function updateRequest(idBoton, nuevasPreguntas) {
    // 📜 **Seleccionar el botón con el ID específico**
    const boton = document.querySelector(`a[id='${idBoton}']`);

    if (boton) {
        // 📜 **Buscar el span dentro del botón**
        const span = boton.querySelector("span");

        if (span) {
            // 📜 **Actualizar el texto del span**
            span.textContent = `Preguntas: ${nuevasPreguntas} / 10`;
        }

        // 📜 **Si el conteo llega a 10, agregar la clase 'active'**
        if (nuevasPreguntas >= 10) {
            boton.classList.add("active", "btn-success"); // Agrega las clases necesarias
            boton.classList.remove("btn-outline-primary"); // Remueve clases previas si es necesario
        } else {
            // 📜 **Si baja de 10, quitar la clase 'active'**
            boton.classList.remove("active", "btn-success");
            boton.classList.add("btn-outline-primary");
        }
    }
}