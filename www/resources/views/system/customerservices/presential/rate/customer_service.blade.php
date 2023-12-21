<div class="row p-0 m-0 mb-1">
    <div class="col-sm-4 col-md-4 py-0 px-1">
        <label for="show_item_id" class="form-label fw-semibold fs-sm m-0">Código:</label>
        <input type="text" id="show_item_id" name="show_item_id" class="form-control px-1 pt1 pb1 fs-sm" value="{{ $customer_service->customer_service_id }}" readonly>
    </div>
    <div class="col-sm-10 col-md-6 py-0 px-1">
        <input type="hidden" id="status" value="{{ $customer_service->status }}">
        <label for="show_status" class="form-label fw-semibold fs-sm m-0">Situação</label>
        <input type="text" id="stashow_statustus" name="show_status" class="form-control px-1 pt1 pb1 fs-sm"
            value="{{ \App\Enums\CustomerServices\CustomerServiceStatus::fromKey(\App\Helpers\StringHelper::upper($customer_service->status))->value['label'] }}" readonly>
    </div>
</div>
<div class="row p-0 m-0 mb-1">
    <div class="col-sm-10 col-md-10 py-0 px-1">
        <label for="show_attendance_unit" class="form-label fw-semibold fs-sm m-0">Unidade de Atendimento</label>
        <input type="text" id="show_attendance_unit" class="form-control px-1 pt1 pb1 fs-sm" value="{{ $customer_service->attendance_unit_name }}" readonly>
    </div>
    <div class="col-sm-10 col-md-10 py-0 px-1">
        <label for="show_queue" class="form-label fw-semibold fs-sm m-0">Unidade de Atendimento</label>
        <input type="text" id="show_queue" class="form-control px-1 pt1 pb1 fs-sm" value="{{ $customer_service->queue_description }}" readonly>
    </div>
</div>
<div class="row p-0 m-0 mb-1">
    <div class="col-sm-10 col-md-10 py-0 px-1">
        <label for="show_ticket" class="form-label fw-semibold fs-sm m-0">Ticket</label>
        <input type="text" id="show_attendanshow_ticketce_unit" class="form-control px-1 pt1 pb1 fs-sm"
            value="{{ $customer_service->book_ticket ? $customer_service->ticket_prefix . $customer_service->book_ticket : 'INICIADO SEM ENTRADA EM FILA' }}" readonly>
    </div>
    <div class="col-sm-10 col-md-10 py-0 px-1">
        <label for="show_queue" class="form-label fw-semibold fs-sm m-0">Assunto</label>
        <input type="text" id="show_queue" class="form-control px-1 pt1 pb1 fs-sm"
            value="{{ $customer_service->book_ticket ? \App\Helpers\StringHelper::upper($customer_service->matter_description) : 'INICIADO SEM ENTRADA EM FILA' }}" readonly>
    </div>
</div>
<div class="row p-0 m-0 mb-1">
    <div class="col-sm-10 col-md-10 py-0 px-1">
        <input type="hidden" id="created_at" value="{{ $customer_service->created_at }}">
        <input type="hidden" id="updated_at" value="{{ $customer_service->updated_at }}">
        <label for="show_begin_at" class="form-label fw-semibold fs-sm m-0">Início em</label>
        <input type="text" id="begin_at" name="begin_at" class="form-control px-1 pt1 pb1 fs-sm"
            value="{{ \Carbon\Carbon::parse($customer_service->created_at)->format('d/m/Y') }} às {{ \Carbon\Carbon::parse($customer_service->created_at)->format('H:i:s') }}h" readonly>
    </div>
    <div class="col-sm-10 col-md-10 py-0 px-1">
        <label for="completed_at" class="form-label fw-semibold fs-sm m-0">Conclusão do atendimento</label>
        <input type="text" id="completed_at" name="completed_at" class="form-control px-1 pt1 pb1 fs-sm"
            value="{{ \Carbon\Carbon::parse($customer_service->completed_at)->format('d/m/Y') }} às {{ \Carbon\Carbon::parse($customer_service->completed_at)->format('H:i:s') }}h" readonly>
    </div>
</div>
