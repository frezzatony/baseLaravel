<textarea id="stored_actions" class="d-none">
    {{ !empty($profile->actions) ? $profile->actions->toJson() : '' }}
</textarea>
<div class="row p-0 m-0 mb-1">
    <div class="col-md-9 py-0 px-1">
        <div class="list-group ">
            <div class="list-group-item bg-light fw-semibold fs-sm p-1 ">Módulo</div>
            <div class="col-md-24 p-0 m-0 overflow-auto listgroup-modules" style="height: 50vh;">
                <label class="list-group-item list-group-item-action fs-sm p-1 text-break label-module-template d-none">
                    <input type="radio" name="privileges-module" class="me-1 list-group-radio-label">
                </label>
            </div>
        </div>
    </div>
    <div class="col-md-15 py-0 px-1 module-routines">
        <div class="list-group list-group-routines-template d-none">
            <div class="list-group-item bg-light fw-semibold fs-sm p-1">Rotinas e Ações</div>
            <div class="list-group-item p-1" style="height: 50vh;">
                <div class="tree-checkbox-hierarchical p-0">
                    <div class="d-none fs-sm p-2 alert alert-purple">
                        <i class="ph-warning-circle"></i>
                        O módulo não possui rotinas e/ou ações vinculadas.
                    </div>
                    <ul class="mb-0">
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
