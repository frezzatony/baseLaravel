<form id="form-books">
    <div class="col-md-24 p-1 mb-1">
        <div class="card m-0 p-1 pb-2">
            @include('system.customerservices.presential.components.queue_form')
            <div class="row px-2 mt-1">
                @include('system.customerservices.presential.components.main_menu')
            </div>
        </div>
    </div>

    <div class="col-md-24 p-1 mb-1">
        <div class="card m-0">
            <div class="card-header d-flex pa1 bg-light">
                <i class="ph-chalkboard-teacher fs-sm mt-1 me-1"></i>
                <h5 class="fs-sm fw-normal mb-0 ">Atendimento</h5>
                <div class="d-inline-flex ms-auto">
                    <a class="text-body" data-card-action="collapse">
                        <i class="ph-caret-down"></i>
                    </a>
                </div>
            </div>
            <div class="collapse show">
                <div class="card-body m-0 p-1">
                    <input type="hidden" id="next-book" value="">
                    <div class="row mt-2">
                        <div class="col-md-24 m-0">
                            <div class="alert alert-purple alert-dismissible p-1 queue-empty">
                                <div class="row">
                                    <div class="col-md-24 fs-sm">
                                        <i class="ph-chat-centered-dots fs-sm align-middle"></i> Para visualizar informações mais detalhadas, selecione uma fila.
                                    </div>
                                </div>
                            </div>
                            <div class="alert alert-purple alert-dismissible p-1 queue-no-book d-none">
                                <div class="row">
                                    <div class="col-md-24">
                                        <span class="fw-semibold"> <i class="ph-calendar-blank"></i> A fila não possui espera para assuntos sob sua responsabilidade.</span>
                                    </div>
                                </div>
                            </div>
                            <div class="alert alert-success alert-dismissible p-1 queue-assisting-book d-none">
                                <div class="row">
                                    <div class="col-md-24">
                                        <span class="fw-semibold"><i class="ph-chalkboard-teacher"></i> Você está com um atendimento em progresso</span>
                                    </div>
                                    <div class="row mt-1 text-body fs-sm">
                                        <div class="col-xl-2 col-md-6">
                                            <ul class="list list-unstyled mb-0">
                                                <li>
                                                    <p class="m-0">Início do atendimento</p>
                                                    <p class="m-0"><span class="fw-semibold created-at"></span></p>
                                                </li>
                                                <li>
                                                    <p class="m-0">Tempo em atendimento</p>
                                                    <p class="m-0"><span class="fw-semibold waiting-time"></span></p>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-xl-4 col-md-8">
                                            <ul class="list list-unstyled mb-0">
                                                <li>
                                                    <p class="m-0">Unidade de Atendimento</p>
                                                    <p class="m-0"><span class="fw-semibold attendance-unit"></span></p>
                                                </li>
                                                <li>
                                                    <p class="m-0">Fila de Atendimento</p>
                                                    <p class="m-0"><span class="fw-semibold queue"></span></p>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-xl-4 col-md-8">
                                            <ul class="list list-unstyled mb-0">
                                                <li>
                                                    <p class="m-0">Ticket</p>
                                                    <p class="m-0"><span class="fw-semibold ticket"></span></p>
                                                </li>
                                                <li>
                                                    <p class="m-0">Assunto</p>
                                                    <p class="m-0"><span class="fw-semibold matter"></span></p>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-24">
                                            <button type="button" class="btn btn-success fs-sm px-2 pt1 pb1 me-1 btn-edit-customerservice">
                                                <i class="ph-chalkboard-teacher me-1 fs-sm"></i> Retomar Edição do Atendimento
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="alert alert-warning alert-dismissible p-1 queue-next-book d-none">
                                <div class="row">
                                    <div class="col-md-24">
                                        <span class="fw-semibold title"><i class="ph-calendar-check"></i> Próximo atendimento que você pode assumir</span>
                                    </div>
                                </div>
                                <div class="row mt-1 text-body fs-sm">
                                    <div class="col-md-4">
                                        <ul class="list list-unstyled mb-0">
                                            <li>
                                                <h5 class="m-0 p-0">TICKET</h5>
                                            </li>
                                            <li>
                                                <h5><span class="fw-semibold ticket"></span></h5>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-xl-2 col-md-6">
                                        <ul class="list list-unstyled mb-0">
                                            <li>
                                                <p class="m-0">Entrada na fila</p>
                                                <p class="m-0"><span class="fw-semibold created-at"></span></p>
                                            </li>
                                            <li>
                                                <p class="m-0">Tempo de espera</p>
                                                <p class="m-0"><span class="fw-semibold waiting-time"></span></p>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-xl-4 col-md-8">
                                        <ul class="list list-unstyled mb-0">
                                            <li>
                                                <p class="m-0">Assunto</p>
                                                <p class="m-0"><span class="fw-semibold matter"></span></p>
                                            </li>
                                            <li>
                                                <p class="m-0">Prioridade</p>
                                                <p class="m-0"><span class="fw-semibold call-order"></span></p>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-xl-6 col-md-6">
                                        <ul class="list list-unstyled mb-0">
                                            <li>
                                                <p class="m-0">Quantidade de chamadas</p>
                                                <p class="m-0"><span class="fw-semibold call-count"></span></p>
                                            </li>
                                            <li>
                                                <p class="m-0">Última chamada há</p>
                                                <p class="m-0"><span class="fw-semibold last-call-time"></span></p>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-24">
                                        <button type="button" class="btn btn-danger fs-sm px-2 pt1 pb1 me-1 btn-cancel-customerservice">
                                            <i class="ph-x me-1 fs-sm"></i>Cancelar
                                        </button>
                                        <button type="button" class="btn btn-success fs-sm px-4 pt1 pb1 me-1 btn-begin-customerservice">
                                            <i class="ph-chalkboard-teacher me-1 fs-sm"></i>Iniciar Atendimento
                                        </button>
                                        <button type="button" class="btn btn-warning fs-sm px-4 pt1 pb1 me-1 btn-call-customerservice">
                                            <i class="ph-megaphone me-1 fs-sm"></i>Chamar <span class="ps-1 time-counter"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="alert alert-indigo p-2 queue-manual-dispenser-call-ticket col-md-14 d-none">
                                <div class="row">
                                    <h6 class="mb-0">Chamada de Senhas Retiradas em Dispenser</h6>
                                    <div class="col-md-24 p-0 m-0 call-content"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
