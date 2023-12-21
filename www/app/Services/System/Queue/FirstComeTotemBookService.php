<?php

namespace App\Services\System\Queue;

use App\Enums\Queues\Activity;
use App\Enums\Queues\BookStatus;
use App\Helpers\StringHelper;
use App\Models\System\Queue\FirstComeTotemBook;
use App\Models\System\Queue\Queue;
use App\Models\System\Queue\QueueCallOrder;
use App\Models\System\Queue\QueueMatter;
use App\Models\System\Queue\QueueMatterUserAttendant;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Services\CrudService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class FirstComeTotemBookService extends CrudService
{

    public static function findBookById($queue, int $idBook)
    {
        $bookModel = new FirstComeTotemBook();
        $query = DB::table('books.' . self::getTableName($queue) . ' AS BookTable')
            ->selectRaw('
                "BookTable".*, calls_count.calls_count,' . Queue::getTableName() . '.ticket_prefix
            ')
            ->leftJoin('calls_count', 'calls_count.id', '=', 'BookTable.id')
            ->join(Queue::getTableName(), Queue::getTableName() . '.id', '=', DB::raw($queue->id))
            ->whereRaw("\"BookTable\".id = {$idBook}");
        $sql = "
            WITH 
                calls_count AS({$bookModel->sqlCountCalls($queue)})
        " . $query->toSql();

        return parent::genLazyCollectionFromSql($sql)->first();
    }

    public static function book($queue, $idMatter, $idCallOrder)
    {
        $bookModel = new FirstComeTotemBook();
        $bookModel->setTable('books.' . self::getTableName($queue));

        try {
            DB::beginTransaction();
            $book = $bookModel->create([
                'ticket'                        =>  self::getBookTicket($queue),
                'authentication_code'           =>  StringHelper::random(16),
                'queues_matters_id'              =>  $idMatter,
                'queues_call_orders_id'         =>  $idCallOrder,
                'call_count'                    =>  0,
                'status'                        =>  BookStatus::WAITING_IN_LINE['value'],
                'activity'                      =>  json_encode([
                    [
                        'reference'     =>  'book',
                        'action'        =>  Activity::QUEUING_UP['value'],
                        'time'          =>  now()->timestamp,
                    ]
                ]),
            ]);
            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }

        return [
            'status'    =>  true,
            'book'      =>  $book,
        ];
    }

    public static function callBook($queue, int $idBook, int $idUserCall, $servicePoint)
    {
        $bookModel = new FirstComeTotemBook();
        $bookModel->setTable('books.' . self::getTableName($queue));

        try {
            DB::beginTransaction();
            $bookModel->where('id', $idBook)->get()->first()->update([
                'activity'     =>  DB::raw('activity || \'' . json_encode([
                    'reference'     =>  'book',
                    'action'        =>  Activity::CALL['value'],
                    'users_id'      =>  $idUserCall,
                    'service_point' =>  $servicePoint,
                    'time'          =>  now()->timestamp,
                ]) . '\''),
                'status'    =>  BookStatus::CALLING['value'],
            ]);
            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }

        return [
            'status'    =>  true,
            'book'      =>  self::findBookById($queue, $idBook),
        ];
    }

    public static function cancelBook($queue, int $idBook, int $idUserCall, $justification)
    {
        $bookModel = new FirstComeTotemBook();
        $bookModel->setTable('books.' . self::getTableName($queue));

        try {
            DB::beginTransaction();
            $bookModel->where('id', $idBook)->get()->first()->update([
                'activity'     =>  DB::raw('activity || \'' . json_encode([
                    'reference'     =>  'book',
                    'action'        =>  Activity::CANCELLATION['value'],
                    'users_id'      =>  $idUserCall,
                    'justification' =>  $justification,
                    'time'          =>  now()->timestamp,
                ]) . '\''),
                'status'    =>  BookStatus::CANCELED['value'],
            ]);
            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }

        return [
            'status'    =>  true,
            'book'      =>  self::findBookById($queue, $idBook),
        ];
    }

    public static function findNextAttendantBookByAttendanceUserIdAndQueue($idUser, $queue)
    {
        $query = DB::table('books.' . self::getTableName($queue) . ' AS BookTable')
            ->selectRaw('
                "BookTable".id, "BookTable".created_at, "BookTable".updated_at, 
                "BookTable".status, COALESCE(calls_count.calls_count,0) AS calls_count,
                (CASE
                    WHEN "BookTable".ticket::TEXT ~ \'^[0-9\.]+$\' THEN LPAD("BookTable".ticket::text,3,\'0\')::TEXT
                    ELSE "BookTable".ticket::TEXT
                    END             
                ) AS ticket,' . Queue::getTableName() . '.ticket_prefix,
                ' . QueueMatter::getTableName() . '.description AS matter_description, ' . QueueCallOrder::getTableName() . '.description AS call_order_description
            ')
            ->join(QueueMatter::getTableName(), QueueMatter::getTableName() . '.id', '=', 'BookTable.queues_matters_id')
            ->join(QueueMatterUserAttendant::getTableName(), QueueMatterUserAttendant::getTableName() . '.queues_matters_id', '=', QueueMatter::getTableName() . '.id')
            ->join(QueueCallOrder::getTableName(), QueueCallOrder::getTableName() . '.id', '=', 'BookTable.queues_call_orders_id')
            ->join(Queue::getTableName(), Queue::getTableName() . '.id', '=', DB::raw($queue->id))
            ->leftJoin('calls_count', 'calls_count.id', '=', 'BookTable.id')
            ->join(User::getTableName(), User::getTableName() . '.id', '=', QueueMatterUserAttendant::getTableName() . '.users_id')
            ->whereRaw(QueueMatterUserAttendant::getTableName() . ".users_id = {$idUser}")
            ->where(function ($query) use ($idUser, $queue) {
                $query
                    ->whereRaw("
                    (
                        ((\"users\".\"attributes\"->'queues_matters')::JSONB->>'{$queue->id}') ISNULL
                        OR
                        CONCAT('\"',\"BookTable\".\"queues_matters_id\",'\"') IN(SELECT jsonb_array_elements(((\"attributes\"->'queues_matters')::JSONB->>'{$queue->id}')::JSONB)::TEXT)
                    )
                    ")
                    ->whereRaw("\"BookTable\".status = '" . BookStatus::WAITING_IN_LINE['value'] . "'")
                    ->orWhere(function ($query) use ($idUser) {
                        $query
                            ->whereRaw("\"BookTable\".status = '" . BookStatus::CALLING['value'] . "'")
                            ->whereRaw("\"BookTable\".activity->-1->'users_id' = '{$idUser}'");
                    });
            })

            ->orderByRaw("
                CASE status
                    WHEN '" . BookStatus::ASSISTING['value'] . "' THEN 1
                    ELSE 2
                END
            ")
            ->orderBy(QueueCallOrder::getTableName() . '.weight', 'DESC')
            ->orderBy("BookTable.created_at")
            ->limit(1);

        $bookModel = new FirstComeTotemBook();
        $sql = "
            WITH 
                calls_count AS({$bookModel->sqlCountCalls($queue)})
        " . $query->toSql();
        return parent::genLazyCollectionFromSql($sql);
    }

    public static function createBooksTable($queue)
    {
        $schema = 'books';
        $tableName = self::getTableName($queue);
        if (!Schema::hasTable("{$schema}.{$tableName}")) {
            try {
                DB::beginTransaction();
                Schema::create("{$schema}.{$tableName}", function (Blueprint $table) use ($schema, $tableName) {
                    $table->id();
                    $table->bigInteger('id_parent')->nullable();
                    $table->smallInteger('ticket');
                    $table->string('authentication_code');
                    $table->integer('queues_matters_id');
                    $table->integer('queues_call_orders_id');
                    $table->string('status');
                    $table->integer('users_id_started')->nullable();
                    $table->integer('users_id_completed')->nullable();
                    $table->jsonb('activity')->default('[]');
                    $table->timestamp('called_at')->nullable();
                    $table->timestamp('started_at')->nullable();
                    $table->timestamp('transferred_at')->nullable();
                    $table->timestamp('completed_at')->nullable();
                    $table->timestamps();

                    $table->foreign(['id_parent'], "{$tableName}_fk_{$tableName}_parent")->references(['id'])->on("{$schema}.{$tableName}")->onUpdate('CASCADE')->onDelete('RESTRICT');
                    $table->foreign(['queues_matters_id'], "{$tableName}_fk_queues_matters")->references(['id'])->on(QueueMatter::getTableName())->onUpdate('CASCADE')->onDelete('RESTRICT');
                    $table->foreign(['queues_call_orders_id'], "{$tableName}_fk_queues_call_orders")->references(['id'])->on(QueueCallOrder::getTableName())->onUpdate('CASCADE')->onDelete('RESTRICT');
                    $table->foreign(['users_id_started'], "{$tableName}_fk_users_started")->references(['id'])->on(User::getTableName())->onUpdate('CASCADE')->onDelete('RESTRICT');
                    $table->foreign(['users_id_completed'], "{$tableName}_fk_users_completed")->references(['id'])->on(User::getTableName())->onUpdate('CASCADE')->onDelete('RESTRICT');
                });
                DB::commit();
            } catch (QueryException $e) {
                DB::rollBack();
                return false;
            }
        }

        return [
            'status'    =>  true,
        ];
    }

    public static function getTableName($queue)
    {
        return  "queue_{$queue->id}";
    }

    private static function getBookTicket($queue)
    {
        $bookModel = new FirstComeTotemBook();
        $bookModel->setTable('books.' . self::getTableName($queue));
        $maxTicket = $bookModel
            ->whereDate('created_at', '=', now()->toDateString())
            ->max('ticket');
        return $maxTicket + 1;
    }
}
