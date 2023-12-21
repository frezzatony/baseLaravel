@extends('layouts.system.' . Request::get('_layout'))

@section('content')
    <form id="form-customer-service-presential" autcomplete="off">
        @csrf
        <input type="hidden" id="id" name="id" value="{{ $customer_service->customer_service_id }}">
        <input type="hidden" id="queue_id" name="queue_id" value="{{ $customer_service->queue_id }}">
        <input type="hidden" id="conclude" name="conclude" value="0">
        <div class="d-lg-flex p-2 bg-light">
            <ul class="nav nav-tabs nav-tabs-vertical nav-tabs-vertical-start wmin-lg-200 mt-4 mb-lg-0 " role="tablist">
                <li class="nav-item bg-white" role="presentation">
                    <a href="#tab-customer-service" class="nav-link fs-sm p-1 m-0 active" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        Atendimento
                    </a>
                </li>
                <li class="nav-item bg-white" role="presentation">
                    <a href="#tab-customer-service-person" class="nav-link fs-sm p-1 m-0" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        Pessoa Interessada
                    </a>
                </li>
                <li class="nav-item bg-white" role="presentation">
                    <a href="#tab-customer-service-comments" class="nav-link fs-sm p-1 m-0" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        Observações
                    </a>
                </li>
                <li class="nav-item bg-white" role="presentation">
                    <a href="#tab-customer-service-attachments" class="nav-link fs-sm p-1 m-0" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        Anexos
                    </a>
                </li>
            </ul>

            <div class="tab-content flex-lg-fill">
                <div class="tab-pane fade active show" id="tab-customer-service" role="tabpanel">
                    <ul class="nav nav-tabs nav-tabs-highlight">
                        <li class="nav-item">
                            <a href="#tab-customer-service-customer-service" class="nav-link fs-sm p-1 m-0 active" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                                Atendimento
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#tab-customer-service-timeline" class="nav-link fs-sm p-1 m-0" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                                Linha do Tempo
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content flex-lg-fill">
                        <div id="tab-customer-service-customer-service" class="tab-pane fade border-start border-end border-bottom active show" role="tabpanel">
                            <div class="col-md-24 p-2 bg-white">
                                @include('system.customerservices.presential.edit.customer_service')
                            </div>
                        </div>
                        <div class="tab-content flex-lg-fill">
                        </div>
                        <div id="tab-customer-service-timeline" class="tab-pane fade border-start border-end border-bottom" role="tabpanel">
                            <div class="col-md-24 p-2 bg-white" style="height: 60vh; overflow-y: auto">
                                @include('system.customerservices.presential.edit.timeline')
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade " id="tab-customer-service-person" role="tabpanel">
                    <ul class="nav nav-tabs nav-tabs-highlight">
                        <li class="nav-item">
                            <a href="#tab-customer-service-person-person" class="nav-link fs-sm p-1 m-0 active" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                                Pessoa
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#tab-customer-service-person-address" class="nav-link fs-sm p-1 m-0" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                                Endereco
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#tab-customer-service-person-contacts" class="nav-link fs-sm p-1 m-0" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                                Contatos
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content flex-lg-fill">
                        <div id="tab-customer-service-person-person" class="tab-pane fade border-start border-end border-bottom active show" role="tabpanel">
                            <div class="col-md-24 p-2 bg-white">
                                @include('system.customerservices.presential.edit.forms.default.person_person')
                            </div>
                        </div>
                    </div>

                    <div class="tab-content flex-lg-fill">
                        <div id="tab-customer-service-person-address" class="tab-pane fade border-start border-end border-bottom " role="tabpanel">
                            <div class="col-md-24 p-2 bg-white address">
                                @include('layouts.common.crud.address', ['address_required' => false, 'input_prefix' => 'person_', 'address' => json_decode($person->address ?? null, true)])
                            </div>
                        </div>
                    </div>

                    <div class="tab-content flex-lg-fill">
                        <div id="tab-customer-service-person-contacts" class="tab-pane fade border-start border-end border-bottom " role="tabpanel">
                            <div class="col-md-24 p-2 bg-white vh-50 contacts">
                                @include('layouts.common.crud.contacts', ['contacts' => $person->contacts ?? null])
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade " id="tab-customer-service-comments" role="tabpanel">
                    <ul class="nav nav-tabs nav-tabs-highlight">
                        <li class="nav-item">
                            <a href="#tab-customer-service-comments-public" class="nav-link fs-sm p-1 m-0 active" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                                Observações Públicas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#tab-customer-service-comments-internal" class="nav-link fs-sm p-1 m-0" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                                Observações Internas
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content flex-lg-fill">
                        <div class="tab-pane fade border-start border-end border-bottom active show" id="tab-customer-service-comments-public" role="tabpanel">
                            <div class="col-md-24 p-2 bg-white">
                                @include('system.customerservices.presential.edit.public_comments')
                            </div>
                        </div>
                    </div>
                    <div class="tab-content flex-lg-fill">
                        <div class="tab-pane fade border-start border-end border-bottom " id="tab-customer-service-comments-internal" role="tabpanel">
                            <div class="col-md-24 p-2 bg-white">
                                @include('system.customerservices.presential.edit.internal_comments')
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="tab-customer-service-attachments" role="tabpanel">
                    <ul class="nav nav-tabs nav-tabs-highlight">
                        <li class="nav-item"><a href="tab-customer-service-attachments-attachments" class="nav-link fs-sm p-1 m-0 active">Anexos</a></li>
                    </ul>
                    <div class="tab-content flex-lg-fill">
                        <div class="tab-pane fade border-start border-end border-bottom active show" id="tab-customer-service-attachments-attachments" role="tabpanel">
                            <div class="col-md-24 p-2 bg-white">
                                @include('system.customerservices.presential.edit.attachments')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="col-md-24 p-0 modal-footer-template d-none">
        <div class="col-md-6 p-0 m-0">
            <button type="button" class="btn btn-secondary fs-sm px-2 py-1 me-2" data-bs-dismiss="modal">
                <i class="ph-x me-1 fs-sm"></i>Fechar
            </button>
        </div>
        <div class="d-flex justify-content-end m-0">
            @if ($customer_service->status != \App\Enums\CustomerServices\CustomerServiceStatus::COMPLETED['value'])
                <button type="button" class="btn btn-secondary fs-sm px-2 py-1 me-2 btn-save-conclude-crud">
                    <i class="ph-floppy-disk me-1 fs-sm"></i>Salvar e Encerrar
                </button>
            @endif
            @if ($customer_service->status == \App\Enums\CustomerServices\CustomerServiceStatus::COMPLETED['value'])
                <button type="button" class="btn btn-secondary fs-sm px-2 py-1 me-2 btn-save-close-crud">
                    <i class="ph-floppy-disk me-1 fs-sm"></i>Salvar e Fechar
                </button>
            @endif
            <button type="button" class="btn btn-outline-secondary fs-sm px-2 py-1 btn-save-crud">
                <i class="ph-floppy-disk me-1 fs-sm"></i>Salvar
            </button>
        </div>
    </div>
@endsection
