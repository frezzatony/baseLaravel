<?php

namespace App\Http\Controllers\System\CustomerServices;

use App\Http\Controllers\Controller;
use App\Services\System\Queue\QueueMatterUserAttendantService;
use App\Services\System\Queue\QueueService;
use App\Services\System\UserService;
use Illuminate\Http\Request;

class MyMattersController extends Controller
{

    public function edit(int $idQueue)
    {
        $queue = QueueService::findById($idQueue);
        if (!$queue) {
            return response()->json([
                'status'    =>  'error',
                'message'   =>  'Os dados informados para a fila são inválidos.',
            ], 500);
        }

        return view('system.customerservices.mymatters.edit', [
            'queue'                 =>  $queue,
            'queue_user_matters'    =>  QueueMatterUserAttendantService::findAllMattersByQueueIdAndUserId((int)$idQueue, auth()->user()->id),
            'user_matters'          =>  data_get(json_decode(auth()->user()->attributes), "queues_matters.{$queue->id}"),
        ]);
    }

    public function update(Request $request)
    {
        $queue = QueueService::findById($request->queue_id);
        if (!$queue) {
            return response()->json([
                'status'    =>  'error',
                'message'   =>  'Os dados informados para a fila são inválidos.',
            ], 500);
        }

        $queueUserMatters = QueueMatterUserAttendantService::findAllMattersByQueueIdAndUserId((int)$queue->id, auth()->user()->id);
        if (!$queueUserMatters) {
            return response()->json([
                'status'    =>  'error',
                'message'   =>  'Os dados informados para a fila são inválidos.',
            ], 500);
        }
        $userMatters = json_decode(auth()->user()->attributes, true)['queues_matters'] ?? [];
        $userMatters[$queue->id] = !empty($request->matters) ? explode(',', $request->matters) : '[]';
        $saveResponse = UserService::updateAttributes(auth()->user(), [
            'queues_matters'    =>  $userMatters,
        ]);

        if ($saveResponse == false) {
            return response([
                'status'    =>  'error',
                'message'   =>  'Houve um erro ao executar a operação.',
            ], 200);
        }

        if (($saveResponse['status'] ?? null) == true) {
            return response([
                'status'    =>  'success',
                'message'   =>  'Assuntos atualizados.',
            ], 200);
        }
    }
}
