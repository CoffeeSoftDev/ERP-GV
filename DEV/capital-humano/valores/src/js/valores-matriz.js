class Matrix extends App {
    constructor(link, div_modulo) {
        super(link, div_modulo);
    }

    render() {
        this.layout();
        this.filterBar();
        this.ls();
    }

    async getListEvaluated() {
        let data = await useFetch({ url: this._link, data: { opc: "getEvaluated", udn: $("#id_udn").val() } });
        $("#id_evaluated").option_select({ select2: true, father: true, data: data });
    }

    async getListEvaluators() {
        let data = await useFetch({ url: this._link, data: { opc: "getEvaluators" } });
        $("#id_evaluator").option_select({ select2: true, father: true, data: data, placeholder: "Seleccione uno o varios evaluadores" });
    }

    matrixModal(id) {
        bootbox.dialog({
            title: "Nueva Matriz",
            size: "extra-large",
            closeButton: true,
            message: `
                <form class="row" id="formMatrix" novalidate>
                    <div class="col-2 mb-2">
                        <label for="id_udn" class="form-label fw-bold">UDN</label>
                        <select class="form-select" id="id_udn" name="id_UDN" onchange="matrix.getListEvaluated()" required></select>
                    </div>
                    <div class="col-4 mb-2">
                        <label for="id_evaluated" class="form-label fw-bold">Evaluado</label>
                        <select class="form-select" id="id_evaluated" name="id_evaluated" required></select>
                    </div>
                    <div class="col-4 mb-3">
                        <label for="id_evaluator" class="form-label fw-bold">Evaluadores</label>
                        <select class="form-select" id="id_evaluator" name="id_evaluator"></select>
                    </div>
                    <div class="col-2 text-center">
                        <label for="" class="form-label fw-bold">­</label>
                        <button type="button" class="btn btn-primary col-12" id="btnAddEvaluator"><i class="icon-plus"></i>Agregar</button>
                    </div>
                </form>
                <div class="col-12 h-[50vh] overflow-y-auto rounded-2xl" id="evaluatorsContainer">
                     <table class="min-w-full text-sm text-left  border border-gray-700">
                        <thead>
                        <tr class="bg-[#F2F5F9] rounded-t-xl">
                            <th class="px-4 py-3 font-semibold text-xs uppercase text-[#003360] rounded-tl-xl">Evaluador</th>
                            <th class="px-4 py-3 font-semibold text-xs uppercase text-[#003360] rounded-tr-xl">Acciones</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-300" id="tbodyEvaluadores">
                        <!-- filas dinámicas aquí -->
                        </tbody>
                    </table>
                </div>
                <div class="col-12 flex justify-end">
                    <button type="button" class="btn btn-primary" id="btnSaveMatrix">Guardar Matriz</button>
                </div>
            `,
        });

        $("#id_udn").option_select({ select2: false, father: true, data: udn });
        matrix.getListEvaluated();
        matrix.getListEvaluators();


        // Agregar evaluador
        $("#btnAddEvaluator").click(() => {
            // Agregar evaluador
            let evaluador = $("#id_evaluator").val();

            if (evaluador) {
                let nombreEvaluador = $("#id_evaluator option:selected").text();
                let idEvaluado = $("#id_evaluated").val();

                // Validar que el evaluador no sea el mismo que el evaluado
                if (evaluador.includes(idEvaluado)) {
                    alert({ icon: "error", text: "El evaluador no puede ser el mismo que el evaluado", btn1: true, btn1Text: "Ok" });
                    return;
                }

                // Validar que no se repita el evaluador
                let existe = false;
                $("#tbodyEvaluadores tr").each(function () {
                    let id = $(this).data("id");
                    if (id == evaluador) {
                        existe = true;
                        return false; // Salir del bucle
                    }
                });

                if (existe) {
                    alert({ icon: "error", text: "El evaluador ya fue agregado", btn1: true, btn1Text: "Ok" });
                    return;
                }

                // Agregar fila a la tabla
                $("#tbodyEvaluadores").append(`
                    <tr class="" data-id="${evaluador}">
                        <td class="px-4 py-3">${nombreEvaluador}</td>
                        <td class="px-4 py-3">
                            <button class="btn btn-outline-danger btn-sm btnDeleteEvaluator"><i class="icon-trash"></i> Eliminar</button>
                        </td>
                    </tr>
                `);

                // Limpiar select
                $("#id_evaluator").val(null).trigger("change");

                // Agregar evento al boton eliminar
                $(".btnDeleteEvaluator").off("click").on("click", function () {
                    $(this).closest("tr").remove();
                });
            }
        });

        // Guardar matriz
        $("#btnSaveMatrix").click(() => {
            let evaluadores = [];
            $("#tbodyEvaluadores tr").each(function () {
                evaluadores.push($(this).data("id"));
            });

            // Validar que el campo evaluador no este vacio
            if (evaluadores.length == 0) {
                alert({ icon: "error", text: "Seleccione al menos un evaluador", btn1: true, btn1Text: "Ok" });
                return;
            }

            // Validar que el array de evaluadores no contenga al evaluado
            if (evaluadores.includes($("#id_evaluated").val())) {
                alert({ icon: "error", text: "El evaluador no puede ser el mismo que el evaluado", btn1: true, btn1Text: "Ok" });
                return;
            }

            // Validar que haya seleccionado al menos un evaluado
            if ($("#id_evaluated").val() == null || $("#id_evaluated").val() == "") {
                alert({ icon: "error", text: "Seleccione al menos un evaluado", btn1: true, btn1Text: "Ok" });
                return;
            }

            $("#formMatrix button[type='submit']").attr("disabled", "disabled");
            matrix.addMatrix(id, evaluadores);
        });
    }

    addMatrix(id, evaluadores) {
        let datos = {
            opc: "addMatrix",
            id_udn: $("#id_udn").val(),
            id_evaluated: $("#id_evaluated").val(), // Evaluado
            id_evaluator: evaluadores // Evaluadores
        };

        fn_ajax(datos, ctrl).then((response) => {
            if (response.status == 200) {
                alert({ icon: "success", text: response.message });
                $("#formMatrix button[type='submit']").removeAttr("disabled");
                $("#id_evaluator").val(null).trigger("change");
                $("#tbodyEvaluadores").empty();
                matrix.ls();
            } else {
                alert({ icon: "error", text: response.message, btn1: true, btn1Text: "Ok" });
                $("#formMatrix button[type='submit']").removeAttr("disabled");
            }
        });
    }

    async editMatriz(id) {
        let data = await useFetch({ url: this._link, data: { opc: "getMatrix", id: id } });
        let evaluators = await useFetch({ url: this._link, data: { opc: "getEvaluators" } });

        bootbox.dialog({
            title: `Editar Matriz de ` + data.matrix[0].nombre,
            closeButton: true,
            size: "extra-large",
            message: `
                <form class="row" id="formMatrix" novalidate>
                    <div class="col-4 mb-3">
                        <label for="id_evaluator" class="form-label fw-bold">Evaluadores</label>
                        <select class="form-select" id="id_evaluator" name="id_evaluator"></select>
                    </div>
                    <div class="col-2 text-center">
                        <label for="" class="form-label fw-bold">­</label>
                        <button type="button" class="btn btn-primary col-12" id="btnSaveMatrixEdit">Guardar</button>
                    </div>
                </form>
                <div class="col-12 h-[50vh] overflow-y-auto rounded-2xl" id="evaluatorsEditContainer">
                   
                </div>
                `,
        });
        $("#id_evaluator").option_select({ select2: true, father: true, data: evaluators, placeholder: "Seleccione uno o varios evaluadores" });

        matrix.lsEvaluators(data.matrix[0].id_evaluated);

        // Guardar evaluador
        $("#btnSaveMatrixEdit").off("click").on("click", function () {
            // Validar que el campo evaluador no este vacio
            if ($("#id_evaluator").val() == null || $("#id_evaluator").val() == "") {
                alert({ icon: "error", text: "Seleccione al menos un evaluador", btn1: true, btn1Text: "Ok" });
                return false;
            }
            //Validar que el array de evaluadores no contenga al evaluado
            if ($("#id_evaluator").val().includes(data.matrix[0].id_evaluated)) {
                alert({ icon: "error", text: "El evaluador no puede ser el mismo que el evaluado", btn1: true, btn1Text: "Ok" });
                return false;
            }

            // console.log("lista de evaluadores:",data.evaluadores);
            // console.log("evaluador:",$("#id_evaluator").val());
            // // Validar que el evaluador no se repita
            // if (data.evaluadores.some(e => e.id_evaluator == $("#id_evaluator").val())) {
            //     alert({ icon: "error", text: "No se puede agregar el mismo evaluador", btn1: true, btn1Text: "Ok" });
            //     return false;
            // }

            $("#formMatrix button[type='submit']").attr("disabled", "disabled");
            matrix.updateMatrix(id, $("#id_evaluator").val(), data.matrix[0].id_evaluated);
        });
    }

    updateMatrix(id, evaluators, evaluated) {
        let datos = {
            opc: "editMatrix",
            id_evaluator: [evaluators],
            id_matrix: id,
        };

        fn_ajax(datos, ctrl).then((response) => {
            if (response.status == 200) {
                alert({ icon: "success", text: response.message });
                $("#formMatrix button[type='submit']").removeAttr("disabled");
                $("#id_evaluator").val(null).trigger("change");
                matrix.lsEvaluators(evaluated);
                matrix.ls();
            } else {
                alert({ icon: "error", text: response.message, btn1: true, btn1Text: "Ok" });
                $("#formMatrix button[type='submit']").removeAttr("disabled");
            }
        });
    }

    cancelMatriz(id) {
        this.swalQuestion({
            opts: {
                title: "¿Cancelar esta matriz?",
                text: "Una vez cancelada, no podrás reutilizarla en evaluaciones.",
            },
            data: {
                opc: "cancelMatrix",
                status: 0,
                idMatrix: id,
            },
            methods: {
                request: (res) => {
                    if (res.status == 200) {
                        alert({
                            icon: "success",
                            text: res.message,
                            timer: 1500,
                        });
                        matrix.ls();
                    } else {
                        alert({
                            icon: "error",
                            text: res.message,
                            timer: 1500,
                        });
                    }
                }
            }
        });
    }

    lsEvaluators(idEvaluado) {
        let rangePicker = getDataRangePicker("calendar");

        this.createTable({
            parent: "evaluatorsEditContainer",
            idFilterBar: "filterBarEvaluators",
            data: { opc: "lsEvaluators", employee: idEvaluado, fi: rangePicker.fi, ff: rangePicker.ff },
            conf: {
                datatable: true,
                pag: 6
            },
            coffeesoft: true,
            attr: {
                color_th: "bg-primary",
                id: "tableAss",
                center: [1, 2],
                extends: true
            },
        });
    }

    deleteEvaluator(idMatrix, idEvaluator, idEvaluated) {
        this.swalQuestion({
            opts: {
                title: "¿Eliminar este evaluador?",
                text: "Una vez eliminado, no podrás recuperarlo.",
            },
            data: {
                opc: "deleteEvaluator",
                id_matrix: idMatrix,
                id_evaluator: idEvaluator
            },
            methods: {
                request: (res) => {
                    if (res.status == 200) {
                        alert({
                            icon: "success",
                            text: res.message,
                            timer: 1500,
                        });
                        matrix.ls();
                        this.lsEvaluators(idEvaluated);
                    } else {
                        alert({
                            icon: "error",
                            text: res.message,
                            timer: 1500,
                        });
                    }
                }
            }
        });
    }
}