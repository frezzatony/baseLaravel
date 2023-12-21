<?php

namespace App\Services\System;

use App\Helpers\Crud\System\RoutineHelper;
use App\Helpers\DBHelper;
use Illuminate\Support\Facades\DB;
use App\Lib\SearchFilters;
use App\Models\System\Module;
use App\Models\System\Routine;
use App\Models\System\RoutineAction;
use App\Services\CrudService;
use Exception;
use Illuminate\Support\Facades\Request;

class RoutineService extends CrudService
{

    public static function findAllByFilters($filters = [], $params = [])
    {
        $query = DB::table(Routine::getTableName())
            ->selectRaw('
                COUNT(1) OVER() AS count_items,
                ' . Routine::getTableName() . '.id,  ' . Routine::getTableName() . '.name,  ' . Routine::getTableName() . '.modules_id, 
                ' . Routine::getTableName() . '.slug,  ' . Routine::getTableName() . '.is_active, 
                ' . Routine::getTableName() . '.created_at,  ' . Routine::getTableName() . '.updated_at, 
                ' . Module::getTableName() . '.id as module_id,
                ' . Module::getTableName() . '.name as module_name
            ')
            ->join(Module::getTableName(), Module::getTableName() . '.id', '=', Routine::getTableName() . '.modules_id');

        $sql = "
            WITH 
                routine_actions AS(" . RoutineAction::sqlJsonAggByRoutineId() . "),   
                routines AS({$query->toSql()}) 
        ";

        $query = DB::table('routines')
            ->selectRaw('
                COUNT(1) OVER() AS count_filtered_items, routines.*, routine_actions.*
            ')
            ->leftJoin('routine_actions', 'routine_actions.routines_id', '=', 'routines.id');

        $strWhereFilter = !empty($filters) ? SearchFilters::getStrWherere(RoutineHelper::searchFilters(), $filters) : null;
        if ($strWhereFilter) {
            $query->whereRaw($strWhereFilter);
        }

        self::setDatabaseDefaults($query, RoutineHelper::listItems(), $params);
        $sql .= $query->toSql();

        return parent::genLazyCollectionFromSql($sql);
    }

    public static function findById(int $id)
    {
        return self::findAllByFilters(['id' => $id], ['limit' => 1])->first();
    }

    public static function store($saveData)
    {
        try {
            DB::beginTransaction();
            $routine = Routine::create($saveData['routine']);
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }

        if (!empty($saveData['actions'])) {
            $actionsResponse = RoutineActionService::updateRoutineActions($routine, $saveData['actions']);
            if ($actionsResponse === false) {
                DB::rollBack();
                return false;
            }
        }

        DB::commit();
        return [
            'status'    =>  true,
            'id'        =>  $routine->id,
            'actions'   =>  $actionsResponse['actions'] ?? [],
        ];
    }

    public static function update($routine, $saveData)
    {
        try {
            DB::beginTransaction();
            Routine::where('id', $routine->id)->get()->first()->update($saveData['routine']);
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }

        $actionsResponse = RoutineActionService::updateRoutineActions($routine, $saveData['actions']);
        if ($actionsResponse === false) {
            DB::rollBack();
            return false;
        }

        DB::commit();
        return [
            'status'    =>  true,
            'id'        =>  $routine->id,
            'actions'   =>  $actionsResponse['actions'],
        ];
    }

    public static function destroy($id)
    {
        $canDelete = DBHelper::checkCanDeleteRow('public', Routine::getTableName(), $id);
        if ($canDelete !== true) {
            return [
                'status'    =>  false,
                'used_in'   =>  $canDelete['tables'],
            ];
        }

        try {
            DB::beginTransaction();
            Routine::where('id', ($id))->each(function ($row, $key) {
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
