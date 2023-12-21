<div class="row p-0 m-0 mb-1">
    <div class="col-md-7 py-0 px-1">
        <input type="hidden" id="person_id" name="person_id">
        <label for="person_cpf" class="form-label fw-semibold fs-sm m-0">CPF</label>
        <input type="text" id="person_cpf" name="person_cpf" class="form-control px-1 pt1 pb1 fs-sm" placeholder="___.___.___-__" value="{{ $person->cpf_cnpj ?? null }}">
    </div>
</div>
<div class="row p-0 m-0 mb-1">
    <div class="col-md-16 py-0 px-1">
        <label for="person_name" class="form-label fw-semibold fs-sm m-0">Nome</label>
        <input type="text" id="person_name" name="person_name" class="form-control px-1 pt1 pb1 fs-sm uppercase" value="{{ $person->name ?? null }}">
    </div>
    <div class="col-md-16 py-0 px-1">
        <label for="person_social_name" class="form-label fw-semibold fs-sm m-0">Nome social</label>
        <input type="text" id="person_social_name" name="person_social_name" class="form-control px-1 pt1 pb1 fs-sm uppercase" value="{{ $person->social_name ?? null }}">
    </div>
</div>
