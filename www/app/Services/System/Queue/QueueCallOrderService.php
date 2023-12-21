<?php

namespace App\Services\System\Queue;

use App\Helpers\Crud\System\Queue\QueueCallOrderHelper;
use App\Lib\SearchFilters;
use App\Models\System\Queue\QueueCallOrder;
use Illuminate\Support\Facades\DB;
use App\Services\CrudService;
use Illuminate\Database\QueryException;

class QueueCallOrderService extends CrudService
{
    public static function findAllByFilters($filters = [], $params = [])
    {
        $query = DB::table(QueueCallOrder::getTableName())
            ->selectRaw('*');

        $sql = "
            WITH 
                call_orders AS({$query->toSql()}) 
        ";

        $query = DB::table('call_orders')
            ->selectRaw('
            COUNT(1) OVER() AS count_filtered_items, call_orders.*
        ');

        $strWhereFilter = !empty($filters) ? SearchFilters::getStrWherere(QueueCallOrderHelper::searchFilters(), $filters) : null;
        if ($strWhereFilter) {
            $query->whereRaw($strWhereFilter);
        }
        self::setDatabaseDefaults($query, null, $params);

        $sql .= $query->toSql();
        return parent::genLazyCollectionFromSql($sql);
    }

    public static function findByUuid($uuid)
    {
        return QueueCallOrder::where('uuid', $uuid)->first();
    }

    public static function updateQueueCallOrders($queue, $callOrders)
    {
        DB::beginTransaction();

        if (empty($callOrders)) {
            try {
                QueueCallOrder::where('queues_id', $queue->id)->each(function ($row, $key) {
                    $row->delete();
                });
                DB::commit();
                return [
                    'status'    =>  true,
                ];
            } catch (QueryException $e) {
                DB::rollBack();
                return false;
            }
        }

        $storedCallOrders = QueueCallOrder::where('queues_id', $queue->id)->get();
        foreach ($callOrders as $callOrder) {
            $storedCallOrder = $storedCallOrders->filter(function ($value, $key) use ($callOrder) {
                return $value->uuid == $callOrder['uuid'];
            });

            if ($storedCallOrder->count() == 0) {
                try {
                    QueueCallOrder::create([
                        'uuid'          =>  $callOrder['uuid'],
                        'queues_id'     =>  $queue->id,
                        'description'   =>  $callOrder['description'],
                        'weight'        =>  $callOrder['weight']
                    ]);
                } catch (QueryException $e) {
                    DB::rollBack();
                    return false;
                }
            }

            if ($storedCallOrder->count() > 0) {
                try {
                    QueueCallOrder::where([
                        'uuid'      =>  $storedCallOrder->first()->uuid,
                        'queues_id' =>  $queue->id,
                    ])->get()->first()->update([
                        'description'   =>  $callOrder['description'],
                        'weight'        =>  $callOrder['weight'],
                    ]);
                } catch (QueryException $e) {
                    DB::rollBack();
                    return false;
                }
            }
        }

        foreach ($storedCallOrders as $storedCallOrder) {
            if (!in_array($storedCallOrder->uuid, array_column($callOrders, 'uuid'))) {
                try {
                    QueueCallOrder::where([
                        'uuid'      =>  $storedCallOrder->uuid,
                        'queues_id' =>  $queue->id,
                    ])->each(function ($row, $key) {
                        $row->delete();
                    });
                } catch (QueryException $e) {
                    DB::rollBack();
                    return false;
                }
            }
        }

        DB::commit();
        return [
            'status'    =>  true,
        ];
    }
}
