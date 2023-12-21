@extends('layouts.system.' . Request::get('_layout'))

@section('content')
    <div class="row m-0 p-0 border-bottom">
        <div class="col-md-24 p-2">
            <h6 class="mb-0 fs-base">{{ $notification->title ?? null }}</h6>
            <div class="fs-sm">
                <span class="letter-icon-title"><span class="text-muted">De:</span> {{ $notification->author ?? null }}</span>
                <span class="float-end text-muted">Em: {{ $notification->created_at->format('d/m/Y') }} às {{ $notification->created_at->format('H:i:s') }}h</span>
            </div>
        </div>
    </div>
    <div class="row p-0 m-1">
        <div class="col-md-24 p-1 fs-sm">
            {!! $notification->text ?? null !!}
        </div>
    </div>
@endsection

<div class="col-md-24 p-0 modal-footer-template d-none">
    <div class="col-md-6 p-0 m-0">
        <button type="button" class="btn btn-secondary fs-sm px-2 py-1 me-2" data-bs-dismiss="modal">
            <i class="ph-x me-1 fs-sm"></i>Fechar
        </button>
    </div>
</div>

@section('js-files')
@endsection
