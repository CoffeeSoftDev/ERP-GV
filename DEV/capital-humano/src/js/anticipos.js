window.ctrlCH = "ctrl/ctrl-ch.php";
const anticipos = new Anticipos(ctrlCH);
anticipos.container = "#filterBar";
anticipos.tbContainer = "#tbDatos";

$(async () => {
    await anticipos.filterAnticipos();
    setTimeout(async () => {
        await anticipos.tbAnticipos();
    }, 500);
});
function noData() {
    alert({ icon: "warning", title: "Anticipos no disponibles", text: "Únicamente del 5 al 9 o del 20 al 25 de cada mes", btn1: true });
}
function diasTranscurridos(fecha) {
    // Convertir la fecha ingresada en un timestamp
    const timestampFecha = new Date(fecha).getTime();

    // Obtener el timestamp de hoy
    const hoy = Date.now();

    // Calcular la diferencia en milisegundos entre la fecha ingresada y hoy
    const diferenciaMilisegundos = hoy - timestampFecha;

    // Calcular el número de días de diferencia
    const diasDiferencia = Math.floor(diferenciaMilisegundos / (1000 * 60 * 60 * 24));

    return diasDiferencia;
}

const advance = () => anticipos.advance();

function formato_ancitipos(id){
    sessionStorage.setItem('anticipo',id);
    sessionStorage.setItem('fechas',$("#iptDate").valueDates());
    window.open('formato_anticipos.php', '_blank');
}