<?php

namespace Modules\User\Social;

use Modules\User\Contracts\UserRepositoryInterface;
use Modules\User\Social\Traits\SocialLogin;

class FacebookLogin implements SocialUserInterface
{
    use SocialLogin;

    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->provider = 'facebook';
    }
}
