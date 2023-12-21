<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Services\Api\FetchService;
use Illuminate\Http\Request;

class FetchController extends Controller
{
    public function items(Request $request, $serviceName)
    {
        $prop = array_merge($request->all(), [
            'service'   =>  $serviceName,
            'path'      =>  $request->get('path'),
            'filters'   =>  $this->getFilters($request),
        ]);

        $prop['params'] =   [
            'order_by_column'   =>  [
                'key'   => ($prop['order']['column'] ?? 2) - 1,
                'dir'   =>  $prop['order']['dir'] ?? null,
            ],
            'limit'             =>  !empty($prop['length'])  ? ($prop['length'] == 'all' ? '' : $prop['length']) : 10,
            'limit_start'       =>  $prop['start'] ?? 0,
            'order_by'          =>  $prop['order_by'] ?? null,
            'include_master'    =>  auth()->user()->is_master ?? false,
        ];

        if ($request->get('_datatable')) {
            return $this->datatableReturn($prop);
        }

        return response()->json($this->getData($prop));
    }

    private function datatableReturn($prop)
    {
        $prop['datatables'] = true;
        $data = $this->getData($prop);
        return response()->json([
            'draw'              =>  $prop['draw'] ?? null,
            'data'              =>  $data,
            'recordsTotal'      =>  $data[0]->count_items ?? ($data[0]['count_items'] ?? 0),
            'recordsFiltered'   =>  $data[0]->count_filtered_items ?? ($data[0]['count_filtered_items'] ?? 0),
        ]);
    }

    private function getData($prop)
    {
        $data = [];
        foreach ($this->genData($prop) as $item) {
            $data[] = $item;
        }
        return $data;
    }

    private function genData($prop)
    {
        $crudHelperClass = "{$prop['service']}Helper";
        $helperPath = (($prop['path'] ?? null) ? $prop['path'] . '/' : '') . $crudHelperClass;

        if (file_exists(app_path("Helpers/Crud/{$helperPath}.php")) && ($prop['format'] ?? true)) {
            $dataHelper = new ('App\\Helpers\\Crud\\' . str_replace('/', '\\', $helperPath));
            $listItemsOptions = $dataHelper::listItems();
            $listProperties = method_exists($dataHelper, 'listProperties') ? $dataHelper::listProperties() : [];
        }

        $fetchService = new FetchService;
        foreach ($fetchService->getItems($prop) ?? [] as $key => $item) {
            $itemRowData = ($prop['datatables'] ?? false)
                ? [
                    'count_items'           =>  $item->count_items,
                    'count_filtered_items'  =>  $item->count_filtered_items,
                    '_prop'                 =>  [],
                ]
                : [];

            if (empty($dataHelper)) {
                yield $item;
            }

            if (!empty($dataHelper)) {
                foreach (array_column($listItemsOptions, 'column_list') as $key => $columnList) {
                    if (!empty($listItemsOptions[$key]['list_format'])) {
                        $itemRowData[$columnList] = $listItemsOptions[$key]['list_format']($item->{$columnList});
                    }
                    if (empty($listItemsOptions[$key]['list_format'])) {
                        $itemRowData[$columnList] = $item->{$columnList};
                    }
                }

                foreach ($listProperties as $key => $property) {
                    $itemRowData['_prop'][$key] = $item->{$property['column']};
                }

                yield $itemRowData;
            }
        };
    }

    private function getFilters($request)
    {
        return array_merge(
            is_array($request->get('filter')) ? $request->get('filter') : [],
            is_array($request->get('dynamic_filter')) ? $request->get('dynamic_filter') : []
        );
    }
}
