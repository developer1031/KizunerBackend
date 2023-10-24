<?php

namespace Modules\Kizuner\Models;

use App\User;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;
use Modules\Helps\Models\Help;
use Modules\Helps\Models\SampleHelp;

class Skill extends Model
{
    protected $fillable = ['name', 'suggest' , 'admin', 'created_at', 'updated_at'];

    public function hangouts()
    {
        return $this->morphedByMany(Hangout::class, 'skillable');
    }

    public function helps()
    {
        return $this->morphedByMany(Help::class, 'skillable');
    }

    public function users()
    {
        return $this->morphedByMany(User::class, 'skillable');
    }

    public function sampleHelps()
    {
        return $this->morphedByMany(SampleHelp::class, 'skillable');
    }
}
