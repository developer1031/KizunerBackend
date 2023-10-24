<?php

namespace Modules\Auth\Resolvers;

use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Modules\Auth\Contracts\SocialUserResolverInterface;
use Laravel\Socialite\Facades\Socialite;
use Modules\User\Contracts\UserRepositoryInterface;

class SocialUserResolver implements SocialUserResolverInterface
{

    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @inheritDoc
     */
    public function resolveUserByProviderCredentials(string $provider, string $accessToken): ?Authenticatable
    {
        $data = $this->getSocialUser($provider, $accessToken);

        return !$this->getUser($data) ? $this->createNewUser($data) : $this->getUser($data);
    }

    private function getSocialUser(string $provider, string $accessToken)
    {
        $socialUser =  Socialite::driver($provider)->userFromToken($accessToken);

        return [
            'name'              => $socialUser->name,
            'email'             => $socialUser->email,
            'social_id'         => $socialUser->id,
            'social_avatar'     => $socialUser->avatar,
            'social_provider'   => $provider
        ];
    }

    private function getUser($data)
    {
        $query = User::where('social_id', $data['social_id'])
            ->where('social_provider', $data['social_provider']);

        if ($data['email']) {
            $query->orWhere('email', $data['email']);
        }
        return $query->first();
    }

    /**
     * @param $data
     * @return User
     */
    private function createNewUser($data)
    {
        return $this->userRepository->create([
            'name'              => $data['name'],
            'email'             => $data['email'],
            'social_id'         => $data['social_id'],
            'social_provider'   => $data['social_provider'],
            'email_verified_at' => Carbon::now()
        ]);
    }
}
