<?php

namespace Modules\User\Services\User;

use App\EmailVerify;
use App\User;
use Cake\Chronos\Date;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\KizunerApi\Transformers\UserTransform;
use Modules\User\Contracts\UserRepositoryInterface;
use Modules\User\Events\Auth\UserLoginEvent;
use Modules\User\Events\Auth\UserLogoutEvent;
use Modules\User\Events\UserCreatedEvent;
use Modules\User\Exceptions\MissingInfoException;
use Modules\User\Exceptions\WrongPinException;
use Modules\User\Http\Requests\Api\User\EmailPinVerifyRequest;
use Modules\User\Http\Requests\Api\User\SignInByEmailRequest;
use Modules\User\Http\Requests\Api\User\SignInBySocialRequest;
use Modules\User\Http\Requests\Api\User\SignUpRequest;
use Modules\User\Notifications\VerifyEmail;
use Modules\User\Social\SocialLoginFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Exception\ExceptionInterface;

class AuthManager
{

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var SocialLoginFactory
     */
    private $socialLoginFactory;

    /**
     * AuthManager constructor.
     * @param UserRepositoryInterface $userRepository
     * @param SocialLoginFactory $socialLoginFactory
     */
    public function __construct(
        UserRepositoryInterface $userRepository,
        SocialLoginFactory $socialLoginFactory
    ) {
        $this->socialLoginFactory = $socialLoginFactory;
        $this->userRepository = $userRepository;
    }

    /**
     * @return JsonResponse
     */
    public function logout()
    {
        if (Auth::check()) {
            event(new UserLogoutEvent(Auth::user()));
            Auth::user()->oAuthAccessToken()->delete();
            return new JsonResponse([
                'data' => [
                    'status' => 'success'
                ]
            ], Response::HTTP_OK);
        }
        return new JsonResponse([
            'error' => [
                'message' => 'Unauthorized'
            ]
        ], Response::HTTP_FORBIDDEN);
    }

    /**
     * @param SignInByEmailRequest $request
     * @return JsonResponse
     */
    public function signInByEmail(SignInByEmailRequest $request)
    {
        $credentials = $request->all(['email', 'password']);

        if (!Auth::attempt($credentials)) {
            return new JsonResponse([
                'errors' => [
                    'message' => 'Incorrect email or password.'
                ]
            ], Response::HTTP_UNAUTHORIZED);
        }
        $this->clearOldToken($request->user());
        $token = $this->createToken($request->user());

        if ($this->isBlocked($request->user())) {
            return new JsonResponse([
                'message' => 'Your Account is suspended!',
                'error' => [
                    'message' => 'Your Account is suspended!'
                ]
            ], Response::HTTP_FORBIDDEN);
        }

        event(new UserLoginEvent($request->user()));

        $request->user()->last_send_mail = null;
        $request->user()->save();

        return new JsonResponse([
            'data' => [
                'access_token' => $token->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse(
                    $token->token->expires_at
                )->toDateTimeString(),
                'self' => fractal($request->user(), new UserTransform())
            ]
        ], Response::HTTP_OK);
    }

