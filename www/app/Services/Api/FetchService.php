<?php

namespace App\Services\Api;

class FetchService
{
    public function getItems($prop = [])
    {
        $serviceClass = "{$prop['service']}Service";
        $servicePath = (($prop['path'] ?? null) ? $prop['path'] . '/' : '') . $serviceClass;
        if (!file_exists(app_path("Services/{$servicePath}.php"))) {
            return null;
        }
        $dataService = new ('App\\Services\\' . str_replace('/', '\\', $servicePath));
        $methodService = $prop['method'] ?? 'findAllByFilters';
        return $dataService->{$methodService}(($prop['filters'] ?? null), ($prop['params'] ?? null));
    }
}
