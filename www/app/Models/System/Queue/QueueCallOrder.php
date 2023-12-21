<?php

namespace App\Models\System\Queue;

use App\Models\CoreModel;
use Illuminate\Support\Facades\DB;

class QueueCallOrder extends CoreModel
{

    protected $table = 'queues_call_orders';
    protected $fillable = [
        'id', 'uuid', 'queues_id', 'description', 'weight',
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
                        'weight'," . self::getTableName() . ".weight
                    ) 
                    ORDER BY " . self::getTableName() . ".weight DESC, " . self::getTableName() . ".description ASC
                ) call_orders
            ")
            ->groupBy(self::getTableName() . '.queues_id')
            ->toSql();
    }
}
