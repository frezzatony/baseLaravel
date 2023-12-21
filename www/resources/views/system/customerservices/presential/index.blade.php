@extends('layouts.system.' . Request::get('_layout'))
@section('content')
    <div class="card customerservices-presential-books">
        <div class="card-header d-flex p-1">
            <h5 class="fs-sm mb-0 px-1">
                <i class="ph-keyboard"></i> Atendimentos Presenciais
            </h5>
        </div>
        <div class="card-body p-0 m-0 bg-silver">
            @include('system.customerservices.presential.components.book')
            @include('system.customerservices.presential.components.daily_customer_services')
        </div>
    </div>
@endsection

@include('layouts.common.crud.js_files')

@section('css-files')
    <link href="{{ asset('assets/js/vendor/forms/tags/css/bootstrap-tokenfield.min.css') }}" id="stylesheet" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/js/vendor/files/file-manager.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('js-files')
    <script type="text/javascript" src="{{ asset('assets/js/vendor/time/timeElapsed.js') }}?v={{ time() }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/vendor/forms/tags/bootstrap-tokenfield.min.js') }}?v={{ time() }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/vendor/uploaders/dropzone.min.js') }}?v={{ time() }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/vendor/files/file-manager.js') }}?v={{ time() }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/vendor/trees/fancytree_all.min.js') }}?v={{ time() }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/custom/system/customerservices/presential/form-common.js') }}?v={{ time() }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/custom/system/customerservices/presential/form-common-my-matters.js') }}?v={{ time() }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/custom/system/customerservices/presential/form-common-my-priorities.js') }}?v={{ time() }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/custom/system/customerservices/presential/rate-form-common.js') }}?v={{ time() }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/custom/system/customerservices/presential/first-come-manual.js') }}?v={{ time() }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/custom/system/customerservices/presential/daily-tickets.js') }}?v={{ time() }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/custom/system/customerservices/presential/index.js') }}?v={{ time() }}"></script>
@endsection
