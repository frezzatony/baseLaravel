<?php

namespace App\Models\System\AttendanceUnit;

use App\Models\CoreModel;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AttendanceUnitManagerUser extends CoreModel
{

    protected $table = 'attendance_unit_managers_users';
    protected $fillable = [
        'id', 'attendance_units_id', 'users_id',
    ];

    public static function sqlJsonAggByAttendanceUnitId()
    {
        return DB::table(self::getTableName())
            ->selectRaw("
                " . self::getTableName() . ".attendance_units_id,
                JSON_AGG(
                    JSON_BUILD_OBJECT(
                        'id'," . self::getTableName() . ".id,
                        'attendance_units_id'," . self::getTableName() . ".attendance_units_id,
                        'user_id'," . self::getTableName() . ".users_id,
                        'user_cpf'," . User::getTableName() . ".login,
                        'user_name_show',(CASE WHEN " . User::getTableName() . ".social_name NOTNULL THEN " . User::getTableName() . ".social_name ELSE " . User::getTableName() . ".name END)
                    ) 
                ) managers_users
            ")
            ->groupBy(self::getTableName() . '.attendance_units_id')
            ->join(User::getTableName(), User::getTableName() . '.id', '=', self::getTableName() . '.users_id')
            ->toSql();
    }
}
