<?php

namespace App\Models\System\Contact;

use App\Models\CoreModel;
use Illuminate\Support\Facades\DB;

class Contact extends CoreModel
{

    protected $table = 'contacts';
    protected $fillable = [
        'contacts_types_id', 'contact', 'preferred', 'invalid', 'comments', 'uuid',
    ];

    public static function sqlJsonAggByContactId()
    {
        return DB::table(self::getTableName())
            ->selectRaw("
                " . self::getTableName() . ".id,
                JSON_AGG(
                    JSON_BUILD_OBJECT(
                        'id'," . self::getTableName() . ".id,
                        'contacts_types_id'," . self::getTableName() . ".contacts_types_id,
                        'uuid'," . self::getTableName() . ".uuid,
                        'contact'," . self::getTableName() . ".contact,
                        'preferred'," . self::getTableName() . ".preferred,
                        'invalid'," . self::getTableName() . ".invalid,
                        'comments'," . self::getTableName() . ".comments,
                        'created_at'," . self::getTableName() . ".created_at,
                        'updated_at'," . self::getTableName() . ".updated_at,
                    ) 
                ) contacts
            ")
            ->groupBy(self::getTableName() . '.id')
            ->toSql();
    }
}
