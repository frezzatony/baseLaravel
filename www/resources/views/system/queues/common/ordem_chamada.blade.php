<textarea id="stored_priorities" class="d-none">
    {{ 
        !empty($queue) 
            ? (!empty($queue->call_orders) ? json_encode($queue->call_orders) : null)
            : json_encode(array_values(\App\Enums\Queues\WeightOrder::asArray())) 
    }}
</textarea>

<div class="row p-0 m-0">
    <div class="fs-sm p-2 alert alert-purple">
        <div class="row">
            <div class="col-md-24">
                <i class="ph-chat-centered-dots fs-sm align-middle"></i> Tipos de atendimento com peso mais alto têm preferência no atendimento.
            </div>
            <div class="col-md-24">
                <i class="ph-chat-centered-dots fs-sm align-middle"></i> Atendimentos com mesmo peso têm preferência por ordem chegada.
            </div>
            <div class="col-md-24">
                <i class="ph-chat-centered-dots fs-sm align-middle"></i> Filas sem ordens de chamada têm atendimentos com preferência por ordem de chegada.
            </div>
        </div>
    </div>
</div>
<div class="d-flex justify-content-end m-0 mb-1 sticky-top bg-white">
    <button type="button" class="btn btn-secondary fs-sm px-1 py-0 btn-add-priority">
        <i class="ph-plus fs-sm"></i>Adicionar nova ordem de chamada
    </button>
</div>

<table id="tbl-appendgrid-queues-call-orders" data-name="priorities" class="table-scroll border hb-300"></table>
