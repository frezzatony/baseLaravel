<?php

namespace App\Models\System\Queue;

use App\Models\CoreModel;
use Illuminate\Support\Facades\DB;

class QueueOperationWeekdayHour extends CoreModel
{

    protected $table = 'queues_operation_hours';
    protected $fillable = [
        'id', 'uuid', 'queues_operation_weekdays_id', 'start', 'end',
    ];

    public static function sqlJsonAggByWeekdayId()
    {
        return DB::table(self::getTableName())
            ->selectRaw("
                " . self::getTableName() . ".queues_operation_weekdays_id,
                JSON_AGG(
                    JSON_BUILD_OBJECT(
                        'uuid'," . self::getTableName() . ".uuid,
                        'start',to_char(" . self::getTableName() . ".start,'HH24:MI'),
                        'end',to_char(" . self::getTableName() . ".end,'HH24:MI')
                    ) 
                    ORDER BY " . self::getTableName() . ".start ASC, " . self::getTableName() . ".end ASC
                ) hours
            ")
            ->groupBy(self::getTableName() . '.queues_operation_weekdays_id')
            ->toSql();
    }
}
