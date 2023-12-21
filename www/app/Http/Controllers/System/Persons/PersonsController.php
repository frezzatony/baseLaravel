<?php

namespace App\Http\Controllers\System\Persons;

use App\Helpers\Crud\System\Person\PersonHelper;
use App\Http\Controllers\Controller;
use App\Services\System\Person\PersonService;
use Illuminate\Http\Request;

class PersonsController extends Controller
{
    public function index()
    {
        return view('system.persons.index', $this->getViewData(true));
    }

    public function attachments(Request $request)
    {
        $viewData = $this->getViewData(false, !empty((int)$request->get('crud_id')) ? $request->get('crud_id') : null);
        if (empty($viewData['person'])) {
            abort(404);
        }

        $attachmentCatalog = PersonService::getAttachmentsCatalog($viewData['person']);

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

    public function destroy(Request $request)
    {
        $persons = (!empty($request->get('ids')) && is_array($request->get('ids')))
            ? PersonService::findAllByFilters([
                'id'    =>  [
                    [
                        'operator'  =>  'in',
                        'value'     =>  implode(',', $request->get('ids'))
                    ]
                ]
            ])
            : null;
        if ($persons->count() != count($request->get('ids'))) {
            return response([
                'message'   =>  'Validação de dados violada.',
            ], 402);
        }

        $destroyReturn = [
            'deleted'   =>  [],
            'error'     =>  [],
            'status'    =>  'success',
        ];
        foreach ($persons as $attendanceunit) {
            $destroyResponse = PersonService::destroy($attendanceunit);
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
}
