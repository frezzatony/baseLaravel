<div class="row p-0 m-0 mb-1">
    <div class="d-flex justify-content-end m-0 mb-1 p-0">
        <button type="button" class="btn btn-secondary fs-sm pa-1 py-0 btn-crud-contacts-add-contact">
            <i class="ph-plus fs-sm "></i>Adicionar novo contato
        </button>
    </div>
    <textarea class="d-none stored-contacts">{!! $contacts ?? null !!}</textarea>
    <table id="tbl-crud-contacts" class="table-scroll hb-300"></table>
    <div class="crud-contacts-contact-template d-none">
        <input type="hidden" class="uuid">
        <div class="row m-0 mt-2">
            <div class="col-md-10 py-0 px-1 contact_type">
                <label class="form-label fw-semibold fs-sm m-0">Tipo de Contato<span class="text-danger">*</span></label>
                <select class="form-select px-1 pt1 pb1 fs-sm "></select>
            </div>
            <div class="col-md-6 py-0 px-1 mt-3">
                <div class="d-flex align-items-center preferred">
                    <input type="checkbox">
                    <label class="ms-2">Preferencial</label>
                </div>
            </div>
            <div class="col-md-6 py-0 px-1 mt-3">
                <div class="d-flex align-items-center invalid">
                    <input type="checkbox">
                    <label class="ms-2">Inválido</label>
                </div>
            </div>
        </div>
        <div class="row m-0 mt-2">
            <div class="col-md-14 py-0 px-1 contact">
                <label class="form-label fw-semibold fs-sm m-0">Contato<span class="text-danger">*</span></label>
                <input type="text" class="form-control px-1 pt1 pb1 fs-sm">
            </div>
        </div>
        <div class="row m-0 mt-2">
            <div class="col-md-24 py-0 px-1 comments">
                <label class="form-label fw-semibold fs-sm m-0">Comentários</label>
                <input type="text" class="form-control px-1 pt1 pb1 fs-sm">
            </div>
        </div>
        <div class="row m-0 mt-2">
            <div class="col-md-24 p-0">
                <div class="d-flex justify-content-end m-0 px-1">
                    <button type="button" class="btn btn-danger p-1 btn-crud-contacts-contact-remove" data-bs-popup="tooltip" title="Excluir" data-bs-placement="top">
                        <i class="ph-trash fs-sm"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
