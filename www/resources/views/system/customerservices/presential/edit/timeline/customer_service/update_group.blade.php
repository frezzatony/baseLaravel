<div class="card mt-2">
    <div class="card-header p-1 bg-light">
        <span class="fw-semibold">{{ $group['label'] ?? '' }}</span>
    </div>
    <div class="card-body p-1 ps-3">
        @foreach ($group['children'] as $node)
            @if ($node['type'] == 'input' && !empty($node['label']))
                <p class="m-0">{{ $node['label'] }}: <span class="fw-semibold">{{ isset($node['show_format']) ? $node['show_format']($node['value']) : $node['value'] }}</span></p>
            @endif

            @if ($node['type'] == 'group' && !empty($node['children']))
                @include('system.customerservices.presential.edit.timeline.customer_service.update_group', ['group' => $node])
            @endif
        @endforeach
    </div>
</div>
