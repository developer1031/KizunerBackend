<?php

namespace Modules\User\Repositories;

use App\User;
use Illuminate\Support\Facades\Log;
use Modules\User\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{

    /**
     * @inheritDoc
     */
    public function create(array $data)
    {
        if (!array_key_exists('social_provider', $data)) {
            $data['password'] = bcrypt($data['password']);
        }
        $user = new User($data);
        $user->save();
        return $user;
    }

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        return User::find($id);
    }

    public function delete($id)
    {
        User::destroy($id);
    }

    public function updateInfo($userId, array $data)
    {
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        if (isset($data['username'])) {
            $userExixtUserName = User::where('username', $data['username'])->first();

            $current_user = auth()->user();
            if ($userExixtUserName) {
                if ($userExixtUserName->id != $current_user->id) {
                    return null;
                }
            }
        }

        $user = User::find($userId);
        $user->update($data);
        return $user;
    }

    public function update($data)
    {
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        $user = User::find($data['id'])->update($data);
        return $user;
    }

    public function getByEmail(string $email)
    {
        $user = User::where('email', $email)->first();
        return $user;
    }

    public function getUserNameByIds(array $ids)
    {
        $users = User::select('name')
                      ->whereIn('id', $ids)
                      ->limit(3)
                      ->get();
        return $users;
    }
}
