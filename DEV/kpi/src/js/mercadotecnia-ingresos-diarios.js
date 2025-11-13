const ctrlIngresosDiarios = "ctrl/ctrl-mercadotecnia-ingresos-diarios.php";
var ingresosDiarios;

class IngresosDiarios extends App {
    constructor(link, div_modulo) {
        super(link, div_modulo);
    }

    filterIngresosDiarios() {
        const filter = [
            {
                opc  : "select",
                class: "col-12 col-sm-4 col-lg-3",
                id   : "rptIngresosDiarios",
                lbl  : "Consultar",

                data: [
                    { id: "lsCapturarIngresos", valor: "Captura de ingresos" },
                    { id: "IngresosPorDia", valor: "Ingresos por día" },
                ],

                onchange: "ingresosDiarios.lsIngresosDiarios()",
            },

            {
                opc:'btn',
                class:'col-sm-2',
                text: "<i class='icon-search'></i>",
                fn: "ingresosDiarios.lsIngresosDiarios()",
            }
        ];

        $("#filterIngresosDiarios").content_json_form({ data: filter, type: "" });
    }

    lsCapturarIngresos() {
        this.createTable({
            idFilterBar: "filterBar",
            parent     : "contentIngresosDiarios",

            data: {
                opc        : $("#Mes option:selected").text(),
                mesCompleto: $("#Mes option:selected").text(),
            },

            attr: {
                color_th: "bg-primary",

                center: [1, 2, 3, 4, 5,6,8],
                class : "table table-bordered text-uppercase",
                f_size: "12",

                col: [1, 2],
                extends:true
            },
        });
    }

    lsIngresosDiarios() {

        this.createTable({


            idFilterBar: "filterBar",
            parent     : "contentIngresosDiarios",
            

            data: {
                opc        : $("#rptIngresosDiarios").val(),
                mesCompleto: $("#Mes option:selected").text(),
            },


            conf:{datatable:false},

            attr: {
                id:'tableIngresosDiarios',
                center     : [1, 2, 3, 4, 5, 6, 7,8],
                color_th: "bg-primary",
                
                class      : 'table table-sm table-bordered text-uppercase',
                f_size     : "14",
                color_group: "bg-disabled2",
                col        : [2],
                extends: true,
            },
        
        });


    }

    setVentas(event, idVenta) {
        const name  = event.target.name;
        const value = event.target.value;

        let send = {

            opc     : "setVentas",
            [name]  : value,
            id_venta: idVenta,
            name    : name,
            fecha   : event.target.getAttribute('fecha'),
            UDN: $('#UDN').val()

        };
        
          // fecha: event.target.getAttribute('fecha')
        fn_ajax(send, this._link).then((data) => {
           
        
            $('#Total_' + idVenta).html(data.Total_);
        
        });
    }
}
