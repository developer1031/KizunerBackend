<?php

namespace Modules\Helps\Transformers;

use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use League\Fractal\TransformerAbstract;
use Modules\Category\Transformers\CategoryTransform;
use Modules\Helps\Models\Help;
use Modules\Helps\Models\HelpOffer;
use Modules\Kizuner\Models\Hangout;
use Modules\Kizuner\Models\React;
use Modules\KizunerApi\Transformers\HangoutTransform;
use Modules\KizunerApi\Transformers\LocationTransform;
use Modules\KizunerApi\Transformers\MediaTransform;
use Modules\KizunerApi\Transformers\SkillTransform;
use Modules\KizunerApi\Transformers\UserTransform;
use Modules\Upload\Models\Upload;

class HelpTransform extends TransformerAbstract
{

    protected $defaultIncludes = [
        'skills',
        'user',
        'media',
        'location',
        'categories',
    ];

    private $isDetail = false;

    public function __construct($isDetail = false)
    {
        $this->isDetail = $isDetail;
    }

    public function transform(Help $help)
    {

        $disk = \Storage::disk('gcs');

        $commentCount = DB::table('comment_comments')
            ->where('comment_comments.commentable_id', $help->id)
            ->count();

        $helpReactCount = React::where([
            'reactable_id'     => $help->id,
            'reactable_type'   => Help::class,
            'react_type'    => 'like'
        ])->count();

        $likeCheck = React::where([
            'user_id'           => app('request')->user()->id,
            'reactable_id'      => $help->id,
            'reactable_type'    => Help::class
        ])->count();

        $casts = collect();
        $show_help = false;

        $hangout_near_by = null;
        if ($this->isDetail) {
            $lat        = $help->location ? $help->location->lat : null;
            $lng        = $help->location ? $help->location->lng : null;
            $radius     = 15;

            $casts = collect();
            if ($help->location && $lat && $lng) {

                if ($lat && $lng) {
                    try {
                        $casts = User::select('users.*')->selectRaw('(6371 * acos (
                                              cos ( radians('  . $lat .  ') )
                                              * cos( radians( locations.lat ) )
                                              * cos( radians( locations.lng ) - radians('  . $lng .  ') )
                                              + sin ( radians(' . $lat . ') )
                                              * sin( radians( locations.lat ) )
                                            )
                                ) AS distance')

                            ->selectRaw('ROUND((6371 * acos (
                                    cos ( radians('  . $lat .  ') )
                                    * cos( radians( locations.lat ) )
                                    * cos( radians( locations.lng ) - radians('  . $lng .  ') )
                                    + sin ( radians(' . $lat . ') )
                                    * sin( radians( locations.lat ) )
                                  )
                                ),0) AS group_distance')

                            ->leftJoin('locations', 'locations.locationable_id', '=', 'users.id')
                            ->whereRaw('
                                (6371 * acos (
                                                cos ( radians('  . $lat .  ') )
                                                * cos( radians( locations.lat ) )
                                                * cos( radians( locations.lng ) - radians('  . $lng .  ') )
                                                + sin ( radians(' . $lat . ') )
                                                * sin( radians( locations.lat ) )
                                                )
                                ) < ' . $radius)

                            ->whereRaw('
                                (6371 * acos (
                                                cos ( radians('  . $lat .  ') )
                                                * cos( radians( locations.lat ) )
                                                * cos( radians( locations.lng ) - radians('  . $lng .  ') )
                                                + sin ( radians(' . $lat . ') )
                                                * sin( radians( locations.lat ) )
                                                )
                                ) > ' . 2)

                            ->groupBy('group_distance')
                            ->orderBy('distance')->take(30)->get();
                    } catch (\Exception $e) {
                    }
                }
            }

            $casts->map(function ($cast) use ($disk) {
                $avatar = null;
                if ($cast->is_fake) {
                    $avatar = $cast->fake_avatar;
                } else {
                    $u_avatar = $cast->medias()->where('type', 'user.avatar')->first();
                    if ($u_avatar) {
                        $avatar = $disk->url($u_avatar->path);
                    }
                }
                $cast['avatar'] = $avatar;
                return $cast;
            });

            $help_skills = $help->skills->pluck('id')->toArray();
            $hangout_near_by = Hangout::whereHas('skills', function ($query) use ($help_skills) {
                $query->whereIn('skill_id', $help_skills);
            })
                ->whereHas('location', function ($query) {
                    $query->where('address', '!=', '.');
                })
                ->groupBy('title')
                ->orderBy('is_fake', 'asc')->take(20)->get();

            /*
            $lat        = $help->location->lat;
            $lng        = $help->location->lng;
            $radius     = 20;
            if(intval($lat) > 0 && intval($lng) > 0) {
                $hangout_near_by = Hangout::select('hangout_hangouts.*')->selectRaw('(6371 * acos (
                                              cos ( radians('  . $lat .  ') )
                                              * cos( radians( locations.lat ) )
                                              * cos( radians( locations.lng ) - radians('  . $lng .  ') )
                                              + sin ( radians(' . $lat . ') )
                                              * sin( radians( locations.lat ) )
                                            )
                                ) AS distance')
                    ->leftJoin('locations', 'locations.locationable_id', '=', 'hangout_hangouts.id')
                    ->whereRaw('
                            (6371 * acos (
                                              cos ( radians('  . $lat .  ') )
                                              * cos( radians( locations.lat ) )
                                              * cos( radians( locations.lng ) - radians('  . $lng .  ') )
                                              + sin ( radians(' . $lat . ') )
                                              * sin( radians( locations.lat ) )
                                            )
                            ) < ' . $radius . '

                    ')->orderBy('hangout_hangouts.created_at', 'desc')->orderBy('distance')
                    ->take(10)->get();
            }
            */
        }

