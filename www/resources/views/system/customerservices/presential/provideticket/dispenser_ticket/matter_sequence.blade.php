@extends('layouts.system.' . Request::get('_layout'))

<div class="tickets-content" style="min-height: 30vh; overflow:hidden; position: relative;">
    <div style="min-height: 30vh; ">
        <div class="col-md-24 p-2 d-flex flex-column priorities bg-white" style="position: absolute">
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
                <h6 class="f7 ms-1">Prioridade: {{ $ticket->description }}</h6>
                <div class="list-group">
                    @foreach ($ticket->tickets as $matter)
                        <div class="list-group-item list-group-item-indigo bg-white user-select-none d-flex justify-content-end p-1 m-0">
                            <div class="col-md-14 pt-1 m-0 f7 fw-bold align-self-center">
                                {{ $matter->description }}
                            </div>
                            <div class="col-md-3 py-0 px-1 align-self-end">
                                <label for="description" class="form-label fw-semibold fs-sm m-0">Senha:</label>
                                <input type="number" id="description" name="description" class="form-control px-1 pt1 pb1 fs-sm" value="{{ $matter->ticket }}" step="1" min="1">
                            </div>
                            <div class="col-md-7 m-0 align-self-end">
                                <div class="col-md-24 p-0 m-0 d-flex flex-column">
                                    <button type="button" class="btn btn-info fs-sm px-2 pt1 pb1 me-1 btn-call-ticket" data-uuid-priority="{{ $ticket->uuid }}" data-uuid-matter="{{ $matter->uuid }}">
                                        <i class="ph-megaphone me-1 fs-sm "></i>Chamar
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="col-md-24 p-0 m-0 mb-2 mt-2">
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
