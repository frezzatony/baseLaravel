<div class="sidebar sidebar-dark sidebar-main sidebar-expand-lg sidebar-main-resized">
    <div class="sidebar-section bg-black bg-opacity-10 border-bottom border-bottom-white border-opacity-10">
        <a href="#" class="d-block align-items-center py-2 sidebar-main-resize">
            <div class="sidebar-logo d-flex justify-content-center align-items-center">

                <img src="/assets/images/logo_minimo_civis.png" class="sidebar-logo-icon" alt="" width="38">
                <img src="/assets/images/logo_civis.png" class="sidebar-resize-hide ms-2" alt="Civis" width="140">
            </div>
        </a>
    </div>

    <div class="sidebar-content">
        <div class="sidebar-section">
            <ul class="nav nav-sidebar" data-nav-type="accordion">
                <li class="nav-item-header">
                    <div class="text-uppercase fs-sm lh-sm opacity-50 sidebar-resize-hide">Módulos</div>
                    <i class="ph-dots-three sidebar-resize-show"></i>
                </li>
                @foreach ($_user_modules as $module)
                    <li class="nav-item">
                        <a href="{{ $module->id == $_active_module->id ? '#' : url('/system/modules/change/' . $module->slug) }}" class="nav-link">
                            <i class="{{ $module->icon }}"></i>
                            <span>{{ App\Helpers\StringHelper::nameComplete($module->name) }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

    </div>
</div>
