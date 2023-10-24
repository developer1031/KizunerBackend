<?php

namespace Modules\KizunerApi\Transformers;

use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Fractal\TransformerAbstract;
use Modules\Category\Models\Category;
use Modules\Category\Transformers\CategoryTransform;
use Modules\Feed\Models\Timeline;
use Modules\Helps\Models\Help;
use Modules\Helps\Models\HelpOffer;
use Modules\Kizuner\Models\Hangout;
use Modules\Kizuner\Models\Offer;
use Modules\Kizuner\Models\Skill;
use Modules\Kizuner\Models\User\Follow;
use Modules\Kizuner\Models\User\Friend;
use Modules\Rating\Domains\Queries\UserRatingQuery;
use Modules\Wallet\Domains\Wallet;
use Modules\Wallet\Stripe\StripeCustomer;
use phpseclib\Crypt\Random;

class UserTransform extends TransformerAbstract
{
    private $categories = null;
    private $skillCollection = null;
    private $is_profile = false;

    public function __construct($categories = null, $skillCollection=null) {
        if($categories) $this->categories = $categories;
        if($skillCollection) $this->skillCollection = $skillCollection;
    }

    public $defaultIncludes = [
        'specialities',
        'categories',
        'allSpecs',
        'allCategories'
    ];

    public function includeAllSpecs() {
        // return $this->collection(Skill::where('suggest', 1)->orderBy('name')->get(), new SkillTransform());
        return $this->collection(Skill::orderBy('name')->get(), new SkillTransform());
    }

    public function includeAllCategories() {
        return $this->collection(Category::orderBy('name')->get(), new CategoryTransform());
    }

    public function includeSpecialities(User $user)
    {
        $skills = $user->skills;
        if($this->skillCollection) {
            foreach ($this->skillCollection as $skill) {
                $skills->push($skill);
            }
        }
        $skillsIds = $skills->pluck('id')->toArray();

        //Sugguestion
        $suggests = Skill::where('suggest', 1)->orderBy('name')->get();
        foreach($suggests as $key => $skill) {
            if( in_array($skill->id, $skillsIds)) {
                $suggests[$key]->suggest = false;
            }
        }
        $skills = $skills->merge($suggests);

        return $this->collection($skills, new SkillTransform());
        //return $this->collection(Skill::where('suggest', 1)->orderBy('name')->get(), new SkillTransform());
    }

    public function includeCategories(User $user) {
        $categories = $user->categories;

        if($this->categories) {
            $categories = $this->categories;
        }

        return $this->collection($categories, new CategoryTransform());
    }

