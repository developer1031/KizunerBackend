<?php

namespace Modules\Kizuner\Models;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;

class Location extends Model
{

    protected $fillable = ['address', 'lat', 'lng', 'short_address'];

    public function locationable()
    {
        return $this->morphTo();
    }
}
