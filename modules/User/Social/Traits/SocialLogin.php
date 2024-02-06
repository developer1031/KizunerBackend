<?php

namespace Modules\User\Social\Traits;

use App\User;
use Carbon\Carbon;
use GeneaLabs\LaravelSocialiter\Socialiter;
use Laravel\Socialite\Facades\Socialite;
use Modules\User\Events\UserCreatedEvent;
use Illuminate\Support\Facades\Log;

trait SocialLogin
{
    /** @var $provider */
    protected $provider;

    /**
     * @param $data
     * @return bool
     */
    public function checkUserExist($data)
    {
        $query = User::where('social_id', $data['social_id'])
                    ->where('social_provider', $data['social_provider']);

        if ($data['email']) {
            $query->orWhere('email', $data['email']);
        }
        $user = $query->first();

        if ($user) {
            return $user;
        }
        return false;
    }

    /**
     * @return string
     */
    public function getProvider()
    {
        return $this->provider;
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
            'social_provider'   => $this->getProvider(),
            'email_verified_at' => Carbon::now(),
        ]);
    }

    /**
     * @param $token
     * @return mixed
     */
    private function getSocialUser($token, $secret = null)
    {
        try {
          $provider = $this->getProvider();
          
          if ($this->getProvider() == 'apple') {
            $socialUser = Socialite::driver($provider)->userFromToken($token);

            return [
                'name'              => app('request')->name,
                'email'             => $socialUser->email,
                'social_id'         => $socialUser->id,
                'social_avatar'     => $socialUser->avatar,
                'social_provider'   => $this->getProvider()
            ];
          }

          $socialUser = Socialite::driver($provider)->userFromToken($token);
          return [
              'name'              => $socialUser->name,
              'email'             => $socialUser->email,
              'social_id'         => $socialUser->id,
              'social_avatar'     => $socialUser->avatar,
              'social_provider'   => $this->getProvider()
          ];
        } catch (\Exception $e) {
          Log::debug($e->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function create($token, $secret = null)
    {
        // Check and verify Token
        $data = $this->getSocialUser($token, $secret);

        if ($data != null) {
          // Check User Exist
          $user = $this->checkUserExist($data);

          if (!$user) {
              $user = $this->createNewUser($data);
              event(new UserCreatedEvent($user));
          }

          return $user;
        }
        
        return null;
    }
}
