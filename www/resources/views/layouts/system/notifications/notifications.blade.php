<div class="dropdown-menu dropdown-menu-end wmin-lg-500 p-0 notifications-dropdown-resume" data-bs-popper="static">
    <div class="d-flex align-items-center py-1 px-1 bg-silver">
        <h6 class="mb-0 fs-sm">Notificações não lidas</h6>
    </div>
    <div class="row p-0 m-0">
        <div class="col-md-24 p-0 m-0">
            @include('layouts.system.notifications.datatable')
        </div>
    </div>


    <div class="row p-1 m-0 mt-2 border-top pt-1 fs-sm h-80px notifications-read-area d-none">
    </div>

    <div class="d-flex border-top py-2 px-3">
        <a href="#" class="text-body fs-sm notifications-mark-all-as-read">
            <i class="ph-checks me-1"></i>
            Marcar Todas como Lidas
        </a>
        <a href="/system/notifications" class="text-body ms-auto fs-sm notifications-show-all" data-load="">
            Minhas Notificações
            <i class="ph-arrow-circle-right ms-1"></i>
        </a>
    </div>
</div>
