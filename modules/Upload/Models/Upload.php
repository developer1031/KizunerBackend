<?php

namespace Modules\Upload\Models;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;

class Upload extends Model
{
    protected $fillable = ['path', 'type', 'thumb'];

    /**
     * Get the owning mediable model.
     */
    public function uploadable()
    {
        return $this->morphTo();
    }
}
