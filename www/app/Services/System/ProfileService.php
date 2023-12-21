<?php

namespace App\Services\System;

use App\Helpers\Crud\System\ProfileHelper;
use App\Helpers\DBHelper;
use Illuminate\Support\Facades\DB;
use App\Lib\SearchFilters;
use App\Models\Profile;
use App\Models\ProfileAction;
use App\Services\CrudService;
use App\Services\System\ProfileActionService;
use Exception;

class ProfileService extends CrudService
{
    public static function findAllByFilters($filters = [], $params = [])
    {
        $query = DB::table(Profile::getTableName())
            ->selectRaw('
                COUNT(1) OVER() AS count_items,
                id, name, is_active, can_edit,
                created_at, updated_at
            ');

        $sql = "
            WITH 
                profiles_actions AS(" . ProfileAction::sqlJsonAggByProfileId() . "),
                profiles AS({$query->toSql()}) 
        ";

        $query = DB::table('profiles')
            ->selectRaw('
                COUNT(1) OVER() AS count_filtered_items, profiles.*, profiles_actions.*
            ')
            ->leftJoin('profiles_actions', 'profiles_actions.profiles_id', '=', 'profiles.id');

        $strWhereFilter = !empty($filters) ? SearchFilters::getStrWherere(ProfileHelper::searchFilters(), $filters) : null;
        if ($strWhereFilter) {
            $query->whereRaw($strWhereFilter);
        }

        self::setDatabaseDefaults($query, ProfileHelper::listItems(), $params);
        $sql .= $query->toSql();

        return parent::genLazyCollectionFromSql($sql);
    }

    public static function findById(int $id)
    {
        return self::findAllByFilters(['id' => $id], ['limit' => 1])->first();
    }

    public static function store($saveData)
    {
        DB::beginTransaction();
        try {
            $profile = Profile::create($saveData['profile']);
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }

        $profile->actions = [];
        if (ProfileActionService::updateProfileActions($profile, $saveData['actions']) === false) {
            DB::rollBack();
            return false;
        }

        DB::commit();
        return [
            'status'    =>  true,
            'id'        =>  $profile->id,
        ];
    }

    public static function update($profile, $saveData)
    {
        DB::beginTransaction();
        try {
            Profile::where('id', $profile->id)->get()->first()->update($saveData['profile']);
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }

        if (ProfileActionService::updateProfileActions($profile, $saveData['actions']) === false) {
            DB::rollBack();
            return false;
        }

        DB::commit();

        return [
            'status'    =>  true,
            'id'        =>  $profile->id,
        ];
    }

    public static function destroy($id)
    {
        $canDelete = DBHelper::checkCanDeleteRow('public', Profile::getTableName(), $id);
        if ($canDelete !== true) {
            return [
                'status'        =>  false,
                'used_in'       =>  $canDelete['tables'] ?? null,
                'can_delete'    =>  $canDelete['can_delete'] ?? true,
            ];
        }

        try {
            DB::beginTransaction();
            Profile::where('id', ($id))->where('can_delete', 't')->each(function ($row, $key) {
                $row->delete();
            });
            DB::commit();

            return [
                'status'    =>  true,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            return [
                'status'            =>  false,
                'console_message'   =>  $e,
            ];
        }
    }
}
