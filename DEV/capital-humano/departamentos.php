<div class="row col-12">
    <form id="formDpto" novalidate class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 justify-content-end mb-3">
        <div class="col mb-3">
            <label for="cbUDNDepto">UDN</label>
            <select class="form-select cbUDN" name="udn" id="cbUDNDepto"></select>
        </div>
        <div class="col mb-3">
            <label for="departamento">Departamento</label>
            <input type="text" class="form-control" name="departamento" id="departamento" placeholder="Nuevo departamento" required>
        </div>
        <div class="col mb-3">
            <label for=""> </label>
            <button type="submit" id="btnDepto" class="btn btn-primary col-12"><i class="icon-plus"></i> Departamentos</button>
        </div>
    </form>
    <hr>
    <div class="col-md-12" id="tbDatosDpto"></div>
</div>
<script src='src/js/departamentos.js?t=<?php echo time();?>'></script>