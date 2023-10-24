<?php

namespace Modules\User\Social;

interface SocialUserInterface
{
    /**
     * Return new User if Not exist, and old User If exist by checking token
     * @param $token
     */
    public function create($token);
}
