<div class="row p-0 m-0 mb-1">
    <div class="col-md-4 py-0 px-1">
        <label class="form-label fw-semibold fs-sm m-0">Código:</label>
        <input type="text" name="show_item_id" class="form-control px-1 pt1 pb1 fs-sm" readonly>
    </div>
    <div class="col-md-5 py-0 px-1">
        <label for="is_active" class="form-label fw-semibold fs-sm m-0">Ativo<span class="text-danger">*</span></label>
        <select id="is_active" name="is_active" class="form-select px-1 pt1 pb1 fs-sm">
            <option value="t">SIM</option>
            <option value="f">NÃO</option>
        </select>
    </div>
</div>
<div class="row p-0 m-0 mb-1">
    <div class="col-md-12 py-0 px-1">
        <label for="modules_id" class="form-label fw-semibold fs-sm m-0">Módulo<span class="text-danger">*</span></label>
        <select id="modules_id" name="modules_id" class="form-select px-1 pt1 pb1 fs-sm"></select>
    </div>
</div>
<div class="row p-0 m-0 mb-1">
    <div class="col-md-12 py-0 px-1">
        <label class="form-label fw-semibold fs-sm m-0">Nome:<span class="text-danger">*</span></label>
        <input type="text" id="name" name="name" class="form-control px-1 pt1 pb1 fs-sm uppercase">
    </div>
    <div class="col-md-7 py-0 px-1">
        <label class="form-label fw-semibold fs-sm m-0">Slug:<span class="text-danger">*</span></label>
        <input type="text" id="slug" name="slug" class="form-control px-1 pt1 pb1 fs-sm lowercase">
    </div>
</div>
