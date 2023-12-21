<?php

namespace App\Services\System\Queue;

use App\Models\System\Queue\QueueOperationWeekdayHour;
use App\Models\System\Queue\QueueOperationWeekday;
use Illuminate\Support\Facades\DB;
use App\Services\CrudService;
use Illuminate\Database\QueryException;

class QueueOperationWeekdayService extends CrudService
{
    public static function updateQueueOperationWeekdays($queue, $weekdays)
    {
        DB::beginTransaction();

        foreach (\App\Enums\Weekday::asArray() as $weekday) {

            $hasStoredWeekday = !empty($queue->weekdays) && $queue->weekdays->filter(function ($value) use ($weekday) {
                return $value->weekday = $weekday['isodow'];
            })->count() > 0;

            if (!$hasStoredWeekday) {
                try {
                    $operationWeekday = QueueOperationWeekday::create([
                        'queues_id'     =>  $queue->id,
                        'weekday'       =>  $weekday['isodow'],
                        'availability'  =>  $weekdays[$weekday['isodow']]['availability'],
                    ]);
                } catch (QueryException $e) {
                    DB::rollBack();
                    return false;
                }
            }

            if ($hasStoredWeekday) {
                try {
                    $operationWeekday = QueueOperationWeekday::where([
                        'queues_id' =>  $queue->id,
                        'weekday'   =>  $weekday['isodow'],
                    ])->get()->first();
                    $operationWeekday->update([
                        'queues_id'     =>  $queue->id,
                        'weekday'       =>  $weekday['isodow'],
                        'availability'  =>  $weekdays[$weekday['isodow']]['availability'],
                    ]);
                } catch (QueryException $e) {
                    DB::rollBack();
                    return false;
                }
            }

            if (!QueueOperationWeekdayHourService::updateQueueOperationWeekdayHours($operationWeekday, $weekdays[$weekday['isodow']]['hours'] ?? [])) {
                DB::rollBack();
                return false;
            }
        }

        DB::commit();
        return [
            'status'    =>  true,
        ];
    }
}
