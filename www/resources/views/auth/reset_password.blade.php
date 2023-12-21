@extends('layouts.auth.' . Request::get('_layout'))

@section('content')
    <div class="content-wrapper">
        <div class="content-inner">
            <div class="content d-flex justify-content-center align-items-center">
                <form action="{{ route('password.store') }}" method="POST" autocomplete="new-password" class="login-form">
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">
                    <div class="card mb-0">
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <div class="d-inline-flex bg-primary bg-opacity-10 text-primary lh-1 rounded-pill p-3 mb-3 mt-1">
                                    <i class="ph-user ph-2x"></i>
                                </div>
                                <h5 class="mb-0">Recuperação de Senha</h5>
                                <span class="d-block text-muted">Vamos criar uma nova senha para você</span>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">E-mail</label>
                                <input type="email" id="email_fake_prevent_autocomplete" name="email_fake_prevent_autocomplete" class="d-none">
                                <div class="form-control-feedback form-control-feedback-start">
                                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required>
                                    <div class="form-control-feedback-icon">
                                        <i class="ph-at text-muted"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Nova Senha</label>
                                <div class="form-control-feedback form-control-feedback-start">
                                    <input type="password" id="password" name="password" class="form-control" required>
                                    <div class="form-control-feedback-icon">
                                        <i class="ph-lock text-muted"></i>
                                    </div>
                                </div>
                                @if ($errors->any())
                                    <div class="invalid-feedback d-block">
                                        {!! implode('', $errors->all(':message')) !!}
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Repetir Nova Senha</label>
                                <div class="form-control-feedback form-control-feedback-start">
                                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                                    <div class="form-control-feedback-icon">
                                        <i class="ph-lock text-muted"></i>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ph-arrow-counter-clockwise me-2"></i>
                                Redefinir senha
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
