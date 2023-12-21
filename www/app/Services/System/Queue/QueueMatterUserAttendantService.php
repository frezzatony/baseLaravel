<?php

namespace App\Services\System\Queue;

use App\Helpers\Crud\System\Queue\QueueMatterHelper;
use App\Lib\SearchFilters;
use App\Models\System\Queue\QueueMatter;
use App\Models\System\Queue\QueueMatterUserAttendant;
use Illuminate\Support\Facades\DB;
use App\Services\CrudService;
use Illuminate\Database\QueryException;

class QueueMatterUserAttendantService extends CrudService
{
    public static function findAllByFilters($filters = [], $params = [])
    {
        $query = DB::table(QueueMatter::getTableName())
            ->selectRaw('
                queues_matters.id AS matter_id, queues_matters.description AS matter_description, 
                queues_matters.queues_id AS queue_id,
                queues_matters_users_attendants.users_id AS user_attendant_id,
                queues_matters.created_at, queues_matters.updated_at 
            ')
            ->join(QueueMatterUserAttendant::getTableName(), QueueMatterUserAttendant::getTableName() . '.queues_matters_id', '=', QueueMatterUserAttendant::getTableName() . '.id');

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

    public static function findAllMattersByQueueIdAndUserId(int $queueId, int $userId)
    {
        return self::findAllByFilters(
            [
                'queues_id'         =>  $queueId,
                'user_attendant_id' =>  $userId,
            ],
            ['order_by' =>  'matter_description ASC']
        );
    }

    public static function updateQueueMatterUsersAttendant($matter, $attendants)
    {
        DB::beginTransaction();

        if (empty($attendants)) {
            try {
                QueueMatterUserAttendant::where('queues_matters_id', $matter->id)->each(function ($row, $key) {
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

        $storedUsersAttendants = QueueMatterUserAttendant::where('queues_matters_id', $matter->id)->get();
        foreach ($attendants as $attendant) {
            $storedUserAttendant = $storedUsersAttendants->filter(function ($value, $key) use ($attendant) {
                return $value->users_id == $attendant;
            });

            if ($storedUserAttendant->count() == 0) {
                try {
                    QueueMatterUserAttendant::create([
                        'users_id'           =>  $attendant,
                        'queues_matters_id'   =>  $matter->id,
                    ]);
                } catch (QueryException $e) {
                    DB::rollBack();
                    return false;
                }
            }
        }

        foreach ($storedUsersAttendants as $storedUserAttendant) {
            if (!in_array($storedUserAttendant->users_id, $attendants)) {
                try {
                    QueueMatterUserAttendant::where([
                        'users_id'          =>  $storedUserAttendant->users_id,
                        'queues_matters_id'  =>  $matter->id,
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
