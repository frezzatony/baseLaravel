@extends('layouts.system.' . Request::get('_layout'))

@section('content')
    <form id="form-queue" autcomplete="off">
        @csrf
        <input type="hidden" name="id" value="{{ $queue->id ?? null }}">
        <div class="d-lg-flex p-2 bg-light">
            <ul class="nav nav-tabs nav-tabs-vertical nav-tabs-vertical-start wmin-lg-200 mt-4 mb-lg-0 " role="tablist">
                <li class="nav-item bg-white" role="presentation">
                    <a href="#tab-fila-cadastro" class="nav-link fs-sm p-1 m-0 active " data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        Cadastro<span class="text-danger">*</span>
                    </a>
                </li>
                <li class="nav-item bg-white" role="presentation">
                    <a href="#tab-fila-ordem-chamada" class="nav-link fs-sm p-1 m-0 " data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        Ordem de prioridade
                    </a>
                </li>
                <li class="nav-item bg-white" role="presentation">
                    <a href="#tab-fila-usuarios" class="nav-link fs-sm p-1 m-0 " data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        Usuários<span class="text-danger">*</span>
                    </a>
                </li>
                <li class="nav-item bg-white" role="presentation">
                    <a href="#tab-fila-assuntos" class="nav-link fs-sm p-1 m-0 " data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        Assuntos<span class="text-danger">*</span>
                    </a>
                </li>
            </ul>

            <div class="tab-content flex-lg-fill">
                <div class="tab-pane fade active show" id="tab-fila-cadastro" role="tabpanel">
                    <ul class="nav nav-tabs nav-tabs-highlight">
                        <li class="nav-item">
                            <a href="#tab-fila-cadastro-fila" class="nav-link fs-sm p-1 m-0 active" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                                Fila de Atendimento<span class="text-danger">*</span>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content flex-lg-fill">
                        <div class="tab-pane fade border-start border-end border-bottom fade active show" id="tab-fila-cadastro-fila" role="tabpanel">
                            <div class="col-md-24 p-2 bg-white border">
                                @include('system.queues.firstcomemanual.edit.cadastro_fila')
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="tab-fila-ordem-chamada" role="tabpanel">
                    <ul class="nav nav-tabs nav-tabs-highlight">
                        <li class="nav-item">
                            <a href="#tab-fila-ordem-chamada-ordem-chamada" class="nav-link fs-sm p-1 m-0 active" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                                Ordem de chamada
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content flex-lg-fill">
                        <div class="tab-pane fade border-start border-end border-bottom active show" id="tab-fila-ordem-chamada-ordem-chamada" role="tabpanel">
                            <div class="col-md-24 p-2 bg-white border vh-50">
                                @include('system.queues.common.ordem_chamada')
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="tab-fila-usuarios" role="tabpanel">
                    <ul class="nav nav-tabs nav-tabs-highlight">
                        <li class="nav-item">
                            <a href="#tab-fila-usuarios-atendentes" class="nav-link fs-sm p-1 m-0 active" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                                Atendentes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#tab-fila-usuarios-gestores" class="nav-link fs-sm p-1 m-0" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                                Gestores<span class="text-danger">*</span>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content flex-lg-fill">
                        <div class="tab-pane fade border-start border-end border-bottom fade active show" id="tab-fila-usuarios-atendentes" role="tabpanel">
                            <div class="col-md-24 p-2 bg-white border vh-50">
                                @include('system.queues.common.usuarios_atendentes')
                            </div>
                        </div>
                    </div>

                    <div class="tab-content flex-lg-fill">
                        <div class="tab-pane fade border-start border-end border-bottom fade" id="tab-fila-usuarios-gestores" role="tabpanel">
                            <div class="col-md-24 p-2 bg-white border vh-50">
                                @include('system.queues.common.usuarios_gestores')
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-content flex-lg-fill">
                    <div class="tab-pane fade" id="tab-fila-assuntos" role="tabpanel">
                        <ul class="nav nav-tabs nav-tabs-highlight">
                            <li class="nav-item">
                                <a href="#tab-fila-assuntos-assuntos" class="nav-link fs-sm p-1 m-0 active" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                                    Assuntos para atendimento<span class="text-danger">*</span>
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content flex-lg-fill">
                            <div class="tab-pane fade border-start border-end border-bottom fade active show" id="tab-fila-assuntos-assuntos" role="tabpanel">
                                <div class="col-md-24 p-2 bg-white border">
                                    @include('system.queues.common.assuntos_assuntos')
                                </div>
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
