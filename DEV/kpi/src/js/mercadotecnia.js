// Definir rutas
const ctrlmercadotecnia       = "ctrl/ctrl-mercadotecnia-kpi.php";
const ctrlPromediosDiarios    = "ctrl/ctrl-mercadotecnia-promedios-diarios.php";
const ctrlAfluencia           = "ctrl/ctrl-mercadotecnia-afluencia.php";

var app;
var ventas, promediosDiarios,afluencia;


$(function () {
  // Instanciar objetos
  app                 = new App(ctrlmercadotecnia, "");
  ingresosDiarios     = new IngresosDiarios(ctrlIngresosDiarios);
  promediosDiarios    = new PromediosDiarios(ctrlPromediosDiarios);
  promediosAcomulados = new PromediosAcomulados(ctrlPromediosAcomulados);
    afluencia = new Afluencia(ctrlAfluencia,"");

  app.initComponents();
});

class App extends Templates {
  static udn = [];

  constructor(link, div_modulo) {
    super(link, div_modulo);
  }

  initComponents() {
    fn_ajax({ opc: "initComponents" }, this._link).then((data) => {

      App.udn = data.udn;
      
      this.createFilterBar();
      const udns = $('#UDN').val();

    //   this.tabsKPIFogaza();
    //   afluencia.render();

      
    this.tabsKPI();
    this.filterBarTotalIngresos();
    this.lsTotalIngresos();

    ingresosDiarios.filterIngresosDiarios();
    ingresosDiarios.lsIngresosDiarios();

    // --
    promediosDiarios.filterPromediosDiarios();
     

    });
  }


  tabsKPIFogaza(){
 
    $("#contentData").simple_json_tab({ data: [
        { tab: "Afluencia", id: "tab-afluencia", active: true },
        { tab: "Ventas", id: "tab-ventas" },
    ] });

  }

  tabsKPI() {
    let jsonTab = [

        {
            tab: "Ingresos diarios",
            id: "tab-ingresos-diarios",
            fn: " ingresosDiarios.lsIngresosDiarios()",
            active: true, // indica q pestaña se activara por defecto,

            contenedor: [
                {
                    id: "filterIngresosDiarios",
                    class: "col-12 mb-3",
                },
                {
                    id: "contentIngresosDiarios",
                    class: "col-12 ",
                },
            ],
        },



      {
        tab: "Total de ingresos",
        id: "tab-total-ingresos",
        fn: "app.lsTotalIngresos()",

        contenedor: [
          {
            id: "filterBarTotalIngresos",
            class: "col-12 ",
          },

          {
            id: "contentTotalIngresos",
            class: "col-12",
          },
        ],
      },

   
      {
        tab: "Promedios diarios",
        id: "tab-promedios-diarios",
        fn: "promediosDiarios.lsPromedios()",

        contenedor: [
          {
            id: "filterPromediosDiarios",
            class: "col-12 mb-3",
          },
          {
            id: "contentPromediosDiarios",
            class: "col-12 ",
          },
        ],
      },

      {
        tab: "Promedios acomulados",
        id: "tab-promedios-acomulados",
        fn: "promediosAcomulados.lsPromediosAcomulados()",

        contenedor: [
          {
            id: "filterPromediosAcomulados",
            class: "col-12  mb-1",
          },
          {
            id: "contentPromediosAcomulados",
            class: "col-12 ",
          },
        ],
      },
    ];

    this.createTabs({
      data: jsonTab,
      id: "contentData",
    });
  }

  filterBarTotalIngresos() {
    let json = [
      {
        opc: "select",

        data: [
            {  id: "PromediosDiarios", valor: "Promedios diarios" },
            {  id: "ComparativaMensual", valor: "Comparativa Mensual - Ingresos" },
            {  id: "ComparativaMensualPromedios", valor: "Comparativa Mensual - Promedios",},
        ],

        onchange: "app.lsTotalIngresos()",

        id: "concepto",
        class: "col-4 col-sm-4",
        lbl: "Buscar por:",
      },
    ];

    $("#filterBarTotalIngresos").content_json_form({ data: json, type: "" });
  }

  lsTotalIngresos() {

    this.createTable({
      idFilterBar: "filterBar",
      parent: "contentTotalIngresos",

      data: {
        opc        : $("#concepto").val(),
        mesCompleto: $("#Mes option:selected").text(),
      },

      conf:{
        datatable:false,
        //   fn_datatable:'data_fixed_table',
      },
    
      attr: {
        right    : [2, 3, 4],
        center   : [5],
        // color_col: [1],
        color_th : "bg-primary",

        color_group: "bg-disabled2",

        class    : 'mt-2 table table-bordered table-striped table-sm text-uppercase',
        f_size   : 14,
        extends: true
      },
      
    });
  }



  // Options:

  createTabs(options) {
    var opts = {
      data: [{ tab: "tabs", id: "tabs", active: true }],
      id: "ContentData",

      ...options,
    };

    $("#" + opts.id).simple_json_tab({ data: opts.data });
  }

  createFilterBar(options) {
    let filter = [
      {
        opc: "select",
        data: App.udn,
        id: "UDN",
        class: "col-6 col-sm-3",
        lbl: "UDN:",
        onchange:'setTemplates()'
      },

      {
        opc: "select",
        id: "Anio",
        class: "col-6 col-sm-3",
        lbl: "Año",
        data: [
          { id: 2025, valor: 2025 },
          { id: 2024, valor: 2024 },
          { id: 2023, valor: 2023 },
          { id: 2022, valor: 2022 },
          { id: 2021, valor: 2021 },
        ],
      },

      {
        opc: "select",
        id: "Mes",
        class: "col-6 col-sm-3",
        fn: 'app.initComponents()',
        lbl: "Mes",
        data: [
          { id: 1, valor: "Enero" },
          { id: 2, valor: "Febrero" },
          { id: 3, valor: "Marzo" },
          { id: 4, valor: "Abril" },
          { id: 5, valor: "Mayo" },
          { id: 6, valor: "Junio" },
          { id: 7, valor: "Julio" },
          { id: 8, valor: "Agosto" },
          { id: 9, valor: "Septiembre" },
          { id: 10, valor: "Octubre" },
          { id: 11, valor: "Noviembre" },
          { id: 12, valor: "Diciembre" },
        ],
      },
    //   {
    //     opc: "btn",
    //     id: "btnBuscar",
    //     text: "Buscar",
    //       fn: 'app.lsTotalIngresos()',
    //     class: "col-6 col-sm-3",
    //   },
    ];

    $("#filterBar").content_json_form({ data: filter, type: "" });
    $("#Mes").val("11");
  }


}

// Operations.
function setTemplates(){
    const UDNs = $('#UDN').val();

    if(UDNs == 6 ){
        app.tabsKPIFogaza();
        afluencia.render();
    }else{
        // app.tabsKPI();
    
    }
}
