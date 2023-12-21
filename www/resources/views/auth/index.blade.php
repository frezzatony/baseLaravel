@extends('layouts.auth.' . Request::get('_layout'))

@section('content')
    <div class="content d-flex justify-content-center align-items-center">
        <form class="login-form" method="POST" action="{{ route('public.auth.login') }}">
            @csrf
            <div class="card mb-0">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center mt-3 p-0">
                            <img src="/assets/images/logo_civis_dark.png" height="28">
                        </div>
                        <div class="d-block align-items-center justify-content-center m-0 p-0">
                            <img src="/assets/images/brasao.png" width="150">
                        </div>
                        <h5 class="mb-0">Acessar sua conta</h5>
                        <span class="d-block text-muted">Informe suas credencias</span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">CPF:</label>
                        <div class="form-control-feedback form-control-feedback-start">
                            <input type="text" id="username" name="username" class="form-control">
                            <div class="form-control-feedback-icon">
                                <i class="ph-user-circle text-muted"></i>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Senha</label>
                        <div class="form-control-feedback form-control-feedback-start">
                            <input type="password" class="form-control" name="password">
                            <div class="form-control-feedback-icon">
                                <i class="ph-lock text-muted"></i>
                            </div>
                        </div>
                    </div>
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    <div class="mb-3">
                        <button type="submit" class="btn btn-secondary w-100">Acessar</button>
                    </div>

                    <div class="text-center">
                        <a href="/forgot-password">Esqueceu a senha?</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
