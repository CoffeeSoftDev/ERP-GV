function lsUDN() {
    let datos = new FormData();
    datos.append("opc", "lsUDN");
    send_ajax(datos, "ctrl/ctrl-usuarios.php").then((data) => {
        console.log(data);
    });
}

lsUDN();