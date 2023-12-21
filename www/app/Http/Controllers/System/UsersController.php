<?php

namespace App\Http\Controllers\System;

use App\Helpers\Crud\System\UserHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\System\UserUpdateProfileRequest;
use App\Http\Requests\System\UserUpdateConfigRequest;
use App\Http\Requests\System\UserSaveRequest;
use App\Models\User;
use App\Services\System\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function index()
    {
        return view('system.users.index', $this->getViewData(true));
    }

    public function create()
    {
        return view('system.users.create');
    }

    public function store(UserSaveRequest $request)
    {
        $saveResponse = UserService::store($this->getSaveData($request));

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

        if (empty($data['user'])) {
            abort(404);
        }

        return view('system.users.edit', $data);
    }

    public function update(UserSaveRequest $request)
    {
        $viewData = $this->getViewData(false, $request->get('id'));
        if (empty($viewData['user'])) {
            abort(404);
        }

        $saveResponse = UserService::update($viewData['user'], $this->getSaveData($request));

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
        $users = (!empty($request->get('ids')) && is_array($request->get('ids')))
            ? UserService::findAllByFilters([
                'id'    =>  [
                    [
                        'operator'  =>  'in',
                        'value'     =>  implode(',', $request->get('ids'))
                    ]
                ]
            ])
            : null;
        if ($users->count() != count($request->get('ids'))) {
            return response([
                'message'   =>  'Validação de dados violada.',
            ], 402);
        }

        $destroyReturn = [
            'deleted'   =>  [],
            'error'     =>  [],
            'status'    =>  'success',
        ];
        foreach ($users as $user) {
            $destroyResponse = auth()->user()->id != $user->id
                ? UserService::destroy($user->id)
                : [
                    'status'    =>  false,
                ];
            if ($destroyResponse['status'] == false) {
                $destroyReturn['error'][] = $user->id;
                $destroyReturn['status'] = 'warning';
            }
            if ($destroyResponse['status'] == true) {
                $destroyReturn['deleted'][] = $user->id;
            }
        }

        if (count($destroyReturn['error']) && count($destroyReturn['deleted'])) {
            $destroyReturn['message'] = 'Alguns itens não puderam ser excluídos';
        }
        if (count($destroyReturn['error']) && !count($destroyReturn['deleted'])) {
            $destroyReturn['message'] = (count($destroyReturn['error']) > 1 ? 'Os itens não poderam ser excluídos' : 'O item não pôde ser excluído.');
            $destroyReturn['status'] = 'error';
        }
        if (!count($destroyReturn['error']) && count($destroyReturn['deleted'])) {
            $destroyReturn['message'] = (count($destroyReturn['deleted']) > 1 ? 'Itens excluídos' : 'Item excluído.');
        }

        return response($destroyReturn, 200);
    }

    public function detail()
    {
        return view('system.users.detail', [
            'user'  =>  UserService::findById(Auth::id(), true),
        ]);
    }

    public function modify(UserUpdateProfileRequest $request)
    {
        $viewData = $request->only(["name", "social_name"]);
        User::where('id', Auth::id())->update($viewData);
        return response(["message" => "Atualizado"], 200);
    }

    public function updateConfig(UserUpdateConfigRequest $request)
    {
        $user = User::find(Auth::id());
        if (Hash::check($request->current_password, $user->password)) {
            User::where('id', Auth::id())->update(["password" => Hash::make($request->password)]);
            return response(["message" => "Senha Atualizada"], 200);
        }
        return response(["message" => "Senha Errada"], 200);
    }

    private function getViewData($complete = false, $id = null)
    {
        $data = [
            'user'  =>  !empty((int)$id) ? UserService::findById($id) : null,
        ];
        if ($complete) {
            $searchFilters = UserHelper::searchFilters();
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
                    'default_filters'   =>  UserHelper::defaultSearchFilters(),
                ]
            );
        }
        return $data;
    }

    private function getSaveData(Request $request)
    {
        $data = [
            'user'  =>  $request->only([
                'is_active', 'name', 'social_name', 'login', 'email', 'password',
            ]),
            'profiles'  =>  $request->get('profiles'),
        ];
        $data['user']['login'] =  preg_replace('/[^0-9]/', '', $data['user']['login']);
        return $data;
    }
}
