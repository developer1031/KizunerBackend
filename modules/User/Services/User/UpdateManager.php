<?php

namespace Modules\User\Services\User;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Modules\Upload\Contracts\UploadRepositoryInterface;
use Modules\Upload\Exceptions\FileNotExistException;
use Modules\Kizuner\Contracts\LocationRepositoryInterface;
use Modules\KizunerApi\Transformers\UserTransform;
use Modules\User\Contracts\UserRepositoryInterface;
use Modules\User\Exceptions\AuthException;
use Modules\User\Http\Requests\Api\Update\AuthUpdateRequest;
use Modules\User\Http\Requests\Api\Update\GeneralUpdateRequest;
use Modules\User\Http\Requests\Api\Update\IndentityUpdateRequest;
use Modules\User\Http\Requests\Api\Update\LocationUpdateRequest;
use Symfony\Component\HttpFoundation\Response;

class UpdateManager
{

    private $userRepository;

    private $locationRepository;

    private $uploadRespository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        LocationRepositoryInterface $locationRepository,
        UploadRepositoryInterface $uploadRepository
    ) {
        $this->uploadRespository = $uploadRepository;
        $this->locationRepository = $locationRepository;
        $this->userRepository = $userRepository;
    }

    public function updateGeneralInfo(GeneralUpdateRequest $request)
    {
        $data = [];

        if ($request->exists('about')) {
            $data['about'] = $request->get('about');
        }

        if ($request->exists('gender')) {
            $data['gender'] = $request->get('gender');
        }

        if ($request->exists('birth_date')) {
            $data['birth_date'] = $request->get('birth_date');
            $data['age']  = $years = Carbon::parse($data['birth_date'])->age;
        }

        if ($request->exists('social')) {
            $data['social'] = (string)json_encode($request->get('social'));
        }

        if ($request->exists('social')) {
            $data['social'] = (string)json_encode($request->get('social'));
        }

        if ($request->exists('language')) {
            $data['language'] = $request->get('language');
        }

        if ($request->exists('country')) {
            $data['country'] = $request->get('country');
        }

        //Update resident
        if ($request->exists('residentAddress') && $request->exists('residentLat') && $request->exists('residentLng')) {
            $data['address'] = json_encode([
                'residentAddress' => $request->residentAddress,
                'residentLat' => $request->residentLat,
                'residentLng' => $request->residentLng,
                'short_address' => $request->short_address
            ]);
        }

        $user = $this->userRepository->updateInfo(app('request')->user()->id, $data);

        if ($request->exists('specialities')) {
            if (count((array)$request->get('specialities')) == 0) {
                $user->skills()->sync([]);
            } elseif ($request->get('specialities')) {
                $specialities = $request->get('specialities');
                $user->skills()->sync($specialities);
            }
        }

        if ($request->exists('categories')) {
            if (count((array)$request->get('categories')) == 0) {
                $user->categories()->sync([]);
            } elseif ($request->get('categories')) {
                $categories = $request->get('categories');
                $user->categories()->sync($categories);
            }
        }

        return fractal($user, new UserTransform());
    }

    public function updateIdentityInfo(IndentityUpdateRequest $request)
    {
        $currentUser = app('request')->user();
        $data = $request->all(['name', 'email', 'phone', 'username']);

        if ($data['email'] !== $currentUser->email) {
            $data['email_verified_at'] = null;
        }

        if ($data['phone'] !== $currentUser->phone) {
            $data['phone_verified_at'] = null;
        }

        $user = $this->userRepository->updateInfo($currentUser->id, $data);
        
        if ($user == null) {
            return abort(400, 'username is existed');
        }
        $token = $this->createToken($user);
        $response = [
            'data' => [
                'access_token' => $token->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse(
                    $token->token->expires_at
                )->toDateTimeString(),
                'self' => fractal($user, new UserTransform())
            ]
        ];
        return $response;
    }

    public function updateAuthInfo(AuthUpdateRequest $request)
    {
        if (!Hash::check(
            $request->get('password_current'),
            $request->user()->password
        )) {
            throw new AuthException('Wrong current password');
        }
        $user = $this->userRepository->updateInfo($request->user()->id, $request->all(['password']));
        $token = $this->createToken($user);
        $response = [
            'data' => [
                'access_token' => $token->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse(
                    $token->token->expires_at
                )->toDateTimeString(),
                'self' => fractal($$user, new UserTransform())
            ]
        ];
        return $response;
    }

    public function removeMedia()
    {
        $type = app('request')->input('type');
        $user = app('request')->user();

        $media = $user->medias()->where('type', $type)->first();

        if ($media) {
            $disk = \Storage::disk('gcs');
            $disk->delete([$media->thumb, $media->path]);
            $check = $this->uploadRespository->delete($media->id);

            $user->avatar_id = null;
            $user->save();

            if ($check) {
                return [
                   'data' => [
                       'status' => true,
                       'message' => 'Deleted'
                   ]
                ];
            }
        }

        throw new FileNotExistException('File not Exist');
    }

    public function updateLocation(LocationUpdateRequest $request)
    {
        $user = app('request')->user();
        $location = $this->locationRepository->create($request->all(['address', 'lat', 'lng', 'short_address']));
        $user->location()->delete();
        $user->location()->save($location);
        return fractal($user, new UserTransform());
    }

    private function createToken($user)
    {
        $tokenResult = $user->createToken($user->email);
        $token = $tokenResult->token;
        $token->save();
        return $tokenResult;
    }
}
