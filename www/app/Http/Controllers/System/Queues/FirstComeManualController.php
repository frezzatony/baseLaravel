<?php

namespace App\Http\Controllers\System\Queues;

use App\Http\Controllers\Controller;
use App\Http\Requests\System\Queue\FirstComeManualSaveRequest;
use App\Services\System\AttendanceUnit\AttendanceUnitService;
use App\Services\System\Queue\FirstComeManualService;
use Illuminate\Http\Request;

class FirstComeManualController extends Controller
{
    public function create()
    {
        return view('system.queues.firstcomemanual.edit', $this->getViewData());
    }

    public function store(FirstComeManualSaveRequest $request)
    {
        $saveResponse = FirstComeManualService::store($this->getSaveData($request));

        if ($saveResponse == false) {
            return response()->json([
                'status'    =>  'error',
                'message'   =>  'Houve um erro ao executar a operação.',
            ]);
        }

        if (($saveResponse['status'] ?? null) == true) {
            return response()->json([
                'status'    =>  'success',
                'id'        =>  $saveResponse['id'],
                'message'   =>  'Item criado.',
            ]);
        }
    }

    public function edit(int $id)
    {
        $viewData = $this->getViewData($id);
        if (empty($viewData['queue'])) {
            abort(404);
        }
        return view('system.queues.firstcomemanual.edit', $viewData);
    }

    public function update(FirstComeManualSaveRequest $request)
    {
        $viewData = $this->getViewData($request->get('id'));
        if (empty($viewData['queue'])) {
            abort(404);
        }

        $saveResponse = FirstComeManualService::update($viewData['queue'], $this->getSaveData($request));

        if ($saveResponse == false) {
            return response([
                'status'    =>  'error',
                'message'   =>  'Houve um erro ao executar a operação.',
            ], 200);
        }

        if (($saveResponse['status'] ?? false) == true) {
            return response([
                'status'    =>  'success',
                'id'        =>  $saveResponse['id'],
                'message'   =>  'Item atualizado.',
            ], 200);
        }
    }

    private function getViewData($id = null)
    {
        $data = [
            'queue'             =>  !empty((int)$id) ? FirstComeManualService::findById($id) : null,
            'attendance_units'  =>  AttendanceUnitService::findAllByFilters([
                'is_active' =>  'true'
            ])
        ];
        return $data;
    }

    private function getSaveData(Request $request)
    {
        $saveData = [
            'queue'         =>  $request->only([
                'is_active', 'description', 'attendance_units_id', 'point_quantity', 'point_name', 'ticket_prefix',
                'ticket_withdrawal', 'ticket_sequence', 'ticket_reset', 'reset_tickets_counter', 'max_daily_tickets'
            ]),
            'call_orders'   =>  $request->input('priorities'),
            'attendants'    =>  $request->input('attendants'),
            'managers'      =>  $request->input('managers'),
            'matters'        =>  $request->input('matters'),
        ];
        return $saveData;
    }
}
