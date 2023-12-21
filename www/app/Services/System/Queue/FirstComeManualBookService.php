<?php

namespace App\Services\System\Queue;

use App\Enums\Queues\Activity;
use App\Enums\Queues\BookStatus;
use App\Enums\Queues\TicketSequence;
use App\Helpers\StringHelper;
use App\Models\System\Queue\FirstComeManualBook;
use App\Models\System\Queue\Queue;
use App\Models\System\Queue\QueueCallOrder;
use App\Models\System\Queue\QueueMatter;
use App\Models\System\Queue\QueueMatterUserAttendant;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Services\CrudService;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class FirstComeManualBookService extends CrudService
{

    public static function findBookById($queue, int $idBook)
    {
        $bookModel = new FirstComeManualBook();
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

    public static function book($queue, $uuidPriority, $uuidMatter = null, int $ticket)
    {
        $bookModel = new FirstComeManualBook();
        $bookModel->setTable('books.' . self::getTableName($queue));

        $priority = QueueCallOrderService::findByUuid($uuidPriority);
        $matter = null;
        if ($uuidMatter) {
            $matter = QueueMatterService::findByUuid($uuidMatter);
        }

        if (in_array($queue->ticket_sequence, [TicketSequence::ISSUE['value']])) {
            $ticketSave = $ticket;
        }
        if (in_array($queue->ticket_sequence, [TicketSequence::PRIORITY['value']])) {
            $ticketSave = (!empty($matter) ? mb_strtoupper($matter->description) :  mb_strtoupper($priority->description)) . ' ' . $ticket;
        }

        DB::beginTransaction();
        try {
            $book = $bookModel->create([
                'ticket'                        => $ticketSave,
                'authentication_code'           =>  StringHelper::random(16),
                'queues_matters_id'              =>  $matter->id ?? null,
                'queues_call_orders_id'         =>  $priority->id,
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
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }

        $queueLastTickets = [];
        foreach ($queue->last_tickets as $uuid => $lastTicket) {
            if (in_array($queue->ticket_sequence, [TicketSequence::ISSUE['value']])) {
                $lastTicket = collect($lastTicket)->map(function ($matter, $uuidMatterTicket) use ($ticket, $uuidMatter) {
                    if ($uuidMatterTicket == $uuidMatter) {
                        $matter->provided = $ticket;
                        $matter->provided_time = now();
                    }
                    return $matter;
                });
            }
            if (in_array($queue->ticket_sequence, [TicketSequence::PRIORITY['value']])) {
                if ($uuid == $uuidPriority) {
                    $lastTicket->provided = $ticket;
                    $lastTicket->provided_time = now();
                }
            }
            $queueLastTickets[$uuid] = $lastTicket;
        }

        if (FirstComeManualService::update($queue, ['queue' => ['last_tickets' => $queueLastTickets]]) == false) {
            return false;
        }

        DB::commit();
        return [
            'status'    =>  true,
            'book'      =>  $book,
        ];
    }

    public static function callBook($queue, int $idBook, int $idUserCall, $servicePoint)
    {
        $bookModel = new FirstComeManualBook();
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
        $bookModel = new FirstComeManualBook();
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
                ) AS ticket, ' . Queue::getTableName() . '.ticket_prefix,
                ' . QueueMatter::getTableName() . '.description AS matter_description, ' . QueueCallOrder::getTableName() . '.description AS call_order_description
            ')
            ->leftJoin(QueueMatter::getTableName(), QueueMatter::getTableName() . '.id', '=', 'BookTable.queues_matters_id')
            ->leftJoin(QueueMatterUserAttendant::getTableName(), QueueMatterUserAttendant::getTableName() . '.queues_matters_id', '=', QueueMatter::getTableName() . '.id')
            ->join(QueueCallOrder::getTableName(), QueueCallOrder::getTableName() . '.id', '=', 'BookTable.queues_call_orders_id')
            ->join(Queue::getTableName(), Queue::getTableName() . '.id', '=', DB::raw($queue->id))
            ->leftJoin('calls_count', 'calls_count.id', '=', 'BookTable.id')
            ->whereRaw("
                (
                    CASE
                        WHEN \"BookTable\".queues_matters_id NOTNULL THEN " . QueueMatterUserAttendant::getTableName() . ".users_id = {$idUser}
                        ELSE TRUE
                    END
                )
            ")
            ->where(function ($query) use ($idUser) {
                $query
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

        $bookModel = new FirstComeManualBook();
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
                    $table->string('ticket');
                    $table->string('authentication_code');
                    $table->integer('queues_matters_id')->nullable();
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

    public static function getBookTicketsByMatterSequence($queue)
    {
        return $queue->call_orders->map(function ($priority) use ($queue) {
            $priority->tickets = $queue->matters->map(function ($matter) use ($queue, $priority) {
                $priorityTickets = collect($queue->last_tickets->get($priority->uuid));
                $lastTicket = $priorityTickets->get($matter->uuid);
                $matter->ticket = 1;

                $lastTicketTime = Carbon::now();
                $now = Carbon::now();

                if ($lastTicket->provided_time) {
                    $lastTicketTime = new Carbon($lastTicket->provided_time);
                }
                if ($lastTicketTime->diff($now)->days == 0 && (empty($queue->reset_tickets_counter) || (($lastTicket->provided + 1) <= $queue->reset_tickets_counter))) {
                    $matter->ticket  = $lastTicket->provided + 1;
                }
                return $matter;
            });
            return $priority;
        });


        return $queue->matters->map(function ($matter) use ($queue) {
            $lastTicket = $queue->last_tickets->get($matter->uuid);
            $matter->ticket = 1;

            $lastTicketTime = Carbon::now();
            $now = Carbon::now();

            if ($lastTicket->provided_time) {
                $lastTicketTime = new Carbon($lastTicket->provided_time);
            }
            if ($lastTicketTime->diff($now)->days == 0 && (empty($queue->reset_tickets_counter) || (($lastTicket->provided + 1) <= $queue->reset_tickets_counter))) {
                $matter->ticket  = $lastTicket->provided + 1;
            }
            return $matter;
        });
    }

    public static function getBookTicketsByPrioritySequence($queue)
    {
        return $queue->call_orders->map(function ($priority) use ($queue) {
            $lastTicket = $queue->last_tickets->get($priority->uuid);
            $priority->ticket = 1;

            $lastTicketTime = Carbon::now();
            $now = Carbon::now();

            if ($lastTicket->provided_time) {
                $lastTicketTime = new Carbon($lastTicket->provided_time);
            }

            if (!$queue->ticket_reset) {
                $priority->ticket  = $lastTicket->provided + 1;
            }

            if (
                $queue->ticket_reset &&
                $lastTicketTime->diff($now)->days == 0 &&
                (empty($queue->reset_tickets_counter) || (($lastTicket->provided + 1) <= $queue->reset_tickets_counter))
            ) {
                $priority->ticket  = $lastTicket->provided + 1;
            }
            return $priority;
        });
    }

    public static function getTableName($queue)
    {
        return  "queue_{$queue->id}";
    }
}
