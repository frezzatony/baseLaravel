<!doctype html>
<html lang="pt-BR" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="/assets/images/favicon/favicon.png" sizes="96x96" />
    <title>{{ config('app.name') }}</title>

    @include('layouts.auth.head-css')
    @include('layouts.auth.head-js')

    @yield('css-files')
</head>

<body>
    <div class="page-content">
        <div class="content-wrapper">
            <div id="contents" class="col-md-24 p-2">
                @yield('content')
            </div>
        </div>
    </div>
    @yield('js-files')
</body>
