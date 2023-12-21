<div class="dropdown-submenu">
    <a href="#" class="dropdown-item fs-sm p-1 text-nowrap">
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
