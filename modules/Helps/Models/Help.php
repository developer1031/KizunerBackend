<?php

namespace Modules\Helps\Models;

use App\User;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Category\Models\Category;
use Modules\Comment\Contracts\Data\CommentableInterface;
use Modules\Comment\Models\Comment;
use Modules\Kizuner\Models\Location;
use Modules\Kizuner\Models\Offer;
use Modules\Kizuner\Models\React;
use Modules\Kizuner\Models\Skill;
use Modules\Upload\Models\Upload;

class Help extends Model implements CommentableInterface
{
  use SoftDeletes;
  protected $table = 'help_helps';

  const STATUS_ONLINE = 'online';
  const STATUS_NO_TIME = 'no_time';
  const STATUS_OFFERED = 'offered';

  const PAYMENT_METHOD_CREDIT = 'credit';
  const PAYMENT_METHOD_CRYPTO = 'crypto';
  const PAYMENT_METHOD_BOTH = 'both';

  public static $type = [
    'single'   =>  1,
    'anytime'  =>  2
  ];

  protected $fillable = [
    'title',
    'description',
    'start',
    'end',
    'budget',
    'room_id',
    'available',
    'capacity',
    'is_min_capacity',
    'friends',
    'type',
    'schedule',
    'is_fake',
    'cover_img',
    'offer_accepted',
    'available_status',
    'is_sent_to_users',
    'is_sent_to_admin',
    'short_address',
    'is_refund',
    'offer_completed',
    'payment_method',
    'amount',
    'is_range_price',
    'min_amount',
    'max_amount',
    'subject_cancel',
    'message_cancel',
    'is_able_contact',
    'is_cancel',
    'refund_crypto_wallet_id',
    'card_id',
    'currency',
  ];

  protected $casts = [
    'friends' => 'array'
  ];

  public function comments(): MorphMany
  {
    return $this->morphMany(Comment::class, 'commentable');
  }

  public function location()
  {
    return $this->morphOne(Location::class, 'locationable');
  }

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

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function reacts()
  {
    return $this->morphMany(React::class, 'reactable');
  }

  public function offers()
  {
    return $this->hasMany(HelpOffer::class, 'help_id', 'id');
  }

  public function getOfferAccepted()
  {
    return $this->offers()->where('status', HelpOffer::$status['accept'])->get();
  }

  public function getCancelEvidenceMedia()
  {
    return $this->media()->where('status', HelpOffer::$status['accept'])->get();
  }

  public function countOfferAccepted()
  {
    return $this->offers()->whereIn('status', [HelpOffer::$status['accept']])->count();
  }

  public function countOfferCompleted()
  {
    return $this->offers()->whereIn('status', [HelpOffer::$status['completed']])->count();
  }
}
