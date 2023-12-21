<?php

namespace App\Models\System\Queue;

use App\Models\CoreModel;
use Illuminate\Support\Facades\DB;

class QueueMatterUserAttendant extends CoreModel
{

    protected $table = 'queues_matters_users_attendants';
    protected $fillable = [
        'id', 'queues_matters_id', 'users_id',
    ];

    public static function sqlJsonAggByMatterId()
    {
        return DB::table(self::getTableName())
            ->selectRaw("
                " . self::getTableName() . ".queues_matters_id,
                JSON_AGG(
                    " . self::getTableName() . ".users_id
                ) users
            ")
            ->groupBy(self::getTableName() . '.queues_matters_id')
            ->toSql();
    }
}
