window.bodyModal = window.bodyModal || "";
window.nameColaborador = window.nameColaborador || "";
window.idColaborador = window.idColaborador || "";

$(document).ready(function () {
  if (localStorage.getItem("colaborador")) {
    nameColaborador = localStorage.getItem("colaborador");
    idColaborador = localStorage.getItem("idColaborador");
    $("#titleCredito").html(nameColaborador.toUpperCase());
  }

  prioridad = [
    {
      responsivePriority: 1,
      targets: 0,
    },
  ];
  dataTable_responsive("#tbCreditos", prioridad);

  $("#btnNuevoCredito").on("click", () => {
    modalAgregarFormCredito();
  });
});

function toggleStatus(id) {
  const BTN = $("#btnStatus" + id);
  const ESTADO = BTN.attr("estado");

  let estado = 0;
  let iconToggle = '<i class="icon-toggle-off"></i>';
  let question = "¿DESEA DESACTIVARLO?";
  if (ESTADO == 0) {
    estado = 1;
    iconToggle = '<i class="icon-toggle-on"></i>';
    question = "¿DESEA ACTIVARLO?";
  }

  swal_question(question).then((result) => {
    if (result.isConfirmed) {
      BTN.html(iconToggle);
      BTN.attr("estado", estado);
    }
  });
}

function modalAgregarFormCredito(id) {
  localStorage.setItem("idColaborador", id);
  title = "Nuevo crédito";
  bodyModal = bootbox.dialog({
    title: title,
    size: "large",
    centerVertical: true,
    message: `
          <div class="row mb-3">
              <div class="col-12 col-sm-6 mb-3">
                    <label class="form-label fw-bold">No. Crédito</label>
                    <div class="input-group">
                      <span class="input-group-text" id="gv-no-credito"><i class="icon-hash-1"></i></span>
                      <input type="text" class="form-control" id="iptNoCredito"
                          placeholder="0000000000" aria-label="Género"
                          aria-describedby="gv-no-credito" onkeyup="validacionesFormCredito()">
                    </div>
                    <span class="form-text text-danger hide">
                        <i class="icon-warning-1"></i>
                        El campo es requerido.
                    </span>
              </div>
              <div class="col-12 col-sm-6 mb-3">
                    <label class="form-label fw-bold">Alta de crédito</label>
                    <input type="date" class="form-control" id="iptAltaCredito" placeholder="0000000000" aria-label="Género"
                        aria-describedby="gv-no-credito" onkeyup="validacionesFormCredito()">
                    <span class="form-text text-danger hide">
                        <i class="icon-warning-1"></i>
                        El campo es requerido.
                    </span>
              </div>
              <div class="col-12 col-sm-6 mb-3">
                    <label class="form-label fw-bold">Tipo de crédito</label>
                    <select class="form-select" id="cbTipoCredito" aria-label="Tipo de crédito" onkeyup="validacionesFormCredito()">
                        <option hidden value="0" selected>Selecciona una opción</option>
                        <option value="1">C. PERSONAL</option>
                        <option value="2">FONACOT</option>
                        <optgroup label="INFONAVIT">
                            <option value="1">CFM</option>
                            <option value="3">CFSM</option>
                            <option value="2">VSMG</option>
                        </optgroup>
                    </select>
                    <span class="form-text text-danger hide">
                        <i class="icon-warning-1"></i>
                        El campo es requerido.
                    </span>
              </div>
              <div class="col-12 col-sm-6 mb-3">
                    <label class="form-label fw-bold">Descuento quincenal</label>
                    <div class="input-group">
                      <span class="input-group-text" id="gv-no-credito"><i class="icon-dollar"></i></span>
                      <input type="text" class="form-control text-end" id="iptDescuentoQuincenal"
                          placeholder="0.00" aria-label="Género"
                          aria-describedby="gv-no-credito" onkeyup="validacionesFormCredito()">
                    </div>
                    <span class="form-text text-danger hide">
                        <i class="icon-warning-1"></i>
                        El campo es requerido.
                    </span>
              </div>
              <div class="col-12 col-sm-6 mb-3">
                    <label class="form-label fw-bold">Descuento mensual</label>
                    <div class="input-group">
                      <span class="input-group-text" id="gv-no-credito"><i class="icon-dollar"></i></span>
                      <input type="text" class="form-control text-end" id="iptDescuentoMensual"
                          placeholder="0.00" aria-label="Género"
                          aria-describedby="gv-no-credito" onkeyup="validacionesFormCredito()">
                    </div>
                    <span class="form-text text-danger hide">
                        <i class="icon-warning-1"></i>
                        El campo es requerido.
                    </span>
              </div>
              <div class="col-12 col-sm-6 mb-3">
                    <label class="form-label fw-bold">Total de pagos quincenales</label>
                    <div class="input-group">
                      <span class="input-group-text" id="gv-no-credito"><i class="icon-hash-1"></i></span>
                      <input type="text" class="form-control" id="iptTotalPagosQuincenales"
                          placeholder="0000000000" aria-label="Género"
                          aria-describedby="gv-no-credito" onkeyup="validacionesFormCredito()">
                    </div>
                    <span class="form-text text-danger hide">
                        <i class="icon-warning-1"></i>
                        El campo es requerido.
                    </span>
              </div>
          </div>
          <hr/>
          <div class="col-12 mb-3 d-flex justify-content-between">
              <button class="btn btn-primary col-5" onclick="guardarModalFormCredito()">Guardar</button>
              <button class="btn btn-outline-danger col-5 bootbox-close-button">Cancelar</button>
          </div> 
      `,
  });
}

