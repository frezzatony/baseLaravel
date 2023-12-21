<?php

namespace App\Services\System\Queue;

use App\Models\System\Queue\QueueOperationWeekdayHour;
use Illuminate\Support\Facades\DB;
use App\Services\CrudService;
use Illuminate\Database\QueryException;

class QueueOperationWeekdayHourService extends CrudService
{
    public static function updateQueueOperationWeekdayHours($operationWeekday, $hours)
    {
        DB::beginTransaction();

        if (empty($hours)) {
            try {
                QueueOperationWeekdayHour::where('queues_operation_weekdays_id', $operationWeekday->id)->each(function ($row, $key) {
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

        $storedOperationWeekdayHours = QueueOperationWeekdayHour::where('queues_operation_weekdays_id', $operationWeekday->id)->get();
        foreach ($hours ?? [] as $hour) {
            $storedHour = $storedOperationWeekdayHours->filter(function ($value, $key) use ($hour) {
                return $value->uuid == $hour['uuid'];
            });

            if ($storedHour->count() == 0) {
                try {
                    QueueOperationWeekdayHour::create([
                        'queues_operation_weekdays_id'  =>  $operationWeekday->id,
                        'uuid'                          =>  $hour['uuid'],
                        'start'                         =>  $hour['start'],
                        'end'                           =>  $hour['end'],
                    ]);
                } catch (QueryException $e) {
                    DB::rollBack();
                    return false;
                }
            }

            if ($storedHour->count() > 0) {
                try {
                    QueueOperationWeekdayHour::where([
                        'uuid'                          =>  $storedHour->first()->uuid,
                        'queues_operation_weekdays_id'  =>  $operationWeekday->id,
                    ])->get()->first()->update([
                        'start'                         =>  $hour['start'],
                        'end'                           =>  $hour['end'],
                    ]);
                } catch (QueryException $e) {
                    DB::rollBack();
                    return false;
                }
            }
        }

        foreach ($storedOperationWeekdayHours as $storedHour) {
            if (!in_array($storedHour->uuid, array_column($hours, 'uuid'))) {
                try {
                    QueueOperationWeekdayHour::where([
                        'uuid'                          =>  $storedHour->uuid,
                        'queues_operation_weekdays_id'  =>  $operationWeekday->id,
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
