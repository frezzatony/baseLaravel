<?php

namespace App\Http\Controllers\System\Messages;

use App\Helpers\Crud\System\Messages\CategoryHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\System\Messages\CategorySaveRequest;
use App\Services\System\Messages\CategoryService;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function index()
    {
        return view('system.messages.categories.index', $this->getViewData(true));
    }

    public function create()
    {
        return view('system.messages.categories.edit');
    }

    public function store(CategorySaveRequest $request)
    {
        $saveResponse = CategoryService::store($this->getSaveData($request));

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
        if (empty($data['category'])) {
            abort(404);
        }

        return view('system.messages.categories.edit', $data);
    }

    public function update(CategorySaveRequest $request)
    {
        $viewData = $this->getViewData(false, $request->get('id'));
        if (empty($viewData['category'])) {
            abort(404);
        }

        $saveResponse = CategoryService::update($viewData['category'], $this->getSaveData($request));

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
        $categorys = (!empty($request->get('ids')) && is_array($request->get('ids')))
            ? CategoryService::findAllByFilters([
                'id'    =>  [
                    [
                        'operator'  =>  'in',
                        'value'     =>  implode(',', $request->get('ids'))
                    ]
                ]
            ])
            : null;
        if ($categorys->count() != count($request->get('ids'))) {
            return response([
                'message'   =>  'Validação de dados violada.',
            ], 402);
        }

        $destroyReturn = [
            'deleted'           =>  [],
            'error'             =>  [],
            'console_message'   =>  '',
        ];

        foreach ($categorys as $category) {
            $destroyResponse = CategoryService::destroy($category->id);
            if ($destroyResponse['status'] == false) {
                $destroyReturn['error'][] = $category->id;
                if (!empty($destroyResponse['used_in'])) {
                    $destroyReturn['console_message'] .= ($destroyReturn['console_message'] ? '<br>' : '');
                    $destroyReturn['console_message'] .= "O item de código [{$category->id}] é utilizado por outros registros.";
                }
            }
            if ($destroyResponse['status'] == true) {
                $destroyReturn['deleted'][] = $category->id;
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
            'category'   =>  !empty((int)$id) ? CategoryService::findById($id) : null,
        ];
        if ($complete) {
            $searchFilters = CategoryHelper::searchFilters();
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
                    'default_filters'   =>  CategoryHelper::defaultSearchFilters(),
                ]
            );
        }
        return $data;
    }

    private function getSaveData(Request $request)
    {
        return [
            'category'   =>  $request->only([
                'is_active', 'description'
            ])
        ];
    }
}
