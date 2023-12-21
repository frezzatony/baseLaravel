@extends('layouts.auth.' . Request::get('_layout'))

@section('content')
    <div class="content-wrapper">
        <div class="content-inner">
            <div class="content d-flex justify-content-center align-items-center">
                <form action="{{ route('password.email') }}" method="POST" autocomplete="off" class="login-form">
                    @csrf
                    <div class="card mb-0">
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <div class="d-inline-flex bg-primary bg-opacity-10 text-primary lh-1 rounded-pill p-3 mb-3 mt-1">
                                    <i class="ph-arrows-counter-clockwise ph-2x"></i>
                                </div>
                                <h5 class="mb-0">Recuperação de Senha</h5>
                                <span class="d-block text-muted">Enviaremos as instruções no seu e-mail</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">E-mail</label>
                                <input type="email" id="email_fake_prevent_autocomplete" name="email_fake_prevent_autocomplete" class="d-none" value="fake@fake.com">
                                <div class="form-control-feedback form-control-feedback-start">
                                    <input type="email" id="email" name="email" class="form-control {{ $errors->any() ? 'is-invalid' : '' }}" value="{{ old('email') }}">
                                    <div class="form-control-feedback-icon">
                                        <i class="ph-at text-muted"></i>
                                    </div>
                                </div>
                                @if ($errors->any())
                                    <div class="invalid-feedback d-block">
                                        {!! implode('', $errors->all(':message')) !!}
                                    </div>
                                @endif
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ph-arrow-counter-clockwise me-2"></i>
                                Restaurar senha
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
