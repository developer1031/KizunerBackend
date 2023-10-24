<?php

namespace Modules\Admin\Http\Controllers\Dashboard;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Kizuner\Models\Skill;
use Yajra\DataTables\Facades\DataTables;

class DashboardController
{
    public function index(Request $request)
    {
        $from   = ($request->has('from') && $request->from) ? $request->from : Carbon::now()->subMonths(3)->format('Y-m-d');
        $to     = ($request->has('to') && $request->to) ? $request->to : Carbon::now()->format('Y-m-d');

        //User Statistic Segment
        $userStatistic = [];
        $userStatistic['total'] = DB::table('users')
                                    ->where('admin', false)
                                    ->whereNull('deleted')
                                    ->whereBetween('created_at', [$from, $to])
                                    ->where('is_fake', '<>', 1)
                                    ->count();
        $currentMonth = Carbon::now();
        $month1 = Carbon::now()->subMonths(5);
        $month2 = Carbon::now()->subMonths(4);
        $month3 = Carbon::now()->subMonths(3);
        $month4 = Carbon::now()->subMonths(2);
        $month5 = Carbon::now()->subMonths(1);

        $month[] = $month1->format('F');
        $month[] = $month2->format('F');
        $month[] = $month3->format('F');
        $month[] = $month4->format('F');
        $month[] = $month5->format('F');
        $month[] = $currentMonth->format('F');

        $startMonth1 = Carbon::now()->subMonths(5)->startOfMonth();
        $endMonth1 = Carbon::now()->subMonths(5)->endOfMonth();
        $userStatistic['users'][] = DB::table('users')
                        ->where('admin', false)
                        ->whereNull('deleted')
                        ->whereBetween('created_at', [$startMonth1, $endMonth1])
                        ->where('is_fake', '<>', 1)
                        ->count();

        $startMonth2 = Carbon::now()->subMonths(4)->startOfMonth();
        $endMonth2 = Carbon::now()->subMonths(4)->endOfMonth();
        $userStatistic['users'][] = DB::table('users')
            ->where('admin', false)
            ->whereNull('deleted')
            ->whereBetween('created_at', [$startMonth2, $endMonth2])
            ->where('is_fake', '<>', 1)
            ->count();


        $startMonth3 = Carbon::now()->subMonths(3)->startOfMonth();
        $endMonth3 = Carbon::now()->subMonths(3)->endOfMonth();
        $userStatistic['users'][] = DB::table('users')
            ->where('admin', false)
            ->whereNull('deleted')
            ->whereBetween('created_at', [$startMonth3, $endMonth3])
            ->where('is_fake', '<>', 1)
            ->count();


        $startMonth4 = Carbon::now()->subMonths(2)->startOfMonth();
        $endMonth4 = Carbon::now()->subMonths(2)->endOfMonth();
        $userStatistic['users'][] = DB::table('users')
            ->where('admin', false)
            ->whereNull('deleted')
            ->whereBetween('created_at', [$startMonth4, $endMonth4])
            ->where('is_fake', '<>', 1)
            ->count();

        $startMonth5 = Carbon::now()->subMonths(1)->startOfMonth();
        $endMonth5 = Carbon::now()->subMonths(1)->endOfMonth();
        $prevUserMonth = DB::table('users')
            ->where('admin', false)
            ->whereNull('deleted')
            ->whereBetween('created_at', [$startMonth5, $endMonth5])
            ->where('is_fake', '<>', 1)
            ->count();
        $userStatistic['users'][] = $prevUserMonth;

        $startMonth6 = Carbon::now()->startOfMonth();
        $endMonth6 = Carbon::now()->endOfMonth();
        $currentUserMonth = DB::table('users')
            ->where('admin', false)
            ->whereNull('deleted')
            ->whereBetween('created_at', [$startMonth6, $endMonth6])
            ->where('is_fake', '<>', 1)
            ->count();
        $userStatistic['users'][] = $currentUserMonth;

        $userStatistic['gap'] = $currentUserMonth - $prevUserMonth;

        $hangoutCount = DB::table('hangout_hangouts')->whereBetween('created_at', [$from, $to])->count();
        $helpCount = DB::table('help_helps')->whereBetween('created_at', [$from, $to])->count();

        $offerCount   = DB::table('hangout_offers')->whereBetween('created_at', [$from, $to])->count();
        $offerHelpCount   = DB::table('help_offers')->whereBetween('created_at', [$from, $to])->count();

        //Likes
        $likeCount   = DB::table('reacts')->where('react_type', 'like')->whereBetween('created_at', [$from, $to])->count();

        //Shares
        $shareCount   = DB::table('reacts')->where('react_type', 'share')->whereBetween('created_at', [$from, $to])->count();

        //Comments
        $commentsCount   = DB::table('comment_comments')->whereBetween('created_at', [$from, $to])->count();


        /*
         * Cast + guest by location
         */
        $casts = DB::select('SELECT country, COUNT(*) as casts
                            FROM (SELECT T3.`country` AS country, T2.`email`
                                FROM `hangout_hangouts` T1
                                LEFT JOIN users T2 ON T1.`user_id` = T2.`id`
                                LEFT JOIN country T3 ON T2.`country` = T3.`id`
                                WHERE T1.is_fake <> 1
                                GROUP BY T1.`user_id`) AS T_1
                            GROUP BY country
                            ');

        $guests = DB::select('SELECT country, COUNT(*) AS guest
                        FROM (SELECT T3.`country` AS country, T2.`email`
                            FROM `hangout_offers` T1
                            LEFT JOIN users T2 ON T1.sender_id = T2.`id`
                            LEFT JOIN country T3 ON T2.`country` = T3.`id`
                            GROUP BY T1.`sender_id`) AS T_1
                        GROUP BY country');

        $casts_guests = [];
        foreach ($casts as $cast) {
            $country = $cast->country ? $cast->country : 'Other';
            $casts_guests[$country] = [
                'cast' => $cast->casts,
                'guest' => 0
            ];
        }
        foreach ($guests as $guest) {
            $country = $guest->country ? $guest->country : 'Other';
            if(isset($casts_guests[$country])) {
                $casts_guests[$country]['guest'] = $guest->guest;
            }
            else {
                $casts_guests[$country] = [
                    'cast' => 0,
                    'guest' => $guest->guest
                ];
            }
        }
        ksort($casts_guests);


        return view('dashboard::index')
                    ->with([
                        'user'          => $userStatistic,
                        'hangoutCount'  => $hangoutCount,
                        'helpCount'     => $helpCount,
                        'offerCount'    => $offerCount,
                        'offerHelpCount'    => $offerHelpCount,
                        'months'        => $month,

                        'hangout_statistic' => getStatistic('hangout'),
                        'hangout_statistic_offer' => getStatistic('hangout_offer'),
                        'help_statistic' => getStatistic('help'),
                        'help_statistic_offer' => getStatistic('help_offer'),
                        'likeCount'    => $likeCount,
                        'likeCount_statistic'    => getStatistic('likes'),

                        'shareCount'   => $shareCount,
                        'shareCount_statistic'    => getStatistic('shares'),

                        'commentsCount'    => $commentsCount,
                        'commentsCount_statistic'    => getStatistic('comments'),

                        'from' => $from,
                        'to' => $to,
                        'casts_guests' => $casts_guests
                    ]);
    }

    public function userData()
    {
        $userQuery = DB::table('users')
            ->select(
                'users.id as id',
                'users.name as name',
                'users.email as email',
                'uploads.thumb as thumb',
                'w.balance as balance'
            )
            ->leftJoin('uploads', 'uploads.id', '=', 'users.avatar_id')
            ->leftJoin('wallet_wallets as w', 'w.user_id', '=', 'users.id')
            ->where('admin', false)
            ->where('is_fake', 0)
            ->whereNull('deleted')
            ->orderBy('users.created_at', 'desc');
        return DataTables::query($userQuery)->make(true);
    }


}
