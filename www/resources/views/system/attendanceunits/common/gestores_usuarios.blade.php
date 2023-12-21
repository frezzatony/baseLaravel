<textarea id="stored_managers_users" class="d-none">
    {{ !empty($attendance_unit->managers_users) ? $attendance_unit->managers_users->toJson() : '' }}
</textarea>

<div class="d-flex justify-content-end m-0 mb-1 sticky-top bg-white">
    <button type="button" class="btn btn-secondary fs-sm px-1 py-0 btn-add-action">
        <i class="ph-plus fs-sm"></i>Adicionar novo usuário
    </button>
</div>
<table id="tbl-appendgrid-attendanceunits-managers-users" name="managers" class="table-scroll border hb-300"></table>
