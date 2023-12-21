<div class="col-md-24 p-1">
    <div class="card">
        <div class="card-body p-1">
            <table id="dt-itens" class="table datatable-basic table-bordered table-striped table-hover" data-url="{{ route('api.system.items', 'Notification') }}?path=System/Notification">
                <thead>
                    <tr class="bg-light f8">
                        <th class="select-checkbox mw1"></th>
                        <th class="pa1 f8">Remetente</th>
                        <th class="pa1 f8">Título</th>
                        <th class="pa1 f8">Resumo</th>
                        <th class="pa1 f8">Data Envio</th>
                        <th class="pa1 f8">Data Leitura</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

@include('layouts.common.datatable-files')