    public function transform(User $user)
    {

        //dd($this->collection(Skill::first(), new SkillTransform()));

        $disk = \Storage::disk('gcs');
        $avatar = $user->medias()->where('type', 'user.avatar')->first();
        $cover = $user->medias()->where('type', 'user.cover')->first();
        $location = $user->location;

        $resident = $user->address ? json_decode($user->address) : null;
        if($resident) {
            $resident = [
                'address' => $resident->residentAddress,
                'lat' => $resident->residentLat,
                'long' => $resident->residentLng,
                'short_address' => isset($resident->short_address) && $resident->short_address ? $resident->short_address : ''
            ];
        }

        $currentUser = null;
        $wallet = Wallet::findByUserId($user->id);

        if(!$wallet) {
            try {
                $email = $user->email ? $user->email : $user->id . '@kizuner.app';
                $name = $user->name ? $user->name : $user->email;

                // Create Stripe Customer
                $stripeCustomer = StripeCustomer::create($email, $name);
                // Create Wallet for New Registered User
                $wallet = Wallet::create($user->id, $stripeCustomer->id);
            } catch (\Exception $e) {
                Log::error($e->getMessage());
            }
        }

        if (\Auth::check()) {
            $currentUser = app('request')->user()->id;
        }
        $friendship = 'guest';
        $friendshipId = null;

        $userId = $user->id;
        $status = Friend::$status['accept'];

        $friendCount = Friend::where(function ($query) use ($userId, $status) {
                                $query->where('user_id', $userId);
                                $query->where('status', $status);
                            })
                                ->orWhere(function ($query) use ($userId, $status) {
                            $query->where('friend_id', $userId);
                            $query->where('status', $status);
                        })->count();

        $followCount = Follow::where('follow_id', $userId)->count();
        $followingCount = Follow::where('user_id', $userId)->count();

        $castCount = Offer::where('receiver_id', $userId)->distinct('hangout_id')->where('status', Offer::$status['approved'])->count();
        $guestCount = Offer::where('sender_id', $userId)->where('status', Offer::$status['approved'])->count();

        $helpsCount = $user->helpOffers()->distinct('help_id')->where('status', HelpOffer::$status['approved'])->count();
        $helpedCount = $user->helpedOffers()->where('status', HelpOffer::$status['approved'])->count();

        //$helpsCount = Help::where('user_id', $userId)->count();
        //$helpedCount = $user->helpedOffers()->where('status', HelpOffer::$status['completed'])->count();

        $follow = false;

        if ($currentUser != $user->id) {
            $friend = Friend::where(function ($query) use ($currentUser, $user) {
                                $query->where('user_id', $currentUser);
                                $query->where('friend_id', $user->id);
                            })
                            ->orWhere(function ($query) use ($currentUser, $user) {
                                $query->where('friend_id', $currentUser);
                                $query->where('user_id', $user->id);
                            })
                            ->first();

            $follow = Follow::where([
                'user_id'   => $currentUser,
                'follow_id' => $user->id
            ])->first();

            if ($follow) {
                $follow = $follow->id;
            }

            if ($friend) {
                $friendshipId = $friend->id;
                switch ($friend->status) {
                    case 1:
                        if ($currentUser == $friend->user_id) {
                            $friendship = 'requested';
                        } else {
                            $friendship = 'pending';
                        }
                        break;
                    case 2:
                        $friendship = 'friend';
                        break;
                    default:
                        $friendship = 'guest';
                }
            }
        }

        $rateInfo = (new UserRatingQuery($user->id, 1))->execute();

        $age = ageFromBirthDate($user->birth_date);

        //$user_language = [];
        //if(!is_array($user->language)) {
        //    array_push($user_language, $user->language);
        //}

        //dd( $user->language );


        $country = ($user->myCountry) ? [
            'id'        => $user->myCountry->id,
            'country'   => $user->myCountry->country,
            'city'      => $user->myCountry->city,
            'latitude'  => $user->myCountry->latitude,
            'longitude' => $user->myCountry->longitude,
            'altitude'  => $user->myCountry->altitude,
        ] : null;

        $leaderBoard = $user->leaderBoard;
        $point = $leaderBoard ? $leaderBoard->point : 0;
        $badge = $leaderBoard ? $leaderBoard->badge : 0;

        //$has_posted = Timeline::where('user_id', $user->id)->first();
        //$has_posted = $has_posted ? true : false;
        $has_posted = $user->is_added_first_post ? true : false;

        /** Counting */
        return [
            'id'        => $user->id,
            'name'      => $user->name,
            'about'     => $user->about,
            'phone'     => $user->phone,
            'email'     => $user->email,
            'social_id'     => $user->social_id,
            'social'    => $user->social == null ? [] : json_decode($user->social),
            'birth_date' => $user->birth_date,
            'gender'     => $user->gender,
            'age'        => $age,
            'email_verified_at' => $user->email_verified_at,
            'phone_verified_at' => $user->phone_verified_at,

            'hangout_help_notification' => $user->hangout_help_notification,
            'hangout_help_email_notification' => $user->hangout_help_email_notification,
            'message_notification' => $user->message_notification,
            'message_email_notification' => $user->message_email_notification,
            'follow_notification' => $user->follow_notification,
            'follow_email_notification' => $user->follow_email_notification,
            'comment_notification' => $user->comment_notification,
            'comment_email_notification' => $user->comment_email_notification,
            'like_notification' => $user->like_notification,
            'like_email_notification' => $user->like_email_notification,
            'friendship' => [
                'status' => $friendship,
                'id'     => $friendshipId
            ],
            'follow'     => $follow,
            'kizuna'     => !$user->is_fake ? $wallet->balance : random_int(20, 100),
            'stripe_id'     => !$user->is_fake ? $wallet->stripe_id : random_int(20, 100),
            'username'     => !$user->is_fake ? $user->username : random_int(99999, 99999999),
            'location'   => [
                'address' => $location == null ? null : $location->address,
                'lat'     => $location == null ? null : $location->lat,
                'lng'    => $location == null ? null : $location->lng,
                'short_address'    => $location == null ? null : $location->short_address
            ],
            'media'    => [
                'social_avatar' => !$user->is_fake ? $user->social_avatar : $user->fake_avatar,
                'avatar' => [
                    'path'  => !$user->is_fake ? ($avatar == null ? null : $disk->url($avatar->path)) : $user->fake_avatar,
                    'thumb' => !$user->is_fake ? ($avatar == null ? null : $disk->url($avatar->thumb)) : $user->fake_avatar
                ],
                'cover' => [
                    'path'  => !$user->is_fake ? ($cover == null ? null : $disk->url($cover->path)) : $user->fake_avatar,
                    'thumb' => !$user->is_fake ? ($cover == null ? null : $disk->url($cover->thumb)) : $user->fake_avatar
                ]
            ],
            'rating' => [
                'rating' => $rateInfo['avg'],
                'count'  => $rateInfo['count']
            ],
            'relation'   => [
                'cast'          => $castCount,
                'guest'         => $guestCount,
                'helps'         => $helpsCount,
                'helped'        => $helpedCount,
                'friend'        => $friendCount,
                'follower'      => $followCount,
                'following'      => $followingCount,
            ],
            'language'          => $user->language,
            'resident'  => $resident,

            'country'   => $country,
            'point'     => $point,
            'badge'     => $badge,
            'has_posted' => $has_posted,
            // 'allSpecs'   => $this->collection(Skill::first(), new SkillTransform()),
            // 'allCategories'   => $this->collection(Category::all(), new CategoryTransform()),
            'payouts_enabled' => $wallet->payouts_enabled,
        ];
    }
}
