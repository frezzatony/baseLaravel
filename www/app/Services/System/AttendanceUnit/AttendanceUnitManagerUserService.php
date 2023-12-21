<?php

namespace App\Services\System\AttendanceUnit;

use App\Helpers\DBHelper;
use App\Models\System\AttendanceUnit\AttendanceUnitManagerUser;
use App\Services\CrudService;
use Exception;
use Illuminate\Support\Facades\DB;

class AttendanceUnitManagerUserService extends CrudService
{
    public static function updateAttendanceUnitManagerUsers($attendanceUnit, $managerUsers)
    {
        if (empty($managerUsers)) {
            try {
                DB::beginTransaction();
                AttendanceUnitManagerUser::where('attendance_units_id', $attendanceUnit->id)->each(function ($row, $key) {
                    $row->delete();
                });
                DB::commit();

                return [
                    'status'    =>  true,
                ];
            } catch (Exception $e) {
                DB::rollBack();
                return false;
            }
        }

        DB::beginTransaction();

        foreach ($attendanceUnit->managers_users ?? [] as $storedManagerUser) {
            $keyStoredManagerUser = array_search($storedManagerUser->user_id, array_column($managerUsers, 'user_id'));
            if ($keyStoredManagerUser === false) {
                try {
                    AttendanceUnitManagerUser::destroy($storedManagerUser->id);
                } catch (Exception $e) {
                    DB::rollBack();
                    return false;
                }
            }

            if ($keyStoredManagerUser !== false) {
                unset($managerUsers[$keyStoredManagerUser]);
            }
        }

        foreach ($managerUsers as $managerUser) {
            try {
                AttendanceUnitManagerUser::create([
                    'attendance_units_id'   =>  $attendanceUnit->id,
                    'users_id'              =>  (int)$managerUser['user_id']
                ]);
            } catch (Exception $e) {
                DB::rollBack();
                return false;
            }
        }

        DB::commit();
        return [
            'status'    =>  true,
        ];
    }
}
