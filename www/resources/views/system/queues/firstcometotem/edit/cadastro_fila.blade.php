<div class="row p-0 m-0 mb-1">
    <div class="col-md-4 py-0 px-1">
        <label for="show_item_id" class="form-label fw-semibold fs-sm m-0">Código</label>
        <input type="text" id="show_item_id" class="form-control px-1 pt1 pb1 fs-sm" value="{{ $queue->id ?? null }}" readonly>
    </div>
    <div class="col-md-5 py-0 px-1">
        <label for="is_active" class="form-label fw-semibold fs-sm m-0">Ativa<span class="text-danger">*</span></label>
        <select id="is_active" name="is_active" class="form-select px-1 pt1 pb1 fs-sm">
            <option value="t" {{ $queue->is_active ?? true ? 'selected' : '' }}>SIM</option>
            <option value="f" {{ !($queue->is_active ?? true) ? 'selected' : '' }}>NÃO</option>
        </select>
    </div>
    <div class="col-md-15 py-0 px-1">
        <label for="type" class="form-label fw-semibold fs-sm m-0">Tipo</label>
        <input type="text" id="type" class="form-control px-1 pt1 pb1 fs-sm" readonly value="{{ \App\Enums\Queues\Type::FIRST_COME_TOTEM['label'] }}">
    </div>
</div>
<div class="row p-0 m-0 mb-1">
    <div class="col-md-12 py-0 px-1">
        <label for="description" class="form-label fw-semibold fs-sm m-0">Descrição<span class="text-danger">*</span></label>
        <input type="text" id="description" name="description" class="form-control px-1 pt1 pb1 fs-sm uppercase" value="{{ $queue->description ?? null }}">
    </div>
    <div class="col-md-12 py-0 px-1">
        <label for="attendance_units_id" class="form-label fw-semibold fs-sm m-0">Unidade de Atendimento<span class="text-danger">*</span></label>
        <select id="attendance_units_id" name="attendance_units_id" class="form-select px-1 pt1 pb1 fs-sm">
            <option value=""></option>
            @foreach ($attendance_units as $attendanceUnit)
                <option value="{{ $attendanceUnit->id }}" {{ $attendanceUnit->id == ($queue->attendance_units_id ?? null) ? 'selected' : '' }}>{{ \Str::upper($attendanceUnit->name) }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="row p-0 m-0 mb-1">
    <div class="col-md-8 py-0 px-1">
        <label for="point_name" class="form-label fw-semibold fs-sm m-0">Nome Ponto de Atendimento<span class="text-danger">*</span></label>
        <input type="text" id="point_name" name="point_name" class="form-control px-1 pt1 pb1 fs-sm uppercase" value="{{ $queue->point_name ?? null }}">
    </div>
    <div class="col-md-8 py-0 px-1">
        <label for="point_quantity" class="form-label fw-semibold fs-sm m-0">Qtd. Pontos de Atendimento<span class="text-danger">*</span></label>
        <input type="number" step="1" min="0" id="point_quantity" name="point_quantity" class="form-control px-1 pt1 pb1 fs-sm" value="{{ $queue->point_quantity ?? null }}">
    </div>
</div>
<div class="row p-0 m-0 mb-1">
    <div class="col-md-8 py-0 px-1">
        <label for="ticket_prefix" class="form-label fw-semibold fs-sm m-0">Prefixo em Tickets</label>
        <input type="text" id="ticket_prefix" name="ticket_prefix" class="form-control px-1 pt1 pb1 fs-sm uppercase" value="{{ $queue->ticket_prefix ?? null }}">
    </div>
    <div class="col-md-8 py-0 px-1">
        <label for="max_daily_tickets" class="form-label fw-semibold fs-sm m-0">Qtd. Máx. Tickets Diários</label>
        <input type="number" step="1" min="0" id="max_daily_tickets" name="max_daily_tickets" class="form-control px-1 pt1 pb1 fs-sm " value="{{ $queue->max_daily_tickets ?? null }}">
    </div>
</div>
