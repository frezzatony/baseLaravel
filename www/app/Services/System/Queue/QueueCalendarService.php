<?php

namespace App\Services\System\Queue;

use App\Models\System\Queue\QueueCalendar;
use Illuminate\Support\Facades\DB;
use App\Services\CrudService;
use Illuminate\Database\QueryException;

class QueueCalendarService extends CrudService
{
    public static function updateQueueCalendarDates($queue, $dates)
    {
        DB::beginTransaction();
        if (empty($dates)) {
            try {
                QueueCalendar::where('queues_id', $queue->id)->each(function ($row, $key) {
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

        $storedDates = QueueCalendar::where('queues_id', $queue->id)->get();
        foreach ($dates as $date) {
            $storedDate = $storedDates->filter(function ($value, $key) use ($date) {
                return $value->uuid == $date['uuid'];
            });

            if ($storedDate->count() == 0) {
                try {
                    $storedDate = QueueCalendar::create([
                        'uuid'          =>  $date['uuid'],
                        'queues_id'     =>  $queue->id,
                        'date'          =>  $date['date'],
                        'availability'  =>  $date['availability'],
                        'full_day'      =>  $date['full_day'],
                        'reason'        =>  $date['reason'],
                    ]);
                } catch (QueryException $e) {
                    DB::rollBack();
                    return false;
                }
            }

            if ($storedDate->count() > 0) {
                $storedDate = $storedDate->first();
                try {
                    QueueCalendar::where([
                        'uuid'      =>  $storedDate->uuid,
                        'queues_id' =>  $queue->id,
                    ])->get()->first()->update([
                        'date'          =>  $date['date'],
                        'availability'  =>  $date['availability'],
                        'full_day'      =>  $date['full_day'],
                        'reason'        =>  $date['reason'],
                    ]);
                } catch (QueryException $e) {
                    DB::rollBack();
                    return false;
                }
            }

            if (!QueueCalendarHourService::updateQueueCalendarDateHours($storedDate, $date['hours'] ?? [])) {
                DB::rollBack();
                return false;
            }
        }

        foreach ($storedDates as $storedDate) {
            if (!in_array($storedDate->uuid, array_column($dates, 'uuid'))) {
                try {
                    QueueCalendar::where([
                        'uuid'      =>  $storedDate->uuid,
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
