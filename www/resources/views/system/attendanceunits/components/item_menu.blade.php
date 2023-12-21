@extends('layouts.system.' . Request::get('_layout'))
@section('content')
    <div class="card">
        <div class="card-header d-flex bg-light p-1">
            <h5 class="fs-base mb-0 px-1">Módulos</h5>
        </div>
        <div class="card-body p-0">
            <div class="col-md-24 border-bottom py-1 m-0">
                <button type="button" href="{{ route('system.modules.create') }}" class="btn btn-outline-light fs-sm px-1 py-0 btn-add-module">
                    <i class="ph-file fs-sm"></i>Nova
                </button>
            </div>
        </div>
    </div>
@endsection

@section('js-files')
    <script type="text/javascript" src="{{ asset('assets/js/custom/system/modules/index.js') }}?v={{ time() }}"></script>
@endsection
