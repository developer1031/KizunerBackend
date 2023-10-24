<?php

namespace Modules\Category\Models;

use App\User;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'suggest' , 'admin', 'created_at', 'updated_at', 'type'];

    public function hangouts()
    {
        return $this->morphedByMany(Hangout::class, 'categoryable');
    }

    public function users()
    {
        return $this->morphedByMany(User::class, 'categoryable');
    }
}
