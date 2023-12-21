<?php

namespace App\Models\System;

use App\Models\CoreModel;
use Illuminate\Support\Facades\DB;

class RoutineAction extends CoreModel
{

    protected $table = "routines_actions";
    protected $fillable = [
        'id', 'slug', 'description', 'routines_id'
    ];

    public static function sqlJsonAggByRoutineId()
    {
        return DB::table(self::getTableName())
            ->selectRaw("
                " . self::getTableName() . ".routines_id,
                JSON_AGG(
                    JSON_BUILD_OBJECT(
                        'id'," . self::getTableName() . ".id,
                        'slug'," . self::getTableName() . ".slug,
                        'description'," . self::getTableName() . ".description
                    ) 
                    ORDER BY " . self::getTableName() . ".slug ASC
                ) routine_actions
            ")
            ->groupBy(self::getTableName() . '.routines_id')
            ->toSql();
    }
}
