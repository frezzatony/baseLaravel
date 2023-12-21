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
                                    <i class="ph-envelope ph-2x"></i>
                                </div>
                                <h5 class="mb-0">Verifique seu e-mail</h5>
                                <span class="d-block text-muted">As instruções e um link de redefinição de senha foram enviados para lá.</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
