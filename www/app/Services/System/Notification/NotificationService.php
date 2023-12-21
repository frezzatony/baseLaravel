<?php

namespace App\Services\System\Notification;

use App\Helpers\Crud\System\Notification\NotificationHelper;
use Illuminate\Support\Facades\DB;
use App\Helpers\DBHelper;
use App\Lib\SearchFilters;
use App\Services\CrudService;
use Exception;
use Illuminate\Notifications\DatabaseNotification;

class NotificationService extends CrudService
{

    public static function findAllByFilters($filters = [], $params = [])
    {
        $query = DB::table(with(new DatabaseNotification)->getTable())
            ->selectRaw("
                COUNT(1) OVER() AS count_items, 
                id, notifiable_id,
                data->>'author' AS author, data->>'title' AS title, data->>'resume' AS resume, data->>'text' AS text,
                read_at, created_at, updated_at
            ");

        if (empty($params['is_master']) || $params['is_master'] != true) {
            $query->whereRaw('notifiable_id = ' . auth()->user()->id);
        }

        $sql = "
            WITH 
                notifications AS({$query->toSql()}) 
        ";

        $query = DB::table('notifications')
            ->selectRaw('
                COUNT(1) OVER() AS count_filtered_items, notifications.*
            ');

        $strWhereFilter = !empty($filters) ? SearchFilters::getStrWherere(NotificationHelper::searchFilters(), $filters) : null;
        if ($strWhereFilter) {
            $query->whereRaw($strWhereFilter);
        }

        self::setDatabaseDefaults($query, NotificationHelper::listItems(), $params);
        $sql .= $query->toSql();
        return parent::genLazyCollectionFromSql($sql);
    }

    public static function findById($id)
    {
        return self::findAllByFilters(['id' => $id], ['limit' => 1,])->first();
    }

    public static function markAsRead($id)
    {
        $notification = DatabaseNotification::where([
            ['id', $id],
        ])
            ->limit(1)
            ->get()
            ->first();
        $notification->markAsRead();
    }

    public static function destroy($id)
    {
        $canDelete = DBHelper::checkCanDeleteRow('public', with(new DatabaseNotification)->getTable(), $id);
        if ($canDelete !== true) {
            return [
                'status'    =>  false,
                'used_in'   =>  $canDelete['tables'],
            ];
        }

        try {
            DB::beginTransaction();
            DatabaseNotification::where('id', ($id))->each(function ($row, $key) {
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
