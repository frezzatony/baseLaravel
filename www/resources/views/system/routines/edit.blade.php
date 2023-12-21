@extends('layouts.system.' . Request::get('_layout'))

@section('content')
    <form id="form-routine" autcomplete="off">
        @csrf
        <input type="hidden" name="id" value="{{ $routine->id }}">
        <div class="d-lg-flex p-2 bg-light">
            <ul class="nav nav-tabs nav-tabs-vertical nav-tabs-vertical-start wmin-lg-200 mt-4 mb-lg-0 " role="tablist">
                <li class="nav-item bg-white" role="presentation">
                    <a href="#tab-routine-cadastro" class="nav-link fs-sm p-1 m-0 active " data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        Cadastro
                    </a>
                </li>
                <li class="nav-item bg-white" role="presentation">
                    <a href="#tab-routine-acoes" class="nav-link fs-sm p-1 m-0 " data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        Ações
                    </a>
                </li>
            </ul>

            <div class="tab-content flex-lg-fill">
                <div class="tab-pane fade active show" id="tab-routine-cadastro" role="tabpanel">
                    <ul class="nav nav-tabs nav-tabs-highlight">
                        <li class="nav-item"><a href="tab-routine-cadastro-rotina" class="nav-link fs-sm p-1 m-0 active">Rotina</a></li>
                    </ul>
                    <div class="tab-content flex-lg-fill">
                        <div class="tab-pane fade border-start border-end border-bottom active show" id="tab-routine-cadastro-rotina" role="tabpanel">
                            <div class="col-md-24 p-2 bg-white border">
                                @include('system.routines.edit.cadastro_rotina')
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="tab-routine-acoes" role="tabpanel">
                    <ul class="nav nav-tabs nav-tabs-highlight">
                        <li class="nav-item"><a href="tab-routine-acoes-acoes" class="nav-link fs-sm p-1 m-0 active">Ações</a></li>
                    </ul>
                    <div class="tab-content flex-lg-fill">
                        <div class="tab-pane fade border-start border-end border-bottom active show" id="tab-routine-acoes-acoes" role="tabpanel">
                            <div class="col-md-24 p-2 bg-white border vh-50">
                                @include('system.routines.common.acoes_acoes')
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
