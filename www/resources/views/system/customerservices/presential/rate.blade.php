@extends('layouts.system.' . Request::get('_layout'))

@section('content')
    <form id="form-rate-customer-service-presential" autcomplete="off">
        @csrf
        <input type="hidden" name="id" value="{{ $customer_service->customer_service_id }}">
        <input type="hidden" name="queue_id" value="{{ $customer_service->queue_id }}">
        <div class="d-lg-flex p-2 bg-light">
            <ul class="nav nav-tabs nav-tabs-vertical nav-tabs-vertical-start wmin-lg-200 mt-4 mb-lg-0 " role="tablist">
                <li class="nav-item bg-white" role="presentation">
                    <a href="#tab-customer-service" class="nav-link fs-sm p-1 m-0" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        Atendimento
                    </a>
                </li>
                <li class="nav-item bg-white" role="presentation">
                    <a href="#tab-customer-service-rate" class="nav-link fs-sm p-1 m-0 active" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        Classificação
                    </a>
                </li>
            </ul>

            <div class="tab-content flex-lg-fill">
                <div class="tab-pane fade " id="tab-customer-service" role="tabpanel">
                    <ul class="nav nav-tabs nav-tabs-highlight">
                        <li class="nav-item">
                            <a href="#tab-customer-service-customer-service" class="nav-link fs-sm p-1 m-0 active" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                                Resumo do Atendimento
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content flex-lg-fill">
                        <div id="tab-customer-service-customer-service" class="tab-pane fade border-start border-end border-bottom active show" role="tabpanel">
                            <div class="col-md-24 p-2 bg-white">
                                @include('system.customerservices.presential.rate.customer_service')
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade active show" id="tab-customer-service-rate" role="tabpanel">
                    <ul class="nav nav-tabs nav-tabs-highlight">
                        <li class="nav-item">
                            <a href="#tab-customer-service-rate-tags" class="nav-link fs-sm p-1 m-0 active" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                                Tags
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#tab-customer-service-rate-problem-solution" class="nav-link fs-sm p-1 m-0" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                                Problema e Solução
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content flex-lg-fill">
                        <div id="tab-customer-service-rate-tags" class="tab-pane fade border-start border-end border-bottom active show" role="tabpanel">
                            <div class="col-md-24 p-2 bg-white vh-25">
                                @include('system.customerservices.presential.rate.tags')
                            </div>
                        </div>
                    </div>

                    <div class="tab-content flex-lg-fill">
                        <div id="tab-customer-service-rate-problem-solution" class="tab-pane fade border-start border-end border-bottom" role="tabpanel">
                            <div class="col-md-24 p-2 bg-white">
                                @include('system.customerservices.presential.rate.problem_solution')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

<div class="col-md-24 p-0 modal-footer-template d-none">
    <div class="col-md-6 p-0 m-0">
        <button type="button" class="btn btn-secondary fs-sm px-2 py-1 me-2" data-bs-dismiss="modal">
            <i class="ph-x me-1 fs-sm"></i>Fechar
        </button>
    </div>
    <div class="d-flex justify-content-end m-0">
        <button type="button" class="btn btn-secondary fs-sm px-2 py-1 me-2 btn-save-close-crud">
            <i class="ph-floppy-disk me-1 fs-sm"></i>Salvar e Fechar
        </button>
        <button type="button" class="btn btn-outline-secondary fs-sm px-2 py-1 btn-save-crud">
            <i class="ph-floppy-disk me-1 fs-sm"></i>Salvar
        </button>
    </div>
</div>
