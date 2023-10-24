<?php

namespace Modules\Auth\Services;

use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Auth;
use Modules\Auth\Contracts\AuthManagerInterface;
use Modules\Auth\Http\Requests\RefreshTokenRequest;
use Modules\Auth\Http\Requests\SocialTokenRequest;
use Modules\Auth\Http\Requests\TokenRequest;
use Modules\Auth\Passport\RefreshTokenGenerator;
use Modules\Auth\Passport\SocialUserTokenGenerator;
use Modules\Auth\Passport\UserTokenGenerator;

class AuthManager implements AuthManagerInterface
{
    /**
     * @inheritDoc
     */
    public function createCredentials(TokenRequest $request)
    {
        $email      = $request->get('email');
        $password   = $request->get('password');

        try {
            $response = UserTokenGenerator::handle($email, $password);
            $tokens = json_decode((string)$response->getBody() , true);
        } catch (ClientException $exception) {
            throw $exception;
        }
        return $tokens;
    }

    /**
     * @inheritDoc
     */
    public function createRefreshToken(RefreshTokenRequest $request)
    {
        $refreshToken = $request->get('refresh_token');

        try {
            $response = RefreshTokenGenerator::handle($refreshToken);
            $tokens = json_decode((string)$response->getBody() , true);
        } catch (ClientException $exception) {
            throw $exception;
        }
        return $tokens;
    }

    /**
     * @inheritDoc
     */
    public function createSocialToken(SocialTokenRequest $request)
    {
        $accessToken    = $refreshToken = $request->get('access_token');
        $providerName   = $refreshToken = $request->get('provider');

        try {
            $response = SocialUserTokenGenerator::handle($providerName, $accessToken);
            $tokens = json_decode((string)$response->getBody() , true);
        } catch (ClientException $exception) {
            throw $exception;
        }
        return $tokens;
    }

    /**
     * @inheritDoc
     */
    public function removeUserToken()
    {
        if (Auth::check()) {
            return Auth::user()->oAuthAccessToken()->delete();
        }
        throw new \Exception('Unauthorized');
    }
}
