<?php

namespace Modules\Kizuner\Models\User;

use App\User;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;
use Modules\Kizuner\Contracts\Data\RelationInterface;

class Block extends Model implements RelationInterface
{
    protected $table = 'friend_blocks';

    protected $fillable = [
        'user_id',
        'block_id'
    ];
    /**
     * Get the user that has the follow.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
