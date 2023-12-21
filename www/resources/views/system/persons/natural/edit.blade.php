@extends('layouts.system.' . Request::get('_layout'))

@section('content')
    <form id="form-person" autcomplete="off">
        @csrf
        <input type="hidden" name="id" value="{{ $person->id ?? null }}">
        <input type="hidden" id="type" name="type" value="{{ \App\Enums\PersonType::NATURAL['value'] }}">
        <div class="d-lg-flex p-2 bg-light">
            <ul class="nav nav-tabs nav-tabs-vertical nav-tabs-vertical-start wmin-lg-200 mt-4 mb-lg-0 " role="tablist">
                <li class="nav-item bg-white" role="presentation">
                    <a href="#tab-person" class="nav-link fs-sm p-1 m-0 active " data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        Cadastro<span class="text-danger">*</span>
                    </a>
                </li>
                <li class="nav-item bg-white" role="presentation">
                    <a href="#tab-address" class="nav-link fs-sm p-1 m-0 " data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        Endereço
                    </a>
                </li>
                <li class="nav-item bg-white" role="presentation">
                    <a href="#tab-contacts" class="nav-link fs-sm p-1 m-0 " data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        Contatos
                    </a>
                </li>
                <li class="nav-item bg-white" role="presentation">
                    <a href="#tab-attachments" class="nav-link fs-sm p-1 m-0 " data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        Anexos
                    </a>
                </li>
            </ul>

            <div class="tab-content flex-lg-fill">
                <div class="tab-pane fade active show" id="tab-person" role="tabpanel">
                    <ul class="nav nav-tabs nav-tabs-highlight">
                        <li class="nav-item">
                            <a href="#tab-person-person" class="nav-link fs-sm p-1 m-0 active" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                                Pessoa Física<span class="text-danger">*</span>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content flex-lg-fill">
                        <div class="tab-pane fade border-start border-end border-bottom fade active show" id="tab-person-person" role="tabpanel">
                            <div class="col-md-24 p-2 bg-white border">
                                @include('system.persons.natural.edit.person_person')
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade " id="tab-address" role="tabpanel">
                    <ul class="nav nav-tabs nav-tabs-highlight">
                        <li class="nav-item">
                            <a href="#tab-person-address" class="nav-link fs-sm p-1 m-0 active" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                                Endereço
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content flex-lg-fill">
                        <div class="tab-pane fade border-start border-end border-bottom fade active show" id="tab-person-address" role="tabpanel">
                            <div class="col-md-24 p-2 bg-white border address">
                                @include('layouts.common.crud.address', ['address_required' => false, 'address' => !empty($person->address) ? $person->address->toArray() : null])
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="tab-contacts" role="tabpanel">
                    <ul class="nav nav-tabs nav-tabs-highlight">
                        <li class="nav-item">
                            <a href="#tab-person-address" class="nav-link fs-sm p-1 m-0 active" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                                Contatos
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content flex-lg-fill">
                        <div class="tab-pane fade border-start border-end border-bottom fade active show" id="tab-person-address" role="tabpanel">
                            <div class="col-md-24 p-2 bg-white border vh-50 contacts">
                                @include('layouts.common.crud.contacts', ['contacts' => $person->contacts ?? null])
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="tab-attachments" role="tabpanel">
                    <ul class="nav nav-tabs nav-tabs-highlight">
                        <li class="nav-item">
                            <a href="#tab-person-attachments" class="nav-link fs-sm p-1 m-0 active" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                                Anexos
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content flex-lg-fill">
                        <div class="tab-pane fade border-start border-end border-bottom fade active show" id="tab-person-attachments" role="tabpanel">
                            <div class="col-md-24 p-2 bg-white border vh-50">
                                @include('layouts.common.crud.attachments')
                            </div>
                        </div>
                    </div>
                </div>

            </div>
    </form>
@endsection

@include('layouts.common.modal.footer_buttons_crud')

@section('js-files')
@endsection
