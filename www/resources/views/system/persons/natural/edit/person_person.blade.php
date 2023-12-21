<div class="row p-0 m-0 mb-1">
    <div class="col-md-4 py-0 px-1">
        <label class="form-label fw-semibold fs-sm m-0">Código:</label>
        <input type="text" name="show_item_id" class="form-control px-1 pt1 pb1 fs-sm" value="{{ $person->id ?? null }}" readonly>
    </div>
    <div class="col-md-5 py-0 px-1">
        <label for="is_active" class="form-label fw-semibold fs-sm m-0">Ativa:<span class="text-danger">*</span></label>
        <select id="is_active" name="is_active" class="form-select px-1 pt1 pb1 fs-sm">
            <option value="t" {{ $person->is_active ?? true ? 'selected' : '' }}>SIM</option>
            <option value="f" {{ !($person->is_active ?? true) ? 'selected' : '' }}>NÃO</option>
        </select>
    </div>
</div>
<div class="row p-0 m-0 mb-1">
    <div class="col-md-7 py-0 px-1">
        <label for="cpf_cnpj" class="form-label fw-semibold fs-sm m-0">CPF:<span class="text-danger">*</span></label>
        <input type="text" id="cpf_cnpj" name="cpf_cnpj" class="form-control px-1 pt1 pb1 fs-sm" placeholder="___.___.___-__" value="{{ $person->cpf_cnpj ?? null }}">
    </div>
    <div class="col-md-7 py-0 px-1">
        <label for="birthdate" class="form-label fw-semibold fs-sm m-0">Data de Nascimento:</label>
        <input type="date" max="{{ date('Y-m-d') }}" id="birthdate" name="birthdate" class="form-control px-1 pt1 pb1 fs-sm" placeholder="________" value="{{ $person->cpf_cnpj ?? null }}">
    </div>
</div>
<div class="row p-0 m-0 mb-1">
    <div class="col-md-24 py-0 px-1">
        <label for="name" class="form-label fw-semibold fs-sm m-0">Nome:<span class="text-danger">*</span></label>
        <input type="text" id="name" name="name" class="form-control px-1 pt1 pb1 fs-sm uppercase" value="{{ $person->name ?? null }}">
    </div>
</div>
<div class="row p-0 m-0 mb-1">
    <div class="col-md-24 py-0 px-1">
        <label for="social_name" class="form-label fw-semibold fs-sm m-0">Nome Social:</label>
        <input type="text" id="social_name" name="social_name" class="form-control px-1 pt1 pb1 fs-sm uppercase" value="{{ $person->social_name ?? null }}">
    </div>
</div>
