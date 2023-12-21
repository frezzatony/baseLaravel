<div class="navbar navbar-expand-lg navbar-static shadow py-1 px-0">
    <div class="container-fluid">
        <div class="col-md-16 p-0">
            <div class="col-md-10 p-0">
                <div class="col-md-3 p-0 "><i class="{{ $_active_module->icon }} ph-2x "></i></div>
                <div class="col-md-19 p-0">
                    <div class="fs-sm">Módulo</div>
                    <div><strong>{{ App\Helpers\StringHelper::nameComplete($_active_module->name) }}</strong></div>
                </div>
            </div>
        </div>
        <ul class="nav order-1 ">
            <li class="nav-item nav-item-dropdown-lg dropdown" data-bs-popup="popover" data-bs-placement="bottom" data-bs-trigger="hover" data-bs-content="Atalhos Favoritos">
                <a href="#" class="navbar-nav-link navbar-nav-link-icon rounded-pill notifications-dropdown-resume-button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                    <i class="ph-star"></i>
                </a>
            </li>
            <li class="nav-item nav-item-dropdown-lg dropdown " data-bs-popup="popover" data-bs-placement="bottom" data-bs-trigger="hover" data-bs-content="Notificações">
                <a href="#" class="navbar-nav-link navbar-nav-link-icon rounded-pill notifications-dropdown-resume-button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                    <i class="ph-bell notifications-icon"></i>
                    <span class="badge bg-green text-white position-absolute top-0 end-0 translate-middle-top zindex-1 rounded-pill mt-2 me-2 notifications-unread"></span>
                </a>
                @include('layouts.system.notifications.notifications')
            </li>
            <li class="nav-item nav-item-dropdown-lg dropdown" data-bs-popup="popover" data-bs-placement="bottom" data-bs-trigger="hover" data-bs-content="Fila de Impressão">
                <a href="#" class="navbar-nav-link navbar-nav-link-icon rounded-pill notifications-dropdown-resume-button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                    <i class="ph-printer"></i>
                </a>
            </li>
            <li class="nav-item nav-item-dropdown-lg dropdown" data-bs-popup="popover" data-bs-placement="bottom" data-bs-trigger="hover" data-bs-content="Minha conta">
                <a href="#" class="navbar-nav-link align-items-center rounded px-1 pt-2" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="d-none d-lg-inline-block mx-lg-2"><i class="ph-user-circle"></i> {{ App\Helpers\StringHelper::nameAbbreviated(auth()->user()->name) }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end">
                    <a href="{{ route('system.users.detail') }}" class="dropdown-item">Meus dados</a>
                    <a href="{{ route('public.auth.logout') }}" class="dropdown-item">Sair</a>
                </div>
            </li>
        </ul>
    </div>
</div>
