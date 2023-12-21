 <div class="col-md-24 p-1 mb-1">
     <div class="card m-0 shadow border ">
         <div class="card-body m-0 py-1 px-2">
             <button type="button" href="{{ route('system.modules.create') }}" class="btn btn-outline-secondary fs-sm px-1 py-0 btn-add-item">
                 <i class="ph-file fs-sm"></i>Novo
             </button>
             <div class="btn-group">
                 <button type="button" class="btn btn btn-outline-secondary fs-sm px-1 py-0 dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Gerenciar</button>
                 <div class="dropdown-menu p-1" style="">
                     <button class="dropdown-item text-start fs-sm px-1 py-0 btn-remove-item" disabled><i class="ph-trash fs-sm me-1"></i>Excluir</button>
                 </div>
             </div>
         </div>
     </div>
 </div>
