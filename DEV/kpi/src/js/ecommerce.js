window.ctrlecommerce = "ctrl/ctrl-ecommerce.php";

window.ctrlmercadotecnia = "ctrl/ctrl-mercadotecnia.php";
let kpi = {};
let udn = "";
let analisis = {};

$(function () {
  initComponents(ctrlmercadotecnia).then((data) => {
    kpi = new Modulo_busqueda(ctrlmercadotecnia, "");
    kpi.json_filter_bar = jsonBar();
    kpi.filterBar("filterBar");

    ecommerceDate();

    analisis = new kpis(ctrlecommerce, "");

    // Init components:
    analisis.initComponents();
  });
});

function jsonBar() {
  return [
    {
      opc: "input-group",
      id: "iptFecha",
      class: "col-3",
      lbl: "Rango de fecha",
      icon: "icon-calendar",
    },
    {
      opc: "btn",
      id: "btnBuscar",
      text: "Buscar",
      fn: "analisis.lsbusqueda()",
      class: "col-2",
    },
  ];
}

class kpis extends Complements {
  constructor(link, div_modulo) {
    super(link, div_modulo);
  }

  initComponents() {
    this.tabsKpi();
    this.lsEcommerce();
    
  }

  lsbusqueda(){
    this.lsEcommerce();
    this.lsEcommerceFz();
    this.lsCcvqt();
  }

  tabsKpi() {
    let jsonTab = [
      {
        tab: "Ecommerce Sonora's",
        id: "tab-ecommerce-sonoras",
        active: true, // indica q pestaña se activara por defecto
        fn: "analisis.lsEcommerce()",
        contenedor: [
          {
            id: "contentDataIngresos",
            class: "col-12",
          },
        ],
      },
      {
        tab: "Ecommerce Fogaza",
        id: "tab-ecommerce-fogaza",
        fn: "analisis.lsEcommerceFz()",
      },

      {
        tab: "CCVT QT",
        id: "tab-ccvt-qt",
        fn: "analisis.lsCcvqt()",
      },
    ];

    $("#contentData").simple_json_tab({ data: jsonTab });
  }

  lsEcommerce() {
    let iptFecha = ipt_date("iptFecha");
    this.dataSearchTable = {
      tipo: "text",
      opc: "lsEcommerce",
      fi: iptFecha.fi,
      ff: iptFecha.ff,
    };

    this.attr_table = {
      id: "tblEcommerceSonoras",
      center: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
    };

    this.searchTable({ id: "contentDataIngresos", datatable: true });

    // ajax.then((data) => {
    //     $("#contentDataIngresos").rpt_json_table2({
    //         data: data,
    //         f_size: "12",
    //     });
    // });
  }

  lsEcommerceFz() {
    let iptFecha = ipt_date("iptFecha");

    this._dataSearchTable = {
      tipo: "text",
      opc: "lsEcommerceFz",
      fi: iptFecha.fi,
      ff: iptFecha.ff,
    };
    this._attr_table = {
      id: "tblEcommerceFogaza",
    };
    this.searchTable({ id: "tab-ecommerce-fogaza", datatable: true });

    // ajax.then((data) => {
    //     $("#contentDataIngresos").rpt_json_table2({
    //         data: data,
    //         f_size: "12",
    //     });
    // });
  }

  lsCcvqt() {
    let iptFecha = ipt_date("iptFecha");

    this._dataSearchTable = {
      tipo: "text",
      opc: "lsCCVTQT",
      fi: iptFecha.fi,
      ff: iptFecha.ff,
    };

    this._attr_table = {
      id: "tblCcvqt",
      left: [1],
      center: [2],
    };
    
   this.searchTable({ id: "tab-ccvt-qt", datatable: true});

//     ajax.then((data) => {
//         $("#tab-ccvt-qt").rpt_json_table2({
//             data: data,
//             id: "tblCcvqt",
//             left: [1],
//             center: [2],
//         });

//         $("#tblCcvqt").simple_data_table_no('tblCcvqt', 10);
//   });
  }
}

function ecommerceDate() {
  $("#iptFecha").daterangepicker({
      startDate: moment().subtract(8, "days"),
      endDate: moment().subtract(1, "days"),
        showDropdowns: true,
        ranges: {
            "Últimos 7 días": [moment().subtract(8, "days"), moment().subtract(1, "days")],
            "Mes actual": [moment().startOf("month"), moment().subtract(1, "days")],
            "Mes anterior": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")],
            "Año actual": [moment().startOf("year"), moment().subtract(1, "days")],
            "Año anterior": [moment().subtract(1, "year").startOf("year"), moment().subtract(1, "year").endOf("year")],
        },
  });

  $("#iptFecha")
    .next("span")
    .on("click", () => {
      $("#iptDate").click();
    });
  // cbEcommerce(start, end);
}

function ipt_date(idFecha) {
  const fi = $("#" + idFecha)
    .data("daterangepicker")
    .startDate.format("YYYY-MM-DD");
  const ff = $("#" + idFecha)
    .data("daterangepicker")
    .endDate.format("YYYY-MM-DD");
  return { fi, ff };
}
