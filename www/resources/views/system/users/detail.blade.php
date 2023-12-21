@extends('layouts.system.' . Request::get('_layout'))

@section('content')
    <div class="d-lg-flex align-items-lg-start">
        <div class="sidebar sidebar-component sidebar-expand-lg bg-transparent shadow-none me-lg-3">
            <div class="sidebar-content">
                <div class="card">
                    <div class="sidebar-section-body text-center">
                        <div class="card-img-actions d-inline-block mb-3">
                            <img class="img-fluid rounded-circle" src="/assets/images/empty-user.png" width="150" height="150" alt="">
                        </div>
                        <h6 class="mb-0">{{ $user->name }}</h6>
                    </div>
                    <ul class="nav nav-sidebar" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a href="#profile" class="nav-link active" data-bs-toggle="tab" aria-selected="true" role="tab">
                                <i class="ph-user me-2"></i>
                                Meu Perfil
                            </a>
                        </li>
                        <li class="nav-item-divider"></li>
                        <li class="nav-item">
                            <a href="{{ route('public.auth.logout') }}" class="nav-link" aria-selected="false">
                                <i class="ph-sign-out me-2"></i>
                                Sair
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="tab-content flex-fill">
            <div class="tab-pane fade active show" id="profile" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Informações de Perfil</h5>
                    </div>
                    <div class="card-body">
                        <form id="personal-information-form">
                            @csrf
                            <div class="row ">
                                <div class="col">
                                    <div class="mb-3">
                                        <label class="form-label">Nome Completo</label>
                                        <input type="text" value="{{ $user->name }}" class="form-control" name="name">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="mb-3">
                                        <label class="form-label">Nome Social</label>
                                        <input type="text" value="{{ $user->social_name }}" class="form-control" name="social_name">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="mb-3">
                                        <label class="form-label">CPF</label>
                                        <input type="text" value="{{ $user->login }}" readonly class="form-control">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="mb-3">
                                        <label class="form-label">E-mail</label>
                                        <input type="text" value="{{ $user->email }}" readonly class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <div class="mb-3">
                                        <label class="form-label">Criado em</label>
                                        <input type="text" value="{{ $user->created_at->format('d/m/Y H:i:s') }}" readonly class="form-control">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="mb-3">
                                        <label class="form-label">Atualizado em</label>
                                        <input type="text" value="{{ $user->updated_at->format('d/m/Y H:i:s') }}" readonly class="form-control">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class=" col form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" id="flexCheckDisabled" checked name="ativo">
                                        <label class="form-check-label" for="flexCheckDisabled">
                                            Ativo
                                        </label>
                                    </div>
                                    <div class="col form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" value="" id="flexCheckCheckedDisabled" checked disabled>
                                        <label class="form-check-label" for="flexCheckCheckedDisabled">
                                            Email Verificado
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="button" id="personal-information-button" class="btn btn-primary">Salvar</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Alterar Senha</h5>
                    </div>
                    <div class="card-body">
                        <form id="update-user-config-form">
                            @csrf
                            <div class="row">
                                <div class="col">
                                    <div class="mb-3">
                                        <label class="form-label">Senha Atual</label>
                                        <input type="password" placeholder="Insira a Senha Atual" name="current_password" class="form-control">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="mb-3">
                                        <label class="form-label">Nova Senha</label>
                                        <input type="password" placeholder="Insira a nova Senha" name="password" class="form-control">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="mb-3">
                                        <label class="form-label">Confirme a Senha</label>
                                        <input type="password" placeholder="Confirme a Nova Senha" name="password_confirmation" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="button" id="personal-config-button" class="btn btn-primary">Salvar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('layouts.common.modal.footer_buttons_crud')

@section('js-files')
    <script type="text/javascript" src="{{ asset('/assets/js/vendor/ui/fab.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/custom/system/users/detail.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/helpers/form.js') }}"></script>
@endsection
