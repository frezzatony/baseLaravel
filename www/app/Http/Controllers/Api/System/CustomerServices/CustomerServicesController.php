<?php

namespace App\Http\Controllers\Api\System\CustomerServices;

use App\Enums\Queues\Type;
use App\Http\Controllers\Controller;
use App\Http\Requests\System\CustomerServices\CustomerServicesCallRequest;
use App\Http\Requests\System\CustomerServices\CustomerServicesCancelRequest;
use App\Services\System\Queue\FirstComeManualBookService;
use App\Services\System\Queue\FirstComeTotemBookService;
use App\Services\System\Queue\QueueService;
use App\Services\System\WebsocketService;
use Illuminate\Support\Facades\Auth;

class CustomerServicesController extends Controller
{
    public function callBook(CustomerServicesCallRequest $request)
    {
        $queue = QueueService::findById($request->input('queue'));
        switch ($queue->type) {
            case Type::FIRST_COME_TOTEM['value']:
                $book = FirstComeTotemBookService::callBook($queue, $request->input('book'), Auth::user()->id, $request->input('service_point'));
                break;
            case Type::FIRST_COME_MANUAL['value']:
                $book = FirstComeManualBookService::callBook($queue, $request->input('book'), Auth::user()->id, $request->input('service_point'));
                break;
        }

        if ($book == false) {
            return response([
                'status'    =>  'error',
                'message'   =>  'Não foi possível chamar o atendimento.',
            ], 400);
        }

        WebsocketService::message('queues.book', json_encode([
            'action'    =>  'calling',
            'queue'     =>  $queue->id,
        ]));
        return response([
            'status'    =>  'success',
            'book'      =>  $book['book'],
        ], 200);
    }

    public function cancelBook(CustomerServicesCancelRequest $request)
    {
        $queue = QueueService::findById($request->input('queue'));
        switch ($queue->type) {
            case Type::FIRST_COME_TOTEM['value']:
                $book = FirstComeTotemBookService::cancelBook($queue, $request->input('book'), Auth::user()->id, $request->input('justification'));
                break;
            case Type::FIRST_COME_MANUAL['value']:
                $book = FirstComeManualBookService::cancelBook($queue, $request->input('book'), Auth::user()->id, $request->input('justification'));
                break;
        }

        if ($book == false) {
            return response([
                'status'    =>  'error',
                'message'   =>  'Não foi possível cancelar o atendimento.',
            ], 400);
        }

        WebsocketService::message('queues.book', json_encode([
            'action'    =>  'canceled',
            'queue'     =>  $queue->id,
        ]));
        return response([
            'status'    =>  'success',
        ], 200);
    }
}
