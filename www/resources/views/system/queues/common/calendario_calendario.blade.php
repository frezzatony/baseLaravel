<textarea id="stored_calendar" class="d-none">
    {{ !empty($queue->calendar) ? $queue->calendar->toJson() : '' }}
</textarea>
<div class="row">
    <div class="col-md-24 d-flex justify-content-end m-0 mb-1 sticky-top bg-white">
        <button type="button" class="btn btn-secondary fs-sm px-1 py-0 btn-add-calendar-date">
            <i class="ph-plus fs-sm"></i>Adicionar nova data
        </button>
    </div>
</div>
<div class="row p-0 m-0 mb-1">
    <div class="col-md-10 py-0 px-1">
        <div class="list-group ">
            <div class="list-group-item bg-light fw-semibold fs-sm p-1 ">Datas</div>
            <div class="col-md-24 p-0 m-0 overflow-auto listgroup-calendar-dates" style="height: 50vh;">
                <label class="list-group-item empty-calendar-dates fs-sm p-1 text-break ">
                    <div class="fs-sm p-2 alert alert-purple m-1 ">
                        <i class="ph-warning-circle fs-sm align-middle"></i> Esta fila de atendimento não possui datas personalizadas.
                    </div>
                </label>
                <label class="list-group-item list-group-item-calendar-dates fs-sm p-1 text-break label-calendar-date-template overflow-hidden d-none">
                    <input type="hidden">
                    <div class="row">
                        <div class="col-md-2 px-2 me-2 align-middle">
                            <input type="radio" class="list-group-radio-label align-bottom">
                        </div>
                        <div class="col-md-21 m-0 p-0 pe-2">
                            <input type="date" class="form-control px-1 pt1 pb1 fs-sm pe-3 uppercase" min="{{ date('Y-m-d') }}">
                            <div class="position-absolute end-0 top-0 pt-4 translate-middle-y me-2">
                                <button type="button" class="btn btn-sm btn-icon pa1 fs-sm text-danger btn-remove-date" data-bs-popup="tooltip" title="Remover data" data-bs-placement="top">
                                    <i class="ph-x ph-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </label>
            </div>
        </div>
    </div>
    <div class="col-md-14 py-0 px-1 calendar-dates-details">
        <div class="list-group list-group-calendar-dates-empty">
            <div class="list-group-item bg-light fw-semibold fs-sm p-1">Detalhes</div>
            <div class="list-group-item p-1" style="height: 50vh;">
                <div class="fs-sm p-2 alert alert-purple">
                    <i class="ph-chat-centered-dots fs-sm align-middle"></i> Selecione uma data para editar os detalhes.
                </div>
            </div>
        </div>
        <div class="list-group list-group-calendar-dates-datails-template d-none">
            <div class="list-group-item bg-light fw-semibold fs-sm p-1">Detalhes</div>
            <div class="list-group-item p-1 vh-50">
                <div class="row px-2">
                    <div class="col-md-14 py-0 px-1">
                        <label class="form-label fw-semibold fs-sm m-0">Haverá atendimento<span class="text-danger">*</span></label>
                        <select class="form-select px-1 pt1 pb1 fs-sm" data-id="availability">
                            <option value="t">SIM</option>
                            <option value="f">NÃO</option>
                        </select>
                    </div>
                    <div class="col-md-10 py-0 px-1">
                        <label class="form-label fw-semibold fs-sm m-0">Dia todo<span class="text-danger">*</span></label>
                        <select class="form-select px-1 pt1 pb1 fs-sm" data-id="full_day">
                            <option value="t">SIM</option>
                            <option value="f">NÃO</option>
                        </select>
                    </div>
                </div>
                <div class="row p-2">
                    <div class="col-md-24 py-0 px-1">
                        <label class="form-label fw-semibold fs-sm m-0">Motivo</label>
                        <input class="form-control px-1 pt1 pb1 fs-sm" data-id="reason">
                    </div>
                </div>
                <div class="row p-0 m-0 calendar-date-details-hours d-none">
                    <fieldset class="mb-3 shadow px-1">
                        <legend class="fs-sm fw-bold border-bottom">
                            <div class="col-md-12 p-0">
                                Horários
                            </div>
                            <div class="col-md-12 p-0 d-flex justify-content-end m-0 mb-1 sticky-top bg-white">
                                <button type="button" class="btn btn-secondary fs-sm px-1 py-0 btn-add-calendar-hour">
                                    <i class="ph-plus fs-sm"></i>Adicionar novo horário
                                </button>
                            </div>
                        </legend>
                        <table class="table-scroll border hb-300 tbl-calendar-date-details-hours"></table>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
</div>
