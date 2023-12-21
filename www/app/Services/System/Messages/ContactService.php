<?php

namespace App\Services\System\Messages;

use App\Helpers\Crud\System\Messages\ContactHelper;
use Illuminate\Support\Facades\DB;
use App\Helpers\DBHelper;
use App\Lib\SearchFilters;
use App\Models\System\Messages\Contact;
use App\Services\CrudService;
use Exception;

class ContactService extends CrudService
{
    public static function findAllByFilters($filters = [], $params = [])
    {
        $query = DB::table(Contact::getTableName())
            ->selectRaw('
                COUNT(1) OVER() AS count_items,
                id, name, telegram, email, is_active,
                created_at, updated_at
            ');

        $sql = "
            WITH 
                contacts AS({$query->toSql()}) 
        ";

        $query = DB::table('contacts')
            ->selectRaw('
                COUNT(1) OVER() AS count_filtered_items, contacts.*
            ');

        $strWhereFilter = !empty($filters) ? SearchFilters::getStrWherere(ContactHelper::searchFilters(), $filters) : null;
        if ($strWhereFilter) {
            $query->whereRaw($strWhereFilter);
        }

        self::setDatabaseDefaults($query, ContactHelper::listItems(), $params);
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
            $holiday = Contact::create($saveData['contact']);
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

    public static function update($contact, $saveData)
    {
        try {
            DB::beginTransaction();
            Contact::where('id', $contact->id)->get()->first()->update($saveData['contact']);
            DB::commit();

            return [
                'status'    =>  true,
                'id'        =>  $contact->id,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public static function destroy($id)
    {
        $canDelete = DBHelper::checkCanDeleteRow('', Contact::getTableName(), $id);
        if ($canDelete !== true) {
            return [
                'status'    =>  false,
                'used_in'   =>  $canDelete['tables'],
            ];
        }

        try {
            DB::beginTransaction();
            Contact::where('id', ($id))->each(function ($row, $key) {
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