        //$show_help = ($help->user_id != app('request')->user()->id) && (Carbon::create($help->start)->lte(Carbon::now()) || Carbon::create($help->end)->gte(Carbon::now()));

        $show_help = ($help->user_id != app('request')->user()->id) && (Carbon::create($help->end)->gte(Carbon::now()));

        $offer = HelpOffer::where('sender_id', app('request')->user()->id)
            ->where('help_id', $help->id)
            ->where('status', '<>', HelpOffer::$status['cancel'])
            ->first();
        $offerCheck = $offer == null ? false : true;

        $friends = null;
        if ($help->friends) {
            $friends_ids = $help->friends;
            $users_friends = \App\User::whereIn('id', $friends_ids)->get();
            $friends = fractal($users_friends, new UserTransform());
        }

        $help_start = $help->start ? Carbon::create($help->start) : null;
        $help_end = $help->end ? Carbon::create($help->end) : null;

        if (in_array($help->available_status, ['no_time', 'combine']) || $help->is_fake) {
            $show_help = ($help->user_id != app('request')->user()->id);
            $help_start = null;
            $help_end = null;
        }

        return [
            'id'            => $help->id,
            'type'          => $help->type,
            'title'         => $help->title,
            'description'   => $help->description,
            'status'        => $help->status,
            'created_at'    => $help->created_at,
            'updated_at'    => $help->updated_at,
            'comment_count'  => $commentCount,
            'liked'         => $likeCheck == 0 ? false : true,
            'like_count'    => $helpReactCount,
            'room_id'       => $help->room_id,
            'start'         => $help_start,
            'end'           => $help_end,
            'available_status' => $help->available_status,
            'casts'         => $casts,
            'show_help'     => $show_help,
            'capacity'      => $help->capacity,
            'offered'       => $offerCheck,
            'offers_count'  => $help->offers()
                ->whereNotIn('status', [
                    HelpOffer::$status['cancel'],
                    HelpOffer::$status['reject']
                ])
                ->count(),
            'friends'       => $friends,
            'schedule'      => $help->schedule,
            'dymanic_link'  => dynamicUrl('help', $help->id),
            'isMinCapacity' => $help->is_min_capacity,
            'hangouts'      => fractal($hangout_near_by, new HangoutTransform()),
            'payment_method' => $help->payment_method,
            'amount' => $help->amount,
            'is_range_price'       => $help->is_range_price,
            'min_amount'       => $help->min_amount,
            'max_amount'       => $help->max_amount,
            'user_id' => $help->user_id,
            'card_id' => $help->card_id,
            'currency' => $help->currency,
            'refund_crypto_wallet_id' => $help->refund_crypto_wallet_id,
        ];
    }

    public function includeMedia(Help $help)
    {
        $media = $help->media;
        if ($media) {
            return $this->collection($media, new MediaTransform());
        }
        if ($help->is_fake) {
            $upload = new Upload();
            $upload->id = null;
            $upload->path = $help->cover_img;
            $upload->thumb = $help->cover_img;
            $upload->type = 'image';
            return $this->collection($upload, new MediaTransform());
        }
    }

    public function includeUser(Help $help)
    {
        $user = $help->user;
        if ($user) {
            return $this->item($user, new UserTransform());
        }
    }

    public function includeLocation(Help $help)
    {
        $location = $help->location;
        if ($help->available_status != 'online' && (!$help->is_fake && $location)) {
            return $this->item($location, new LocationTransform());
        }
    }

    public function includeSkills(Help $help)
    {
        $skills = $help->skills;

        if (!empty($skills)) {
            return $this->collection($skills, new SkillTransform());
        }
    }

    public function includeCategories(Help $help)
    {
        $categories = $help->categories;
        if (!empty($categories)) {
            return $this->collection($categories, new CategoryTransform());
        }
    }
}