function modalEditarFormCredito(id) {
  localStorage.setItem("idColaborador", id);
  title = "Editar crédito";
  bodyModal = bootbox.dialog({
    title: title,
    size: "large",
    centerVertical: true,
    message: `
          <div class="row mb-3">
              <div class="col-12 col-sm-6 mb-3">
                    <label class="form-label fw-bold">No. Crédito</label>
                    <div class="input-group">
                      <span class="input-group-text" id="gv-no-credito"><i class="icon-hash-1"></i></span>
                      <input type="text" class="form-control" id="iptNoCredito"
                          placeholder="0000000000" aria-label="Género"
                          aria-describedby="gv-no-credito" onkeyup="validacionesFormCredito()">
                    </div>
                    <span class="form-text text-danger hide">
                        <i class="icon-warning-1"></i>
                        El campo es requerido.
                    </span>
              </div>
              <div class="col-12 col-sm-6 mb-3">
                    <label class="form-label fw-bold">Alta de crédito</label>
                    <input type="date" class="form-control" id="iptAltaCredito" placeholder="0000000000" aria-label="Género"
                        aria-describedby="gv-no-credito" onkeyup="validacionesFormCredito()">
                    <span class="form-text text-danger hide">
                        <i class="icon-warning-1"></i>
                        El campo es requerido.
                    </span>
              </div>
              <div class="col-12 col-sm-6 mb-3">
                    <label class="form-label fw-bold">Tipo de crédito</label>
                    <select class="form-select" id="cbTipoCredito" aria-label="Tipo de crédito" onkeyup="validacionesFormCredito()">
                        <option hidden value="0" selected>Selecciona una opción</option>
                        <option value="1">C. PERSONAL</option>
                        <option value="2">FONACOT</option>
                        <optgroup label="INFONAVIT">
                            <option value="1">CFM</option>
                            <option value="3">CFSM</option>
                            <option value="2">VSMG</option>
                        </optgroup>
                    </select>
                    <span class="form-text text-danger hide">
                        <i class="icon-warning-1"></i>
                        El campo es requerido.
                    </span>
              </div>
              <div class="col-12 col-sm-6 mb-3">
                    <label class="form-label fw-bold">Descuento quincenal</label>
                    <div class="input-group">
                      <span class="input-group-text" id="gv-no-credito"><i class="icon-dollar"></i></span>
                      <input type="text" class="form-control" id="iptDescuentoQuincenal"
                          placeholder="0000000000" aria-label="Género"
                          aria-describedby="gv-no-credito" onkeyup="validacionesFormCredito()">
                    </div>
                    <span class="form-text text-danger hide">
                        <i class="icon-warning-1"></i>
                        El campo es requerido.
                    </span>
              </div>
              <div class="col-12 col-sm-6 mb-3">
                    <label class="form-label fw-bold">Descuento mensual</label>
                    <div class="input-group">
                      <span class="input-group-text" id="gv-no-credito"><i class="icon-dollar"></i></span>
                      <input type="text" class="form-control" id="iptDescuentoMensual"
                          placeholder="0000000000" aria-label="Género"
                          aria-describedby="gv-no-credito" onkeyup="validacionesFormCredito()">
                    </div>
                    <span class="form-text text-danger hide">
                        <i class="icon-warning-1"></i>
                        El campo es requerido.
                    </span>
              </div>
              <div class="col-12 col-sm-6 mb-3">
                    <label class="form-label fw-bold">Total de pagos quincenales</label>
                    <div class="input-group">
                      <span class="input-group-text" id="gv-no-credito"><i class="icon-hash-1"></i></span>
                      <input type="number" class="form-control" id="iptTotalPagosQuincenales"
                          placeholder="0000000000" aria-label="Género"
                          aria-describedby="gv-no-credito" onkeyup="validacionesFormCredito()">
                    </div>
                    <span class="form-text text-danger hide">
                        <i class="icon-warning-1"></i>
                        El campo es requerido.
                    </span>
              </div>
          </div>
          <hr/>
          <div class="col-12 mb-3 d-flex justify-content-between">
              <button class="btn btn-primary col-5" onclick="guardarModalFormCredito()">Guardar</button>
              <button class="btn btn-outline-danger col-5 bootbox-close-button">Cancelar</button>
          </div> 
      `,
  });
}

