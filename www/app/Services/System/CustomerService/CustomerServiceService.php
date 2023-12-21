<?php

namespace App\Services\System\CustomerService;

use App\Enums\Queues\Type;
use App\Helpers\Crud\System\CustomerService\CustomerServiceHelper;
use App\Lib\Attachments;
use App\Lib\SearchFilters;
use App\Models\AttachmentCatalog;
use App\Models\System\AttendanceUnit\AttendanceUnit;
use App\Models\System\Queue\FirstComeManualBook;
use App\Models\System\Queue\FirstComeTotemBook;
use App\Models\System\Queue\Queue;
use App\Models\System\Queue\QueueMatter;
use App\Models\User;
use App\Services\CrudService;
use App\Services\System\Queue\FirstComeManualBookService;
use App\Services\System\Queue\FirstComeTotemBookService;
use App\Services\System\Queue\QueueService;
use App\Services\System\UserService;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CustomerServiceService extends CrudService
{

    public static function findAllByFilters($filters = [], $params = [])
    {
        $query = DB::table(DB::raw("public.findAllCustomerServices() AS \"CustomerServicesTable\""))
            ->selectRaw('
                "CustomerServicesTable".*, ' . Queue::getTableName() . '.description AS queue_description,
                ' . Queue::getTableName() . '.ticket_prefix, ' . Queue::getTableName() . '.point_name,
                ' . AttendanceUnit::getTableName() . '.name AS attendance_unit_name, 
                ' . QueueMatter::getTableName() . '.description AS matter_description
            ')
            ->join(Queue::getTableName(), Queue::getTableName() . '.id', '=', 'CustomerServicesTable.queue_id')
            ->join(AttendanceUnit::getTableName(), AttendanceUnit::getTableName() . '.id', '=', Queue::getTableName() . '.attendance_units_id')
            ->leftJoin(QueueMatter::getTableName(), QueueMatter::getTableName() . '.id', '=', 'CustomerServicesTable.queues_matters_id');
        $strWhereFilter = !empty($filters) ? SearchFilters::getStrWherere(CustomerServiceHelper::searchFilters(), $filters) : null;
        if ($strWhereFilter) {
            $query->whereRaw($strWhereFilter);
        }

        self::setDatabaseDefaults($query, CustomerServiceHelper::listItems(), $params);
        return parent::genLazyCollectionFromSql($query->toSql());
    }

    public static function findByQueueIdAndId(int $idQueue, int $idCustomerService)
    {
        $customerService = self::findAllByFilters(['id' => $idCustomerService, 'queue_id' => $idQueue], ['limit' => 1,])->first();
        if (empty($customerService)) {
            return null;
        }

        $customerService->activity = $customerService->activity->sortByDesc(function ($item) {
            return $item->time;
        });
        return $customerService;
    }

    public static function findNextAttendantBookByAttendanceUserIdAndQueueIdAndFilters(array $filters)
    {
        if (empty($filters['queue_id']) || empty((int)$filters['queue_id'])) {
            return [];
        }
        if (empty($filters['user_id'])) {
            $filters['user_id'] = Auth::user()->id;
        }

        $queues = QueueService::findAllQueuesByUserId($filters['user_id'])->filter(function ($queue) use ($filters) {
            return $queue->id == $filters['queue_id'];
        });

        if (empty($queues->first())) {
            return [];
        }

        switch ($queues->first()->type) {
            case Type::FIRST_COME_TOTEM['value']:
                return FirstComeTotemBookService::findNextAttendantBookByAttendanceUserIdAndQueue($filters['user_id'], $queues->first());
                break;
            case Type::FIRST_COME_MANUAL['value']:
                return FirstComeManualBookService::findNextAttendantBookByAttendanceUserIdAndQueue($filters['user_id'], $queues->first());
                break;
        }
    }

    public static function findAssistingByAttendanceByUserId($filters = [])
    {
        if (empty($filters['user_id'])) {
            $filters['user_id'] = Auth::user()->id;
        }
        $user = UserService::findById($filters['user_id'], true);
        if (empty($user->attributes['in_service']->queue)) {
            return null;
        }

        $queues = QueueService::findAllQueuesByUserId($filters['user_id'])->filter(function ($queue) use ($user) {
            return $queue->id == $user->attributes['in_service']->queue;
        });

        if (empty($queues->first())) {
            return [];
        }

        return self::findAllByFilters([
            'id'   =>  (int)$user->attributes['in_service']->customer_service,
            'queue_id'              =>  (int)$user->attributes['in_service']->queue,
            'user_id'               =>  (int)$filters['user_id']
        ], [
            'limit' => 1
        ]);
    }

    public static function getAttachmentsCatalog($customerService)
    {
        return new Attachments([
            'path'                          =>  storage_path('app/customerservice/presential'),
            'catalog_id'                    =>  $customerService->attachments_catalog_id,
            'create_catalog_if_not_exists'  =>  true,
        ]);
    }

    public static function createCustomerServiceQueueTable($queue)
    {
        $schema = 'customer_services';
        $tableName = "queue_{$queue->id}";
        $bookModel = self::getBookModel($queue);

        if (!Schema::hasTable("{$schema}.{$tableName}")) {
            try {
                DB::beginTransaction();
                Schema::create("{$schema}.{$tableName}", function (Blueprint $table) use ($tableName, $bookModel) {
                    $table->id();
                    $table->bigInteger('book_id')->nullable();
                    $table->string('status');
                    $table->jsonb('activity')->default('[]');
                    $table->jsonb('form_data')->default('[]');
                    $table->jsonb('tags')->default('[]');
                    $table->bigInteger('attachments_catalog_id');
                    $table->integer('users_id_responsibility');
                    $table->text('problem_description')->nullable();
                    $table->text('resolution_description')->nullable();
                    $table->timestamp('transferred_at')->nullable();
                    $table->timestamp('completed_at')->nullable();
                    $table->timestamps();

                    $table->foreign(['book_id'], "{$tableName}_fk_book")->references(['id'])->on($bookModel->table)->onUpdate('CASCADE')->onDelete('RESTRICT');
                    $table->foreign(['users_id_responsibility'], "{$tableName}_fk_users_")->references(['id'])->on(User::getTableName())->onUpdate('CASCADE')->onDelete('RESTRICT');
                    $table->foreign(['attachments_catalog_id'], "{$tableName}_fk_attachments_catalog")->references(['id'])->on(AttachmentCatalog::getTableName())->onUpdate('CASCADE')->onDelete('RESTRICT');
                });
                DB::commit();
            } catch (QueryException $e) {
                print_R($e->getMessage());
                exit;
                DB::rollBack();
                return false;
            }
        }

        return [
            'status'    =>  true,
        ];
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
}
