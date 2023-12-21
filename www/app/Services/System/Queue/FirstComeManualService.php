<?php

namespace App\Services\System\Queue;

use App\Enums\Queues\TicketSequence;
use App\Enums\Queues\TicketWithdrawal;
use Illuminate\Support\Facades\DB;
use App\Helpers\Crud\System\ModuleHelper;
use App\Helpers\Crud\System\Queue\QueueHelper;
use App\Helpers\DBHelper;
use App\Models\System\Module;
use App\Lib\SearchFilters;
use App\Models\System\Queue\Queue;
use App\Models\System\Queue\QueueCallOrder;
use App\Models\System\Queue\QueueMatter;
use App\Models\System\Queue\QueueUserAttendant;
use App\Models\System\Queue\QueueUserManager;
use App\Services\CrudService;
use App\Services\System\CustomerService\CustomerServiceService;
use Exception;
use Illuminate\Database\QueryException;


class FirstComeManualService extends CrudService
{
    public static function findAllByFilters($filters = [], $params = [])
    {
        $query = DB::table(Queue::getTableName())
            ->selectRaw("
                COUNT(1) OVER() AS count_items,
                id, attendance_units_id, description, is_active, type, point_name, point_quantity,
                ticket_prefix, ticket_withdrawal, ticket_sequence, ticket_reset, reset_tickets_counter, last_tickets,
                max_daily_tickets
            ");

        $sql = "
            WITH 
                call_orders AS(" . QueueCallOrder::sqlJsonAggByQueueId() . "),  
                users_attendants AS(" . QueueUserAttendant::sqlJsonAggByQueueId() . "),  
                users_managers AS(" . QueueUserManager::sqlJsonAggByQueueId() . "), 
                matters AS(" . QueueMatter::sqlJsonAggByQueueId() . "),
                queues AS({$query->toSql()}) 
        ";

        $query = DB::table('queues')
            ->selectRaw('
                COUNT(1) OVER() AS count_filtered_items, queues.*, call_orders.call_orders, 
                users_attendants.users_attendants, users_managers.users_managers, matters.matters
            ')
            ->leftJoin('call_orders', 'call_orders.queues_id', '=', 'queues.id')
            ->leftJoin('users_attendants', 'users_attendants.queues_id', '=', 'queues.id')
            ->leftJoin('users_managers', 'users_managers.queues_id', '=', 'queues.id')
            ->leftJoin('matters', 'matters.queues_id', '=', 'queues.id');

        $strWhereFilter = !empty($filters) ? SearchFilters::getStrWherere(QueueHelper::searchFilters(), $filters) : null;
        if ($strWhereFilter) {
            $query->whereRaw($strWhereFilter);
        }

        self::setDatabaseDefaults($query, QueueHelper::listItems(), $params);
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
            $saveData['queue']['type'] = \App\Enums\Queues\Type::FIRST_COME_MANUAL['value'];
            $saveData['queue']['last_tickets'] = json_encode(self::getLastCallSaveData(null, $saveData));
            $queue = Queue::create($saveData['queue']);
            $queue->weekdays = collect();
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }

        if (
            !QueueCallOrderService::updateQueueCallOrders($queue, $saveData['call_orders']) ||
            !QueueUserAttendantService::updateQueueUsersAttendants($queue, $saveData['attendants']) ||
            !QueueUserManagerService::updateQueueUsersManagers($queue, $saveData['managers']) ||
            !QueueMatterService::updateQueueMatters($queue, $saveData['matters']) ||
            !FirstComeManualBookService::createBooksTable($queue)  ||
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
            $saveData['queue']['last_tickets'] = json_encode(self::getLastCallSaveData($queue, $saveData));
            temp($saveData['queue']);
            Queue::where('id', $queue->id)->get()->first()->update($saveData['queue']);
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }

        if (
            isset($saveData['call_orders']) && !QueueCallOrderService::updateQueueCallOrders($queue, $saveData['call_orders']) ||
            isset($saveData['attendants']) && !QueueUserAttendantService::updateQueueUsersAttendants($queue, $saveData['attendants']) ||
            isset($saveData['managers']) && !QueueUserManagerService::updateQueueUsersManagers($queue, $saveData['managers']) ||
            isset($saveData['matters']) && !QueueMatterService::updateQueueMatters($queue, $saveData['matters']) ||
            !FirstComeManualBookService::createBooksTable($queue) ||
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

    private static function getLastCallSaveData($queue = null, $queueSaveData)
    {
        if (!empty($queueSaveData['queue']['last_tickets'])) {
            return $queueSaveData['queue']['last_tickets'];
        }

        if (!empty($queue) && $queue->ticket_withdrawal == $queueSaveData['queue']['ticket_withdrawal'] && $queue->ticket_sequence == $queueSaveData['queue']['ticket_sequence']) {
            return $queue->last_tickets;
        }

        $lastCallTemplate =  [
            'provided_time' =>  '',
            'provided'      =>  0,
            'called'        =>  0,
        ];
        if ($queueSaveData['queue']['ticket_sequence'] == TicketSequence::PRIORITY['value']) {
            $lastCall = [];
            foreach ($queueSaveData['call_orders'] as $callOrder) {
                $lastCall[$callOrder['uuid']] = $lastCallTemplate;
            }
        }

        if ($queueSaveData['queue']['ticket_sequence'] == TicketSequence::ISSUE['value']) {
            $lastCall = [];
            foreach ($queueSaveData['call_orders'] as $callOrder) {
                $lastCall[$callOrder['uuid']] = [];
                foreach ($queueSaveData['matters'] as $matter) {
                    $lastCall[$callOrder['uuid']][$matter['uuid']] = $lastCallTemplate;
                }
            }
        }

        return $lastCall;
    }
}
