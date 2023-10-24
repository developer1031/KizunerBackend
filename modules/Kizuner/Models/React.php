<?php

namespace Modules\Kizuner\Models;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;

class React extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'reacted_user_id',
        'react_type'
    ];

    public function reactable()
    {
        return $this->morphTo();
    }
}
