@if (empty($node['children']) && !($dropdown_item ?? false))
    <a href="{{ $node['attributes']->get('href') }}" data-load="{{ $node['attributes']->get('hloadref') }}" class="dropdown-item fs-sm p-1 text-nowrap menu-link">
        @if ($level > 0)
            <i class="ph-arrow-square-right fs-xs"></i>&nbsp;
        @endif
        {{ $node['attributes']->get('label') }}
    </a>
@endif

@if (empty($node['children']) && ($dropdown_item ?? false))
    <a href="{{ $node['attributes']->get('href') }}" data-load="{{ $node['attributes']->get('load') }}" class="dropdown-item fs-sm p-1 text-nowrap menu-link">
        @if ($level > 0)
            <i class="ph-arrow-square-right fs-xs"></i>&nbsp;
        @endif
        {{ $node['attributes']->get('label') }}
    </a>
@endif

@if (!empty($node['children']) && !($dropdown_item ?? false))
    @include('layouts.system.module_menu.node-dropdown', [
        'level' => $level,
    ])
@endif

@if (!empty($node['children']) && ($dropdown_item ?? false))
    @include('layouts.system.module_menu.node-submenu-dropdown', [
        'level' => $level,
    ])
@endif
