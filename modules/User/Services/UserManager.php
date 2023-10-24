<?php

namespace Modules\User\Services;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\KizunerApi\Transformers\UserTransform;
use Modules\User\Contracts\UserRepositoryInterface;
use Modules\User\Http\Requests\UserCreateRequest;
use Modules\User\Http\Requests\UserUpdateRequest;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\DataTables;

class UserManager
{

    /** @var UserRepositoryInterface $userRepository */
    private $userRepository;

    /** @var DataTables */
    private $dataTables;

    /**
     * UserManager constructor.
     * @param UserRepositoryInterface $userRepository
     * @param DataTables $dataTables
     */
    public function __construct(
        UserRepositoryInterface $userRepository,
        DataTables $dataTables
    ) {
        $this->dataTables = $dataTables;
        $this->userRepository = $userRepository;
    }

    /**
     * @param UserCreateRequest $request
     * @return \Illuminate\Http\JsonResponse | User
     */
    public function create(UserCreateRequest $request, $token = false)
    {
        $user = $this->userRepository->create($request->all(['name', 'email', 'phone', 'password', 'language']));

        if ($token) {
            $token = $this->createToken($user);
            return new JsonResponse([
                'access_token' => $token->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse(
                    $token->token->expires_at
                )->toDateTimeString()
            ], Response::HTTP_CREATED);
        }
        return $user;
    }

    /**
     * @param UserApiLoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(UserApiLoginRequest $request)
    {
        //
    }

    /**
     * @param User $user
     * @param bool $rememberMe
     * @return \Laravel\Passport\PersonalAccessTokenResult
     */
    private function createToken(User $user, $rememberMe = false)
    {
        /** @var User $user */
        $tokenResult = $user->createToken($user->email);
        $token = $tokenResult->token;

        if ($rememberMe) {
            $token->expires_at = Carbon::now()->addWeeks(1);
        }
        $token->save();
        return $tokenResult;
    }

    public function delete($id)
    {
        $currentUserId = Auth::id();

        if ($id == $currentUserId) {
            return false;
        }
        return $this->userRepository->delete($id);
    }

    public function update(UserUpdateRequest $request)
    {
        if ($request->get('password_current')) {
            if (!Hash::check(
                $request->get('password_current'),
                $this->userRepository->get($request->get('id'))->password
            )) {
                return false;
            }
            $user = $this->userRepository
                            ->update($request->all(['id', 'name', 'email', 'phone', 'password', 'language']));
        }
        $user = $this->userRepository->update($request->all(['id', 'name', 'email', 'phone', 'language']));
        return $user;
    }

    public function get($id)
    {
        $user = $this->userRepository->get($id);
        return $user;
    }

    public function getDatatable()
    {
        return $this->dataTables->eloquent(User::query())->make(true);
    }

    public function getUser(string $id = null)
    {
        if ($id != null) {
            $user = $this->userRepository->get($id);
            $user->last_send_mail = null;
            $user->save();
        } else {
            $user = app('request')->user();
        }
        return fractal($user, new UserTransform());
    }
}
