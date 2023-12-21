@extends('layouts.system.' . Request::get('_layout'))

@section('content')
    <form id="form-messages-category" autcomplete="off">
        @csrf
        <input type="hidden" name="id" value="{{ $category->id ?? null }}">
        <div class="row m-0 mt-3">
            <div class="col-md-5 d-flex justify-content-end px-0 py-1 m-0">
                <label for="show_item_id" class="form-label fw-semibold fs-sm d-block">Código:<span class="text-danger">*</span></label>
            </div>
            <div class="col-sm-6 px-1">
                <input type="text" id="show_item_id" class="form-control px-1 pt1 pb1 fs-sm" value="{{ $category->id ?? null }}" readonly>
            </div>
        </div>
        <div class="row m-0">
            <div class="col-md-5 d-flex justify-content-end px-0 py-1 m-0">
                <label for="is_active" class="form-label fw-semibold fs-sm m-0">Ativa:<span class="text-danger">*</span></label>
            </div>
            <div class="col-sm-6 px-1 pb-2">
                <select id="is_active" name="is_active" class="form-select px-1 pt1 pb1 fs-sm">
                    <option value="t" {{ $category->is_active ?? true ? 'selected' : '' }}>SIM</option>
                    <option value="f" {{ !($category->is_active ?? true) ? 'selected' : '' }}>NÃO</option>
                </select>
            </div>
        </div>
        <div class="row m-0">
            <div class="col-md-5 d-flex justify-content-end px-0 py-1 m-0">
                <label for="description" class="form-label fw-semibold fs-sm d-block">Nome:<span class="text-danger">*</span></label>
            </div>
            <div class="col-sm-18 px-1">
                <input type="text" id="description" name="description" class="form-control px-1 pt1 pb1 fs-sm uppercase" value="{{ $category->description ?? null }}">
            </div>
        </div>
    </form>
@endsection

@include('layouts.common.modal.footer_buttons_crud')

@section('js-files')
@endsection
