<?php

namespace App\Http\Controllers\System;

use App\Helpers\Crud\System\ModuleHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\System\ModuleSaveRequest;
use App\Models\User;
use App\Services\System\ModuleService;
use App\Services\System\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModulesController extends Controller
{
    public function index()
    {
        return view('system.modules.index', $this->getViewData(true));
    }

    public function change($moduleSlug)
    {
        $module = ModuleService::findBySlug($moduleSlug, auth()->user()->is_master);
        if (empty($module)) {
            abort(404);
        }

        UserService::updateAttributes(Auth::user(), [
            'module_id' =>  $module->id,
        ]);

        Auth::setUser(User::find(Auth::user()->id)->limit(1)->first());
        return redirect()->route('system.main')->with('messages', [
            [
                'status'    =>  'success',
                'message'   =>  'Módulo selecionado.'
            ]
        ]);
    }

    public function create()
    {
        return view('system.modules.create');
    }

    public function store(ModuleSaveRequest $request)
    {
        $saveResponse = ModuleService::store($this->getSaveData($request));

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

        if (empty($data['module'])) {
            abort(404);
        }

        return view('system.modules.edit', $data);
    }

    public function update(ModuleSaveRequest $request)
    {
        $viewData = $this->getViewData(false, $request->get('id'));
        if (empty($viewData['module'])) {
            abort(404);
        }

        $saveResponse = ModuleService::update($viewData['module'], $this->getSaveData($request));

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
        $modules = (!empty($request->get('ids')) && is_array($request->get('ids')))
            ? ModuleService::findAllByFilters([
                'id'    =>  [
                    [
                        'operator'  =>  'in',
                        'value'     =>  implode(',', $request->get('ids'))
                    ]
                ]
            ])
            : null;
        if ($modules->count() != count($request->get('ids'))) {
            return response([
                'message'   =>  'Validação de dados violada.',
            ], 402);
        }

        $destroyReturn = [
            'deleted'           =>  [],
            'error'             =>  [],
            'console_message'   =>  '',
        ];

        foreach ($modules as $module) {
            $destroyResponse = ModuleService::destroy($module->id);
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
            'module'    =>  !empty((int)$id) ? ModuleService::findById($id, auth()->user()->is_master) : null,
        ];
        if ($complete) {
            $searchFilters = ModuleHelper::searchFilters();
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
                    'default_filters'   =>  ModuleHelper::defaultSearchFilters(),
                ]
            );
        }
        return $data;
    }

    private function getSaveData(Request $request)
    {
        return [
            'module'    =>  $request->only([
                'name', 'slug', 'is_active'
            ])
        ];
    }
}
