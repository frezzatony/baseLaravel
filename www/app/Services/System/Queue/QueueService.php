<?php

namespace App\Services\System\Queue;

use Illuminate\Support\Facades\DB;
use App\Helpers\Crud\System\Queue\QueueHelper;
use App\Helpers\StringHelper;
use App\Lib\SearchFilters;
use App\Models\System\Queue\Queue;
use App\Models\System\Queue\QueueUserAttendant;
use App\Services\CrudService;

class QueueService extends CrudService
{

    public static function findAllByFilters($filters = [], $params = [])
    {
        $query = DB::table(Queue::getTableName())
            ->selectRaw('
                COUNT(1) OVER() AS count_items,
                id, description, is_active, type, point_name, point_quantity, attendance_units_id,
                ticket_sequence, ticket_withdrawal, reset_tickets_counter, ticket_reset
            ');

        $sql = "
            WITH 
                queues AS({$query->toSql()}) 
        ";

        $query = DB::table('queues')
            ->selectRaw('
                COUNT(1) OVER() AS count_filtered_items, queues.*
            ');

        $strWhereFilter = !empty($filters) ? SearchFilters::getStrWherere(QueueHelper::searchFilters(), $filters) : null;
        if ($strWhereFilter) {
            $query->whereRaw($strWhereFilter);
        }

        self::setDatabaseDefaults($query, QueueHelper::listItems(), $params);
        $sql .= $query->toSql();
        return parent::genLazyCollectionFromSql($sql);
    }

    public static function findById(int $id)
    {
        return self::findAllByFilters(['id' => $id], ['limit' => 1])->first();
    }

    public static function findAllAttendancePointsByQueueIdAndFilters($filters = [])
    {
        if (empty($filters['queue_id']) || !(int)$filters['queue_id']) {
            return [];
        }

        $queue = self::findById($filters['queue_id']);
        $data = [];
        for ($i = 1; $i <= $queue->point_quantity; $i++) {
            $data[] = [
                'id'            =>  $i,
                'description'   =>  StringHelper::upper("{$queue->point_name} {$i}"),
            ];
        }
        return collect($data);
    }

    public static function findAllQueuesByUserId(int $idUser)
    {
        $query = DB::table(Queue::getTableName())
            ->distinct()
            ->selectRaw(Queue::getTableName() . '.*')
            ->join(QueueUserAttendant::getTableName(), QueueUserAttendant::getTableName() . '.queues_id', '=', Queue::getTableName() . '.id')
            ->whereRaw(QueueUserAttendant::getTableName() . ".users_id = {$idUser}");

        $sql = $query->toSql();
        return parent::genLazyCollectionFromSql($sql);
    }
}
