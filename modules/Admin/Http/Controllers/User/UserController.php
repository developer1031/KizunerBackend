<?php

namespace Modules\Admin\Http\Controllers\User;

use App\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Jobs\DeleteUserJob;
use Modules\Wallet\Domains\Queries\UserWalletQuery;
use Ramsey\Uuid\Uuid;
use Yajra\DataTables\Facades\DataTables;

class UserController
{
    public function index()
    {
        return view('user::index');
    }

    public function show(string $id)
    {
        $user = DB::table('users as u')
                    ->select(
                        'u.id as id',
                        'u.name as name',
                        'u.social_provider as social_provider',
                        'u.email as email',
                        'u.phone as phone',
                        'u.phone_verified_at as phone_verified_at',
                        'u.email_verified_at as email_verified_at',
                        'u.block as block',
                        'up.thumb as avatar',
                        'u.created_at as created_at',
                        'u.gender as gender',
                        'u.birth_date as birth_date',
                        'u.phone as phone',
                        'up.thumb as avatar',
                        'u.deleted as deleted',
                        'u.phone_verified_at as phone_verified_at',
                        'u.email_verified_at as email_verified_at',
                        'u.username as username',
                        'u.hangout_help_notification as hangout_help_notification',
                        'u.hangout_help_email_notification as hangout_help_email_notification',
                        'u.message_notification as message_notification',
                        'u.message_email_notification as message_email_notification',
                        'u.follow_notification as follow_notification',
                        'u.follow_email_notification as follow_email_notification',
                        'u.comment_notification as comment_notification',
                        'u.comment_email_notification as comment_email_notification',
                        'u.like_notification as like_notification',
                        'u.like_email_notification as like_email_notification'
                    )
                    ->leftJoin('uploads as up', 'up.id', '=', 'u.avatar_id')
                    ->where('u.id', $id)
                    ->first();


        //Get Wallet
        // $wallet = (new UserWalletQuery($user->id))->execute();
        // if (!$wallet) {
        //     $wallet['balance'] = 0;
        //     $wallet['today'] = 0;
        // }
        //Count Offer
        $countOffer = DB::select("SELECT count(id) as offer_count FROM hangout_offers WHERE receiver_id = '".$user->id."'");
        $countOfferComplete = DB::select("SELECT count(id) as offer_count FROM hangout_offers WHERE receiver_id = '".$user->id."' AND status=5");
        $countOfferProcess = DB::select("SELECT count(id) as offer_count FROM hangout_offers WHERE receiver_id = '".$user->id."' AND status=3");

        $skills  = DB::table('skills as s')
                     ->select('s.name as name')
                     ->leftJoin('skillables as sk', 'sk.skill_id', '=', 's.id')
                     ->where('sk.skillable_id', $user->id)
                     ->groupBy('s.id')
                     ->get()
                     ->pluck('name');

        return view('user::show')->with([
            'user'      => $user,
            // 'wallet'    => $wallet,
            'skills'    => $skills,
            'statistic' => [
                'offer'         => $countOffer[0]->offer_count,
                'complete'      => $countOfferComplete[0]->offer_count,
                'proccess'      => $countOfferProcess[0]->offer_count,
            ]
        ]);
    }

    public function update(string $id)
    {
        $request = app('request');
        $user = User::find($id);
        $type = $request->input('type');

        if ($type == 'block') {
            $block = $user->block == 0 ? false : true;
            $user->block = !$block;
            $user->save();
            return response()->json([
                'status' => true
            ], Response::HTTP_OK);
        }

        if ($type == 'delete') {
            $user->deleted = true;
            $user->block = true;
            $user->deleted_email = $user->email;
            $user->deleted_phone = $user->phone;
            $user->fcm_token     = null;
            $user->email = Uuid::uuid4() . '@kizuna.app.deleted';
            $user->phone = Uuid::uuid4() . '_phone_deleted';
            $user->save();
            DeleteUserJob::dispatch($user->id);
            return response()->json([
                'status' => true
            ], Response::HTTP_OK);
        }

        if ($type == 'info') {
            $user->name = $request->name;
            $user->phone = $request->phone;
            $user->email = $request->email;
            $user->username = $request->username;
            
            $user->hangout_help_notification = $request->hangout_help_notification;
            $user->hangout_help_email_notification = $request->hangout_help_email_notification;
            $user->message_notification = $request->message_notification;
            $user->message_email_notification = $request->message_email_notification;
            $user->follow_notification = $request->follow_notification;
            $user->follow_email_notification = $request->follow_email_notification;
            $user->comment_notification = $request->comment_notification;
            $user->comment_email_notification = $request->comment_email_notification;
            $user->like_notification = $request->like_notification;
            $user->like_email_notification = $request->like_email_notification;
            if ($request->has('phone_verified_at')) {
                $user->phone_verified_at = $request->phone_verified_at;
            } else {
                $user->phone_verified_at = null;
            }
            if ($request->has('email_verified_at')) {
                $user->email_verified_at = $request->email_verified_at;
            } else {
                $user->email_verified_at = null;
            }
            $user->save();
            return redirect()->back()->withSuccess("Update user information successful!");
        }
    }

    public function data()
    {
        $userQuery = DB::table('users')
                    ->select(
                        'users.id as id',
                        'users.name as name',
                        'users.email as email',
                        'users.username as username',
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

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}
