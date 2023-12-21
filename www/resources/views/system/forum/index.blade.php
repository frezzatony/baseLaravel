@extends('layouts.system.' . Request::get('_layout'))
@section('content')
    <p>{!! captcha_img('math') !!}</p>
    <div class="card ">
        <div class="card-body d-flex p-1">
            <div class="d-lg-flex align-items-lg-center mb-1">
                <div class="d-block me-lg-3 mb-lg-0 pt-2 ps-2">
                    <img src="/assets/images/misc/90f27d0f-3217-4b42-8a24-00241e53cfab.png" class="rounded" alt="" width="80">
                </div>
                <div class="flex-fill">
                    <h5 class="mb-0">Boas Vindas ao Fórum do Conhecimento</h5>
                    <ul class="list-inline list-inline-bullet text-muted mt-1 mt-lg-0 mb-0">
                        <li class="list-inline-item">Construindo Diálogos e Compartilhando Soluções</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-5 pe-0 me-2 ">
            <div class="sidebar-content">
                <div class="row mb-2">
                    <div class="col-md-24 ps-2">
                        <div class="btn-group">
                            <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ph-chats-circle me-2"></i>
                                <strong>Criar</strong>
                            </button>
                            <div class="dropdown-menu" style="">
                                <a href="#" class="dropdown-item"><i class="ph-users-three me-2"></i> Diálogo</a>
                                <a href="#" class="dropdown-item"><i class="ph-list-checks me-2"></i> Enquete</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="sidebar-section-header border-bottom  p-1 f7">
                        <span class="fw-semibold">Fórum</span>
                    </div>

                    <div class="nav nav-sidebar">
                        <li class="nav-item">
                            <a href="#" class="nav-link mx-1 px-1">
                                <div>
                                    <span class="badge bg-primary ms-auto d-inline pa1 f7 me-1">&nbsp;</span>
                                </div>
                                <div>Diálogos</div>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link mx-1 px-1">
                                <div>
                                    <span class="badge bg-teal ms-auto d-inline pa1 f7 me-1">&nbsp;</span>
                                </div>
                                <div>Enquetes</div>
                            </a>
                        </li>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-18 m-0 p-0">
            <div class="card">
                <div class="sidebar-section-header border-bottom p-1 f7">
                    <span class="fw-semibold">Pesquisar Tópicos</span>
                </div>
                <div class="sidebar-section-body px-1 py-2">
                    <div class="form-control-feedback form-control-feedback-end">
                        <input type="textbox" name="filters-crud_rule_0_value_0"class="form-control py-1 px-1 fs-sm" placeholder="Pesquisar...">
                        <div class="form-control-feedback-icon pa2">
                            <i class="ph-magnifying-glass fs-sm"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body m-0 p-2">
                    @foreach ($topicos_prefeitura as $key => $topico)
                        <div class="col-md-24 p-0 m-0 mb-4 pb-2">
                            <div class="col-md-2 col-lg-1 p-0 pt-2">
                                <img src="/assets/images/demo/users/face<?= $key + 1 ?>.jpg" class="rounded-circle m-0" alt="" style="max-width: 44px; height: 44px;">
                            </div>
                            <div class="col-md-20 ms-1">
                                <div class="f7">
                                    <a href="#">
                                        <strong>{{ $topico['titulo'] }}</strong>
                                    </a>
                                    <span class="text-purple f8">{{ $topico['categoria'] }}</span>
                                </div>
                                <div class="fs-sm">{{ $topico['descritivo'] }}</div>
                            </div>
                            <div class="col-md-2 p-0 f7 pt-3">
                                <div>
                                    <span class="badge bg-{{ $topico['cor'] }} ms-auto">{{ $topico['status'] }}</span>
                                </div>
                                <div>
                                    <i class="ph-chat-circle-text"></i> {{ $topico['comentarios'] }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js-files')
@endsection
