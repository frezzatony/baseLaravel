<?php

namespace App\Services\System;

use Exception;
use App\Models\User;
use App\Lib\SearchFilters;
use App\Models\UserProfile;
use App\Models\ProfileAction;
use App\Services\CrudService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Helpers\Crud\System\UserHelper;
use App\Models\System\RoutineAction;

class UserService extends CrudService
{

    public static function findAllByFilters($filters = [], $params = [])
    {
        $query = DB::table('users')
            ->selectRaw('
                COUNT(1) OVER() AS count_items,
                id, name, social_name, email, login, is_master, is_active, attributes,
                created_at, updated_at
            ')
            ->whereNull('deleted_at');

        if (empty($params['include_master']) || $params['include_master'] !== true) {
            $query->whereRaw("is_master <> 't'");
        }

        $sql = "
            WITH 
                users_profiles AS(" . UserProfile::sqlJsonAggByUserId() . "),    
                users AS({$query->toSql()})
        ";

        $query = DB::table('users')
            ->selectRaw("
                COUNT(1) OVER() AS count_filtered_items, users.*, users_profiles.*,
                (CASE WHEN users.social_name <> '' THEN users.social_name ELSE users.name END) AS name_show
            ")
            ->leftJoin(UserProfile::getTableName(), UserProfile::getTableName() . '.users_id', '=', 'users.id');

        $strWhereFilter = !empty($filters) ? SearchFilters::getStrWherere(UserHelper::searchFilters(), $filters) : null;
        if ($strWhereFilter) {
            $query->whereRaw($strWhereFilter);
        }
        self::setDatabaseDefaults($query, UserHelper::listItems(), $params);
        $sql .= $query->toSql();

        return parent::genLazyCollectionFromSql($sql);
    }

    public static function findById(int $id, $includeMaster = false)
    {
        return self::findAllByFilters(['id' => $id], ['limit' => 1, 'include_master' => $includeMaster])->first();
    }

    public static function findByLogin($login, $includeMaster = false)
    {
        return self::findAllByFilters(['login' => $login], ['limit' => 1, 'include_master' => $includeMaster])->first();
    }

    public static function findByEmail($email, $includeMaster = false)
    {
        return self::findAllByFilters(['email' => $email], ['limit' => 1, 'include_master' => $includeMaster])->first();
    }

    public static function checkUserPermissionByActionSlug($user, string $actionSlug)
    {
        if ($user->is_master) {
            return true;
        }

        return DB::table('users')
            ->selectRaw("'t'::BOOL")
            ->join(UserProfile::getTableName(), UserProfile::getTableName() . '.users_id', '=', 'users.id')
            ->join(ProfileAction::getTableName(), ProfileAction::getTableName() . '.profiles_id', '=', UserProfile::getTableName() . '.profiles_id')
            ->join(RoutineAction::getTableName(), RoutineAction::getTableName() . '.id', '=', ProfileAction::getTableName() . '.routines_actions_id')
            ->whereRaw('users.id = ' . $user->id)
            ->whereRaw(RoutineAction::getTableName() . '.slug = \'' . $actionSlug . '\'')
            ->limit(1)->exists();
    }

    public static function updateAttributes($user, $attributes = [])
    {
        $userAttributes = $user->attributes();
        foreach ($attributes as $key => $attribute) {
            $userAttributes->put($key, $attribute);
        }

        if (!(int)$userAttributes->get('module_id')) {
            $module = $user->is_master
                ? ModuleService::findAllByFilters(['slug' => 'gestao_sistema'], ['limit' => 1])->first()
                : ModuleService::findAllModulesActiveByUserId($user->id)->first();
            if (empty($module)) {
                return false;
            }
            $userAttributes->put('module_id', $module->id);
        }

        return self::update($user, [
            'user'  =>  [
                'attributes'    =>  $userAttributes->toJson(),
            ],
        ]);
    }

    public static function store($saveData)
    {
        $saveData['user']['password'] = Hash::make($saveData['user']['password']);

        DB::beginTransaction();
        try {
            $user = User::create($saveData['user']);
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }

        $user->profiles = [];
        if (UserProfileService::updateUserProfiles($user, $saveData['profiles']) === false) {
            DB::rollBack();
            return false;
        }

        DB::commit();
        return [
            'status'    =>  true,
            'id'        =>  $user->id,
        ];
    }

    public static function update($user, $saveData)
    {
        if (empty($saveData['user']['password'])) {
            unset($saveData['user']['password']);
        }
        if (!empty($saveData['user']['password'])) {
            $saveData['user']['password'] = Hash::make($saveData['user']['password']);
        }

        try {
            DB::beginTransaction();
            User::where('id', $user->id)->get()->first()->update($saveData['user']);
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }

        if (isset($saveData['profiles']) && UserProfileService::updateUserProfiles($user, $saveData['profiles']) === false) {
            DB::rollBack();
            return false;
        }

        DB::commit();
        return [
            'status'    =>  true,
            'id'        =>  $user->id,
        ];
    }

    public static function destroy($id)
    {
        return [
            'status'    =>  User::destroy($id)
        ];
    }
}
