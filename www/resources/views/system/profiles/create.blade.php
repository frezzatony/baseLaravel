@extends('layouts.system.' . Request::get('_layout'))

@section('content')
    <form id="form-profile" autcomplete="off">
        @csrf
        <input type="hidden" name="id" value="">
        <div class="d-lg-flex p-2 bg-light">
            <ul class="nav nav-tabs nav-tabs-vertical nav-tabs-vertical-start wmin-lg-200 mt-4 mb-lg-0 " role="tablist">
                <li class="nav-item bg-white" role="presentation">
                    <a href="#tab-profile-cadastro" class="nav-link fs-sm p-1 m-0 active " data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        Cadastro
                    </a>
                </li>
                <li class="nav-item bg-white" role="presentation">
                    <a href="#tab-privilegios-privilegios" class="nav-link fs-sm p-1 m-0 " data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        Privilégios
                    </a>
                </li>
            </ul>

            <div class="tab-content flex-lg-fill">
                <div class="tab-pane fade active show" id="tab-profile-cadastro" role="tabpanel">
                    <ul class="nav nav-tabs nav-tabs-highlight">
                        <li class="nav-item"><a href="tab-profile-cadastro-perfil" class="nav-link fs-sm p-1 m-0 active">Perfil</a></li>
                    </ul>
                    <div class="tab-content flex-lg-fill">
                        <div class="tab-pane fade border-start border-end border-bottom active show" id="tab-profile-cadastro-perfil" role="tabpanel">
                            <div class="col-md-24 p-2 bg-white border">
                                @include('system.profiles.create.cadastro_perfil')
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="tab-privilegios-privilegios" role="tabpanel">
                    <ul class="nav nav-tabs nav-tabs-highlight">
                        <li class="nav-item"><a href="tab-privilegios-privilegios-acoes" class="nav-link fs-sm p-1 m-0 active">Ações atribuídas</a></li>
                    </ul>
                    <div class="tab-content flex-lg-fill">
                        <div class="tab-pane fade border-start border-end border-bottom active show" id="tab-privilegios-privilegios-acoes" role="tabpanel">
                            <div class="col-md-24 p-2 bg-white border hv-50">
                                @include('system.profiles.common.privilegios_privilegios')
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
