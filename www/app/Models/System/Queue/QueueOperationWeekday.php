<?php

namespace App\Models\System\Queue;

use App\Models\CoreModel;
use Illuminate\Support\Facades\DB;

class QueueOperationWeekday extends CoreModel
{

    protected $table = 'queues_operation_weekdays';
    protected $fillable = [
        'id', 'queues_id', 'weekday', 'availability',
    ];

    public static function sqlJsonAggByQueueId()
    {
        return DB::table(self::getTableName())
            ->selectRaw("
            " . self::getTableName() . ".queues_id,
                JSON_AGG(
                    JSON_BUILD_OBJECT(
                        'id'," . self::getTableName() . ".id,
                        'weekday'," . self::getTableName() . ".weekday,
                        'availability'," . self::getTableName() . ".availability,
                        'hours',hours.hours
                    ) 
                    ORDER BY " . self::getTableName() . ".weekday ASC
                ) weekdays
            ")
            ->leftJoin(
                DB::raw('(' .
                    QueueOperationWeekdayHour::sqlJsonAggByWeekdayId() .
                    ') as hours'),
                function ($join) {
                    $join->on('hours.queues_operation_weekdays_id', '=', self::getTableName() . '.id');
                }
            )
            ->groupBy(self::getTableName() . '.queues_id')
            ->toSql();
    }
}
