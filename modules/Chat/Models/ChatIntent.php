<?php

namespace Modules\Chat\Models;

use App\User;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Comment\Contracts\Data\CommentableInterface;
use Modules\Comment\Models\Comment;
use Modules\Kizuner\Models\Location;
use Modules\Kizuner\Models\React;
use Modules\Kizuner\Models\Skill;
use Modules\Upload\Models\Upload;

class ChatIntent extends Model
{
    protected $table = 'chat_intents';

    protected $fillable = [
        'intent',
        'reply',
    ];
}
