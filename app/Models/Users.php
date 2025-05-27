<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'username',
        'password',
        'role',
        'change_password_at',
        'delete_at',
    ];

    protected $hidden = [
        'password',
    ];

    protected static function boot()
    {
        parent::boot();
        $prefix = 'HRUUS-';
        static::creating(function ($model) use ($prefix) {
            $date = date('dmY');
            $model->user_id = $prefix . $date . '-' . str_pad(static::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
        });
    }
}
