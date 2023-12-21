<?php

namespace App\Services\System\Messages;

use App\Helpers\Crud\System\Messages\CategoryHelper;
use Illuminate\Support\Facades\DB;
use App\Helpers\DBHelper;
use App\Lib\SearchFilters;
use App\Models\System\Messages\Category;
use App\Services\CrudService;
use Exception;

class CategoryService extends CrudService
{
    public static function findAllByFilters($filters = [], $params = [])
    {
        $query = DB::table(Category::getTableName())
            ->selectRaw('
                COUNT(1) OVER() AS count_items,
                id, description, is_active,
                created_at, updated_at
            ');

        $sql = "
            WITH 
                categories AS({$query->toSql()}) 
        ";

        $query = DB::table('categories')
            ->selectRaw('
                COUNT(1) OVER() AS count_filtered_items, categories.*
            ');

        $strWhereFilter = !empty($filters) ? SearchFilters::getStrWherere(CategoryHelper::searchFilters(), $filters) : null;
        if ($strWhereFilter) {
            $query->whereRaw($strWhereFilter);
        }

        self::setDatabaseDefaults($query, CategoryHelper::listItems(), $params);
        $sql .= $query->toSql();

        return parent::genLazyCollectionFromSql($sql);
    }

    public static function findById(int $id)
    {
        return self::findAllByFilters(['id' => $id], ['limit' => 1,])->first();
    }

    public static function store($saveData)
    {
        try {
            DB::beginTransaction();
            $holiday = Category::create($saveData['category']);
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

    public static function update($category, $saveData)
    {
        try {
            DB::beginTransaction();
            Category::where('id', $category->id)->get()->first()->update($saveData['category']);
            DB::commit();

            return [
                'status'    =>  true,
                'id'        =>  $category->id,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public static function destroy($id)
    {
        $canDelete = DBHelper::checkCanDeleteRow('', Category::getTableName(), $id);
        if ($canDelete !== true) {
            return [
                'status'    =>  false,
                'used_in'   =>  $canDelete['tables'],
            ];
        }

        try {
            DB::beginTransaction();
            Category::where('id', ($id))->each(function ($row, $key) {
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
