<?php

namespace App;

use GoldSpecDigital\LaravelEloquentUUID\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Traits\Macroable;
use Laravel\Passport\HasApiTokens;
use Modules\Category\Models\Category;
use Modules\Feed\Models\Timeline;
use Modules\Helps\Models\Help;
use Modules\Helps\Models\HelpOffer;
use Modules\Kizuner\Models\Country;
use Modules\Kizuner\Models\LeaderBoard;
use Modules\Report\Report;
use Modules\Upload\Models\Upload;
use Modules\Kizuner\Models\Hangout;
use Modules\Kizuner\Models\Location;
use Modules\Kizuner\Models\Offer;
use Modules\Kizuner\Models\Ratting;
use Modules\Kizuner\Models\Skill;
use Modules\Kizuner\Models\User\Block;
use Modules\Kizuner\Models\User\Follow;
use Modules\Kizuner\Models\User\Friend;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'gender',
        'about',
        'birth_date',
        'social',
        'age',
        'social_id',
        'social_provider',
        'email_verified_at',
        'phone_verified_at',
        'term_condition',
        'language',
        'is_fake',
        'address',
        'fake_avatar',
        'country',
        'email_notification',
        'last_login',
        'last_send_mail',
        'is_added_first_post',
        'is_first_shared',
        'username',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'language' => 'array'
    ];

    public function location()
    {
        return $this->morphOne(Location::class, 'locationable');
    }

    public function hangouts()
    {
        return $this->hasMany(Hangout::class);
    }
    public function helps()
    {
        return $this->hasMany(Help::class);
    }

    public function offers()
    {
        return $this->hasMany(Offer::class, 'receiver_id', 'id');
    }

    public function helpOffers()
    {
        return $this->hasMany(HelpOffer::class, 'receiver_id', 'id');
    }

    public function helpedOffers()
    {
        return $this->hasMany(HelpOffer::class, 'sender_id', 'id');
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function medias()
    {
        return $this->morphMany(Upload::class, 'uploadable');
    }

    public function rattings()
    {
        return $this->morphMany(Ratting::class, 'ratable');
    }

    public function oAuthAccessToken()
    {
        return $this->hasMany(OauthAccessToken::class);
    }

    //User -> Followers(follow_id) who follow me
    public function followers()
    {
        return $this->hasMany(Follow::class, 'follow_id', 'id');
    }

    //User -> Followed(user_id) follow who
    public function followed()
    {
        return $this->hasMany(Follow::class, 'user_id', 'id');
    }

    // Get all friend of mine, user_id => me, other -> friend_id
    public function friends()
    {
        return $this->hasMany(Friend::class, 'user_id', 'id');
    }

    public function blocks()
    {
        return $this->hasMany(Block::class, 'user_id', 'id');
    }

    public function skills()
    {
        return $this->morphToMany(Skill::class, 'skillable');
    }

    public function categories()
    {
        return $this->morphToMany(Category::class, 'categoryable');
    }

    public function myCountry() {
        return $this->hasOne(Country::class, 'id', 'country');
    }

    public function leaderBoard() {
        return $this->hasOne(LeaderBoard::class, 'user_id', 'id');
    }

    public function feedTimelines() {
        return $this->hasMany(Timeline::class);
    }
}
