<?php

namespace App\Http\Controllers\System;

use App\Helpers\Crud\System\Holiday\HolidayHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\System\HolidaySaveRequest;
use App\Services\System\Holiday\HolidayService;
use Illuminate\Http\Request;

class HolidaysController extends Controller
{
    public function index()
    {
        return view('system.holidays.index', $this->getViewData(true));
    }

    public function create()
    {
        return view('system.holidays.create');
    }

    public function store(HolidaySaveRequest $request)
    {
        $saveResponse = HolidayService::store($this->getSaveData($request));

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
        $data = $this->getViewData(false, $id);

        if (empty($data['holiday'])) {
            abort(404);
        }

        return view('system.holidays.edit', $data);
    }

    public function update(HolidaySaveRequest $request)
    {
        $viewData = $this->getViewData(false, $request->get('id'));
        if (empty($viewData['holiday'])) {
            abort(404);
        }

        $saveResponse = HolidayService::update($viewData['holiday'], $this->getSaveData($request));

        if ($saveResponse == false) {
            return response([
                'status'    =>  'error',
                'message'   =>  'Houve um erro ao executar a operação.',
            ], 200);
        }

        if (($saveResponse['status'] ?? null) == true) {
            return response([
                'status'    =>  'success',
                'id'        =>  $saveResponse['id'],
                'message'   =>  'Item atualizado.',
            ], 200);
        }
    }

    public function destroy(Request $request)
    {
        $holidays = (!empty($request->get('ids')) && is_array($request->get('ids')))
            ? HolidayService::findAllByFilters([
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
            $destroyResponse = HolidayService::destroy($module->id);
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
            'holiday'   =>  !empty((int)$id) ? HolidayService::findById($id) : null,
        ];
        if ($complete) {
            $searchFilters = HolidayHelper::searchFilters();
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
                    'default_filters'   =>  HolidayHelper::defaultSearchFilters(),
                ]
            );
        }
        return $data;
    }

    private function getSaveData(Request $request)
    {
        return [
            'holiday'   =>  $request->only([
                'description', 'annual', 'date', 'type', 'optional', 'time_start', 'time_end'
            ])
        ];
    }
}
