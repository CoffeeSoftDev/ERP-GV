
                window.ctrlmarketing = 'ctrl/ctrl-marketing.php';

                $(function(){
                    listUDN();

                    $('.datepicker').daterangepicker(
                    { 
                        startDate: moment(), 
                        endDate: moment() 
                    },
                    function (startDate, endDate) {
                        alert({
                            icon: 'question',
                            title: 'Se han modificado las fechas',
                            text: '¿Desea actualizar la tabla?',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                console.log(startDate.format('YYYY-MM-DD'));
                                console.log(endDate.format('YYYY-MM-DD'));
                                alert();
                            }
                        });
                    });

                    $('.datepicker').next('span').on('click',function(){
                        $('.datepicker').click();
                    });

                    $('#tbDatos').create_table();

                    $('#btnOk').on('click',()=>{
                        swal_question('¿Esta seguro de realizar esta acción?').then((result)=>{
                            if(result.isConfirmed) alert();
                        });
                    });
                });

                function listUDN(){
                    let datos = new FormData();
                    datos.append('opc', 'listUDN');
                    send_ajax(datos, ctrlmarketing).then((data) => {
                        $("#cbUDN").option_select({data:data});
                    });
                }

                function updateModal(id,title){
                    bootbox.dialog({
                        title: ` EDITAR "${title.toUpperCase()}" `,
                        message: `
                        <form id="modalForm" novalidate>
                            <div class="col-12 mb-3">
                                <label for="cbModal" class="form-label fw-bold">Select</label>
                                <div class="input-group-addon">
                                    <select class="form-select text-uppercase" name="cbModal" id="cbModal"></select>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="iptModal" class="form-label fw-bold">Input</label>
                                <input type="text" class="form-control" name="iptModal" id="iptModal" value="${title}" required>
                                <span class="form-text text-danger hide">
                                    <i class="icon-warning-1"></i>
                                    El campo es requerido.
                                </span>
                            </div>
                            <div class="col-12 mb-3 d-flex justify-content-between">
                                <button type="submit" class="btn btn-primary col-5">Actualizar</button>
                                <button type="button" class="btn btn-outline-danger col-5 bootbox-close-button">Cancelar</button>
                            </div>
                        </form>
                        `,
                    }).on("shown.bs.modal", function () {
                        const opciones = [
                            {'id':1,'valor':'Uno'},
                            {'id':2,'valor':'Dos'},
                            {'id':3,'valor':'Tres'}
                        ];

                        $('#cbModal').option_select({
                            data:opciones,
                            select2:true,
                            father:true,
                            placeholder:'- Seleccionar -'
                        });

                        $('#modalForm').validation_form({"id":id,"opc":"update"},datos=>{
                            for(x of datos) console.log(x);
                        });


                        // send_ajax(datos,ctrlmarketing).then(data=>{
                        //      alert();
                        // });
                    });
                }

                function toggleStatus(id){
                    const BTN = $('#btnStatus'+id);
                    const ESTADO = BTN.attr('estado');

                    let estado = 0;
                    let iconToggle = '<i class="icon-toggle-off"></i>';
                    let question = '¿DESEA DESACTIVARLO?';
                    if ( ESTADO == 0 ) {
                        estado = 1;
                        iconToggle = '<i class="icon-toggle-on"></i>';
                        question = '¿DESEA ACTIVARLO?';
                    }

                    swal_question(question).then((result)=>{
                        if(result.isConfirmed){
                            //let datos = new FormData();
                            //datos.append('opc','');
                            //send_ajax(datos,ctrlmarketing).then((data)=>{
                                // console.log(data);
                                BTN.html(iconToggle);
                                BTN.attr('estado',estado);
                            //});
                        }
                    });
                }
            