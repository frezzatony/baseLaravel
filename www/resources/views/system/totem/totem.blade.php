@extends('layouts.system.empty')

@section('content')
{!! $screens_html!!}
@endsection

@section('css-files')
    <link href="{{ asset('assets/css/custom/system/totem/style.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('js-files')
    <script type="text/javascript" src="{{ asset('assets/js/custom/system/totem/index.js') }}?v={{ time() }}"></script>
@endsection