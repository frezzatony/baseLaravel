@extends('layouts.system.' . Request::get('_layout'))
<div class="col-md-24 p-2 d-flex flex-column " style="">
    <div class="list-group">
        @foreach ($tickets as $ticket)
            <div class="list-group-item list-group-item-indigo bg-white user-select-none d-flex justify-content-end p-1 m-0">
                <div class="col-md-14 pt-1 m-0 f7 fw-bold align-self-center">
                    {{ $ticket->description }}
                </div>
                <div class="col-md-3 py-0 px-1 align-self-end">
                    <label for="description" class="form-label fw-semibold fs-sm m-0">Senha:</label>
                    <input type="number" id="description" name="description" class="form-control px-1 pt1 pb1 fs-sm" value="{{ $ticket->ticket }}" step="1" min="1">
                </div>
                <div class="col-md-7 m-0 align-self-end">
                    <div class="col-md-24 p-0 m-0 d-flex flex-column">
                        <button type="button" class="btn btn-info fs-sm px-2 pt1 pb1 me-1 btn-call-ticket" data-uuid-priority="{{ $ticket->uuid }}">
                            <i class="ph-megaphone me-1 fs-sm "></i>Chamar
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
