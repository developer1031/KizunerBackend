<?php

namespace Modules\Tag\Models;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'reacted_user_id'
    ];

    public function tagable()
    {
        return $this->morphTo();
    }
}
