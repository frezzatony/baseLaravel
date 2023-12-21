<textarea id="stored_routines" class="d-none">
    {{ !empty($routine->routine_actions) ? $routine->routine_actions->toJson() : '' }}
</textarea>

<div class="d-flex justify-content-end m-0 mb-1 sticky-top bg-white">
    <button type="button" class="btn btn-secondary fs-sm px-1 py-0 btn-add-action">
        <i class="ph-plus fs-sm"></i>Adicionar nova ação
    </button>
</div>
<table id="tbl-appendgrid-routines-actions" name="actions" class="table-scroll border hb-300"></table>
