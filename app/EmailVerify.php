<?php

namespace App;


use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;

class EmailVerify extends Model
{
    protected $table = 'email_verify';

    protected $fillable = [
        'email',
        'pin'
    ];
}
