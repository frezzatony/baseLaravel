<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class ProfileAction extends CoreModel
{

    protected $table = "profile_routines_actions";
    protected $fillable = [
        'id', 'profiles_id', 'routines_actions_id',
    ];

    public static function sqlJsonAggByProfileId()
    {
        return DB::table(self::getTableName())
            ->selectRaw("
                " . self::getTableName() . ".profiles_id,
                JSON_AGG(
                    JSON_BUILD_OBJECT(
                        'id'," . self::getTableName() . ".id,
                        'profiles_id'," . self::getTableName() . ".profiles_id,
                        'routines_actions_id'," . self::getTableName() . ".routines_actions_id
                    ) 
                ) actions
            ")
            ->groupBy(self::getTableName() . '.profiles_id')
            ->toSql();
    }
}
