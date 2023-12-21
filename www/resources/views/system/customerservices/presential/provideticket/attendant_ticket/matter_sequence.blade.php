@extends('layouts.system.' . Request::get('_layout'))

<div class="tickets-content" style="min-height: 30vh; overflow:hidden; position: relative;">
    <div style="min-height: 30vh; ">
        <div class="col-md-24 p-2 d-flex flex-column priorities" style="position: absolute">
            @foreach ($tickets as $ticket)
                <button type="button" class="btn btn-outline-secondary btn-labeled btn-labeled-end btn-prioroty-ticket mb-1 fs-sm" data-uuid-priority="{{ $ticket->uuid }}">
                    {{ $ticket->description }}
                    <span class="btn-labeled-icon bg-secondary text-white">
                        <i class="ph-arrow-fat-line-right "></i>
                    </span>
                </button>
            @endforeach
        </div>

        @foreach ($tickets as $ticket)
            <div class="col-md-24 p-2 d-flex flex-column d-none matter" data-uuid-priority="{{ $ticket->uuid }}">
                @foreach ($ticket->tickets as $matter)
                    <button type="button" class="btn btn-outline-secondary btn-labeled btn-labeled-end btn-book-ticket mb-1 fs-sm" data-uuid-priority="{{ $ticket->uuid }}" data-uuid-matter="{{ $matter->uuid }}"
                        data-ticket="{{ $matter->ticket }}">
                        {{ $matter->description }}
                        <span class="btn-labeled-icon bg-secondary text-white">
                            {{ str_pad($matter->ticket, 3, '0', STR_PAD_LEFT) }}
                        </span>
                    </button>
                @endforeach
                <div class="col-md-24 p-0 m-0 mb-2">
                    <button type="button" class="btn btn-outline-secondary fs-sm px-1 py-0 btn-back">
                        <i class="ph-arrow-fat-line-left"></i> Voltar
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</div>

<div class="col-md-24 p-0 modal-footer-template d-none">
</div>
