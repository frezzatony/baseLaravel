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
        <input type="text" id="type" class="form-control px-1 pt1 pb1 fs-sm" readonly value="{{ \App\Enums\Queues\Type::FIRST_COME_MANUAL['label'] }}">
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
    <div class="col-md-9 py-0 px-1">
        <label for="point_name" class="form-label fw-semibold fs-sm m-0">Nome Ponto de Atendimento<span class="text-danger">*</span></label>
        <input type="text" id="point_name" name="point_name" class="form-control px-1 pt1 pb1 fs-sm uppercase" value="{{ $queue->point_name ?? null }}">
    </div>
    <div class="col-md-9 py-0 px-1">
        <label for="point_quantity" class="form-label fw-semibold fs-sm m-0">Quantidade Pontos de Atendimento<span class="text-danger">*</span></label>
        <input type="number" step="1" min="0" id="point_quantity" name="point_quantity" class="form-control px-1 pt1 pb1 fs-sm" value="{{ $queue->point_quantity ?? null }}">
    </div>
</div>
<div class="row p-0 m-0 mb-1">
    <div class="col-md-9 py-0 px-1">
        <label for="ticket_withdrawal" class="form-label fw-semibold fs-sm m-0">Formato de Retirada de Senhas<span class="text-danger">*</span></label>
        <select id="ticket_withdrawal" name="ticket_withdrawal" class="form-select px-1 pt1 pb1 fs-sm">
            <option value=""></option>
            @foreach (\App\Enums\Queues\TicketWithdrawal::asArray() as $key => $ticketWithdrawal)
                <option value="{{ $ticketWithdrawal['value'] }}" {{ $ticketWithdrawal['value'] == ($queue->ticket_withdrawal ?? null) ? 'selected' : '' }}>{{ \Str::upper($ticketWithdrawal['label']) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-9 py-0 px-1">
        <label for="ticket_sequence" class="form-label fw-semibold fs-sm m-0">Sequência de Senhas<span class="text-danger">*</span></label>
        <select id="ticket_sequence" name="ticket_sequence" class="form-select px-1 pt1 pb1 fs-sm">
            <option value=""></option>
            @foreach (\App\Enums\Queues\TicketSequence::asArray() as $key => $ticketSequence)
                <option value="{{ $ticketSequence['value'] }}" {{ $ticketSequence['value'] == ($queue->ticket_sequence ?? null) ? 'selected' : '' }}>{{ \Str::upper($ticketSequence['label']) }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="row p-0 m-0 mb-1">
    <div class="col-md-9 py-0 px-1">
        <label for="ticket_reset" class="form-label fw-semibold fs-sm m-0">Reiniciar Senhas Fim Expediente<span class="text-danger">*</span></label>
        <select id="ticket_reset" name="ticket_reset" class="form-select px-1 pt1 pb1 fs-sm">
            <option value="t" {{ $queue->ticket_reset ?? false ? 'selected' : '' }}>SIM</option>
            <option value="f" {{ !($queue->ticket_reset ?? false) ? 'selected' : '' }}>NÃO</option>
        </select>
    </div>
    <div class="col-md-9 py-0 px-1">
        <label for="reset_tickets_counter" class="form-label fw-semibold fs-sm m-0">Reiniciar Senhas no Contador</label>
        <input type="number" step="1" min="0" id="reset_tickets_counter" name="reset_tickets_counter" class="form-control px-1 pt1 pb1 fs-sm" value="{{ $queue->reset_tickets_counter ?? null }}">
    </div>
</div>

<div class="row p-0 m-0 mb-1">
    <div class="col-md-9 py-0 px-1">
        <label for="max_daily_tickets" class="form-label fw-semibold fs-sm m-0">Qtd. Máx. Tickets Diários</label>
        <input type="number" step="1" min="0" id="max_daily_tickets" name="max_daily_tickets" class="form-control px-1 pt1 pb1 fs-sm " value="{{ $queue->max_daily_tickets ?? null }}">
    </div>
</div>
