<?php

namespace Modules\Kizuner\Models;

use App\User;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;
use Modules\Config\Config;
use Modules\Wallet\Domains\Dto\HistoryDto;
use Modules\Wallet\Domains\Entities\HistoryEntity;
use Modules\Wallet\Domains\Wallet;
use Modules\Wallet\Domains\History;
use Modules\Upload\Models\Upload;

class UserSupport extends Model
{
    use Uuid;
    protected $table = 'user_support';

    //protected $guarded = ['id'];
    protected $fillable = ['user_id', 'name', 'email', 'subject', 'message', 'media'];

    public function user() {
        return $this->hasMany(User::class, 'id', 'user_id');
    }

    public function medias()
    {
        return $this->morphMany(Upload::class, 'uploadable');
    }
}
