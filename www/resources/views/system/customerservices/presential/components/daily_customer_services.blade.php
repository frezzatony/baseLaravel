    <div class="col-md-24 p-1 mb-1">
        <div class="card m-0">
            <div class="card-header d-flex flex-row pa1 bg-light">
                <div class="pb-1 d-flex align-self-center">
                    <i class="ph-address-book fs-sm mt-1 me-1"></i>
                </div>
                <div class="pt-1 me-2">
                    <h5 class="fs-sm fw-normal mb-0 ">
                        Tickets e Atendimentos Presenciais do Dia<span class="text-danger">*</span>:
                    </h5>
                </div>
                <div>
                    <input type="date" max="{{ date('Y-m-d') }}" id="date" name="date" class="form-control px-1 pt1 pb1 fs-sm" value="{{ date('Y-m-d') }}" autocomplete="off">
                </div>
                <div class="d-inline-flex align-self-center ms-auto">
                    <a class="text-body" data-card-action="collapse">
                        <i class="ph-caret-down"></i>
                    </a>
                </div>
            </div>
            <div class="collapse show">
                <div class="card-body m-0 p-1 pt-3 bg-silver">
                    <div class="row">
                        <div class="col-md-24">
                            <div class="tab-pane fade active show" id="tab-person" role="tabpanel">
                                <ul class="nav nav-tabs nav-tabs-highlight">
                                    <li class="nav-item">
                                        <a href="#tab-daily-tickets" class="nav-link fs-sm p-1 m-0 active" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                                            Tickets
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#tab-person-person" class="nav-link fs-sm p-1 m-0 " data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                                            Atendimentos
                                        </a>
                                    </li>
                                </ul>

                                <div class="tab-content flex-lg-fill">
                                    <div class="tab-pane fade border-start border-end border-bottom fade active show" id="tab-daily-tickets" role="tabpanel">
                                        <div class="col-md-24 p-2 bg-white border">
                                            <form id="form-tickets" autocomplete="off">
                                                <div class="alert alert-purple alert-dismissible p-1 mt-1 d-none daily-tickets-empty">
                                                    <div class="row">
                                                        <div class="col-md-24 fs-sm">
                                                            <i class="ph-chat-centered-dots fs-sm align-middle"></i> Para visualizar informações mais detalhadas, selecione uma fila.
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row daily-tickes-list">
                                                    @include('system.customerservices.presential.components.tickets_filters')
                                                    @include('system.customerservices.presential.components.tickets_datatable')
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
