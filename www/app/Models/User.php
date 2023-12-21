<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'social_name',
        'email',
        'login',
        'password',
        'is_active',
        'is_master',
        'attributes',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

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

    public function attributes()
    {
        return collect(json_decode(trim($this->attributes['attributes'])));
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\Auth\QueuedResetPassword($token));
    }

    public function unreadNotificationsByUpdatedAtDesc()
    {
        return $this->morphMany(DatabaseNotification::class, "notifiable")
            ->where('notifiable_id', $this->id)
            ->whereNull('read_at')
            ->orderBy('updated_at', 'DESC')
            ->limit(50);
    }
}
