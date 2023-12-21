 <div class="col-md-24 p-1 mb-1">
     <div class="card m-0 shadow border ">
         <div class="card-body m-0 py-1 px-2">
             <button type="button" class="btn btn-outline-secondary fs-sm px-1 py-0 me-1 btn-add-item">
                 <i class="ph-file fs-sm me-1"></i> Novo Atendimento
             </button>
             @can('system_routine', '"triagem_atendimentos"')
                 <button type="button" class="btn btn-success fs-sm px-1 py-0 btn-provide-ticket" disabled>
                     <i class="ph-ticket fs-sm me-1"></i>Retirada de Ticket
                 </button>
             @endcan
             @can('system_routine', '"efetuar_atendimento_presencial"')
                 <button type="button" class="btn btn-outline-secondary fs-sm px-1 py-0 me-1 btn-my-matters d-none">
                     <i class="ph-chats fs-sm me-1"></i> Meus Assuntos de Atendimento
                 </button>
                 <button type="button" class="btn btn-outline-secondary fs-sm px-1 py-0 me-1 btn-my-priorities d-none">
                     <i class="ph-chats fs-sm me-1"></i> Minhas Prioridades de Atendimento
                 </button>
             @endcan
         </div>
     </div>
 </div>
