<?php

namespace App\Http\Controllers\System\Notifications;

use App\Helpers\Crud\System\Notification\NotificationHelper;
use App\Helpers\CrudHelper;
use App\Http\Controllers\Controller;
use App\Services\System\Notification\NotificationService;
use App\Services\System\WebsocketService;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    public function index()
    {
        return view('system.notifications.notifications.index', $this->getViewData(true));
    }

    public function view($id)
    {
        $viewData = $this->getViewData(false, $id);
        if (!empty($viewData['notification'])) {
            if (empty($viewData['notification']->read_at)) {
                NotificationService::markAsRead($id);
                WebsocketService::message('notifications.user.' . auth()->user()->api_token, json_encode([
                    'action'    =>  'read_message',
                ]));
            }
            return view('system.notifications.notifications.view', $viewData);
        }
    }

    public function destroy(Request $request)
    {
        $holidays = (!empty($request->get('ids')) && is_array($request->get('ids')))
            ? NotificationService::findAllByFilters([
                'id'    =>  [
                    [
                        'operator'  =>  'in',
                        'value'     =>  implode(',', $request->get('ids'))
                    ]
                ]
            ])
            : null;
        if ($holidays->count() != count($request->get('ids'))) {
            return response([
                'message'   =>  'Validação de dados violada.',
            ], 402);
        }

        $destroyReturn = [
            'deleted'           =>  [],
            'error'             =>  [],
            'console_message'   =>  '',
        ];

        foreach ($holidays as $module) {
            $destroyResponse = NotificationService::destroy($module->id);
            if ($destroyResponse['status'] == false) {
                $destroyReturn['error'][] = $module->id;
                if (!empty($destroyResponse['used_in'])) {
                    $destroyReturn['console_message'] .= ($destroyReturn['console_message'] ? '<br>' : '');
                    $destroyReturn['console_message'] .= "O item de código [{$module->id}] é utilizado por outros registros.";
                }
            }
            if ($destroyResponse['status'] == true) {
                $destroyReturn['deleted'][] = $module->id;
            }
        }

        if (count($destroyReturn['error']) && count($destroyReturn['deleted'])) {
            $destroyReturn['status']  = 'info';
            $destroyReturn['message'] = 'Alguns itens não puderam ser excluídos';
        }
        if (count($destroyReturn['error']) && !count($destroyReturn['deleted'])) {
            $destroyReturn['status']  = 'error';
            $destroyReturn['message'] = (count($destroyReturn['error']) > 1 ? 'Os itens não poderam ser excluídos' : 'O item não pôde ser excluído.');
        }
        if (!count($destroyReturn['error']) && count($destroyReturn['deleted'])) {
            $destroyReturn['status']  = 'success';
            $destroyReturn['message'] = (count($destroyReturn['deleted']) > 1 ? 'Itens excluídos' : 'Item excluído.');
        }

        $destroyReturn['message'] .= $destroyReturn['console_message'] ? ('<br><small>' . $destroyReturn['console_message']) . '</small>' : '';
        return response($destroyReturn, 200);
    }

    private function getViewData($complete = false, $id = null)
    {
        $data = [
            'notification'  =>  !empty($id) ? NotificationService::findById($id) : null,
        ];
        if ($complete) {
            $searchFilters = CrudHelper::getHelperViewFilters(NotificationHelper::searchFilters());
            $data = array_merge(
                $data,
                [
                    'dynamic_filters'   =>  array_map(function ($key, $filter) {
                        return [
                            'id'            =>  $key,
                            'label'         =>  $filter['label'],
                            'input_type'    =>  $filter['type'],
                        ];
                    }, array_keys($searchFilters), $searchFilters),
                    'default_filters'   =>  NotificationHelper::defaultSearchFilters(),
                ]
            );
        }
        return $data;
    }
}
