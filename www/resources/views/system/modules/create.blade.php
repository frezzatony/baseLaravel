@extends('layouts.system.' . Request::get('_layout'))

@section('content')
    <form id="form-module" autcomplete="off">
        @csrf
        <input type="hidden" name="id" value="">
        <div class="d-lg-flex p-2 bg-light">
            <ul class="nav nav-tabs nav-tabs-vertical nav-tabs-vertical-start wmin-lg-200 mt-4 mb-lg-0 " role="tablist">
                <li class="nav-item bg-white" role="presentation">
                    <a href="#tab-module-cadastro" class="nav-link fs-sm p-1 m-0 active" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        Cadastro
                    </a>
                </li>
            </ul>

            <div class="tab-content flex-lg-fill">
                <div class="tab-pane fade active show" id="tab-module-cadastro" role="tabpanel">
                    <ul class="nav nav-tabs nav-tabs-highlight">
                        <li class="nav-item"><a href="tab-module-cadastro-cadastro" class="nav-link fs-sm p-1 m-0 active">Cadastro</a></li>
                    </ul>
                    <div class="tab-content flex-lg-fill">
                        <div class="tab-pane fade border-start border-end border-bottom active show" id="tab-module-cadastro-cadastro" role="tabpanel">
                            <div class="col-md-24 p-2 bg-white">
                                <div class="row p-0 m-0 mb-1">
                                    <div class="col-md-4 py-0 px-1">
                                        <label class="form-label fw-semibold fs-sm m-0">Código:</label>
                                        <input type="text" name="show_item_id" class="form-control px-1 pt1 pb1 fs-sm" readonly>
                                    </div>
                                    <div class="col-md-5 py-0 px-1">
                                        <label for="is_active" class="form-label fw-semibold fs-sm m-0">Ativo<span class="text-danger">*</span></label>
                                        <select id="is_active" name="is_active" class="form-select px-1 pt1 pb1 fs-sm">
                                            <option value="t">SIM</option>
                                            <option value="f">NÃO</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row p-0 m-0 mb-1">
                                    <div class="col-md-12 py-0 px-1">
                                        <label class="form-label fw-semibold fs-sm m-0">Nome:</label>
                                        <input type="text" id="name" name="name" class="form-control px-1 pt1 pb1 fs-sm uppercase">
                                    </div>
                                    <div class="col-md-7 py-0 px-1">
                                        <label class="form-label fw-semibold fs-sm m-0">Slug:</label>
                                        <input type="text" id="slug" name="slug" class="form-control px-1 pt1 pb1 fs-sm lowercase">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@include('layouts.common.modal.footer_buttons_crud')

@section('js-files')
@endsection
