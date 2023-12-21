<?php

namespace App\Services\System\AttendanceUnit;

use App\Helpers\Crud\System\AttendanceUnit\AttendanceUnitHelper;
use App\Helpers\DBHelper;
use App\Lib\Attachments;
use App\Lib\SearchFilters;
use App\Models\Address;
use App\Models\System\AttendanceUnit\AttendanceUnit;
use App\Models\System\AttendanceUnit\AttendanceUnitManagerUser;
use App\Services\AddressService;
use App\Services\CrudService;
use Exception;
use Illuminate\Support\Facades\DB;

class AttendanceUnitService extends CrudService
{
    public static function findAllByFilters($filters = [], $params = [])
    {
        $query = DB::table(AttendanceUnit::getTableName())
            ->selectRaw('
                COUNT(1) OVER() AS count_items,
                ' . AttendanceUnit::getTableName() . '.id, ' . AttendanceUnit::getTableName() . '.name, ' . AttendanceUnit::getTableName() . '.slug, ' . AttendanceUnit::getTableName() . '.attachments_catalog_id,
                ' . AttendanceUnit::getTableName() . '.web_page, ' . AttendanceUnit::getTableName() . '.is_active,
                ' . AttendanceUnit::getTableName() . '.addresses_id, 
                created_at, updated_at
            ')
            ->join(Address::getTableName(), Address::getTableName() . '.id', '=', AttendanceUnit::getTableName() . '.addresses_id');

        $sql = "
            WITH 
                managers_users AS (" . AttendanceUnitManagerUser::sqlJsonAggByAttendanceUnitId() . "),
                addresses AS (" . Address::sqlJsonAggByAddressId() . "),
                attendance_units AS({$query->toSql()})
        ";

        $query = DB::table('attendance_units')
            ->selectRaw('
                COUNT(1) OVER() AS count_filtered_items, attendance_units.*,managers_users.*,addresses.addresses
            ')
            ->leftJoin('managers_users', 'managers_users.attendance_units_id', '=', 'attendance_units.id')
            ->join('addresses', 'addresses.id', '=', 'attendance_units.addresses_id');


        $strWhereFilter = !empty($filters) ? SearchFilters::getStrWherere(AttendanceUnitHelper::searchFilters(), $filters) : null;
        if ($strWhereFilter) {
            $query->whereRaw($strWhereFilter);
        }

        self::setDatabaseDefaults($query, AttendanceUnitHelper::listItems(), $params);
        $sql .= $query->toSql();

        return parent::genLazyCollectionFromSql($sql);
    }

    public static function findById(int $id)
    {
        return self::findAllByFilters(['id' => $id], ['limit' => 1])->first();
    }

    public static function findBySlug(string $slug)
    {
        return self::findAllByFilters(['slug' => $slug], ['limit' => 1])->first();
    }

    public static function getAttachmentsCatalog($attendanceUnit)
    {
        return new Attachments([
            'path'                          =>  storage_path('app/catalogs/attendanceunits'),
            'catalog_id'                    =>  $attendanceUnit->attachments_catalog_id,
            'create_catalog_if_not_exists'  =>  !empty($attendanceUnit),
        ]);
    }

    public static function store($saveData)
    {
        $addressResponse = AddressService::store($saveData['address']);
        if ($addressResponse === false) {
            return false;
        }
        $attachmentCatalog = Attachments::createCatalog();
        if ($attachmentCatalog === false) {
            return false;
        }

        $saveData['attendance_unit']['addresses_id'] = $addressResponse['id'];
        $saveData['attendance_unit']['attachments_catalog_id'] = $attachmentCatalog->id;
        try {
            DB::beginTransaction();
            $attendanceUnit = AttendanceUnit::create($saveData['attendance_unit']);
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }

        if (!AttendanceUnitManagerUserService::updateAttendanceUnitManagerUsers($attendanceUnit, $saveData['managers'])) {
            return false;
        }

        DB::commit();
        return [
            'status'                =>  true,
            'id'                    =>  $attendanceUnit->id,
            'attachment_catalog'    =>  $attachmentCatalog->id,
        ];
    }

    public static function update($attendanceUnit, $saveData)
    {
        $address = AddressService::findById($attendanceUnit->addresses_id);
        $addressResponse = AddressService::update($address, $saveData['address']);
        if ($addressResponse === false) {
            return false;
        }

        try {
            DB::beginTransaction();
            AttendanceUnit::where('id', $attendanceUnit->id)->get()->first()->update($saveData['attendance_unit']);
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }

        if (!AttendanceUnitManagerUserService::updateAttendanceUnitManagerUsers($attendanceUnit, $saveData['managers'])) {
            return false;
        }

        DB::commit();
        return [
            'status'    =>  true,
            'id'        =>  $attendanceUnit->id,
        ];
    }

    public static function destroy($attendanceUnit)
    {
        $canDelete = DBHelper::checkCanDeleteRow('public', AttendanceUnit::getTableName(), $attendanceUnit->id);
        if ($canDelete !== true) {
            return [
                'status'    =>  false,
                'used_in'   =>  $canDelete['tables'],
            ];
        }

        try {
            DB::beginTransaction();
            AttendanceUnit::where('id', ($attendanceUnit->id))->each(function ($row, $key) {
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
        } catch (Exception $e) {
            DB::rollBack();
            return [
                'status'            =>  false,
                'console_message'   =>  $e,
            ];
        }
    }
}
