<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Services\CrudService;
use Exception;
use App\Models\Address;

class AddressService extends CrudService
{
    public static function findById(int $id)
    {
        return Address::find($id);
    }

    public static function store($saveData)
    {
        $saveData['cep'] = preg_replace('/[^0-9]/', '', $saveData['cep']);
        foreach ($saveData as &$field) {
            $field = $field ? $field : null;
        }
        DB::beginTransaction();
        try {
            $address = Address::create($saveData);
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }

        DB::commit();
        return [
            'status'    =>  true,
            'id'        =>  $address->id,
        ];
    }

    public static function update($address, $saveData)
    {
        $saveData['cep'] = preg_replace('/[^0-9]/', '', $saveData['cep']);
        foreach ($saveData as &$field) {
            $field = $field ? $field : null;
        }
        DB::beginTransaction();
        try {
            Address::where('id', $address->id)->get()->first()->update($saveData);
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }

        DB::commit();
        return [
            'status'    =>  true,
            'id'        =>  $address->id,
        ];
    }
}
