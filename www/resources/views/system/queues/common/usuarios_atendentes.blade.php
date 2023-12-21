<textarea id="stored_attendants" class="d-none">
    {{ !empty($queue->users_attendants) ? $queue->users_attendants->toJson() : '' }}
</textarea>

<div class="d-flex justify-content-end m-0 mb-1 sticky-top bg-white">
    <button type="button" class="btn btn-secondary fs-sm px-1 py-0 btn-add-attendant">
        <i class="ph-plus fs-sm"></i>Adicionar novo atendente
    </button>
</div>
<table id="tbl-appendgrid-queues-attendants" data-name="attendants" class="table-scroll border hb-300"></table>
