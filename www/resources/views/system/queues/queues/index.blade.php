@extends('layouts.system.' . Request::get('_layout'))
@section('content')
    <div class="card crud-queues">
        <div class="card-header d-flex p-1">
            <h5 class="fs-sm mb-0 px-1">
                <i class="ph-keyboard"></i> Filas de atendimento
            </h5>
        </div>
        <div class="card-body p-0 m-0 bg-silver">
            @include('system.queues.queues.components.main_menu')
            <textarea class="filters-template d-none">
                <?= json_encode($dynamic_filters) ?>
            </textarea>
            <textarea class="filters-default-values d-none">
                <?= json_encode($default_filters) ?>
            </textarea>
            @include('layouts.common.crud.search_box')
            @include('system.queues.queues.components.datatable')

        </div>
    </div>
@endsection

@include('layouts.common.crud.js_files')

@section('css-files')
    <link href="{{ asset('assets/js/vendor/editors/summernote/summernote-bs4.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/js/vendor/jquery.inputpicker/jquery.inputpicker.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/js/vendor/files/file-manager.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('js-files')
    <script type="text/javascript" src="{{ asset('assets/js/vendor/jquery-appendgrid/AppendGrid.js') }}?v={{ time() }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/vendor/jquery.inputpicker/jquery.inputpicker.js') }}?v={{ time() }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/vendor/trees/fancytree_all.min.js') }}?v={{ time() }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/custom/system/queues/form-common-weekdays.js') }}?v={{ time() }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/custom/system/queues/form-common-call-orders.js') }}?v={{ time() }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/custom/system/queues/form-users-attendants.js') }}?v={{ time() }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/custom/system/queues/form-users-managers.js') }}?v={{ time() }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/custom/system/queues/form-matters.js') }}?v={{ time() }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/custom/system/queues/form-calendar.js') }}?v={{ time() }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/custom/system/queues/form-firstcometotem.js') }}?v={{ time() }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/custom/system/queues/form-firstcomemanual.js') }}?v={{ time() }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/custom/system/queues/index.js') }}?v={{ time() }}"></script>
@endsection
