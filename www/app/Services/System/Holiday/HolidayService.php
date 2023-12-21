<?php

namespace App\Services\System\Holiday;

use App\Helpers\Crud\System\Holiday\HolidayHelper;
use Illuminate\Support\Facades\DB;
use App\Helpers\DBHelper;
use App\Lib\SearchFilters;
use App\Models\System\Holiday;
use App\Services\CrudService;
use Carbon\Carbon;
use Exception;

class HolidayService extends CrudService
{

    public static function findAllByFilters($filters = [], $params = [])
    {
        $query = DB::table(Holiday::getTableName())
            ->selectRaw('
                COUNT(1) OVER() AS count_items,
                id, description, date, type, optional, annual, time_start, time_end,
                created_at, updated_at
            ');

        $sql = "
            WITH 
                holidays AS({$query->toSql()}) 
        ";

        $query = DB::table('holidays')
            ->selectRaw('
                COUNT(1) OVER() AS count_filtered_items, holidays.*
            ');

        $strWhereFilter = !empty($filters) ? SearchFilters::getStrWherere(HolidayHelper::searchFilters(), $filters) : null;
        if ($strWhereFilter) {
            $query->whereRaw($strWhereFilter);
        }

        self::setDatabaseDefaults($query, HolidayHelper::listItems(), $params);
        $sql .= $query->toSql();

        return parent::genLazyCollectionFromSql($sql);
    }

    public static function findById(int $id)
    {
        return self::findAllByFilters(['id' => $id], ['limit' => 1,])->first();
    }

    public static function findBySlug(string $slug)
    {
        return self::findAllByFilters(['slug' => $slug], ['limit' => 1,])->first();
    }

    public static function store($saveData)
    {
        try {
            DB::beginTransaction();
            $saveData['holiday']['date'] = new Carbon($saveData['holiday']['date']);
            $saveData['holiday']['date'] = $saveData['holiday']['annual'] == 't' ? $saveData['holiday']['date']->year('1900') : $saveData['holiday']['date'];
            $holiday = Holiday::create($saveData['holiday']);
            DB::commit();

            return [
                'status'    =>  true,
                'id'        =>  $holiday->id,
            ];
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return false;
        }
    }

    public static function update($holiday, $saveData)
    {
        try {
            DB::beginTransaction();
            Holiday::where('id', $holiday->id)->get()->first()->update($saveData['holiday']);
            DB::commit();

            return [
                'status'    =>  true,
                'id'        =>  $holiday->id,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public static function destroy($id)
    {
        $canDelete = DBHelper::checkCanDeleteRow('public', Holiday::getTableName(), $id);
        if ($canDelete !== true) {
            return [
                'status'    =>  false,
                'used_in'   =>  $canDelete['tables'],
            ];
        }

        try {
            DB::beginTransaction();
            Holiday::where('id', ($id))->each(function ($row, $key) {
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
