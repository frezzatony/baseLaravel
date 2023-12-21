@extends('layouts.system.' . Request::get('_layout'))

@section('content')
    <form id="form-user" autcomplete="off">
        @csrf
        <input type="hidden" name="id" value="">
        <div class="d-lg-flex p-2 bg-light">
            <ul class="nav nav-tabs nav-tabs-vertical nav-tabs-vertical-start wmin-lg-200 mt-4 mb-lg-0 " role="tablist">
                <li class="nav-item bg-white" role="presentation">
                    <a href="#tab-user-cadastro" class="nav-link fs-sm p-1 m-0 active " data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        Cadastro
                    </a>
                </li>
                <li class="nav-item bg-white" role="presentation">
                    <a href="#tab-user-perfis" class="nav-link fs-sm p-1 m-0 " data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        Perfis de Acesso
                    </a>
                </li>
            </ul>

            <div class="tab-content flex-lg-fill">
                <div class="tab-pane fade active show" id="tab-user-cadastro" role="tabpanel">
                    <ul class="nav nav-tabs nav-tabs-highlight">
                        <li class="nav-item"><a href="tab-user-cadastro-usuario" class="nav-link fs-sm p-1 m-0 active">Usuário</a></li>
                    </ul>
                    <div class="tab-content flex-lg-fill">
                        <div class="tab-pane fade border-start border-end border-bottom active show" id="tab-user-cadastro-usuario" role="tabpanel">
                            <div class="col-md-24 p-2 bg-white border">
                                @include('system.users.create.cadastro_usuario')
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="tab-user-perfis" role="tabpanel">
                    <ul class="nav nav-tabs nav-tabs-highlight">
                        <li class="nav-item"><a href="tab-user-perfis-perfis" class="nav-link fs-sm p-1 m-0 active">Perfis de Acesso</a></li>
                    </ul>
                    <div class="tab-content flex-lg-fill">
                        <div class="tab-pane fade border-start border-end border-bottom active show" id="tab-user-perfis-perfis" role="tabpanel">
                            <div class="col-md-24 p-2 bg-white border">
                                @include('system.users.common.perfis_perfis')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@include('layouts.common.modal.footer_buttons_crud')
