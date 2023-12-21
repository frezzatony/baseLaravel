<?php

namespace App\Http\Controllers\System\CustomerServices;

use App\Helpers\Crud\System\CustomerService\Forms\DefaultHelper;
use App\Helpers\CrudHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\System\CustomerServices\CustomerServicesSaveRequest;
use App\Services\System\CustomerService\CustomerServicePresentialService;
use App\Services\System\Queue\QueueService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatePresentialController extends Controller
{
    public function edit(int $idQueue, int $idCustomerService)
    {
        $customerService = CustomerServicePresentialService::findByQueueIdAndId($idQueue, $idCustomerService);
        return view('system.customerservices.presential.rate', [
            'customer_service'  =>  $customerService,
        ]);
    }

    public function update(CustomerServicesSaveRequest $request)
    {
        $customerService = CustomerServicePresentialService::findByQueueIdAndId($request->input('queue_id'), $request->get('id'));
        if (empty($customerService)) {
            abort(404);
        }

        $queue = QueueService::findById($request->input('queue_id'));
        $saveResponse = CustomerServicePresentialService::update($customerService, $queue, Auth::user()->id, $this->getSaveData($request));

        if ($saveResponse == false) {
            return response([
                'status'    =>  'error',
                'message'   =>  'Houve um erro ao executar a operação.',
            ], 200);
        }

        if (($saveResponse['status'] ?? null) == 'nothing_to_update') {
            return response([
                'status'    =>  'info',
                'id'        =>  $saveResponse['id'],
                'message'   =>  'Não há alterações para salvar.',
            ], 200);
        }

        if (($saveResponse['status'] ?? null) == 'updated') {
            return response([
                'status'                    =>  'success',
                'id'                        =>  $saveResponse['customer_service']->customer_service_id,
                'timeline_activity_html'    =>  view('system.customerservices.presential.edit.timeline.node', [
                    'form_structure'    =>  CrudHelper::getFormDataStructureWithValues(DefaultHelper::inputs(), json_decode($saveResponse['customer_service']->activity->first()->activity->values, true)),
                    'activity'          =>  $saveResponse['customer_service']->activity->first(),
                ])->render(),
                'message'                   =>  'Item atualizado.',
            ], 200);
        }
    }

    private function getSaveData(Request $request)
    {
        return [
            'customer_service'    =>  $request->only([
                'tags', 'problem_description', 'resolution_description'
            ])
        ];
    }
}