function guardarModalFormCredito() {
  let NOCREDITO = $("#iptNoCredito");
  let ALTA = $("#iptAltaCredito");
  let TIPO = $("#cbTipoCredito");
  let DESCQUINCENAL = $("#iptDescuentoQuincenal");
  let DESCMENSUAL = $("#iptDescuentoMensual");
  let TOTALQUINCENAL = $("#iptTotalPagosQuincenales");
  let COLABORADOR = idColaborador;
  let error = false;

  if (NOCREDITO.val() == "") {
    NOCREDITO.closest(".col-12").find(".text-danger").removeClass("hide");
    error = true;
  } else {
    NOCREDITO.closest(".col-12").find(".text-danger").addClass("hide");
  }
  if (ALTA.val() == "") {
    ALTA.next().removeClass("hide");
    error = true;
  } else {
    ALTA.next().addClass("hide");
  }
  if (TIPO.val() == 0) {
    TIPO.next().removeClass("hide");
    error = true;
  } else {
    TIPO.next().addClass("hide");
  }
  if (DESCQUINCENAL.val() == "") {
    DESCQUINCENAL.closest(".col-12").find(".text-danger").removeClass("hide");
    error = true;
  } else {
    DESCQUINCENAL.closest(".col-12").find(".text-danger").addClass("hide");
  }
  if (DESCMENSUAL.val() == "") {
    DESCMENSUAL.closest(".col-12").find(".text-danger").removeClass("hide");
    error = true;
  } else {
    DESCMENSUAL.closest(".col-12").find(".text-danger").addClass("hide");
  }
  if (TOTALQUINCENAL.val() == "") {
    TOTALQUINCENAL.closest(".col-12").find(".text-danger").removeClass("hide");
    error = true;
  } else {
    TOTALQUINCENAL.closest(".col-12").find(".text-danger").addClass("hide");
  }
}

function validacionesFormCredito() {
  let DESCQUINCENAL = $("#iptDescuentoQuincenal");
  let DESCMENSUAL = $("#iptDescuentoMensual");
  let TOTALQUINCENAL = $("#iptTotalPagosQuincenales");

  //   Validación para el campo de descuento quincenal
  DESCQUINCENAL.on("input", () => {
    let expReg = DESCQUINCENAL.val().replace(/[^\d.]/g, "");
    let decimal = expReg.split(".").length;
    if (decimal > 1) {
      let parts = expReg.split(".");
      expReg = parts[0] + "." + parts.slice(1).join("");
    }
    DESCQUINCENAL.val(expReg);

    let total = DESCQUINCENAL.val() * 2;
    DESCMENSUAL.val(total);

    if (DESCMENSUAL.val() != "") {
      DESCMENSUAL.attr("disabled", true);
    }
    if (DESCQUINCENAL.val() == "") {
      DESCMENSUAL.attr("disabled", false);
      DESCMENSUAL.val("");
    }
  });

  //   Validación para el campo de descuento mensual
  DESCMENSUAL.on("input", () => {
    let expReg = DESCMENSUAL.val().replace(/[^\d.]/g, "");
    let decimal = expReg.split(".").length;
    if (decimal > 1) {
      let parts = expReg.split(".");
      expReg = parts[0] + "." + parts.slice(1).join("");
    }
    DESCMENSUAL.val(expReg);

    let total = DESCMENSUAL.val() / 2;
    DESCQUINCENAL.val(total);

    if (DESCQUINCENAL.val() != "") {
      DESCQUINCENAL.attr("disabled", true);
    }
    if (DESCMENSUAL.val() == "") {
      DESCQUINCENAL.attr("disabled", false);
      DESCQUINCENAL.val("");
    }
  });

  //   Validación para el campo de total de pagos quincenales
  TOTALQUINCENAL.on("input", () => {
    let expReg = TOTALQUINCENAL.val().replace(/[^\d.]/g, "");
    let decimal = expReg.split(".").length;
    if (decimal > 1) {
      let parts = expReg.split(".");
      expReg = parts[0] + "." + parts.slice(1).join("");
    }
    TOTALQUINCENAL.val(expReg);
  });
}