    /**
     * @param SignInBySocialRequest $request
     * @return JsonResponse
     * @throws MissingInfoException
     */
    public function signInBySocial($data)
    {
        $provider = $data['provider'];

        if (!$provider) {
            throw new MissingInfoException('Missing Provider Param in URL: provider={facebook, google}');
        }

        try {
            $user = $this->socialLoginFactory->create($provider, $data['token'], $data['secret']);
            Log::debug("_________1");


            $this->clearOldToken($user);
            Log::debug("_________2");
            $token = $this->createToken($user);
            Log::debug("_________3");
            if ($this->isBlocked($user)) {
                return new JsonResponse([
                    'message' => 'Your account is blocked',
                    'error' => [
                        'message' => 'You account is blocked!'
                    ]
                ], Response::HTTP_FORBIDDEN);
            }
            if (isset($user->name)) {
                $user->username = $this->random_username($user->name);
            } else {
                $user->username = rand(9999999, 99999999999);
            }
            $user->last_send_mail = null;
            Log::debug("_________4");
            $user->save();

            return new JsonResponse([
                'data' => [
                    'access_token' => $token->accessToken,
                    'token_type' => 'Bearer',
                    'expires_at' => Carbon::parse(
                        $token->token->expires_at
                    )->toDateTimeString(),
                    'self' => fractal($user, new UserTransform())
                ]
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => $e->getMessage(),
                'error' => [
                    'message' => $e->getMessage(),
                ]
            ], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * @param SignUpRequest $request
     * @return array
     */
    public function createUser(SignUpRequest $request)
    {
        if (isset($request['name'])) {
            $request['username'] = $this->random_username($request['name']);
        }
        // Log::debug($request->all());
        $data = $request->all(['name', 'email', 'password', 'username']);

        $user = $this->userRepository->create($data);
        $token = $this->createToken($user);
        
        $verifyEmail = EmailVerify::updateOrCreate([
          'email' => $user->email
        ], ['pin' => rand(100000, 999999)]);
        $user->notify(new VerifyEmail($verifyEmail->pin));

        event(new UserCreatedEvent($user));

        return [
            'data' => [
                'access_token' => $token->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse(
                    $token->token->expires_at
                )->toDateTimeString(),
                'self' => fractal($user, new UserTransform())
            ]
        ];
    }

    function random_username($string)
    {
        // $replaced = str_replace(' ', '', $string);
        // $nrRand = rand(1, 99999);

        // $username = trim($replaced) . trim($nrRand);
        return strtolower(bin2hex(openssl_random_pseudo_bytes(8)));
    }


    /**
     * @param User $user
     * @param bool $rememberMe
     * @return \Laravel\Passport\PersonalAccessTokenResult
     */
    private function createToken(User $user)
    {
        /** @var User $user */
        $tokenResult = $user->createToken($user->email);
        Log::debug($tokenResult);
        
        $token = $tokenResult->token;
        $token->save();

        return $tokenResult;
    }

    /**
     * @return array
     */
    public function verifyEmail()
    {
        $currentUser = app('request')->user();

        $verifyEmail = EmailVerify::updateOrCreate([
            'email' => $currentUser->email
        ], ['pin' => rand(100000, 999999)]);

        $currentUser->notify(new VerifyEmail($verifyEmail->pin));

        return [
            'data' => [
                'message' => 'Email verification sent.',
                'status' => true
            ]
        ];
    }

    /**
     * @param EmailPinVerifyRequest $request
     * @return array
     * @throws WrongPinException
     */
    public function verifyEmailPin(EmailPinVerifyRequest $request)
    {
        $currentUser = app('request')->user();

        $emailPin = EmailVerify::where([
            'email' => $currentUser->email,
            'pin' => $request->get('pin')
        ])->first();

        if (!$emailPin) {
            throw new WrongPinException('Your PIN is Incorrect');
        }

        $currentUser->email_verified_at = \Illuminate\Support\Facades\Date::now();
        $currentUser->save();

        return [
            'data' => [
                'message' => 'Email verify successful!',
                'status' => true
            ]
        ];
    }

    public function phoneConfirmation(string $idToken)
    {
        $currentUser = app('request')->user();
        $currentUser->phone_verified_at = Carbon::now();
        $currentUser->save();
        return [
            'data' => [
                'message' => 'Phone verified',
                'status' => true
            ]
        ];
    }

    private function clearOldToken(User $user)
    {
        //Clear all old token
        $oldToken = $user->tokens;
        collect($oldToken)->each(function ($token) {
            $token->delete();
        });
    }

    private function isBlocked($user)
    {
        if ($user->block == 1) {
            return true;
        }
        return false;
    }
}