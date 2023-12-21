<?php

namespace App\Services\System\Queue;

use App\Models\System\AttendanceUnit\AttendanceUnit;
use App\Models\System\Queue\Queue;
use App\Models\System\Queue\QueueUserAttendant;
use Illuminate\Support\Facades\DB;
use App\Services\CrudService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;

class QueueUserAttendantService extends CrudService
{
    public static function updateQueueUsersAttendants($queue, $attendants)
    {
        DB::beginTransaction();

        if (empty($attendants)) {
            try {
                QueueUserAttendant::where('queues_id', $queue->id)->each(function ($row, $key) {
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

        $storedUsersAttendants = QueueUserAttendant::where('queues_id', $queue->id)->get();
        foreach ($attendants as $attendant) {
            $storedUserAttendant = $storedUsersAttendants->filter(function ($value, $key) use ($attendant) {
                return $value->users_id == $attendant['user_id'];
            });

            if ($storedUserAttendant->count() == 0) {
                try {
                    QueueUserAttendant::create([
                        'queues_id'     =>  $queue->id,
                        'users_id'      =>  $attendant['user_id'],
                    ]);
                } catch (QueryException $e) {
                    DB::rollBack();
                    return false;
                }
            }
        }

        foreach ($storedUsersAttendants as $storedUserAttendant) {
            if (!in_array($storedUserAttendant->users_id, array_column($attendants, 'user_id'))) {
                try {
                    QueueUserAttendant::where([
                        'users_id'  =>  $storedUserAttendant->users_id,
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

    public static function findAllAttendanceUnitsByUserAttendantIdAndFilters($filters = [])
    {
        $query = DB::table(QueueUserAttendant::getTableName())
            ->distinct()
            ->select([
                AttendanceUnit::getTableName() . '.id', AttendanceUnit::getTableName() . '.name',
                AttendanceUnit::getTableName() . '.id', AttendanceUnit::getTableName() . '.name',
            ])
            ->join(Queue::getTableName(), Queue::getTableName() . '.id', '=', QueueUserAttendant::getTableName() . '.queues_id')
            ->join(AttendanceUnit::getTableName(), AttendanceUnit::getTableName() . '.id', '=', Queue::getTableName() . '.attendance_units_id')
            ->whereRaw(AttendanceUnit::getTableName() . '.is_active = \'t\'')
            ->whereRaw(Queue::getTableName() . '.is_active = \'t\'')
            ->orderBy(AttendanceUnit::getTableName() . '.name');

        if (empty($filters['user_id']) && !Auth::user()->is_master) {
            $filters['user_id'] = Auth::user()->id;
        }

        if (!empty($filters['user_id'])) {
            $query->whereRaw(QueueUserAttendant::getTableName() . ".users_id = {$filters['user_id']}");
        }

        return parent::genLazyCollectionFromSql($query->toSql());
    }

    public static function findAllQueuesByAttendanceUnitIdAndUserAttendantIdAndFilters($filters = [])
    {
        if (empty($filters['attendance_unit_id']) || !(int)$filters['attendance_unit_id']) {
            return [];
        }

        $query = DB::table(QueueUserAttendant::getTableName())
            ->distinct()
            ->select([
                Queue::getTableName() . '.id', Queue::getTableName() . '.description',
                Queue::getTableName() . '.type', Queue::getTableName() . '.ticket_withdrawal',
                Queue::getTableName() . '.ticket_sequence',
            ])
            ->join(Queue::getTableName(), Queue::getTableName() . '.id', '=', QueueUserAttendant::getTableName() . '.queues_id')
            ->whereRaw(Queue::getTableName() . '.is_active = \'t\'')
            ->orderBy(Queue::getTableName() . '.description');

        if (empty($filters['user_id']) && !Auth::user()->is_master) {
            $filters['user_id'] = Auth::user()->id;
        }

        if (!empty($filters['user_id'])) {
            $query->whereRaw(QueueUserAttendant::getTableName() . ".users_id = {$filters['user_id']}");
        }
        $query->whereRaw(Queue::getTableName() . ".attendance_units_id = {$filters['attendance_unit_id']}");

        return parent::genLazyCollectionFromSql($query->toSql());
    }
}
