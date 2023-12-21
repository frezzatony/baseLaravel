<div class="card-body p-1">
    <div class="col-xl-4 col-md-24 fs-sm">
        <ul class="list list-unstyled mb-0">
            <p class="m-0">Por: <span class="fw-semibold">{{ \App\Helpers\StringHelper::nameAbbreviated($activity->user_name ?? ($activity->social_name ?? 'Pessoa Interessada')) }}</span></p>
            <p class="m-0">Ação: <span class="fw-semibold">Atualização de dados</span></p>
            @foreach (\App\Helpers\CrudHelper::getFormDataStructureWithValues($form_structure, json_decode($activity->activity->values, true)) as $group)
                @include('system.customerservices.presential.edit.timeline.customer_service.update_group', ['group' => $group])
            @endforeach
        </ul>
    </div>
</div>
