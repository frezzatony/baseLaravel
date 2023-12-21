<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\LazyCollection;
use Carbon\Carbon;

class CrudService
{
    protected static function setDatabaseDefaults(&$query, $listProperties = null, $params)
    {
        if ($params['limit'] ?? false) {
            $query->take($params['limit']);
        }
        if ($params['limit_start'] ?? null) {
            $query->skip($params['limit_start']);
        }
        if ($params['order_by'] ?? false) {
            $query->orderByRaw($params['order_by']);
        }
        if (($params['order_by_column'] ?? false) && $listProperties) {
            $listProperties = array_values(
                array_filter($listProperties, function ($column) {
                    return (empty($column['hide_on_view']) || $column['hide_on_view'] != true);
                })
            );
            $query->orderByRaw(self::getOrderRawByColumn($listProperties, $params['order_by_column']));
        }
    }

    protected static function getOrderRawByColumn($listProperties, $column)
    {
        $columnOrder = in_array($column['key'], array_keys($listProperties)) ? $listProperties[$column['key']]['column_order'] : $listProperties[0]['column_order'];
        $dirOrder = in_array(mb_strtoupper($column['dir']), ['ASC', 'DESC']) ? $column['dir'] : 'ASC';
        return "$columnOrder $dirOrder";
    }

    protected static function genLazyCollectionFromSql($sql, $params = [])
    {
        return new LazyCollection(function () use ($sql) {
            foreach (DB::cursor($sql) as $item) {
                foreach ($item as $key => $column) {
                    if (
                        $column && (
                            (str_starts_with(trim($column), '[') && str_ends_with(trim($column), ']'))  ||
                            (str_starts_with(trim($column), '{') && str_ends_with(trim($column), '}'))
                        )
                    ) {
                        $item->{$key} = collect(json_decode(trim($column)));
                    }

                    if ($column && in_array($key, array_merge($params['timestamps'] ?? [], ['created_at', 'updated_at', 'deleted_at']))) {
                        $item->{$key} = Carbon::parse($column);
                    }
                }
                yield $item;
            }
        });
    }
}
