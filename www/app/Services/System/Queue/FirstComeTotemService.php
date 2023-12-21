<?php

namespace App\Services\System\Queue;

use Illuminate\Support\Facades\DB;
use App\Helpers\Crud\System\ModuleHelper;
use App\Helpers\DBHelper;
use App\Models\System\Module;
use App\Lib\SearchFilters;
use App\Models\System\Queue\Queue;
use App\Models\System\Queue\QueueCalendar;
use App\Models\System\Queue\QueueCallOrder;
use App\Models\System\Queue\QueueMatter;
use App\Models\System\Queue\QueueOperationWeekday;
use App\Models\System\Queue\QueueUserAttendant;
use App\Models\System\Queue\QueueUserManager;
use App\Models\User;
use App\Services\CrudService;
use App\Services\System\CustomerService\CustomerServiceService;
use Exception;
use Illuminate\Database\QueryException;


class FirstComeTotemService extends CrudService
{
    public static function findAllByFilters($filters = [], $params = [])
    {
        $query = DB::table(Queue::getTableName())
            ->selectRaw("
                COUNT(1) OVER() AS count_items,
                id, attendance_units_id, description, is_active, type, point_name, point_quantity,
                ticket_prefix, max_daily_tickets
            ");

        $sql = "
            WITH 
                weekdays AS(" . QueueOperationWeekday::sqlJsonAggByQueueId() . "),  
                call_orders AS(" . QueueCallOrder::sqlJsonAggByQueueId() . "),  
                users_attendants AS(" . QueueUserAttendant::sqlJsonAggByQueueId() . "),  
                users_managers AS(" . QueueUserManager::sqlJsonAggByQueueId() . "), 
                matters AS(" . QueueMatter::sqlJsonAggByQueueId() . "),
                calendar AS(" . QueueCalendar::sqlJsonAggByQueueId() . "),
                queues AS({$query->toSql()}) 
        ";

        $query = DB::table('queues')
            ->selectRaw('
                COUNT(1) OVER() AS count_filtered_items, queues.*, weekdays.weekdays, call_orders.call_orders, 
                users_attendants.users_attendants, users_managers.users_managers, matters.matters, calendar.calendar
            ')
            ->leftJoin('weekdays', 'weekdays.queues_id', '=', 'queues.id')
            ->leftJoin('call_orders', 'call_orders.queues_id', '=', 'queues.id')
            ->leftJoin('users_attendants', 'users_attendants.queues_id', '=', 'queues.id')
            ->leftJoin('users_managers', 'users_managers.queues_id', '=', 'queues.id')
            ->leftJoin('matters', 'matters.queues_id', '=', 'queues.id')
            ->leftJoin('calendar', 'calendar.queues_id', '=', 'queues.id');

        $strWhereFilter = !empty($filters) ? SearchFilters::getStrWherere(ModuleHelper::searchFilters(), $filters) : null;
        if ($strWhereFilter) {
            $query->whereRaw($strWhereFilter);
        }

        self::setDatabaseDefaults($query, ModuleHelper::listItems(), $params);
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
            $saveData['queue']['type'] = \App\Enums\Queues\Type::FIRST_COME_TOTEM['value'];
            $queue = Queue::create($saveData['queue']);
            $queue->weekdays = collect();
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }

        if (
            !QueueOperationWeekdayService::updateQueueOperationWeekdays($queue, $saveData['weekdays']) ||
            !QueueCallOrderService::updateQueueCallOrders($queue, $saveData['call_orders']) ||
            !QueueUserAttendantService::updateQueueUsersAttendants($queue, $saveData['attendants']) ||
            !QueueUserManagerService::updateQueueUsersManagers($queue, $saveData['managers']) ||
            !QueueMatterService::updateQueueMatters($queue, $saveData['matters']) ||
            !QueueCalendarService::updateQueueCalendarDates($queue, $saveData['calendar']) ||
            !FirstComeTotemBookService::createBooksTable($queue) ||
            !CustomerServiceService::createCustomerServiceQueueTable($queue)
        ) {
            DB::rollBack();
            return false;
        }

        DB::commit();
        return [
            'status'    =>  true,
            'id'        =>  $queue->id,
        ];
    }

    public static function update($queue, $saveData)
    {
        DB::beginTransaction();
        try {
            Queue::where('id', $queue->id)->get()->first()->update($saveData['queue']);
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }

        if (
            !QueueOperationWeekdayService::updateQueueOperationWeekdays($queue, $saveData['weekdays']) ||
            !QueueCallOrderService::updateQueueCallOrders($queue, $saveData['call_orders']) ||
            !QueueUserAttendantService::updateQueueUsersAttendants($queue, $saveData['attendants']) ||
            !QueueUserManagerService::updateQueueUsersManagers($queue, $saveData['managers']) ||
            !QueueMatterService::updateQueueMatters($queue, $saveData['matters']) ||
            !QueueCalendarService::updateQueueCalendarDates($queue, $saveData['calendar']) ||
            !FirstComeTotemBookService::createBooksTable($queue) ||
            !CustomerServiceService::createCustomerServiceQueueTable($queue)
        ) {
            DB::rollBack();
            return false;
        }

        DB::commit();
        return [
            'status'    =>  true,
            'id'        =>  $queue->id,
        ];
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
