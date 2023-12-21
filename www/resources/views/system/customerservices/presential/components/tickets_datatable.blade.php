<div class="col-md-24 p-1">
    <table id="dt-daily-tickets" class="table table-bordered table-striped table-hover">
        <thead>
            <tr class="bg-light f8">
                <th class="pa1 f8">Ticket</th>
                <th class="pa1 f8">Entrada na Fila</th>
                <th class="pa1 f8">Assunto</th>
                <th class="pa1 f8">Prioridade</th>
                <th class="pa1 f8">Situação</th>
                <th class="pa1 f8">Ações</th>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

<div class="d-none template-tickets-actions-menu">
    <div class="dropdown d-inline-flex">
        <a href="#" class="btn btn-outline-secondary fs-sm px-1 py-0  dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="ph-gear"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-end" style="">
            <button type="button" class="dropdown-item fs-sm p-1 text-nowrap menu-link btn-edit-customerservice">
                <i class="ph-chalkboard-teacher fs-xs mx-2"></i>Retornar Edição do Atendimento
            </button>
            <button type="button" class="dropdown-item fs-sm p-1 text-nowrap menu-link btn-call-ticket">
                <i class="ph-megaphone fs-xs mx-2"></i>Chamar
            </button>
            <button type="button" class="dropdown-item fs-sm p-1 text-nowrap menu-link btn-begin-customerservice">
                <i class="ph-chalkboard-teacher fs-xs mx-2"></i>Iniciar Atendimento
            </button>
            <button type="button" class="dropdown-item fs-sm p-1 text-nowrap menu-link btn-cancel-ticket">
                <i class="ph-x fs-xs mx-2"></i>Cancelar
            </button>
        </div>
    </div>
</div>

@include('layouts.common.datatable-files')

@section('css-files')
    <link href="{{ asset('assets/js/vendor/jquery-querybuilder/css/query-builder.default.css') }}" id="stylesheet" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/search-dynamic-filters.css') }}" rel="stylesheet" type="text/css">
    @parent
@endsection

@section('js-files')
    <script type="text/javascript" src="{{ asset('/assets/js/vendor/jquery-querybuilder/js/query-builder.standalone.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/assets/js/vendor/jquery-querybuilder/i18n/query-builder.pt-BR.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/assets/js/helpers/search-dynamic-filters.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/assets/js/helpers/search-filters.js') }}"></script>
    @parent
@endsection
