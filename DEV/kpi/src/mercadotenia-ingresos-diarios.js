class IngresosDiarios extends App {
    constructor(link, div_modulo) {
        super(link, div_modulo);
    }

 
    lsIngresosDiarios() {


        this.createTable({

            idFilterBar: 'filterBar',
            parent: 'contentIngresosDiarios',

            data: {
                opc: $('#Mes option:selected').text(),
                mesCompleto: $('#Mes option:selected').text()

            },

            conf:{
                datatable:false,
            },

            attr: {
                id: 'tbCapturas',
                center: [1, 2, 3, 4, 5],
                class: 'table table-bordered text-uppercase',
                f_size: '14',
                col: [2]
            }

        });

    }

    setVentas(event, idVenta) {

        const names = event.target.name;
        // const value = event.target.value;

        console.log(names, event);

        // let send = {
        //     opc     : "setVentas",
        //     [names]  : value,
        //     id_venta: idVenta,
        //     name    : names
        // };

        // fn_ajax(send, this._link).then((data) => {
        //     // console.log(data);
        // });
    }




}
