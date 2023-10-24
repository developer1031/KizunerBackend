<?php

namespace Modules\User\Services\User;

use App\ResetPassword;
use Illuminate\Support\Str;
use Modules\Kizuner\Exceptions\PermissionDeniedException;
use Modules\User\Contracts\UserRepositoryInterface;
use Modules\User\Exceptions\AuthException;
use Modules\User\Exceptions\WrongPinException;
use Modules\User\Http\Requests\Api\User\ChangePasswordRequest;
use Modules\User\Http\Requests\Api\User\PinVerifyRequest;
use Modules\User\Notifications\ChangePassword;

class ResetPasswordManager
{
    /** @var UserRepositoryInterface  */
    private $userRepository;

    /**
     * ResetPasswordManager constructor.
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }


    /**
     * @param string $email
     * @return array
     */
    public function sendResetLink(string $email)
    {
        $user = $this->userRepository->getByEmail($email);

        if(!$user) {
            throw new PermissionDeniedException('There is no Account with Email: ' . $email);
        }

        $passwordReset = ResetPassword::updateOrCreate([
            'email' => $email
        ], [
            'token' => Str::random(60),
            'pin'   =>  rand(100000, 999999)
        ]);

        if ($passwordReset) {
            $user->notify(new ChangePassword($passwordReset->pin));
        }
        return [
            'data' => [
                'status' => true,
                'message' => 'Send reset password pin successful'
            ]
        ];
    }

    /**
     * @param PinVerifyRequest $request
     * @return array
     * @throws WrongPinException
     */
    public function verifyPin(PinVerifyRequest $request)
    {
        $pin    = $request->get('pin');
        $email  = $request->get('email');

        $resetPassword = ResetPassword::where([
            'email' => $email,
            'pin'   => $pin
        ])->first();

        if (!$resetPassword) {
            throw new WrongPinException('Your Pin is incorrect');
        }

        $token = $resetPassword->token;
        return [
            'data' => [
                'email' => $email,
                'token' => $token
            ]
        ];
    }

    /**
     * @param ChangePasswordRequest $request
     * @return array
     * @throws AuthException
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $resetPassword = ResetPassword::where([
            'email' => $request->get('email'),
            'token' => $request->get('token'),
        ])->first();

        if (!$resetPassword) {
            throw new AuthException(
                'Wrong token'
            );
        }

        $user = $this->userRepository->getByEmail($resetPassword->email);
        $user->password = bcrypt($request->get('password'));
        $user->save();

        if ($user) {
            $resetPassword->delete();
        }

        return [
            'data' => [
                'message' => 'Password change successful',
                'status'  => true
            ]
        ];
    }
}
