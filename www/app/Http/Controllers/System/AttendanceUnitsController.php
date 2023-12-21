<?php

namespace App\Http\Controllers\System;

use App\Helpers\Crud\System\AttendanceUnit\AttendanceUnitHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\System\AttendanceUnitSaveRequest;
use App\Lib\Attachments;
use App\Services\System\AttendanceUnit\AttendanceUnitService;
use Illuminate\Http\Request;

class AttendanceUnitsController extends Controller
{
    public function index()
    {
        return view('system.attendanceunits.index', $this->getViewData(true));
    }

    public function create()
    {
        return view('system.attendanceunits.create');
    }

    public function store(AttendanceUnitSaveRequest $request)
    {
        $saveResponse = AttendanceUnitService::store($this->getSaveData($request));
        if ($saveResponse == false) {
            return response()->json([
                'status'    =>  'error',
                'message'   =>  'Houve um erro ao executar a operação.',
            ]);
        }

        if (($saveResponse['status'] ?? null) == true) {
            return response()->json([
                'status'                =>  'success',
                'id'                    =>  $saveResponse['id'],
                'attachment_catalog'    =>  $saveResponse['attachment_catalog'],
                'message'               =>  'Item criado.',
            ]);
        }
    }

    public function edit(int $id)
    {
        $data = $this->getViewData(false, $id);

        if (empty($data['attendance_unit'])) {
            abort(404);
        }

        return view('system.attendanceunits.edit', $data);
    }

    public function update(AttendanceUnitSaveRequest $request)
    {
        $viewData = $this->getViewData(false, $request->get('id'));
        if (empty($viewData['attendance_unit'])) {
            abort(404);
        }

        $saveResponse = AttendanceUnitService::update($viewData['attendance_unit'], $this->getSaveData($request));

        if ($saveResponse == false) {
            return response([
                'status'    =>  'error',
                'message'   =>  'Houve um erro ao executar a operação.',
            ], 200);
        }

        if (($saveResponse['status'] ?? false) == true) {
            return response([
                'status'    =>  'success',
                'id'        =>  $saveResponse['id'],
                'message'   =>  'Item atualizado.',
            ], 200);
        }
    }

    public function destroy(Request $request)
    {
        $attendanceunits = (!empty($request->get('ids')) && is_array($request->get('ids')))
            ? AttendanceUnitService::findAllByFilters([
                'id'    =>  [
                    [
                        'operator'  =>  'in',
                        'value'     =>  implode(',', $request->get('ids'))
                    ]
                ]
            ])
            : null;
        if ($attendanceunits->count() != count($request->get('ids'))) {
            return response([
                'message'   =>  'Validação de dados violada.',
            ], 402);
        }

        $destroyReturn = [
            'deleted'   =>  [],
            'error'     =>  [],
            'status'    =>  'success',
        ];
        foreach ($attendanceunits as $attendanceunit) {
            $destroyResponse = AttendanceUnitService::destroy($attendanceunit);
            if ($destroyResponse['status'] == false) {
                $destroyReturn['error'][] = $attendanceunit->id;
                $destroyReturn['status'] = 'warning';
            }
            if ($destroyResponse['status'] == true) {
                $destroyReturn['deleted'][] = $attendanceunit->id;
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

    public function attachments(Request $request)
    {
        $viewData = $this->getViewData(false, !empty((int)$request->get('crud_id')) ? $request->get('crud_id') : null);
        if (empty($viewData['attendance_unit'])) {
            abort(404);
        }

        $attachmentCatalog = AttendanceUnitService::getAttachmentsCatalog($viewData['attendance_unit']);

        if (!empty($attachmentCatalog->catalog)) {
            $response = $attachmentCatalog->{mb_strtolower($request->get('action'))}($request);
            if ($response !== false && is_array($response)) {
                return response($response, 200);
            }
            if ($response !== false && !is_array($response)) {
                return $response;
            }
        }
    }

    private function getViewData($complete = false, $id = null)
    {
        $data = [
            'attendance_unit'   =>  !empty((int)$id) ? AttendanceUnitService::findById($id) : null,
        ];
        if ($complete) {
            $searchFilters = AttendanceUnitHelper::searchFilters();
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
                    'default_filters'   =>  AttendanceUnitHelper::defaultSearchFilters(),
                ]
            );
        }
        return $data;
    }

    private function getSaveData(Request $request)
    {
        return [
            'attendance_unit'  =>  $request->only([
                'is_active', 'name', 'slug', 'web_page'
            ]),
            'address'   =>   [
                'cep'           =>  $request->get('address_cep'),
                'state'         =>  $request->get('address_state'),
                'city'          =>  $request->get('address_city'),
                'neighborhood'  =>  $request->get('address_neighborhood'),
                'street'        =>  $request->get('address_street'),
                'number'        =>  $request->get('address_number'),
                'complement'    =>  $request->get('address_complement'),
                'regional_zone' =>  $request->get('address_regional_zone'),
                'latitude'      =>  $request->get('address_latitude'),
                'longitude'     =>  $request->get('address_longitude'),
            ],
            'managers'          =>  $request->get('managers'),
        ];
    }
}
