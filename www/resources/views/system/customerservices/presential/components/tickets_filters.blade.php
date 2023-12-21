<div class="row align-items-stretch m-0 mb-1">
    <div class="col-md-6 py-0 px-1">
        <label for="tickets_matter" class="form-label fw-semibold fs-sm m-0">Assunto<span class="text-danger">*</span></label>
        <select id="tickets_matter" name="tickets_matter" class="form-select px-1 pt1 pb1 fs-sm"></select>
    </div>
    <div class="col-md-8 py-0 px-1">
        <label for="tickets_priority" class="form-label fw-semibold fs-sm m-0">Prioridade<span class="text-danger">*</span></label>
        <select id="tickets_priority" name="tickets_priority" class="form-select px-1 pt1 pb1 fs-sm"></select>
    </div>
    <div class="col-md-6 py-0 px-1">
        <label for="tickets_status" class="form-label fw-semibold fs-sm m-0">Situação<span class="text-danger">*</span></label>
        <select id="tickets_status" name="tickets_status" class="form-select px-1 pt1 pb1 fs-sm">
            <option value="all">TODAS</option>
            @foreach (App\Enums\Queues\BookStatus::asArray() as $status)
                <option value="{{ App\Helpers\StringHelper::lower($status['value']) }}">
                    {{ App\Helpers\StringHelper::upper($status['label']) }}
                </option>
            @endforeach
        </select>
    </div>
</div>
