<?php

namespace Modules\Auth\Contracts;

use Modules\Auth\Http\Requests\SocialTokenRequest;
use Modules\Auth\Http\Requests\TokenRequest;
use Modules\Auth\Http\Requests\RefreshTokenRequest;

interface AuthManagerInterface
{
    /**
     * @param TokenRequest $request
     * @return mixed
     */
    public function createCredentials(TokenRequest $request);

    /**
     * @param RefreshTokenRequest $request
     * @return mixed
     */
    public function createRefreshToken(RefreshTokenRequest $request);

    /**
     * @param SocialTokenRequest $request
     * @return mixed
     */
    public function createSocialToken(SocialTokenRequest $request);

    /**
     * @return mixed
     * @throws \Exception
     */
    public function removeUserToken();
}
