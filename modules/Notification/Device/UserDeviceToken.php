<?php

namespace Modules\Notification\Device;

use App\User as UserModel;

class UserDeviceToken
{
    public static function getUserDevice(string $userId, string $filterNoti = '')
    {
        $current_user = auth()->user();
        \Log::info("HANDLE________________1");
        if ($current_user) {
            \Log::info("HANDLE________________2");
            if ($filterNoti != '') {
                \Log::info("HANDLE________________3");
                $user = UserModel::select('fcm_token')
                    ->where('id', $userId)
                    ->where('id', '<>', $current_user->id)
                    ->where('notification', true)
                    ->where($filterNoti, true)
                    ->first();
            } else {
                \Log::info("HANDLE________________5");
                $user = UserModel::select('fcm_token')
                    ->where('id', $userId)
                    ->where('id', '<>', $current_user->id)
                    ->where('notification', true)
                    ->first();
            }
        } else {
            if ($filterNoti != '') {
                \Log::info("HANDLE________________4");
                $user = UserModel::select('fcm_token')
                    ->where('id', $userId)
                    ->where('notification', true)
                    ->where($filterNoti, true)
                    ->first();
            } else {
                \Log::info("HANDLE________________5");
                $user = UserModel::select('fcm_token')
                    ->where('id', $userId)
                    ->where('notification', true)
                    ->first();
            }
        }

        \Log::info("HANDLE_____________" . ($user ? $user->fcm_token : null));

        return $user ? $user->fcm_token : null;
    }

    public static function getUserEmail(string $userId, string $filterEmail = '')
    {
        $current_user = auth()->user();
        if ($current_user) {

            if ($filterEmail != '') {
                $user = UserModel::select('email')
                    ->where('id', $userId)
                    ->where('id', '<>', $current_user->id)
                    ->where($filterEmail, true)
                    ->first();
            } else {
                $user = UserModel::select('email')
                    ->where('id', $userId)
                    ->where('id', '<>', $current_user->id)
                    ->first();
            }
        } else {
            if ($filterEmail != '') {
                $user = UserModel::select('email')
                    ->where('id', $userId)
                    ->where($filterEmail, true)
                    ->first();
            } else {
                $user = UserModel::select('email')
                    ->where('id', $userId)
                    ->first();
            }
        }
        return $user ? $user->email : null;
    }

    public static function getUsersDevice(array $userIds)
    {
        $current_user = auth()->user();
        if ($current_user) {
            return UserModel::select('fcm_token')
                ->whereIn('id', $userIds)
                ->where('id', '<>', $current_user->id)
                ->where('notification', true)
                ->get()
                ->pluck('fcm_token')
                ->toArray();
        } else {
            return UserModel::select('fcm_token')
                ->whereIn('id', $userIds)
                ->where('notification', true)
                ->get()
                ->pluck('fcm_token')
                ->toArray();
        }
    }
}
