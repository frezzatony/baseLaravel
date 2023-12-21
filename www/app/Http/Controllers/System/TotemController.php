<?php

namespace App\Http\Controllers\System;

use App\Helpers\StringHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\System\Queue\FirstComeTotemBookRequest;
use App\Services\System\Queue\FirstComeTotemBookService;
use App\Services\System\Queue\FirstComeTotemService;
use App\Services\System\WebsocketService;
use Illuminate\Http\Request;

class TotemController extends Controller
{
    public function index(Request $request, $keyTotem)
    {
        if (!file_exists(app_path("Helpers/Totems/Totem{$keyTotem}Helper.php"))) {
            abort(404);
        }

        $totemHelper = new ('App\\Helpers\\Totems\\' . str_replace('/', '\\', "Totem{$keyTotem}Helper"));
        $screensHtml = $this->getScreensHtml($keyTotem, $totemHelper->screens());

        return view('system.totem.totem', [
            'screens_html'  =>  $screensHtml,
        ]);
    }

    public function book(FirstComeTotemBookRequest $request)
    {
        $queue = FirstComeTotemService::findById($request->input('queue'));
        $book = FirstComeTotemBookService::book($queue, $request->input('matter'), $request->input('call_order'));
        if ($book != false && $book['status'] == true) {
            WebsocketService::message('queues.book', json_encode([
                'action'    =>  'created',
                'queue'     =>  $queue->id,
            ]));
        }
    }

    private function getScreensHtml($keyTotem, $screens)
    {
        $viewsHtml = '';
        foreach ($screens as $key => $screen) {
            $screen['id'] = $key;

            if (empty($screen['queues_id']) && !empty($screen['buttons'])) {
                $viewsHtml .= ($viewsHtml ? "\n" : '') . $this->getHtmlScreenButtons($screen);
            }

            if (!empty($screen['queues_id'])) {
                $viewsHtml .= ($viewsHtml ? "\n" : '') . $this->getHtmlScreenQueue($screen);
            }
        }
        return $viewsHtml;
    }

    private function getHtmlScreenButtons($screen)
    {
        if (sizeof($screen['buttons']) <= 4) {
            return view('system.totem.screen.4_buttons', $screen);
        }
    }

    private function getHtmlScreenQueue($screen)
    {
        $queue = FirstComeTotemService::findById($screen['queues_id']);

        $screenHtml = '';
        foreach ($queue->matters->sortBy('description') as $matter) {
            $matterScreen = [
                'id'        =>  StringHelper::uuid(),
                'subtitle'  =>  'Selecione a prioridade de atendimento',
                'buttons'   =>  $queue->call_orders->map(function ($callOrder) use ($queue, $matter) {
                    return [
                        'title' =>  StringHelper::upper($callOrder->description),
                        'book'  =>  [
                            'queue'         =>  $queue->id,
                            'matter'         =>  $matter->id,
                            'call_order'    =>  $callOrder->id,
                        ],
                    ];
                })->toArray(),
            ];
            $screenHtml .= ($screenHtml ? "\n" : '') . $this->getHtmlScreenButtons($matterScreen);

            $screen['buttons'][] = [
                'title'     =>  StringHelper::upper($matter->description),
                'target'    =>  $matterScreen['id'],
            ];
        }
        $screenHtml .= ($screenHtml ? "\n" : '') . $this->getHtmlScreenButtons($screen);
        return $screenHtml;
    }
}
