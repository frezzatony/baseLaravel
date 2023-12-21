<?php

namespace App\Services\System\Person;

use App\Enums\PersonType;
use App\Helpers\Crud\System\Person\PersonHelper;
use Illuminate\Support\Facades\DB;
use App\Helpers\DBHelper;
use App\Lib\Attachments;
use App\Lib\SearchFilters;
use App\Models\Address;
use App\Services\CrudService;
use App\Models\System\Person\Person;
use App\Models\System\Person\PersonContact;
use App\Services\AddressService;
use Illuminate\Database\QueryException;

class PersonService extends CrudService
{

    public static function findAllByFilters($filters = [], $params = [])
    {
        $query = DB::table(Person::getTableName())
            ->selectRaw('
                COUNT(1) OVER() AS count_items,
                ' . Person::getTableName() . '.id,' . Person::getTableName() . '.is_active, ' . Person::getTableName() . '.cpf_cnpj,
                ' . Person::getTableName() . '.name, ' . Person::getTableName() . '.social_name, ' . Person::getTableName() . '.attachments_catalog_id, 
                (CASE WHEN ' . Person::getTableName() . '.social_name NOTNULL THEN ' . Person::getTableName() . '.social_name ELSE ' . Person::getTableName() . '.name END) as name_show,                
                ' . Person::getTableName() . '.type, persons_types.value AS type_show,
                ' . Person::getTableName() . '.addresses_id, 
                ' . Person::getTableName() . '.created_at, ' . Person::getTableName() . '.updated_at
            ')
            ->join('persons_types', 'persons_types.id', '=', Person::getTableName() . '.type')
            ->join(Address::getTableName(), Address::getTableName() . '.id', '=', Person::getTableName() . '.addresses_id');

        $sql = "
            WITH 
                persons_types AS(" . DBHelper::enumAsSqlTableRecordset(PersonType::class) . "),
                addresses AS (" . Address::sqlJsonAggByAddressId() . "),
                contacts AS (" . PersonContact::sqlJsonAggByPersonId() . "),
                persons AS({$query->toSql()})
        ";

        $query = DB::table('persons')
            ->selectRaw('
                COUNT(1) OVER() AS count_filtered_items, persons.*,addresses.addresses->>0 AS address, contacts.contacts
            ')
            ->join('addresses', 'addresses.id', '=', 'persons.addresses_id')
            ->leftJoin('contacts', 'contacts.persons_id', '=', 'persons.id');

        $strWhereFilter = !empty($filters) ? SearchFilters::getStrWherere(PersonHelper::searchFilters(), $filters) : null;
        if ($strWhereFilter) {
            $query->whereRaw($strWhereFilter);
        }

        self::setDatabaseDefaults($query, PersonHelper::listItems(), $params);
        $sql .= $query->toSql();
        return parent::genLazyCollectionFromSql($sql);
    }

    public static function findById(int $id)
    {
        return self::findAllByFilters(['id' => $id], ['limit' => 1])->first();
    }

    public static function getAttachmentsCatalog($person)
    {
        return new Attachments([
            'path'                          =>  storage_path('app/catalogs/persons'),
            'catalog_id'                    =>  $person->attachments_catalog_id,
            'create_catalog_if_not_exists'  =>  !empty($person),
        ]);
    }

    public static function store($saveData)
    {
        $addressResponse = AddressService::store($saveData['address']);
        if ($addressResponse == false) {
            return false;
        }

        $attachmentCatalog = Attachments::createCatalog();
        if ($attachmentCatalog === false) {
            return false;
        }
        $saveData['person']['addresses_id'] = $addressResponse['id'];
        $saveData['person']['attachments_catalog_id'] = $attachmentCatalog->id;
        $saveData['person']['cpf_cnpj'] = preg_replace('/[^0-9]/', '', $saveData['person']['cpf_cnpj']);
        DB::beginTransaction();
        try {
            $person = Person::create($saveData['person']);
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }
        if (!PersonContactService::updatPersonContacts($person, $saveData['contacts'])) {
            DB::rollBack();
            return false;
        }

        DB::commit();
        return [
            'status'    =>  true,
            'id'        =>  $person->id,
        ];
    }

    public static function update($person, $saveData)
    {
        $address = AddressService::findById($person->addresses_id);
        $addressResponse = AddressService::update($address, $saveData['address']);
        if ($addressResponse == false) {
            return false;
        }
        $saveData['person']['cpf_cnpj'] = preg_replace('/[^0-9]/', '', $saveData['person']['cpf_cnpj']);
        DB::beginTransaction();
        try {
            Person::where('id', $person->id)->get()->first()->update($saveData['person']);
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }
        if (!PersonContactService::updatPersonContacts($person, $saveData['contacts'])) {
            DB::rollBack();
            return false;
        }

        DB::commit();
        return [
            'status'    =>  true,
            'id'        =>  $person->id,
        ];
    }

    public static function destroy($attendanceUnit)
    {
        $canDelete = DBHelper::checkCanDeleteRow('public', Person::getTableName(), $attendanceUnit->id);
        if ($canDelete !== true) {
            return [
                'status'    =>  false,
                'used_in'   =>  $canDelete['tables'],
            ];
        }

        try {
            DB::beginTransaction();
            Person::where('id', ($attendanceUnit->id))->each(function ($row, $key) {
                $row->delete();
            });

            $attachmentCatalog = self::getAttachmentsCatalog($attendanceUnit);
            $destroyAttachmentCatalogResponse = $attachmentCatalog->deleteCatalog();
            if (($destroyAttachmentCatalogResponse['status'] ?? false) == false) {
                return [
                    'status'            =>  false,
                    'console_message'   =>  $destroyAttachmentCatalogResponse['console_message'],
                ];
            }

            DB::commit();

            return [
                'status'    =>  true,
            ];
        } catch (QueryException $e) {
            DB::rollBack();
            return [
                'status'            =>  false,
                'console_message'   =>  $e,
            ];
        }
    }
}
