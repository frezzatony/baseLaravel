<?php

namespace App\Services\System\Contact;

use App\Helpers\Crud\System\Contact\ContactTypeHelper;
use Illuminate\Support\Facades\DB;
use App\Lib\SearchFilters;
use App\Models\System\Contact\ContactType;
use App\Services\CrudService;


class ContactTypeService extends CrudService
{
    public static function findAllByFilters($filters = [], $params = [])
    {
        $query = DB::table(ContactType::getTableName())
            ->selectRaw('
                COUNT(1) OVER() AS count_items,
                id, description, is_active,
                created_at, updated_at
            ');

        $sql = "
            WITH 
                contacs_types AS({$query->toSql()}) 
        ";

        $query = DB::table('contacs_types')
            ->selectRaw('
                COUNT(1) OVER() AS count_filtered_items, contacs_types.*
            ');

        $strWhereFilter = !empty($filters) ? SearchFilters::getStrWherere(ContactTypeHelper::searchFilters(), $filters) : null;
        if ($strWhereFilter) {
            $query->whereRaw($strWhereFilter);
        }

        self::setDatabaseDefaults($query, ContactTypeHelper::listItems(), $params);
        $sql .= $query->toSql();
        return parent::genLazyCollectionFromSql($sql);
    }

    public static function findById(int $id, $includeMaster = false)
    {
        return self::findAllByFilters(['id' => $id], ['limit' => 1,])->first();
    }
}
