<div class="page-header  page-header-static shadow" style="background-color: #f5f5f5;">
    <div class="page-header-content d-lg-flex border">
        <div class="d-flex">
            <div class="d-lg-flex mb-2 mb-lg-0 module-menu">
                @foreach ($_module_menu as $node)
                    @include('layouts.system.module_menu.node', [
                        'level' => 0,
                    ])
                @endforeach
            </div>
        </div>
    </div>
</div>
