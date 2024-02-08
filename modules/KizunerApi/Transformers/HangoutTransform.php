<?php

namespace Modules\KizunerApi\Transformers;

use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use League\Fractal\TransformerAbstract;
use Modules\Category\Models\Category;
use Modules\Category\Transformers\CategoryTransform;
use Modules\Helps\Models\Help;
use Modules\Helps\Transformers\HelpTransform;
use Modules\Kizuner\Models\Hangout;
use Modules\Kizuner\Models\React;
use Modules\Kizuner\Models\Offer;
use Modules\Upload\Models\Upload;

class HangoutTransform extends TransformerAbstract
{
    private $isDetail = false;
    public function __construct($isDetail = false)
    {
        $this->isDetail = $isDetail;
    }

    protected $defaultIncludes = [
        'skills',
        'user',
        'media',
        'location',
        'categories',
    ];

    public function transform(Hangout $hangout)
    {
        $hangoutReactCount = React::where([
            'reactable_id'      => $hangout->id,
            'reactable_type'   => Hangout::class,
            'react_type'        => 'like'
        ])->count();

        $likeCheck = React::where([
            'user_id'           => app('request')->user()->id,
            'reactable_id'      => $hangout->id,
            'reactable_type'    => Hangout::class
        ])->count();

        $offer = Offer::where('sender_id', app('request')->user()->id)
            ->where('hangout_id', $hangout->id)
            ->where('status', '<>', Offer::$status['cancel'])
            ->first();
        $offerCheck = $offer == null ? false : true;

        $commentCount = DB::table('comment_comments')
            ->where('comment_comments.commentable_id', $hangout->id)
            ->get()
            ->count();

        $friends = null;
        if ($hangout->friends) {
            $friends_ids = $hangout->friends;
            $users_friends = \App\User::whereIn('id', $friends_ids)->get();
            $friends = fractal($users_friends, new UserTransform());
        }

        //$show_hangout = ($hangout->user_id != app('request')->user()->id) && (Carbon::create($hangout->start)->lte(Carbon::now()) || Carbon::create($hangout->end)->gte(Carbon::now()));
        $show_hangout = ($hangout->user_id != app('request')->user()->id) && (Carbon::create($hangout->end)->gte(Carbon::now()));

        $offers_count = $hangout->offers()->whereNotIn('status', [
            Offer::$status['cancel'],
            Offer::$status['reject']
        ])->count();

        $lat        = $hangout->location ? $hangout->location->lat : 0;
        $lng        = $hangout->location ? $hangout->location->lng : 0;
        $radius     = 200;

        $help_near_by = null;
        $casts = collect();
        if ($this->isDetail) {
            $hangout_skills = $hangout->skills->pluck('id')->toArray();
            $help_near_by = Help::whereHas('skills', function ($query) use ($hangout_skills) {
                $query->whereIn('skill_id', $hangout_skills);
            })
                ->groupBy('title')
                ->orderBy('is_fake', 'asc')
                ->take(20)->get();

            //Cast
            if ($hangout->location && $lat && $lng) {
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

                            // ->whereRaw('
                            // (6371 * acos (
                            //                 cos ( radians('  . $lat .  ') )
                            //                 * cos( radians( locations.lat ) )
                            //                 * cos( radians( locations.lng ) - radians('  . $lng .  ') )
                            //                 + sin ( radians(' . $lat . ') )
                            //                 * sin( radians( locations.lat ) )
                            //                 )
                            // ) > ' . 2)

                            ->groupBy('group_distance')
                            ->orderBy('distance')->take(30)->get();
                    } catch (Exception $e) {
                    }
                }
            }
        }

        /*
        if(intval($lat) > 0 && intval($lng) > 0) {
            $help_near_by = Help::select('help_helps.*')->selectRaw('(6371 * acos (
                                              cos ( radians('  . $lat .  ') )
                                              * cos( radians( locations.lat ) )
                                              * cos( radians( locations.lng ) - radians('  . $lng .  ') )
                                              + sin ( radians(' . $lat . ') )
                                              * sin( radians( locations.lat ) )
                                            )
                                ) AS distance')
                ->leftJoin('locations', 'locations.locationable_id', '=', 'help_helps.id')
                ->whereRaw('
                            (6371 * acos (
                                              cos ( radians('  . $lat .  ') )
                                              * cos( radians( locations.lat ) )
                                              * cos( radians( locations.lng ) - radians('  . $lng .  ') )
                                              + sin ( radians(' . $lat . ') )
                                              * sin( radians( locations.lat ) )
                                            )
                            ) < ' . $radius . '

                    ')->orderBy('help_helps.created_at', 'desc')->orderBy('distance')->take(10)->get();
        }
        */

        $hangout_start = $hangout->start ? Carbon::create($hangout->start) : null;
        $hangout_end = $hangout->end ? Carbon::create($hangout->end) : null;
        if (in_array($hangout->available_status, ['no_time', 'combine'])) {
            $show_hangout = ($hangout->user_id != app('request')->user()->id);
            $hangout_start = null;
            $hangout_end = null;
        }
        if ($hangout->is_fake) {
            $show_hangout = true;
        }

        if ($hangout->type == Hangout::$type['single']) {

            $disk = \Storage::disk('gcs');
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


            return [
                'is_fake'       => $hangout->is_fake,
                'id'            => $hangout->id,
                'type'          => $hangout->type,
                'offered'       => $offerCheck,
                'title'         => $hangout->title,
                'description'   => $hangout->description,
                'comment_count' => $commentCount,
                'kizuna'        => $hangout->kizuna,
                'offers_count'  => $offers_count,
                'start'         => $hangout_start,
                'end'           => $hangout_end,
                'liked'         => $likeCheck == 0 ? false : true,
                'like_count'    => $hangoutReactCount,
                'capacity'      => $hangout->capacity,
                'updated_at'    => $hangout->updated_at,
                'created_at'    => $hangout->created_at,
                'room_id'       => $hangout->room_id,
                'show_hangout'  => $show_hangout,
                'available_status' => $hangout->available_status,
                'friends'       => $friends,
                'helps'         => fractal($help_near_by, new HelpTransform()),
                'dymanic_link'  => dynamicUrl('hangout', $hangout->id),
                'isMinCapacity' => $hangout->is_min_capacity,
                'casts'         => $casts,
                'offers_accepted'  => $hangout->offers()
                    ->whereIn('status', [
                        Offer::$status['accept'],
                    ])->count(),
                'payment_method' => $hangout->payment_method,
                'amount' => $hangout->amount,
                'is_range_price'       => $hangout->is_range_price,
                'min_amount'       => $hangout->min_amount,
                'max_amount'       => $hangout->max_amount,
    
            ];
        }
        return [
            'id'            => $hangout->id,
            'type'          => $hangout->type,
            'title'         => $hangout->title,
            'description'   => $hangout->description,
            'schedule'      => $hangout->schedule,
            'updated_at'    => $hangout->updated_at,
            'created_at'    => $hangout->created_at,
            'liked'         => $likeCheck == 0 ? false : true,
            'like_count'    => $hangoutReactCount,
            'comment_count' => $commentCount,
            'room_id'       => $hangout->room_id,
            'available_status' => $hangout->available_status,
            'friends'       => $friends,
            'end'           => $hangout_end,
            'show_hangout'  => $show_hangout,
            'offers_count'  => $offers_count,
            'offered'       => $offerCheck,
            'helps'         => fractal($help_near_by, new HelpTransform()),
            'isMinCapacity' => $hangout->is_min_capacity,
            'dymanic_link'  => dynamicUrl('hangout', $hangout->id),
            'offers_accepted'  => $hangout->offers()->whereIn('status', [Offer::$status['accept']])->count(),
            'casts'         => $casts,
            'payment_method' => $hangout->payment_method,
            'amount' => $hangout->amount,
            'is_range_price'       => $hangout->is_range_price,
            'min_amount'       => $hangout->min_amount,
            'max_amount'       => $hangout->max_amount,
        ];
    }

    public function includeSkills(Hangout $hangout)
    {
        $skills = $hangout->skills;

        if (!empty($skills)) {
            return $this->collection($skills, new SkillTransform());
        }
    }

    public function includeCategories(Hangout $hangout)
    {
        $categories = $hangout->categories;
        if (!empty($categories)) {
            return $this->collection($categories, new CategoryTransform());
        }
    }

    public function includeUser(Hangout $hangout)
    {
        $user = $hangout->user;
        return $this->item($user, new UserTransform());
    }

    public function includeLocation(Hangout $hangout)
    {
        $location = $hangout->location;
        if ($hangout->available_status != 'online' && (!$hangout->is_fake && $location)) {
            return $this->item($location, new LocationTransform());
        }
    }

    public function includeMedia(Hangout $hangout)
    {
        $media = $hangout->media;
        if ($media) {
            return $this->collection($media, new MediaTransform());
        }
        if ($hangout->is_fake) {
            $upload = new Upload();
            $upload->id = null;
            $upload->path = $hangout->cover_img;
            $upload->thumb = $hangout->cover_img;
            $upload->type = 'image';
            return $this->collection($upload, new MediaTransform());
        }
    }
}
