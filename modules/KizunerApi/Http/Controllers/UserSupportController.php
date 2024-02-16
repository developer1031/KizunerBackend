<?php

namespace Modules\KizunerApi\Http\Controllers;

use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Config\Config;
use Modules\Helps\Models\Help;
use Modules\Helps\Models\HelpOffer;
use Modules\Kizuner\Http\Requests\Skill\UserSupportRequest;
use Modules\Kizuner\Models\Hangout;
use Modules\Kizuner\Models\LeaderBoard;
use Modules\Kizuner\Models\Offer;
use Modules\KizunerApi\Http\Requests\Status\StatusCreateRequest;
use Modules\KizunerApi\Http\Requests\Status\StatusUpdateRequest;
use Modules\KizunerApi\Services\StatusManager;
use Modules\KizunerApi\Services\UserSupportManager;
use Modules\KizunerApi\Transformers\LeaderBoardByObjectTransform;
use Modules\KizunerApi\Transformers\LeaderBoardTransform;
use Symfony\Component\HttpFoundation\Response;

class UserSupportController
{
    public function addUserSupport(UserSupportManager $userSupportManager, UserSupportRequest $request)
    {
        if ($request->validated()) {
            $response = $userSupportManager->addUserSupport($request);
            return new JsonResponse($response, Response::HTTP_CREATED);
        }
    }
}
