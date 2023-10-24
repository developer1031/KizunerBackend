<?php

namespace Modules\Kizuner\Models\User;

use App\User;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;
use Modules\Kizuner\Contracts\Data\RelationInterface;

class Friend extends Model implements RelationInterface
{
    protected $table = 'friend_friends';

    public static $status = [
        'pending' => 1,
        'accept'  => 2,
        'reject'  => 3,
    ];

    protected $fillable = [
        'user_id',
        'friend_id',
        'status'
    ];

    /**
     * Get the user that has the follow.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
