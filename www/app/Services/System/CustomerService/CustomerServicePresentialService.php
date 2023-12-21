<?php

namespace App\Services\System\CustomerService;

use App\Enums\CustomerServices\CustomerServiceStatus;
use App\Enums\Queues\Activity;
use App\Enums\Queues\BookStatus;
use App\Enums\Queues\Type;
use App\Helpers\Crud\System\CustomerService\Forms\DefaultHelper;
use App\Helpers\CrudHelper;
use App\Lib\Attachments;
use App\Models\System\CustomerService\CustomerService;
use App\Services\CrudService;
use App\Services\System\Person\PersonService;
use App\Services\System\Queue\FirstComeManualBookService;
use App\Services\System\Queue\FirstComeTotemBookService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class CustomerServicePresentialService extends CrudService
{
    public static function findByQueueIdAndId(int $idQueue, int $idCustomerService)
    {
        return CustomerServiceService::findByQueueIdAndId($idQueue, $idCustomerService);
    }

    public static function create($queue, ?int $idBook = null, $idUserCall)
    {
        $customerServiceModel = new CustomerService();
        $customerServiceModel->setTable("customer_services.queue_{$queue->id}");
        $bookModel = CustomerServiceService::getBookModel($queue);

        DB::beginTransaction();
        if ($idBook) {
            $book = self::getBook($queue, $idBook);
            try {
                $bookModel->where('id', $book->id)->get()->first()->update([
                    'activity'  =>  DB::raw('activity || \'' . json_encode([
                        'reference'     =>  'book',
                        'action'        =>  Activity::BEGIN['value'],
                        'users_id'      =>  $idUserCall,
                        'queues_id'     =>  $queue->id,
                        'time'          =>  now()->timestamp,
                    ]) . '\''),
                    'status'    =>  BookStatus::ASSISTING['value'],
                ]);
            } catch (QueryException $e) {
                DB::rollBack();
                return false;
            }
        }

        $attachmentCatalog = Attachments::createCatalog();
        if ($attachmentCatalog === false) {
            return false;
        }
        $formDataValues = CrudHelper::getSaveDataFormDataJson(DefaultHelper::inputs());

        try {
            $customerService = $customerServiceModel->create([
                'book_id'                   =>  $book->id ?? null,
                'status'                    =>  CustomerServiceStatus::ASSISTING['value'],
                'form_data'                 =>  json_encode($formDataValues),
                'activity'                  =>  json_encode([
                    [
                        'reference'     =>  'customer_service',
                        'action'        =>  Activity::BEGIN['value'],
                        'users_id'      =>  $idUserCall,
                        'time'          =>  now()->timestamp,
                    ]
                ]),
                'attachments_catalog_id'    =>  $attachmentCatalog->id,
                'users_id_responsibility'   =>  $idUserCall,
            ]);
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }

        DB::commit();
        return [
            'status'            =>  true,
            'customer_service'  =>  CustomerServiceService::findByQueueIdAndId($queue->id, $customerService->id),
        ];
    }

    public static function update($customerService, $queue, $idUser, $saveData)
    {
        $customerServiceModel = new CustomerService();
        $customerServiceModel->setTable("customer_services.queue_{$queue->id}");

        if (!empty($saveData['person']['person']['cpf_cnpj'])) {
            $personResponse = $saveData['person']['id'] ? PersonService::update(PersonService::findById((int)$saveData['person']['id']), $saveData['person']) : PersonService::store($saveData['person']);
            if ($personResponse == false) {
                return false;
            }
            $saveData['customer_service']['person_id'] = $personResponse['id'];
        }

        $formDataValues = CrudHelper::getSaveDataFormDataJson(DefaultHelper::inputs(), $saveData['customer_service']);
        $diffValues = CrudHelper::getValuesDifference($customerService->form_data->toArray(), $formDataValues);
        if (empty($diffValues) && !$saveData['conclude'] && ($personResponse ?? false) == false) {
            return [
                'status'    =>  'nothing_to_update',
                'id'        =>  $customerService->customer_service_id,
            ];
        }

        DB::beginTransaction();
        if ($saveData['conclude'] && $customerService->book_id) {
            $bookModel = CustomerServiceService::getBookModel($queue);
            $book = self::getBook($queue, $customerService->book_id);
            try {
                $bookModel->where('id', $book->id)->get()->first()->update([
                    'activity'      =>  DB::raw('activity || \'' . json_encode([
                        'reference'     =>  'book',
                        'action'        =>  Activity::CONCLUSION['value'],
                        'users_id'      =>  $idUser,
                        'queues_id'     =>  $queue->id,
                        'time'          =>  now()->timestamp,
                    ]) . '\''),
                    'completed_at'  =>  now(),
                    'status'        =>  BookStatus::COMPLETED['value'],
                ]);
            } catch (QueryException $e) {
                DB::rollBack();
                return false;
            }
        }

        try {
            $customerServiceModel->where('id', $customerService->customer_service_id)->get()->first()->update([
                'form_data'                 =>  json_encode($formDataValues),
                'status'                    =>  DB::raw($saveData['conclude'] ? '\'' . CustomerServiceStatus::COMPLETED['value'] . '\'' : 'status'),
                'activity'                  =>  DB::raw('activity || \'' . json_encode([
                    [
                        'reference'     =>  'customer_service',
                        'action'        =>  Activity::UPDATE['value'],
                        'values'        =>  json_encode($diffValues),
                        'users_id'      =>  $idUser,
                        'time'          =>  now()->timestamp,
                    ]
                ]) . '\''),
                'completed_at'              =>  now(),
                'users_id_responsibility'   =>  $idUser,
            ]);
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }

        DB::commit();
        return [
            'status'            =>  'updated',
            'customer_service'  =>  CustomerServiceService::findByQueueIdAndId($queue->id, $customerService->customer_service_id),
            'person_id'         =>  $personResponse['id'] ?? null,
        ];
    }

    public static function rate($customerService, $queue, $idUser, $saveData)
    {
        $customerServiceModel = new CustomerService();
        $customerServiceModel->setTable("customer_services.queue_{$queue->id}");
        try {
            $customerServiceModel->where('id', $customerService->customer_service_id)->get()->first()->update([
                'tags'                      =>  $saveData['customer_service']['tags'],
                'problem_description'       =>  $saveData['customer_service']['problem_description'],
                'resolution_description'    =>  $saveData['customer_service']['resolution_description'],
                'activity'                  =>  DB::raw('activity || \'' . json_encode([
                    [
                        'reference'     =>  'customer_service',
                        'action'        =>  Activity::RATE['value'],
                        'values'        =>  '[]',
                        'users_id'      =>  $idUser,
                        'time'          =>  now()->timestamp,
                    ]
                ]) . '\''),
            ]);
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }

        DB::commit();
        return [
            'status'            =>  true,
            'customer_service'  =>  CustomerServiceService::findByQueueIdAndId($queue->id, $customerService->customer_service_id),
        ];
    }

    private static function getBook($queue, int $idBook)
    {
        switch ($queue->type) {
            case Type::FIRST_COME_TOTEM['value']:
                return FirstComeTotemBookService::findBookById($queue, $idBook);
                break;
            case Type::FIRST_COME_MANUAL['value']:
                return FirstComeManualBookService::findBookById($queue, $idBook);
                break;
        }
    }
}
