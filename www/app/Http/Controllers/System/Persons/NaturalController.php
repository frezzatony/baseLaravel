<?php

namespace App\Http\Controllers\System\Persons;

use App\Enums\PersonType;
use App\Helpers\Crud\System\Person\PersonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\System\Person\NaturalSaveRequest;
use App\Services\System\Person\PersonService;
use Illuminate\Http\Request;

class NaturalController extends Controller
{
    public function index()
    {
        return view('system.persons.index', $this->getViewData(true));
    }

    public function create()
    {
        return view('system.persons.natural.edit');
    }

    public function store(NaturalSaveRequest $request)
    {
        $saveResponse = PersonService::store($this->getSaveData($request));

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
        if (empty($data['person'])) {
            abort(404);
        }
        return view('system.persons.natural.edit', $data);
    }

    public function update(NaturalSaveRequest $request)
    {
        $data = $this->getViewData(false, $request->get('id'));
        if (empty($data['person'])) {
            abort(404);
        }
        $saveResponse = PersonService::update($data['person'], $this->getSaveData($request));
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
                'message'   =>  'Item atualizado.',
            ]);
        }
    }

    public function destroy(Request $request)
    {
    }

    private function getViewData($complete = false, $id = null)
    {
        $data = [
            'person'    =>  !empty((int)$id) ? PersonService::findById($id) : null,
        ];
        if ($complete) {
            $searchFilters = PersonHelper::searchFilters();
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
                    'default_filters'   =>  PersonHelper::defaultSearchFilters(),
                ]
            );
        }
        return $data;
    }

    private function getSaveData(Request $request)
    {
        $data = [
            'person'    =>  $request->only([
                'is_active', 'name', 'social_name', 'cpf_cnpj', 'birthdate',
            ]),
            'address'   =>   [
                'cep'           =>  $request->input('address_cep'),
                'state'         =>  $request->input('address_state'),
                'city'          =>  $request->input('address_city'),
                'neighborhood'  =>  $request->input('address_neighborhood'),
                'street'        =>  $request->input('address_street'),
                'number'        =>  $request->input('address_number'),
                'complement'    =>  $request->input('address_complement'),
                'regional_zone' =>  $request->input('address_regional_zone'),
                'latitude'      =>  $request->input('address_latitude'),
                'longitude'     =>  $request->input('address_longitude'),
            ],
            'contacts'  =>  $request->input('contact'),
        ];
        $data['person']['cpf_cnpj'] =  preg_replace('/[^0-9]/', '', $data['person']['cpf_cnpj']);
        $data['person']['type'] = PersonType::NATURAL['value'];
        return $data;
    }
}
