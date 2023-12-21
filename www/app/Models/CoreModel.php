<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class CoreModel extends Model
{
    use LogsActivity;

    public static function getTableName($format = false)
    {
        if (!$format) {
            return with(new static)->getTable();
        }

        $table =  array_map(function ($name) {
            return '"' . $name . '"';
        }, explode('.', with(new static)->getTable()));
        return implode('.', $table);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(function () {
                return json_encode([
                    'user'  =>  [
                        'id'    =>  auth()->user()->id ?? 'not_logged_in',
                        'login' =>  auth()->user()->login ?? 'not_logged_in',
                        'name'  =>  auth()->user()->name ?? 'not_logged_in'
                    ]
                ]);
            })
            ->logOnly($this->fillable)
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
