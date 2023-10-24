<?php

namespace Modules\KizunerApi\Http\Controllers;

use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Config\Config;
use Modules\Helps\Models\Help;
use Modules\Helps\Models\HelpOffer;
use Modules\Kizuner\Models\Hangout;
use Modules\Kizuner\Models\LeaderBoard;
use Modules\Kizuner\Models\Offer;
use Modules\KizunerApi\Http\Requests\Status\StatusCreateRequest;
use Modules\KizunerApi\Http\Requests\Status\StatusUpdateRequest;
use Modules\KizunerApi\Services\StatusManager;
use Modules\KizunerApi\Transformers\LeaderBoardByObjectTransform;
use Modules\KizunerApi\Transformers\LeaderBoardTransform;
use Symfony\Component\HttpFoundation\Response;

class LeaderBoardController
{
    public function getLeaderBoard(Request $request)
    {
        $type = $request->type;

        if(!$type || $type=='global') {
            $top3 = LeaderBoard::orderBy('point', 'desc')->orderBy('point', 'desc')->take(3)->get();
            $top10 = LeaderBoard::orderBy('point', 'desc')->orderBy('point', 'desc')->skip(3)->take(10)->get();
        }
        else {
            $current_user = auth()->user();
            if($type=='country') {
                $country_id = $current_user->country;

                $top3 = $leaderboards = LeaderBoard::leftJoin('users', 'leader_board.user_id', '=', 'users.id')
                    ->where('users.country', $country_id)
                    ->orderBy('point', 'desc')
                    ->take(3)
                    ->get();

                $top10 = $leaderboards = LeaderBoard::leftJoin('users', 'leader_board.user_id', '=', 'users.id')
                    ->where('users.country', $country_id)
                    ->orderBy('point', 'desc')
                    ->skip(3)
                    ->take(10)
                    ->get();
            }
            else if($type=='regional') {

                $lat = $current_user->location ? $current_user->location->lat : 0;
                $lng = $current_user->location ? $current_user->location->lng : 0;

                $radius = intval(Config::getConfigVal('reward_radius'));

                $near_users = User::select('users.*')->selectRaw('(6371 * acos (
                                              cos ( radians('  . $lat .  ') )
                                              * cos( radians( locations.lat ) )
                                              * cos( radians( locations.lng ) - radians('  . $lng .  ') )
                                              + sin ( radians(' . $lat . ') )
                                              * sin( radians( locations.lat ) )
                                            )
                                ) AS distance')
                    ->leftJoin('locations', 'locations.locationable_id', '=', 'users.id')
                    ->whereRaw('
                            (6371 * acos (
                                              cos ( radians('  . $lat .  ') )
                                              * cos( radians( locations.lat ) )
                                              * cos( radians( locations.lng ) - radians('  . $lng .  ') )
                                              + sin ( radians(' . $lat . ') )
                                              * sin( radians( locations.lat ) )
                                            )
                            ) <= ' . $radius . '

                    ')
                    ->where('is_fake', '<>', 1)
                    ->orderBy('distance')->take(10)->get();

                //$user_ids = $near_users->pluck('id', 'name');
                $user_ids = [];
                foreach ($near_users as $_user) {
                    array_push($user_ids, $_user->id);
                }

                $top3 = LeaderBoard::whereIn('user_id', $user_ids)->orderBy('point', 'desc')->take(3)->get();
                $top10 = LeaderBoard::whereIn('user_id', $user_ids)->orderBy('point', 'desc')->skip(3)->take(10)->get();
            }
        }

        $data = [
            'top_3' => fractal($top3, new LeaderBoardTransform()),
            'top_10' => fractal($top10, new LeaderBoardTransform()),
        ];

        return new JsonResponse($data, Response::HTTP_CREATED);
    }

    public function leaderboardBy($object, Request $request) {
        $from_date = $request->has('from_date') ? $request->get('from_date') : null;
        $to_date = $request->has('to_date') ? $request->get('to_date') : null;

        //Post Hangout
        if($object=='cast') {
            $top3  = Hangout::select('user_id', DB::raw('count(user_id) quantity'))->where('is_fake', '<>', 1)->groupBy('user_id')->orderBy('quantity', 'desc');
            $top10 = Hangout::select('user_id', DB::raw('count(user_id) quantity'))->where('is_fake', '<>', 1)->groupBy('user_id')->orderBy('quantity', 'desc');
            if($from_date && $to_date) {
                $top3->whereDate('created_at', '>=', $from_date)->whereDate('created_at', '<=', $to_date);
                $top10->whereDate('created_at', '>=', $from_date)->whereDate('created_at', '<=', $to_date);
            }
        }

        //Offer hangouts
        else if($object=='guest') {
            $top3  = Offer::select(DB::raw('sender_id'), DB::raw('count(sender_id) quantity'))->where('status', HelpOffer::$status['completed'])->groupBy('sender_id')->orderBy('quantity', 'desc');
            $top10 = Offer::select(DB::raw('sender_id'), DB::raw('count(sender_id) quantity'))->where('status', HelpOffer::$status['completed'])->groupBy('sender_id')->orderBy('quantity', 'desc');
            if($from_date && $to_date) {
                $top3->whereDate('created_at', '>=', $from_date)->whereDate('created_at', '<=', $to_date);
                $top10->whereDate('created_at', '>=', $from_date)->whereDate('created_at', '<=', $to_date);
            }
        }

        //Post Helpers
        else if($object=='requester') {
            $top3  = Help::select('user_id', DB::raw('count(user_id) quantity'))
                        ->where(function($query) {
                            $query->where('is_fake', 0)->orWhereNull('is_fake');
                        })
                        ->groupBy('user_id')->orderBy('quantity', 'desc');
            $top10 = Help::select('user_id', DB::raw('count(user_id) quantity'))
                        ->where(function($query) {
                            $query->where('is_fake', 0)->orWhereNull('is_fake');
                        })
                        ->groupBy('user_id')->orderBy('quantity', 'desc');
            if($from_date && $to_date) {
                $top3->whereDate('created_at', '>=', $from_date)->whereDate('created_at', '<=', $to_date);
                $top10->whereDate('created_at', '>=', $from_date)->whereDate('created_at', '<=', $to_date);
            }

        }

        //offer Helps
        else if($object=='helper') {
            $top3  = HelpOffer::select('sender_id', DB::raw('count(sender_id) quantity'))->where('status', HelpOffer::$status['completed'])->groupBy('sender_id')->orderBy('quantity', 'desc');
            $top10 = HelpOffer::select('sender_id', DB::raw('count(sender_id) quantity'))->where('status', HelpOffer::$status['completed'])->groupBy('sender_id')->orderBy('quantity', 'desc');
            if($from_date && $to_date) {
                $top3->whereDate('created_at', '>=', $from_date)->whereDate('created_at', '<=', $to_date);
                $top10->whereDate('created_at', '>=', $from_date)->whereDate('created_at', '<=', $to_date);
            }
        }

        $data = [
            'top_3'  => fractal($top3->take(3)->get(), new LeaderBoardByObjectTransform($object)),
            'top_10' => fractal($top10->skip(3)->take(10)->get(), new LeaderBoardByObjectTransform($object)),
        ];
        return new JsonResponse($data, Response::HTTP_CREATED);
    }
}
