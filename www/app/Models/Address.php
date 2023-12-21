<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class Address extends CoreModel
{

    protected $table = 'addresses';
    protected $fillable = [
        'state', 'city', 'neighborhood', 'street', 'number',
        'complement', 'cep', 'latitude', 'longitude', 'regional_zone'
    ];

    public static function sqlJsonAggByAddressId()
    {
        return DB::table(self::getTableName())
            ->selectRaw("
                " . self::getTableName() . ".id,
                JSON_AGG(
                    JSON_BUILD_OBJECT(
                        'id'," . self::getTableName() . ".id,
                        'state'," . self::getTableName() . ".state,
                        'city'," . self::getTableName() . ".city,
                        'neighborhood'," . self::getTableName() . ".neighborhood,
                        'street'," . self::getTableName() . ".street,
                        'number'," . self::getTableName() . ".number,
                        'complement'," . self::getTableName() . ".complement,
                        'cep'," . self::getTableName() . ".cep,
                        'latitude'," . self::getTableName() . ".latitude,
                        'longitude'," . self::getTableName() . ".longitude,
                        'regional_zone'," . self::getTableName() . ".regional_zone
                    ) 
                ) addresses
            ")
            ->groupBy(self::getTableName() . '.id')
            ->toSql();
    }
}
