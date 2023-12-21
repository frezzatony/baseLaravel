<?php

namespace App\Services\System\Queue;

use App\Enums\Queues\BookStatus;
use App\Enums\Queues\Type;
use App\Helpers\Crud\System\Queue\QueueBookHelper;
use App\Helpers\DBHelper;
use Illuminate\Support\Facades\DB;
use App\Lib\SearchFilters;
use App\Models\System\Queue\FirstComeManualBook;
use App\Models\System\Queue\FirstComeTotemBook;
use App\Models\System\Queue\Queue;
use App\Models\System\Queue\QueueCallOrder;
use App\Models\System\Queue\QueueMatter;
use App\Models\System\Queue\QueueMatterUserAttendant;
use App\Services\CrudService;

class QueueBookService extends CrudService
{

    public static function findAllBooksByFilters($filters = [], $params = [])
    {
        $queue = !empty((int)($filters['queue_id'] ?? null)) ? QueueService::findById($filters['queue_id']) : null;
        if (empty($queue)) {
            return [];
        }

        $bookModel = self::getBookModel($queue);

        $query = DB::table("{$bookModel->table} AS BOOK_TABLE")
            ->distinct()
            ->selectRaw('
                COUNT(1) OVER() AS count_items, "BOOK_TABLE".id as book_id,
                (CASE
                    WHEN "BOOK_TABLE".ticket::TEXT ~ \'^[0-9\.]+$\' THEN LPAD("BOOK_TABLE".ticket::text,3,\'0\')::TEXT
                    ELSE "BOOK_TABLE".ticket::TEXT
                END) AS ticket, 
                ' . QueueMatter::getTableName() . '.description AS matter_description,
                ' . QueueCallOrder::getTableName() . '.description AS priority_description, status.id AS status, status.value AS status_description,
                "BOOK_TABLE".created_at
            ')
            ->join('status', 'status.id', '=', 'BOOK_TABLE.status')
            ->leftJoin(QueueMatter::getTableName(), QueueMatter::getTableName() . '.id', '=', "BOOK_TABLE.queues_matters_id")
            ->leftJoin(QueueCallOrder::getTableName(), QueueCallOrder::getTableName() . '.id', '=', "BOOK_TABLE.queues_call_orders_id")
            ->leftJoin(Queue::getTableName(), Queue::getTableName() . '.id', '=',  QueueMatter::getTableName() . '.queues_id')
            ->leftJoin(QueueMatterUserAttendant::getTableName(), QueueMatterUserAttendant::getTableName() . '.queues_matters_id', '=',  QueueMatter::getTableName() . '.id');

        $strWhereFilter = self::getStwWhereFilter($filters) ?: '';
        if ($strWhereFilter) {
            $query->whereRaw($strWhereFilter);
        }

        $sql = "
            WITH 
                status AS(" . DBHelper::enumAsSqlTableRecordset(BookStatus::class) . "),
                books AS({$query->toSql()}) 
        ";
        $query = DB::table('books')
            ->selectRaw('
                COUNT(1) OVER() AS count_filtered_items, books.*
            ');

        self::setFilterDate($filters);
        $strWhereFilter = (!empty($filters) ? SearchFilters::getStrWherere(QueueBookHelper::searchFilters(), $filters) : '');
        if ($strWhereFilter) {
            $query->whereRaw($strWhereFilter);
        }

        if (isset($params['order_by_column']['key'])) {
            $params['order_by_column']['key'] += 1;
        }

        self::setDatabaseDefaults($query, QueueBookHelper::listItems(), $params);
        $sql .= $query->toSql();
        // echo $sql;
        // exit;
        return parent::genLazyCollectionFromSql($sql);
    }

    public static function getBookModel($queue)
    {
        switch ($queue->type) {
            case Type::FIRST_COME_TOTEM['value']:
                $bookModel = new FirstComeTotemBook();
                $bookModel->setTable('books.' . FirstComeTotemBookService::getTableName($queue));
                break;
            case Type::FIRST_COME_MANUAL['value']:
                $bookModel = new FirstComeManualBook();
                $bookModel->setTable('books.' . FirstComeManualBookService::getTableName($queue));
                break;
        }
        return $bookModel;
    }

    private static function setFilterDate(&$filters)
    {
        if (!empty($filters['date_start']) && !empty($filters['date_end'])) {
            $filters['date'] = [
                [
                    'value'     => "{$filters['date_start']},{$filters['date_end']}",
                    'operator'  =>  'between'
                ]
            ];
        }

        if (!empty($filters['date_start']) || !empty($filters['date_end'])) {
            $filters['date'] = [
                [
                    'value'     =>  !empty($filters['date_start']) ? $filters['date_start'] : null,
                    'operator'  =>  'after_or_equal'
                ],
                [
                    'value'     =>  !empty($filters['date_end']) ?  $filters['date_end'] : null,
                    'operator'  =>  'before_or_equal'
                ]
            ];
        }

        if (!empty($filters['date'])) {
            $filters['date'] = [
                [
                    'value'     =>  !empty($filters['date']) ? $filters['date'] : null,
                    'operator'  =>  'equal_date'
                ],
            ];
        }
    }

    private static function getStwWhereFilter(&$filters)
    {
        $strWhereFilter = '';
        if ($filters['matter'] ?? false) {
            if ($filters['matter'] == 'my_matters') {
                $strWhereFilter .= ($strWhereFilter ? ' AND ' : '') . '(
                    ' . QueueMatterUserAttendant::getTableName() . '.users_id = ' . auth()->user()->id . '
                )';
            }

            if ((int)$filters['matter']) {
                $strWhereFilter .= ($strWhereFilter ? ' AND ' : '') . '(
                    ' . QueueMatter::getTableName() . '.id = ' . (int)$filters['matter'] . '
                )';
            }
            unset($filters['matter']);
        }
        return $strWhereFilter;
    }
}
