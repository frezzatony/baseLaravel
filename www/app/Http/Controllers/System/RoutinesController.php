<?php

namespace App\Http\Controllers\System;

use App\Helpers\Crud\System\RoutineHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\System\RoutineSaveRequest;
use App\Services\System\RoutineService;
use Illuminate\Http\Request;

class RoutinesController extends Controller
{
    public function index()
    {
        return view('system.routines.index', $this->getViewData(true));
    }

    public function create()
    {
        return view('system.routines.create');
    }

    public function store(RoutineSaveRequest $request)
    {
        $saveResponse = RoutineService::store($this->getSaveData($request));

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
                'actions'   =>  !empty($saveResponse['actions']) ? $saveResponse['actions']->pluck('id')->toJson() : [],
                'message'   =>  'Item criado.',
            ]);
        }
    }


    public function edit(int $id)
    {
        $viewData = $this->getViewData(false, $id);

        if (empty($viewData['routine'])) {
            abort(404);
        }

        return view('system.routines.edit', $viewData);
    }

    public function update(RoutineSaveRequest $request)
    {
        $viewData = $this->getViewData(false, $request->get('id'));
        if (empty($viewData['routine'])) {
            abort(404);
        }

        $saveResponse = RoutineService::update($viewData['routine'], $this->getSaveData($request));

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
                'actions'   =>  !empty($saveResponse['actions']) ? $saveResponse['actions']->pluck('id') : [],
                'message'   =>  'Item atualizado.',
            ], 200);
        }
    }

    public function destroy(Request $request)
    {
        $routines = (!empty($request->get('ids')) && is_array($request->get('ids')))
            ? RoutineService::findAllByFilters([
                'id'    =>  [
                    [
                        'operator'  =>  'in',
                        'value'     =>  implode(',', $request->get('ids'))
                    ]
                ]
            ])
            : null;
        if ($routines->count() != count($request->get('ids'))) {
            return response([
                'message'   =>  'Validação de dados violada.',
            ], 402);
        }

        $destroyReturn = [
            'deleted'           =>  [],
            'error'             =>  [],
            'console_message'   =>  '',
        ];

        foreach ($routines as $routine) {
            $destroyResponse = RoutineService::destroy($routine->id);
            if ($destroyResponse['status'] == false) {
                $destroyReturn['error'][] = $routine->id;
                if (!empty($destroyResponse['used_in'])) {
                    $destroyReturn['console_message'] .= ($destroyReturn['console_message'] ? '<br>' : '');
                    $destroyReturn['console_message'] .= "O item de código [{$routine->id}] é utilizado por outros registros.";
                }
            }
            if ($destroyResponse['status'] == true) {
                $destroyReturn['deleted'][] = $routine->id;
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
            'routine'   =>  !empty((int)$id) ? RoutineService::findById($id) : null,
        ];
        if ($complete) {
            $searchFilters = RoutineHelper::searchFilters();
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
                    'default_filters'   =>  RoutineHelper::defaultSearchFilters(),
                ]
            );
        }
        return $data;
    }

    private function getSaveData(Request $request)
    {
        $data =  [
            'routine'  =>  $request->only([
                'is_active', 'name', 'slug', 'modules_id'
            ]),
            'actions'  =>  $request->get('actions'),
        ];

        return $data;
    }
}
