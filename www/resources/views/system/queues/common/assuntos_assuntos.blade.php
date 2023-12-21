<textarea id="stored_matters" class="d-none">
    {{ !empty($queue->matters) ? $queue->matters->toJson() : '' }}
</textarea>
<div class="row">
    <div class="col-md-24 d-flex justify-content-end m-0 mb-1 sticky-top bg-white">
        <button type="button" class="btn btn-secondary fs-sm px-1 py-0 btn-add-matter">
            <i class="ph-plus fs-sm"></i>Adicionar novo assunto
        </button>
    </div>
</div>
<div class="row p-0 m-0 mb-1">
    <div class="col-md-10 py-0 px-1">
        <div class="list-group">
            <div class="list-group-item bg-light fw-semibold fs-sm p-1 ">Assuntos</div>
            <div class="col-md-24 p-0 m-0 overflow-auto listgroup-matters" style="height: 50vh;">
                <label class="list-group-item empty-matters fs-sm p-1 text-break overflow-hidden">
                    <div class="fs-sm p-2 alert alert-danger m-1">
                        <i class="ph-warning-circle fs-sm align-middle"></i> Não há assuntos vinculados.
                    </div>
                </label>
                <label class="list-group-item list-group-item-attendant fs-sm p-1 text-break label-attendant-template overflow-hidden d-none">
                    <input type="hidden">
                    <div class="row">
                        <div class="col-md-2 px-2 me-2 align-middle">
                            <input type="radio" class="list-group-radio-label align-bottom">
                        </div>
                        <div class="col-md-21 m-0 p-0 pe-2">
                            <input type="text" class="form-control px-1 pt1 pb1 fs-sm pe-3 uppercase">
                            <div class="position-absolute end-0 top-0 pt-4 translate-middle-y me-2">
                                <button type="button" class="btn btn-sm btn-icon pa1 fs-sm text-danger btn-remove-matter" data-bs-popup="tooltip" title="Remover assunto" data-bs-placement="top">
                                    <i class="ph-x ph-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </label>
            </div>
        </div>
    </div>
    <div class="col-md-14 py-0 px-1 matter-attendants">
        <div class="list-group list-group-matter-attendants-empty ">
            <div class="list-group-item bg-light fw-semibold fs-sm p-1">Atendentes</div>
            <div class="list-group-item p-1" style="height: 50vh;">
                <div class="fs-sm p-2 alert alert-purple">
                    <i class="ph-chat-centered-dots fs-sm align-middle"></i> Selecione o assunto para editar os atendentes.
                </div>
            </div>
        </div>
        <div class="list-group list-group-matter-attendants-template d-none">
            <div class="list-group-item bg-light fw-semibold fs-sm p-1">Atendentes</div>
            <div class="list-group-item empty d-none p-1" style="height: 50vh;">
                <div class="fs-sm p-2 alert alert-purple">
                    <i class="ph-chat-centered-dots fs-sm align-middle"></i> Não há usuários atendentes informados.
                </div>
            </div>
            <div class="list-group-item users d-none p-1" style="height: 50vh;">
                <div class="tree-checkbox-hierarchical p-2">
                    <ul class="mb-0">
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
