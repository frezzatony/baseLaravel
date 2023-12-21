<?php

namespace App\Services\System;

use App\Models\ProfileAction;
use Illuminate\Support\Facades\DB;
use App\Services\CrudService;
use Exception;

class ProfileActionService extends CrudService
{
    public static function updateProfileActions($profile, $actions)
    {
        if (empty($actions)) {
            try {
                DB::beginTransaction();
                ProfileAction::where('profiles_id', $profile->id)->each(function ($row, $key) {
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
        foreach ($profile->actions ?? [] as $storedAction) {
            $keyStoredProfile = array_search($storedAction->routines_actions_id, $actions);
            if ($keyStoredProfile === false) {
                try {
                    ProfileAction::destroy($storedAction->id);
                } catch (Exception $e) {
                    DB::rollBack();
                    return false;
                }
            }

            if ($keyStoredProfile !== false) {
                unset($actions[$keyStoredProfile]);
            }
        }

        foreach ($actions as $action) {
            try {
                ProfileAction::create([
                    'profiles_id'           =>  $profile->id,
                    'routines_actions_id'   =>  (int)$action
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
