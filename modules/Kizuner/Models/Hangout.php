<?php

namespace Modules\Kizuner\Models;

use App\User;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Category\Models\Category;
use Modules\Comment\Contracts\Data\CommentableInterface;
use Modules\Comment\Models\Comment;
use Modules\Upload\Models\Upload;

class Hangout extends Model implements CommentableInterface
{
    use SoftDeletes;

    protected $table = 'hangout_hangouts';

    const PAYMENT_METHOD_CREDIT = 'credit';
    const PAYMENT_METHOD_CRYPTO = 'crypto';
    const PAYMENT_METHOD_BOTH = 'both';

    public static $type = [
        'single'   =>  1,
        'anytime'  =>  2
    ];

    protected $fillable = [
        'type',
        'title',
        'description',
        'start',
        'end',
        'kizuna',
        'capacity',
        'available',
        'is_min_capacity',
        'schedule',
        'index',
        'room_id',
        'friends',
        'available_status',
        'is_sent_to_users',
        'is_sent_to_admin',
        'is_fake',
        'cover_img',
        'short_address',
        'offer_accepted',
        'payment_method',
        'amount',
        'is_range_price',
        'min_amount',
        'max_amount',
        'crypto_wallet_id'
    ];

    protected $casts = [
        'friends' => 'array'
    ];

    /**
     * ============== Relationship definition ==============
     */

    public function skills()
    {
        return $this->morphToMany(Skill::class, 'skillable');
    }

    public function categories()
    {
        return $this->morphToMany(Category::class, 'categoryable');
    }

    public function media()
    {
        return $this->morphMany(Upload::class, 'uploadable');
    }

    public function location()
    {
        return $this->morphOne(Location::class, 'locationable');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function offers()
    {
        return $this->hasMany(Offer::class, 'hangout_id', 'id');
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
