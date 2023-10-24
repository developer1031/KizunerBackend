<?php

namespace App;

use \GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;

class ResetPassword extends Model
{

    protected $table = 'password_resets';

    protected $fillable = [
        'email',
        'token',
        'pin'
    ];
}
