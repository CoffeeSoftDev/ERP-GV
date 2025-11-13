window.ctrlincidencias_calendar = "ctrl/ctrl-incidencias-calendar.php";

$(document).on("sobresJS", function () {
    $("#nav-incidencias-tab").on("click", () => init_components_calendar());
    $("#btnPeriodo").on("click", () => {
        alert({ title: "¿Desea guardar este nuevo período?", text:$("#iptDate").valueDates()[0]+' al '+$("#iptDate").valueDates()[1], icon: "question" }).then((result) => {
            nuevo_periodo(result);
        });
    });
});

function init_components_calendar() {
    tbIncidenciasCalendar();
    $("#iptDate").daterangepicker();
    nav("nav-incidencias-tab", "incidencias-calendar.php");
}

function nuevo_periodo(result) {
    if (result.isConfirmed) {
        let datos = new FormData();
        datos.append("opc", "new_period");
        datos.append("dates", $("#iptDate").valueDates());
        send_ajax(datos, ctrlincidencias_calendar).then((data) => {
            if (data === true) {
                alert();
                tbIncidenciasCalendar();
            } else alert({ title: "Este período ya existe", icon: "error", btn1: true });
        });
    }
}

function tbIncidenciasCalendar() {
    let datos = new FormData();
    datos.append("opc", "tbCalendar");
    send_ajax(datos, ctrlincidencias_calendar).then((data) => {
        $("#tbDatos").html("").create_table(data);
        $("#tbCalendar").table_format({ ordering: false });
    });
}

function delete_periodo(id){
    alert({title:'¿Esta seguro de eliminar este período?',html:'<i class="text-danger icon-attention-1"></i>Esto afectará el funcionamiento de algunos módulos.',icon:'question'}).then(result=>{
        if(result.isConfirmed){
            let datos = new FormData();
            datos.append('opc','delete');
            datos.append('id',id);
            send_ajax(datos,ctrlincidencias_calendar).then(data=>{
                if(data === true){
                    alert({title:'El período se ha eliminado con éxito.',btn1:true});
                    tbIncidenciasCalendar();
                }
                else console.log(data);
            });
        }
    });
}