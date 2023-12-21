@extends('layouts.system.' . Request::get('_layout'))
@section('content')
    <div class="card crud-messages-contacts">
        <div class="card-header d-flex p-1">
            <h5 class="fs-sm mb-0 px-1">
                <i class="ph-keyboard"></i> Contatos para Envio de Notificações
            </h5>
        </div>
        <div class="card-body p-0 m-0 bg-silver">
            @include('system.messages.contacts.components.main_menu')
            <textarea class="filters-template d-none">
                <?= json_encode($dynamic_filters) ?>
            </textarea>
            <textarea class="filters-default-values d-none">
                <?= json_encode($default_filters) ?>
            </textarea>
            @include('layouts.common.crud.search_box')
            @include('system.messages.contacts.components.datatable')

        </div>
    </div>
@endsection

@include('layouts.common.crud.js_files')
@section('js-files')
    <script type="text/javascript" src="{{ asset('assets/js/custom/system/messages/contacts/index.js') }}?v={{ time() }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/custom/system/messages/contacts/form-common.js') }}?v={{ time() }}"></script>
@endsection
