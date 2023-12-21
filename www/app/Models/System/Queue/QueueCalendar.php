<?php

namespace App\Models\System\Queue;

use App\Models\CoreModel;
use Illuminate\Support\Facades\DB;

class QueueCalendar extends CoreModel
{

    protected $table = 'queues_calendar';
    protected $fillable = [
        'id', 'uuid', 'queues_id', 'availability', 'date', 'full_day', 'reason'
    ];

    public static function sqlJsonAggByQueueId()
    {
        return DB::table(self::getTableName())
            ->selectRaw("
                " . self::getTableName() . ".queues_id,
                JSON_AGG(
                    JSON_BUILD_OBJECT(
                        'uuid'," . self::getTableName() . ".uuid,
                        'date'," . self::getTableName() . ".date,
                        'availability'," . self::getTableName() . ".availability,
                        'full_day'," . self::getTableName() . ".full_day,
                        'reason'," . self::getTableName() . ".reason,
                        'hours', hours.hours
                    ) 
                    ORDER BY date DESC
                ) calendar
            ")
            ->leftJoin(
                DB::raw('(' .
                    QueueCalendarHour::sqlJsonAggByCalendarId() .
                    ') as hours'),
                function ($join) {
                    $join->on('hours.queues_calendar_id', '=', self::getTableName() . '.id');
                }
            )
            ->groupBy(self::getTableName() . '.queues_id')
            ->toSql();
    }
}
