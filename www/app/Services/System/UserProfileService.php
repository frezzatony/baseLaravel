<?php

namespace App\Services\System;

use Illuminate\Support\Facades\DB;
use App\Models\UserProfile;
use App\Services\CrudService;
use Exception;


class UserProfileService extends CrudService
{
    public static function updateUserProfiles($user, $profiles)
    {
        if (empty($profiles)) {
            try {
                DB::beginTransaction();
                UserProfile::where('users_id', $user->id)->each(function ($row, $key) {
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
        foreach ($user->profiles ?? [] as $storedProfile) {
            $keyStoredProfile = array_search($storedProfile->id, $profiles);
            if ($keyStoredProfile === false) {
                try {
                    UserProfile::destroy($storedProfile->id);
                } catch (Exception $e) {
                    DB::rollBack();
                    return false;
                }
            }

            if ($keyStoredProfile !== false) {
                unset($profiles[$keyStoredProfile]);
            }
        }

        foreach ($profiles as $profile) {
            try {
                UserProfile::create([
                    'users_id'     =>  $user->id,
                    'profiles_id'  =>  (int)$profile
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
