<div class="row col-12">
    <form novalidate class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 mb-3" id="formPuesto">
        <div class="col mb-3">
            <label for="cbUDN">UDN</label>
            <select class="form-select cbUDN" name="udn" id="cbUDNPuestos"></select>
        </div>
        <div class="col mb-3">
            <label for="cbDepartamento">Departamento</label>
            <select class="form-select" name="departamento" id="cbDepartamento"></select>
        </div>
        <div class="col mb-3">
            <label for="iptPuesto">Puesto</label>
            <input type="text" class="form-control" name="puesto" id="puesto" placeholder="Nuevo puesto" required>
        </div>
        <div class="col mb-3">
            <label for=""> </label>
            <button type="submit" class="btn btn-primary col-12">Crear puesto</button>
        </div>
    </form>
    <hr>
    <div class="col-md-12" id="tbDatosPuestos">
        <table class="table table-hover table-sm">
            <thead>
                <tr>
                    <th>Titulo 1</th>
                    <th>opciones</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Celda 1</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-primary"
                            onClick="updateModal(1,'Celda 1');">
                            <i class="icon-pencil"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" estado="1" id="btnStatus1"
                            onClick="toggleStatus(1)">
                            <i class="icon-toggle-on"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<script src='src/js/puestos.js?t=<?php echo time(); ?>'></script>