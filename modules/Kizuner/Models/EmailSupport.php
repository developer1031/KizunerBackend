<?php

namespace Modules\Kizuner\Models;

use App\User;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\Comment\Contracts\Data\CommentableInterface;
use Modules\Comment\Models\Comment;
use Modules\Upload\Models\Upload;

class EmailSupport extends Model 
{
    protected $table = 'user_support';


    public function medias()
    {
        return $this->morphMany(Upload::class, 'uploadable');
    }

}
