<?php

namespace App\Services\System;

use Illuminate\Support\Facades\DB;
use App\Helpers\Crud\System\ModuleHelper;
use App\Helpers\DBHelper;
use App\Models\System\Module;
use App\Lib\SearchFilters;
use App\Models\ProfileAction;
use App\Models\System\Routine;
use App\Models\System\RoutineAction;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\CrudService;
use Exception;

class ModuleService extends CrudService
{

    public static function findAllByFilters($filters = [], $params = [])
    {
        $query = DB::table(Module::getTableName())
            ->selectRaw('
                COUNT(1) OVER() AS count_items,
                id, name, slug, is_active, list_order, icon,
                created_at, updated_at
            ');

        if (empty($params['include_master']) || $params['include_master'] !== true) {
            $query->whereRaw("is_master <> 't'");
        }
        $sql = "
            WITH 
                modules AS({$query->toSql()}) 
        ";

        $query = DB::table('modules')
            ->selectRaw('
                COUNT(1) OVER() AS count_filtered_items, modules.*
            ');

        $strWhereFilter = !empty($filters) ? SearchFilters::getStrWherere(ModuleHelper::searchFilters(), $filters) : null;
        if ($strWhereFilter) {
            $query->whereRaw($strWhereFilter);
        }

        self::setDatabaseDefaults($query, ModuleHelper::listItems(), $params);
        $sql .= $query->toSql();
        return parent::genLazyCollectionFromSql($sql);
    }

    public static function findById(int $id, $includeMaster = false)
    {
        return self::findAllByFilters(['id' => $id], ['limit' => 1, 'include_master' => $includeMaster])->first();
    }

    public static function findBySlug(string $slug, $includeMaster = false)
    {
        return self::findAllByFilters(['slug' => $slug], ['limit' => 1, 'include_master' => $includeMaster])->first();
    }

    public static function findAllModulesActiveByUserId(int $idUser)
    {
        $query = DB::table(Module::getTableName())
            ->selectRaw('DISTINCT ' . Module::getTableName() . '.*')
            ->join(Routine::getTableName(), Routine::getTableName() . '.modules_id', '=', Module::getTableName() . '.id')
            ->join(RoutineAction::getTableName(), RoutineAction::getTableName() . '.routines_id', '=', Routine::getTableName() . '.id')
            ->join(ProfileAction::getTableName(), ProfileAction::getTableName() . '.routines_actions_id', '=', RoutineAction::getTableName() . '.id')
            ->join(UserProfile::getTableName(), UserProfile::getTableName() . '.profiles_id', '=', ProfileAction::getTableName() . '.profiles_id')
            ->join(User::getTableName(), User::getTableName() . '.id', '=', UserProfile::getTableName() . '.users_id')
            ->whereRaw(Module::getTableName() . ".is_active = 't'")
            ->whereRaw(User::getTableName() . ".id = {$idUser}")
            ->orderBy('list_order', 'ASC');
        $sql = $query->toSql();
        return parent::genLazyCollectionFromSql($sql);
    }

    public static function store($saveData)
    {
        try {
            DB::beginTransaction();
            $module = Module::create($saveData['module']);
            DB::commit();

            return [
                'status'    =>  true,
                'id'        =>  $module->id,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public static function update($module, $saveData)
    {
        try {
            DB::beginTransaction();
            Module::where('id', $module->id)->get()->first()->update($saveData['module']);
            DB::commit();

            return [
                'status'    =>  true,
                'id'        =>  $module->id,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public static function destroy($id)
    {
        $canDelete = DBHelper::checkCanDeleteRow('public', Module::getTableName(), $id);
        if ($canDelete !== true) {
            return [
                'status'    =>  false,
                'used_in'   =>  $canDelete['tables'],
            ];
        }

        try {
            DB::beginTransaction();
            Module::where('id', ($id))->each(function ($row, $key) {
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
