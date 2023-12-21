@extends('layouts.system.' . Request::get('_layout'))

@section('content')
    <form id="form-holiday" autcomplete="off">
        @csrf
        <input type="hidden" name="id" value="">
        <div class="row m-0 mt-3">
            <div class="col-md-5 d-flex justify-content-end px-0 py-1 m-0">
                <label for="show_item_id" class="form-label fw-semibold fs-sm d-block">Código:<span class="text-danger">*</span></label>
            </div>
            <div class="col-sm-6 px-1">
                <input type="text" id="show_item_id" class="form-control px-1 pt1 pb1 fs-sm" readonly>
            </div>
        </div>
        <div class="row m-0">
            <div class="col-md-5 d-flex justify-content-end px-0 py-1 m-0">
                <label for="description" class="form-label fw-semibold fs-sm d-block">Descrição:<span class="text-danger">*</span></label>
            </div>
            <div class="col-sm-18 px-1">
                <input type="text" id="description" name="description" class="form-control px-1 pt1 pb1 fs-sm uppercase">
            </div>
        </div>
        <div class="row m-0">
            <div class="col-md-5 d-flex justify-content-end px-0 py-1 m-0">
                <label for="annual" class="form-label fw-semibold fs-sm d-block">Anual:</label>
            </div>
            <div class="col-sm-18 p-0 m-0 ">
                <span class="d-inline-block pa1">
                    &nbsp;<input type="checkbox" id="annual" name="annual" value="t">
                </span>
                <span class="d-inline-block p-0 align-top">
                    <i class="ph-question fs-6 text-purple" data-bs-popup="popover" data-bs-trigger="hover" data-bs-content="Informa se o feriado/ponto facultativo se repete todos os anos."></i></button>
                </span>
            </div>
        </div>
        <div class="row m-0">
            <div class="col-md-5 d-flex justify-content-end px-0 py-1 m-0">
                <label for="date" class="form-label fw-semibold fs-sm d-block">Data:<span class="text-danger">*</span></label>
            </div>
            <div class="col-sm-18 px-1">
                <input type="date" id="date" name="date" class="form-control col-md-10 px-1 pt1 pb1 fs-sm ">
            </div>
        </div>
        <div class="row m-0">
            <div class="col-md-5 d-flex justify-content-end px-0 py-1 m-0">
                <label for="type" class="form-label fw-semibold fs-sm d-block">Tipo:<span class="text-danger">*</span></label>
            </div>
            <div class="col-sm-18 px-1">
                <select name="type" id="type" class="form-select col-md-10 px-1 pt1 pb1 fs-sm">
                    @foreach (\App\Enums\HolidayType::asArray() as $key => $holidayType)
                        <option value="{{ $key }}">{{ $holidayType }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row m-0">
            <div class="col-md-5 d-flex justify-content-end px-0 py-1 m-0">
                <label for="optional" class="form-label fw-semibold fs-sm d-block">Facultativo:</label>
            </div>
            <div class="col-sm-18 p-0 m-0">
                <div class="d-inline-block pa1">
                    &nbsp;<input type="checkbox" id="optional" name="optional" value="t">
                </div>
            </div>
        </div>
        <div class="row m-0">
            <div class="col-md-5 d-flex justify-content-end px-0 py-1 m-0">
                <label for="time_start" class="form-label fw-semibold fs-sm d-block">Hora Início:</label>
            </div>
            <div class="col-sm-8 px-1">
                <input type="time" id="time_start" name="time_start" class="form-control px-1 pt1 pb1 fs-sm" value="00:00:00">
            </div>
        </div>
        <div class="row m-0">
            <div class="col-md-5 d-flex justify-content-end px-0 py-1 m-0">
                <label for="time_end" class="form-label fw-semibold fs-sm d-block">Hora Final:</label>
            </div>
            <div class="col-sm-8 px-1">
                <input type="time" id="time_end" name="time_end" class="form-control px-1 pt1 pb1 fs-sm" value="00:00:00">
            </div>
        </div>
    </form>
@endsection

@include('layouts.common.modal.footer_buttons_crud')


@section('js-files')
@endsection
