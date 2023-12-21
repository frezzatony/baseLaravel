<div class="btn-group">
    <a href="#" class="btn btn-light border-0 dropdown-toggle p-1 fs-sm text-nowrap" data-bs-toggle="dropdown">
        @if ($level > 0)
            <i class="ph-arrow-square-right fs-xs"></i>&nbsp;
        @endif
        {{ $node['attributes']->get('label') }}
    </a>
    <div class="dropdown-menu">
        @foreach ($node['children'] as $childNode)
            @include('layouts.system.module_menu.node', [
                'level' => $level + 1,
                'node' => $childNode,
                'dropdown_item' => !empty($childNode['children']),
            ])
        @endforeach
    </div>
</div>
