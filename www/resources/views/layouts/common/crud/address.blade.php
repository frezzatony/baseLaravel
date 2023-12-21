<?php $address = !empty($address) ? (array) $address : null; ?>
<div class="row p-0 m-0 mb-1">
    <div class="col-md-4 py-0 px-1">
        <label for="address_cep" class="form-label fw-semibold fs-sm m-0">CEP:{!! $address_required ?? false ? '<span class="text-danger">*</span>' : '' !!}</label>
        <input type="text" id="address_cep" name="{{ $input_prefix ?? '' }}address_cep" class="form-control px-1 pt1 pb1 fs-sm" value="{{ $address['cep'] ?? '' }}">
    </div>
    <div class="col-md-8 py-0 px-1">
        <label for="address_state" class="form-label fw-semibold fs-sm m-0">Estado:{!! $address_required ?? false ? '<span class="text-danger">*</span>' : '' !!}</label>
        <select id="address_state" name="{{ $input_prefix ?? '' }}address_state" class="form-select px-1 pt1 pb1 fs-sm">
            <option value=""></option>
            @foreach (App\Enums\States::asArray() as $state)
                <option value="{{ App\Helpers\StringHelper::upper($state) }}" {{ App\Helpers\StringHelper::upper($address['state'] ?? null) == App\Helpers\StringHelper::upper($state) ? 'selected' : '' }}>
                    {{ App\Helpers\StringHelper::upper($state) }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-12 py-0 px-1">
        <label for="address_city" class="form-label fw-semibold fs-sm m-0">Cidade:{!! $address_required ?? false ? '<span class="text-danger">*</span>' : '' !!}</label>
        <input type="text" id="address_city" name="{{ $input_prefix ?? '' }}address_city" class="form-control px-1 pt1 pb1 fs-sm uppercase" value="{{ $address['city'] ?? '' }}">
    </div>
</div>
<div class="row p-0 m-0 mb-1">
    <div class="col-md-9 py-0 px-1">
        <label for="address_neighborhood" class="form-label fw-semibold fs-sm m-0">Bairro/Localidade:{!! $address_required ?? false ? '<span class="text-danger">*</span>' : '' !!}</label>
        <input type="text" id="address_neighborhood" name="{{ $input_prefix ?? '' }}address_neighborhood" class="form-control px-1 pt1 pb1 fs-sm uppercase" value="{{ $address['neighborhood'] ?? '' }}">
    </div>
    <div class="col-md-15 py-0 px-1">
        <label for="address_street" class="form-label fw-semibold fs-sm m-0">Logradouro:{!! $address_required ?? false ? '<span class="text-danger">*</span>' : '' !!}</label>
        <input type="text" id="address_street" name="{{ $input_prefix ?? '' }}address_street" class="form-control px-1 pt1 pb1 fs-sm uppercase" value="{{ $address['street'] ?? '' }}">
    </div>
</div>
<div class="row p-0 m-0 mb-1">
    <div class="col-md-7 py-0 px-1">
        <label for="address_number" class="form-label fw-semibold fs-sm m-0">Número:</label>
        <input type="text" id="address_number" name="{{ $input_prefix ?? '' }}address_number" class="form-control px-1 pt1 pb1 fs-sm uppercase" value="{{ $address['number'] ?? '' }}">
    </div>
    <div class="col-md-17 py-0 px-1">
        <label for="address_complement" class="form-label fw-semibold fs-sm m-0">Complemento:</label>
        <input type="text" id="address_complement" name="{{ $input_prefix ?? '' }}address_complement" class="form-control px-1 pt1 pb1 fs-sm uppercase" value="{{ $address['complement'] ?? '' }}">
    </div>
</div>
<div class="row p-0 m-0 mb-1">
    <div class="col-md-6 py-0 px-1">
        <label for="address_regional_zone" class="form-label fw-semibold fs-sm m-0">Zona Regional:</label>
        <input type="text" id="address_regional_zone" name="{{ $input_prefix ?? '' }}address_regional_zone" class="form-control px-1 pt1 pb1 fs-sm uppercase" value="{{ $address['regional_zone'] ?? '' }}">
    </div>
    <div class="col-md-9 py-0 px-1">
        <label for="address_latitude" class="form-label fw-semibold fs-sm m-0">Latitude:</label>
        <input type="text" id="address_latitude" name="{{ $input_prefix ?? '' }}address_latitude" class="form-control px-1 pt1 pb1 fs-sm uppercase" value="{{ $address['latitude'] ?? '' }}">
    </div>
    <div class="col-md-9 py-0 px-1">
        <label for="address_longitude" class="form-label fw-semibold fs-sm m-0">Longitude:</label>
        <input type="text" id="address_longitude" name="{{ $input_prefix ?? '' }}address_longitude" class="form-control px-1 pt1 pb1 fs-sm uppercase" value="{{ $address['longitude'] ?? '' }}">
    </div>
</div>
