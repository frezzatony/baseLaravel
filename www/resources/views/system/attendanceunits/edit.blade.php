@extends('layouts.system.' . Request::get('_layout'))

@section('content')
    <form id="form-attendanceunit" autcomplete="off">
        @csrf
        <input type="hidden" name="id" value="{{ $attendance_unit->id }}">
        <div class="d-lg-flex p-2 bg-light">
            <ul class="nav nav-tabs nav-tabs-vertical nav-tabs-vertical-start wmin-lg-200 mt-4 mb-lg-0 " role="tablist">
                <li class="nav-item bg-white" role="presentation">
                    <a href="#tab-attendanceunit-cadastro" class="nav-link fs-sm p-1 m-0 active" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        Cadastro
                    </a>
                </li>
                <li class="nav-item bg-white" role="presentation">
                    <a href="#tab-attendanceunit-endereco" class="nav-link fs-sm p-1 m-0" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        Endereço
                    </a>
                </li>
                <li class="nav-item bg-white" role="presentation">
                    <a href="#tab-attendanceunit-gestores" class="nav-link fs-sm p-1 m-0" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        Gestores
                    </a>
                </li>
                <li class="nav-item bg-white" role="presentation">
                    <a href="#tab-attendanceunit-pagina" class="nav-link fs-sm p-1 m-0" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        Página web
                    </a>
                </li>
                <li class="nav-item bg-white" role="presentation">
                    <a href="#tab-attendanceunit-anexos" class="nav-link fs-sm p-1 m-0" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        Anexos
                    </a>
                </li>
            </ul>

            <div class="tab-content flex-lg-fill">
                <div class="tab-pane fade active show" id="tab-attendanceunit-cadastro" role="tabpanel">
                    <ul class="nav nav-tabs nav-tabs-highlight">
                        <li class="nav-item"><a href="tab-attendanceunit-cadastro-unidade_atendimento" class="nav-link fs-sm p-1 m-0 active">Unidade de Atendimento</a></li>
                    </ul>
                    <div class="tab-content flex-lg-fill">
                        <div class="tab-pane fade border-start border-end border-bottom active show" id="tab-attendanceunit-cadastro-unidade_atendimento" role="tabpanel">
                            <div class="col-md-24 p-2 bg-white">
                                @include('system.attendanceunits.edit.cadastro_unidadeatendimento')
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="tab-attendanceunit-endereco" role="tabpanel">
                    <ul class="nav nav-tabs nav-tabs-highlight">
                        <li class="nav-item"><a href="tab-attendanceunit-endereco-endereco" class="nav-link fs-sm p-1 m-0 active">Endereço</a></li>
                    </ul>
                    <div class="tab-content flex-lg-fill">
                        <div class="tab-pane fade border-start border-end border-bottom active show endereco" id="tab-attendanceunit-endereco-endereco" role="tabpanel">
                            <div class="col-md-24 p-2 bg-white">
                                @include('layouts.common.crud.address', ['address_required' => true, 'address' => $attendance_unit->addresses->first()])
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade " id="tab-attendanceunit-gestores" role="tabpanel">
                    <ul class="nav nav-tabs nav-tabs-highlight">
                        <li class="nav-item"><a href="tab-attendanceunit-gestores-usuarios" class="nav-link fs-sm p-1 m-0 active">Usuários Gestores</a></li>
                    </ul>
                    <div class="tab-content flex-lg-fill">
                        <div class="tab-pane fade border-start border-end border-bottom active show" id="tab-attendanceunit-gestores-usuarios" role="tabpanel">
                            <div class="col-md-24 p-2 bg-white vh-50">
                                @include('system.attendanceunits.common.gestores_usuarios')
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade " id="tab-attendanceunit-pagina" role="tabpanel">
                    <ul class="nav nav-tabs nav-tabs-highlight">
                        <li class="nav-item"><a href="tab-attendanceunit-pagina-pagina" class="nav-link fs-sm p-1 m-0 active">Página Web Pública</a></li>
                    </ul>
                    <div class="tab-content flex-lg-fill">
                        <div class="tab-pane fade border-start border-end border-bottom active show" id="tab-attendanceunit-pagina-pagina" role="tabpanel">
                            <div class="col-md-24 p-2 bg-white">
                                @include('system.attendanceunits.common.pagina_pagina')
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade " id="tab-attendanceunit-anexos" role="tabpanel">
                    <ul class="nav nav-tabs nav-tabs-highlight">
                        <li class="nav-item"><a href="tab-attendanceunit-anexos-anexos" class="nav-link fs-sm p-1 m-0 active">Anexos</a></li>
                    </ul>
                    <div class="tab-content flex-lg-fill">
                        <div class="tab-pane fade border-start border-end border-bottom active show" id="tab-attendanceunit-anexos-anexos" role="tabpanel">
                            <div class="col-md-24 p-2 bg-white">
                                @include('system.attendanceunits.common.attachments')
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
