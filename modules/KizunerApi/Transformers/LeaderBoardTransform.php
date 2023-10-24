<?php

namespace Modules\KizunerApi\Transformers;

use League\Fractal\TransformerAbstract;
use Modules\Config\Config;
use Modules\Kizuner\Models\Country;
use Modules\Kizuner\Models\LeaderBoard;

class LeaderBoardTransform extends TransformerAbstract
{
    protected $defaultIncludes = [
        'user',
    ];

    public function transform(LeaderBoard $leaderBoard)
    {
        $disk = \Storage::disk('gcs');
        $config_data = new Config();
        $trophy_icons_value = json_decode($config_data->getConfig('trophy_icons'), true);
        $trophy_icons = [];
        foreach ($trophy_icons_value as $trophy_icon) {
            array_push($trophy_icons, $disk->url($trophy_icon));
        }
        return [
            'point' => $leaderBoard->point,
            'badge' => $leaderBoard->badge,
            'trophy_icons' => $trophy_icons
        ];
    }

    public function includeUser(LeaderBoard $leaderBoard)
    {
        $user = $leaderBoard->user;
        return $this->item($user, new UserTransform());
    }
}
