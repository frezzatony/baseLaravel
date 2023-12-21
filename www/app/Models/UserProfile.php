<?php

namespace App\Models;

use App\Models\Profile;
use Illuminate\Support\Facades\DB;

class UserProfile extends CoreModel
{
    protected $table = 'users_profiles';
    protected $fillable = ['id', 'users_id', 'profiles_id'];

    public static function sqlJsonAggByUserId()
    {
        return DB::table(self::getTableName())
            ->selectRaw("
                " . self::getTableName() . ".users_id,
                JSON_AGG(
                    JSON_BUILD_OBJECT(
                        'id'," . self::getTableName() . ".id,
                        'profile_id'," . self::getTableName() . ".profiles_id,
                        'profile'," . Profile::getTableName() . ".name
                    ) 
                    ORDER BY " . Profile::getTableName() . ".name ASC
                ) profiles
            ")
            ->join(Profile::getTableName(), Profile::getTableName() . '.id', '=', self::getTableName() . '.profiles_id')
            ->groupBy(self::getTableName() . '.users_id')
            ->toSql();
    }
}
