<?php

namespace App\Services\System\Person;

use App\Models\System\Contact\Contact;
use App\Models\System\Person\PersonContact;
use App\Services\CrudService;
use App\Services\System\Contact\ContactService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class PersonContactService extends CrudService
{

    public static function updatPersonContacts($person, $contacts = [])
    {
        $storedContacts = PersonContact::where('persons_id', $person->id)
            ->join(Contact::getTableName(), Contact::getTableName() . '.id', '=', PersonContact::getTableName() . '.contacts_id')
            ->get();

        if (empty($contacts)) {
            DB::beginTransaction();
            try {
                PersonContact::where('persons_id', $person->id)->each(function ($row, $key) {
                    $row->delete();
                });
                foreach ($storedContacts as $storedContact) {
                    Contact::where('id', $storedContact->contacts_id)->each(function ($row, $key) {
                        $row->delete();
                    });
                }
            } catch (QueryException $e) {
                DB::rollBack();
                return false;
            }

            DB::commit();
            return [
                'status'    =>  true,
            ];
        }

        DB::beginTransaction();
        foreach ($contacts as $contact) {
            $storedContact = $storedContacts->first(function ($item) use ($contact) {
                return $item->uuid === $contact['uuid'];
            });

            if (empty($storedContact)) {
                $newContact = ContactService::store($contact);
                if (!$newContact) {
                    return false;
                }
                PersonContact::create([
                    'persons_id'    =>  $person->id,
                    'contacts_id'   =>  $newContact['id'],
                ]);
            }
            if (!empty($storedContact)) {
                if (!ContactService::update($storedContact, $contact)) {
                    return false;
                }
            }
        }

        DB::commit();
        return [
            'status'    =>  true,
        ];
    }
}
