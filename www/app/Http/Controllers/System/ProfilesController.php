<?php

namespace App\Http\Controllers\System;

use App\Helpers\Crud\System\ProfileHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\System\ProfileSaveRequest;
use App\Services\System\ProfileService;
use Illuminate\Http\Request;

class ProfilesController extends Controller
{

    public function index()
    {
        return view('system.profiles.index', $this->getViewData(true));
    }

    public function create()
    {
        return view('system.profiles.create',);
    }

    public function store(ProfileSaveRequest $request)
    {
        $saveData = $this->getSaveData($request);
        $saveResponse = ProfileService::store($saveData);

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
        $viewData = $this->getViewData(false, $id);

        if (empty($viewData['profile'])) {
            abort(404);
        }

        return view('system.profiles.edit', $viewData);
    }

    public function update(ProfileSaveRequest $request)
    {
        $viewData = $this->getViewData(false, $request->get('id'));
        if (empty($viewData['profile'])) {
            abort(404);
        }

        $saveResponse = ProfileService::update($viewData['profile'], $this->getSaveData($request));

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
        $profiles = (!empty($request->get('ids')) && is_array($request->get('ids')))
            ? ProfileService::findAllByFilters([
                'id'    =>  [
                    [
                        'operator'  =>  'in',
                        'value'     =>  implode(',', $request->get('ids'))
                    ]
                ]
            ])
            : null;
        if ($profiles->count() != count($request->get('ids'))) {
            return response([
                'status'    =>  'error',
                'message'   =>  'Validação de dados violada.',
            ], 402);
        }

        $destroyReturn = [
            'deleted'           =>  [],
            'error'             =>  [],
            'console_message'   =>  '',
        ];

        foreach ($profiles as $profile) {
            $destroyResponse = ProfileService::destroy($profile->id);
            if ($destroyResponse['status'] == false) {
                $destroyReturn['error'][] = $profile->id;
                if ($destroyResponse['can_delete'] == false) {
                    $destroyReturn['console_message'] .= ($destroyReturn['console_message'] ? '<br>' : '');
                    $destroyReturn['console_message'] .= "Por definições do sistema, o item de código [{$profile->id}] não pode ser excluído.";
                }
                if (!empty($destroyResponse['used_in'])) {
                    $destroyReturn['console_message'] .= ($destroyReturn['console_message'] ? '<br>' : '');
                    $destroyReturn['console_message'] .= "O item de código [{$profile->id}] é utilizado por outros registros.";
                }
            }
            if ($destroyResponse['status'] == true) {
                $destroyReturn['deleted'][] = $profile->id;
            }
        }

        if (count($destroyReturn['error']) && count($destroyReturn['deleted'])) {
            $destroyReturn['status']  = 'warning';
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
            'profile'   =>  !empty((int)$id) ? ProfileService::findById($id) : null,
        ];
        if ($complete) {
            $searchFilters = ProfileHelper::searchFilters();
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
                    'default_filters'   =>  ProfileHelper::defaultSearchFilters(),
                ]
            );
        }
        return $data;
    }

    private function getSaveData(Request $request)
    {
        return [
            'profile'   =>  $request->only([
                'is_active', 'name',
            ]),
            'actions'   =>  $request->get('actions'),
        ];
    }
}
