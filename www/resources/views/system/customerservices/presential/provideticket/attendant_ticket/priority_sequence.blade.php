@extends('layouts.system.' . Request::get('_layout'))

<div class="col-md-24 p-2 d-flex flex-column">
    @foreach ($tickets as $ticket)
        <button type="button" class="btn btn-outline-secondary btn-labeled btn-labeled-end btn-book-ticket mb-1 fs-sm" data-uuid-priority="{{ $ticket->uuid }}" data-ticket="{{ $ticket->ticket }}">
            {{ $ticket->description }}
            <span class="btn-labeled-icon bg-secondary text-white">
                {{ str_pad($ticket->ticket, 3, '0', STR_PAD_LEFT) }}
            </span>
        </button>
    @endforeach
</div>

<div class="col-md-24 p-0 modal-footer-template d-none">
</div>
