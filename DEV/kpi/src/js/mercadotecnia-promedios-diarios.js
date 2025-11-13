


class PromediosDiarios extends App{

    lsPromedios() {
        this.createTable({
            idFilterBar: 'filterBar',
            parent: 'contentPromediosDiarios',
            data: {
                opc: $('#rptPromediosDiarios').val(),
            },
            conf: { datatable: false },
            attr: {
                class: 'table table-bordered table-sm  text-uppercase',
                color_th:'bg-primary',
                center: [1, 2],
                right : [2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                f_size: '14',
                color_group:'bg-disabled2',
              
                extends:true,
            }
        });
    
    }

    filterPromediosDiarios() {

        const filter = [
            {
                opc: 'select',
                class: 'col-12 col-sm-4',
                id: 'rptPromediosDiarios',

                data: [

                    { id: 'lsPromedios', valor: 'Mostrar promedios' },
                    { id: 'lsPromediosDia', valor: 'Mostrar promedios por día' },
                ],

                onchange: 'promediosDiarios.lsPromedios()'
            },
            
            {

                opc  : 'btn',
                class: 'col-sm-2',
                text : 'Ver',
                fn: 'promediosDiarios.lsPromedios()'
            }
        ];


        $('#filterPromediosDiarios').content_json_form({ data: filter, type: '' });

    }






}



// class IngresosDiarios extends App {
//     constructor(link, div_modulo) {
//         super(link, div_modulo);
//     }

//     lsCapturarIngresos() {

//         this.createTable({

//             idFilterBar: 'filterBar',
//             parent: 'contentIngresosDiarios',

//             data: {
//                 opc: $('#Mes option:selected').text(),
//                 mesCompleto: $('#Mes option:selected').text()

//             },

//             attr: {
//                 center: [1, 2, 3, 4, 5],
//                 class: 'table table-bordered text-uppercase',
//                 f_size: '12',
//                 col: [2]
//             }

//         });

//     }

//     lsIngresosDiarios() {


//         this.createTable({

//             idFilterBar: 'filterBar',
//             parent: 'contentIngresosDiarios',

//             data: {
//                 opc: $('#Mes option:selected').text(),
//                 mesCompleto: $('#Mes option:selected').text()

//             },

//             attr: {
//                 center: [1, 2, 3, 4, 5],
//                 class: 'table table-bordered text-uppercase',
//                 f_size: '12',
//                 col: [2]
//             }

//         });

//     }

//     setVentas(event, idVenta) {
//         const name = event.target.name;
//         const value = event.target.value;

//         let send = {
//             opc: "setVentas",
//             [name]: value,
//             id_venta: idVenta,
//         };

//         fn_ajax(send, this._link).then((data) => {
//             // console.log(data);
//         });
//     }




// }
