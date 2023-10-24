<?php

namespace Modules\KizunerApi\Http\Controllers;

use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Config\Config;
use Modules\Config\ConfigEntity;
use Modules\Kizuner\Models\LeaderBoard;
use Modules\KizunerApi\Http\Requests\Status\StatusCreateRequest;
use Modules\KizunerApi\Http\Requests\Status\StatusUpdateRequest;
use Modules\KizunerApi\Services\StatusManager;
use Modules\KizunerApi\Transformers\LeaderBoardTransform;
use Modules\KizunerApi\Transformers\SettingRewardTransform;
use Symfony\Component\HttpFoundation\Response;

class SettingsController
{
    public function getRewards()
    {
        $badge_keys = ['badge_01', 'badge_02', 'badge_03', 'badge_04', 'badge_05'];
        $badges = ConfigEntity::whereIn('path', $badge_keys)->get();
        return new JsonResponse( fractal($badges, new SettingRewardTransform())->toArray(), Response::HTTP_CREATED);
    }
}
