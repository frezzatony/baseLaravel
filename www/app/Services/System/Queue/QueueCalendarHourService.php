<?php

namespace App\Services\System\Queue;

use App\Models\System\Queue\QueueCalendarHour;
use Illuminate\Support\Facades\DB;
use App\Services\CrudService;
use Illuminate\Database\QueryException;

class QueueCalendarHourService extends CrudService
{
    public static function updateQueueCalendarDateHours($date, $hours)
    {
        DB::beginTransaction();

        if (empty($hours)) {
            try {
                QueueCalendarHour::where('queues_calendar_id', $date->id)->each(function ($row, $key) {
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

        $storedHours = QueueCalendarHour::where('queues_calendar_id', $date->id)->get();
        foreach ($hours as $hour) {
            $storedHour = $storedHours->filter(function ($value, $key) use ($hour) {
                return $value->uuid == $hour['uuid'];
            });

            if ($storedHour->count() == 0) {
                try {
                    QueueCalendarHour::create([
                        'uuid'                  =>  $hour['uuid'],
                        'queues_calendar_id'    =>  $date->id,
                        'start'                 =>  $hour['start'],
                        'end'                   =>  $hour['end'],
                    ]);
                } catch (QueryException $e) {
                    DB::rollBack();
                    return false;
                }
            }

            if ($storedHour->count() > 0) {
                try {
                    QueueCalendarHour::where([
                        'uuid'                  =>  $storedHour->first()->uuid,
                        'queues_calendar_id'    =>  $date->id,
                    ])->get()->first()->update([
                        'start'                 =>  $hour['start'],
                        'end'                   =>  $hour['end'],
                    ]);
                } catch (QueryException $e) {
                    DB::rollBack();
                    return false;
                }
            }
        }

        foreach ($storedHours as $storedHour) {
            if (!in_array($storedHour->uuid, array_column($hours, 'uuid'))) {
                try {
                    QueueCalendarHour::where([
                        'uuid'                  =>  $storedHour->uuid,
                        'queues_calendar_id'    =>  $date->id,
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
