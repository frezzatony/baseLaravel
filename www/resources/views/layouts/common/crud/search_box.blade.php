<div class="col-md-24 p-1 mb-1">
    <div class="card m-0">
        <div class="card-header d-flex pa1 bg-light">
            <i class="ph-magnifying-glass fs-sm mt-1 me-1"></i>
            <h5 class="fs-sm fw-normal mb-0 ">Localizar
            </h5>
        </div>
        <div class="card-body p-1 fs-sm search-box-body col-md-24 col-lg-18">
            <form class="col-md-24" id="filters-crud" autocomplete="off"></form>
        </div>
    </div>
</div>

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
