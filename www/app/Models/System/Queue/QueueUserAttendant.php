<?php

namespace App\Models\System\Queue;

use App\Models\CoreModel;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class QueueUserAttendant extends CoreModel
{

    protected $table = 'queues_users_attendants';
    protected $fillable = [
        'id', 'queues_id', 'users_id',
    ];

    public static function sqlJsonAggByQueueId()
    {
        return DB::table(self::getTableName())
            ->selectRaw("
                " . self::getTableName() . ".queues_id,
                JSON_AGG(
                    JSON_BUILD_OBJECT(
                        'id'," . self::getTableName() . ".id,
                        'user_id'," . self::getTableName() . ".users_id,
                        'user_cpf'," . User::getTableName() . ".login,
                        'user_name_show',(CASE WHEN " . User::getTableName() . ".social_name NOTNULL THEN " . User::getTableName() . ".social_name ELSE " . User::getTableName() . ".name END)
                    ) 
                    ORDER BY users.social_name ASC, users.name ASC
                ) users_attendants
            ")
            ->join(User::getTableName() . ' AS users', 'users.id', '=', self::getTableName() . '.users_id')
            ->groupBy(self::getTableName() . '.queues_id')
            ->toSql();
    }
}
