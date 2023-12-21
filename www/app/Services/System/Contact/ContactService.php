<?php

namespace App\Services\System\Contact;

use App\Helpers\StringHelper;
use Illuminate\Support\Facades\DB;
use App\Services\CrudService;
use Exception;
use App\Models\System\Contact\Contact;

class ContactService extends CrudService
{
    public static function findById(int $id)
    {
        return Contact::find($id);
    }

    public static function store($saveData)
    {
        DB::beginTransaction();
        try {
            $contact = Contact::create([
                'uuid'              =>  $saveData['uuid'] ?? StringHelper::uuid(),
                'contacts_types_id' =>  $saveData['contact_type'],
                'contact'           =>  trim($saveData['contact']),
                'preferred'         =>  (bool)$saveData['preferred'],
                'invalid'           =>  (bool)$saveData['invalid'],
                'comments'          =>  trim($saveData['comments']),
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }

        DB::commit();
        return [
            'status'    =>  true,
            'id'        =>  $contact->id,
        ];
    }

    public static function update($contact, $saveData)
    {
        DB::beginTransaction();
        try {
            Contact::where('uuid', $contact->uuid)->get()->first()->update([
                'contacts_types_id' =>  $saveData['contact_type'],
                'contact'           =>  trim($saveData['contact']),
                'preferred'         =>  (bool)$saveData['preferred'],
                'invalid'           =>  (bool)$saveData['invalid'],
                'comments'          =>  trim($saveData['comments']),
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }

        DB::commit();
        return [
            'status'    =>  true,
            'id'        =>  $contact->id,
        ];
    }
}
