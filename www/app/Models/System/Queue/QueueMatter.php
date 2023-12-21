<?php

namespace App\Models\System\Queue;

use App\Models\CoreModel;
use Illuminate\Support\Facades\DB;

class QueueMatter extends CoreModel
{

    protected $table = 'queues_matters';
    protected $fillable = [
        'id', 'uuid', 'queues_id', 'description',
    ];

    public static function sqlJsonAggByQueueId()
    {
        return DB::table(self::getTableName())
            ->selectRaw("
                " . self::getTableName() . ".queues_id,
                JSON_AGG(
                    JSON_BUILD_OBJECT(
                        'id'," . self::getTableName() . ".id,
                        'uuid'," . self::getTableName() . ".uuid,
                        'description'," . self::getTableName() . ".description,
                        'users', users.users
                    ) 
                    ORDER BY description ASC
                ) matters
            ")
            ->leftJoin(
                DB::raw('(' .
                    QueueMatterUserAttendant::sqlJsonAggByMatterId() .
                    ') as users'),
                function ($join) {
                    $join->on('users.queues_matters_id', '=', self::getTableName() . '.id');
                }
            )
            ->groupBy(self::getTableName() . '.queues_id')
            ->toSql();
    }
}
