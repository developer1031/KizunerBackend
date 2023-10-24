<?php

namespace Modules\Kizuner\Models;

use App\User;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\Comment\Contracts\Data\CommentableInterface;
use Modules\Comment\Models\Comment;
use Modules\Upload\Models\Upload;

class Status extends Model implements CommentableInterface
{
    protected $fillable = [
        'user_id',
        'status',
        'friends'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function media()
    {
        return $this->morphMany(Upload::class, 'uploadable');
    }

    public function reacts()
    {
        return $this->morphMany(React::class, 'reactable');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
