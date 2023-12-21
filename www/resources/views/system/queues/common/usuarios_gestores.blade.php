<textarea id="stored_managers" class="d-none">
    {{ !empty($queue->users_managers) ? $queue->users_managers->toJson() : '' }}
</textarea>

<div class="d-flex justify-content-end m-0 mb-1 sticky-top bg-white">
    <button type="button" class="btn btn-secondary fs-sm px-1 py-0 btn-add-manager">
        <i class="ph-plus fs-sm"></i>Adicionar novo gestor
    </button>
</div>
<table id="tbl-appendgrid-queues-managers" data-name="managers" class="table-scroll border hb-300"></table>
