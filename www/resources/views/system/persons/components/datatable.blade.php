<div class="col-md-24 p-1">
    <div class="card">
        <div class="card-body p-1">
            <table id="dt-itens" class="table datatable-basic table-bordered table-striped table-hover" data-url="{{ route('api.system.items', 'Person') }}">
                <thead>
                    <tr class="bg-light f8">
                        <th class="select-checkbox mw1"></th>
                        <th class="pa1 mw3">#</th>
                        <th class="pa1 f8">Nome/Razão Social</th>
                        <th class="pa1 f8">Tipo de Pessoa</th>
                        <th class="pa1 f8">CPF/CNPJ</th>
                        <th class="pa1 f8">Ativa</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

@include('layouts.common.datatable-files')
