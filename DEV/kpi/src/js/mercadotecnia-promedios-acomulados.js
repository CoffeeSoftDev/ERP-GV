const ctrlPromediosAcomulados = "ctrl/ctrl-mercadotecnia-promedios-acomulados.php";
let promediosAcomulados;

class PromediosAcomulados extends Templates {
 

    lsPromediosAcomulados() {
        
        this.createTable({
            
            idFilterBar: 'filterBar',
            parent: 'contentPromediosAcomulados',
            
            data: {
                opc: 'lsPromediosAcomulados',
            },
         
            conf: {
                datatable: false,
            },
          
            attr: {
                color_th: 'bg-primary',
                class: 'table table-bordered table-sm text-uppercase ',
                id:'tablePromedios',
                center: [1, 2],
                right: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                f_size: '12',
                color_col: [1,2,3,6,9],
                color:'bg-default ',
                extends:true
            },

            success:(data)=>{
               
            }



        });




    }






    
}
