<?php

namespace App\Models\System\Person;

use App\Models\CoreModel;
use App\Models\System\Contact\Contact;
use Illuminate\Support\Facades\DB;

class PersonContact extends CoreModel
{
    protected $table = 'persons_contacts';
    protected $fillable = [
        'id', 'persons_id', 'contacts_id',
    ];

    public static function sqlJsonAggByPersonId()
    {
        return DB::table(self::getTableName())
            ->selectRaw("
                " . self::getTableName() . ".persons_id,
                JSON_AGG(
                    JSON_BUILD_OBJECT(
                        'id'," . Contact::getTableName() . ".id,
                        'uuid'," . Contact::getTableName() . ".uuid,
                        'type_id'," . Contact::getTableName() . ".contacts_types_id,                        
                        'contact'," . Contact::getTableName() . ".contact,
                        'preferred'," . Contact::getTableName() . ".preferred,
                        'invalid'," . Contact::getTableName() . ".invalid,
                        'comments'," . Contact::getTableName() . ".comments,
                        'created_at'," . Contact::getTableName() . ".created_at,
                        'updated_at'," . Contact::getTableName() . ".updated_at
                    ) 
                ) contacts
            ")
            ->groupBy(self::getTableName() . '.persons_id')
            ->join(Contact::getTableName(), Contact::getTableName() . '.id', '=', self::getTableName() . '.contacts_id')
            ->toSql();
    }
}
