<!doctype html>
<html lang="pt-BR" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-token" content="{{ auth()->user()->api_token }}">
    <link rel="icon" type="image/png" href="/assets/images/favicon/favicon.png" sizes="96x96" />
    <title>{{ config('app.name') }}</title>

    @include('layouts.system.head-css')
    @include('layouts.system.head-js')

    @yield('css-files')
</head>

<body>
    <div class="page-content">
        @include('layouts.system.main-sidebar')
        <div class="content-wrapper">
            @include('layouts.system.main-navbar')
            @include('layouts.system.module_menu.module-menu')
            <div class="content-inner">
                <div id="contents" class="col-md-24 p-2">
                    @yield('content')
                </div>
            </div>
            @include('layouts.system.footer')
        </div>
    </div>
    @include('layouts.common.modal.modal_template')

    <script type="text/javascript" src="{{ asset('/assets/js/vendor/tables/datatables/datatables.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/assets/js/vendor/tables/datatables/extensions/input.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/assets/js/vendor/tables/datatables/extensions/select.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/assets/js/vendor/tables/datatables/extensions/buttons.min.js') }}"></script>
    @yield('js-files')
</body>
