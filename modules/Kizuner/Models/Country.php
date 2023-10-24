<?php

namespace Modules\Kizuner\Models;

use App\User;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\Comment\Contracts\Data\CommentableInterface;
use Modules\Comment\Models\Comment;
use Modules\Upload\Models\Upload;

class Country extends Model
{
    protected $table = 'country';

    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'country');
    }
}
