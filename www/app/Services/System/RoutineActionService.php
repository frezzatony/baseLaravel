<?php

namespace App\Services\System;

use App\Helpers\Crud\System\RoutineActionHelper;
use App\Lib\SearchFilters;
use App\Models\System\Module;
use App\Models\System\Routine;
use App\Models\System\RoutineAction;
use Illuminate\Support\Facades\DB;
use App\Services\CrudService;
use Exception;


class RoutineActionService extends CrudService
{
    public static function findAllByFilters($filters = [], $params = [])
    {
        $query = DB::table(RoutineAction::getTableName())
            ->selectRaw('
            ' . RoutineAction::getTableName() . '.id,' . RoutineAction::getTableName() . '.slug,' . RoutineAction::getTableName() . '.description,
            ' . RoutineAction::getTableName() . '.routines_id AS routine_id,' . Routine::getTableName() . '.name AS routine_name,
            ' . Routine::getTableName() . '.slug AS routine_slug, 
            ' . Module::getTableName() . '.id AS module_id,' . Module::getTableName() . '.name AS module_name,
            ' . RoutineAction::getTableName() . '.created_at, ' . RoutineAction::getTableName() . '.updated_at
            ')
            ->join(Routine::getTableName(), Routine::getTableName() . '.id', '=', RoutineAction::getTableName() . '.routines_id')
            ->join(Module::getTableName(), Module::getTableName() . '.id', '=', Routine::getTableName() . '.modules_id');

        $sql = "
            WITH 
                routines_actions AS({$query->toSql()}) 
        ";

        $query = DB::table('routines_actions')
            ->selectRaw('
                COUNT(1) OVER() AS count_filtered_items, routines_actions.*
            ');

        $strWhereFilter = !empty($filters) ? SearchFilters::getStrWherere(RoutineActionHelper::searchFilters(), $filters) : null;
        if ($strWhereFilter) {
            $query->whereRaw($strWhereFilter);
        }

        self::setDatabaseDefaults($query, RoutineActionHelper::listItems(), $params);
        $sql .= $query->toSql();

        return parent::genLazyCollectionFromSql($sql);
    }

    public static function updateRoutineActions($routine, array $actions = [])
    {
        if (empty($actions)) {
            try {
                DB::beginTransaction();
                RoutineAction::where('routines_actions_id', $routine->id)->each(function ($row, $key) {
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
        foreach ($actions as $action) {
            $storedAction = !empty($routine->routine_actions)
                ? $routine->routine_actions->filter(function ($item) use ($action) {
                    return $item->id == $action['id'];
                })->first()
                : null;

            if (empty($storedAction)) {
                try {
                    RoutineAction::create([
                        'routines_id'   =>  $routine->id,
                        'slug'          =>  $action['slug'],
                        'description'   =>  $action['description'],
                    ]);
                } catch (Exception $e) {
                    DB::rollBack();
                    return false;
                }
            }

            if (!empty($storedAction)) {
                try {
                    RoutineAction::where('id', $storedAction->id)->get()->first()->update([
                        'slug'          =>  $action['slug'],
                        'description'   =>  $action['description'],
                    ]);
                } catch (Exception $e) {
                    DB::rollBack();
                    return false;
                }
            }
        }

        if (!empty($routine->routine_actions)) {
            try {
                RoutineAction::whereIn('id', $routine->routine_actions->filter(function ($item) use ($actions) {
                    return !in_array($item->id, array_column($actions, 'id')) ? $item->id : null;
                })->pluck('id')->toArray())->each(function ($row, $key) {
                    $row->delete();
                });
            } catch (Exception $e) {
                DB::rollBack();
                return false;
            }
        }

        DB::commit();
        return [
            'status'    =>  true,
            'actions'   =>  RoutineAction::where('routines_id', $routine->id)->get(),
        ];
    }
}
