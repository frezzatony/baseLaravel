<?php

namespace App\Http\Controllers\System\Queues;

use App\Http\Controllers\Controller;
use App\Helpers\Crud\System\Queue\QueueHelper;

class QueuesController extends Controller
{
    public function index()
    {
        return view('system.queues.queues.index', $this->getViewData());
    }

    private function getViewData()
    {
        $searchFilters = QueueHelper::searchFilters();
        return [
            'dynamic_filters'   =>  array_map(function ($key, $filter) {
                return [
                    'id'            =>  $key,
                    'label'         =>  $filter['label'],
                    'input_type'    =>  $filter['type'],
                    'values'        =>  $filter['values'] ?? null
                ];
            }, array_keys($searchFilters), $searchFilters),
            'default_filters'   =>  QueueHelper::defaultSearchFilters(),
        ];
    }
}
