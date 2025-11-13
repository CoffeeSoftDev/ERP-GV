$.fn.componentes_layout = function (options) {
  
  var defaults = {
    layout: 'componente_1'  ,
    data_table: [],
    data_form: [],
    link: ''
  };
  
  // Carga opciones por defecto
 var opts = $.fn.extend(defaults, options);
    
    // 
    
};




function ls_table(link) {
  dtx = { opc: "ls_table"};

  fn_ajax(dtx, link, "#content-visor").then((data) => {
    
    $("#content-visor").rpt_json_table2({
      data      : data,
      right     : [2, 3, 4],
    });
  });
  
}



