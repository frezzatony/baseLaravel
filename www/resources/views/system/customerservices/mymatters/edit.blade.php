@extends('layouts.system.' . Request::get('_layout'))

@section('content')
    <form id="form-my-matters" autcomplete="off">
        @csrf
        <input type="hidden" id="queue_id" name="queue_id" value="{{ $queue->id }}">
        <div class="p-2 bg-white">
            <div class="row p-0 m-0 mb-1">
                <div class="col-md-24 py-0 px-1">
                    <label for="queue_description" class="form-label fw-semibold fs-sm m-0">Fila de Atendimento</label>
                    <input type="text" id="queue_description" class="form-control px-1 pt1 pb1 fs-sm" value="{{ $queue->description }}" readonly>
                </div>
            </div>
            <div class="row p-1 m-0 mb-1">
                <div class="card m-0 p-0">
                    <div class="card-header d-flex pa1 bg-light ">
                        <h5 class="fs-sm  mb-0 ">Seus Assuntos para Atendimento</h5>
                    </div>
                    <div class="card-body m-0 p-0">
                        <textarea id="queue_user_matters" class="d-none">{{ !empty($queue_user_matters) ? $queue_user_matters->toJson() : '' }}</textarea>
                        <textarea id="user_matters" class="d-none">{{ !empty($user_matters) ? json_encode($user_matters) : '' }}</textarea>
                        <div class="col-md-24">
                            <small>Selecione assuntos para os quais deseja efetuar atendimentos:</small>
                        </div>
                        <div class="col-md-24">
                            <div class="matters d-none" style="height: 30vh; overflow-y: auto">
                                <div class="tree-checkbox-hierarchical p-1">
                                    <ul class="mb-0">
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @include('layouts.common.modal.footer_buttons_crud')
@endsection
