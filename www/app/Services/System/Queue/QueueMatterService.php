<?php

namespace App\Services\System\Queue;

use App\Helpers\Crud\System\Queue\QueueMatterHelper;
use App\Lib\SearchFilters;
use App\Models\System\Queue\QueueMatter;
use Illuminate\Support\Facades\DB;
use App\Services\CrudService;
use Illuminate\Database\QueryException;

class QueueMatterService extends CrudService
{
    public static function findAllByFilters($filters = [], $params = [])
    {
        $query = DB::table(QueueMatter::getTableName())
            ->selectRaw(
                QueueMatter::getTableName() . '.id AS matter_id, ' . QueueMatter::getTableName() . '.description AS matter_description, 
                ' . QueueMatter::getTableName() . '.queues_id AS queue_id'
            );

        $sql = "
            WITH 
                matters AS({$query->toSql()}) 
        ";

        $query = DB::table('matters')
            ->selectRaw('
            COUNT(1) OVER() AS count_filtered_items, matters.*
        ');

        $strWhereFilter = !empty($filters) ? SearchFilters::getStrWherere(QueueMatterHelper::searchFilters(), $filters) : null;
        if ($strWhereFilter) {
            $query->whereRaw($strWhereFilter);
        }
        self::setDatabaseDefaults($query, null, $params);

        $sql .= $query->toSql();
        return parent::genLazyCollectionFromSql($sql);
    }

    public static function findByUuid($uuid)
    {
        return QueueMatter::where('uuid', $uuid)->first();
    }

    public static function updateQueueMatters($queue, $matters)
    {
        DB::beginTransaction();

        if (empty($matters)) {
            try {
                QueueMatter::where('queues_id', $queue->id)->each(function ($row, $key) {
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

        $storedMatters = QueueMatter::where('queues_id', $queue->id)->get();
        foreach ($matters as $matter) {
            $storedMatter = $storedMatters->filter(function ($value, $key) use ($matter) {
                return $value->uuid == $matter['uuid'];
            });

            if ($storedMatter->count() == 0) {
                try {
                    $storedMatter = QueueMatter::create([
                        'uuid'          =>  $matter['uuid'],
                        'queues_id'     =>  $queue->id,
                        'description'   =>  $matter['description'],
                    ]);
                } catch (QueryException $e) {
                    DB::rollBack();
                    return false;
                }
            }

            if ($storedMatter->count() > 0) {
                $storedMatter = $storedMatter->first();
                try {
                    QueueMatter::where([
                        'uuid'      =>  $storedMatter->uuid,
                        'queues_id' =>  $queue->id,
                    ])->get()->first()->update([
                        'description'   =>  $matter['description'],
                    ]);
                } catch (QueryException $e) {
                    DB::rollBack();
                    return false;
                }
            }

            if (!QueueMatterUserAttendantService::updateQueueMatterUsersAttendant($storedMatter, $matter['users'] ?? [])) {
                DB::rollBack();
                return false;
            }
        }

        foreach ($storedMatters as $storedMatter) {
            if (!in_array($storedMatter->uuid, array_column($matters, 'uuid'))) {
                try {
                    QueueMatter::where([
                        'uuid'      =>  $storedMatter->uuid,
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
