<?php

namespace Modules\KizunerApi\Transformers;

use League\Fractal\TransformerAbstract;
use Modules\Config\Config;
use Modules\Kizuner\Models\Country;
use Modules\Kizuner\Models\LeaderBoard;

class LeaderBoardByObjectTransform extends TransformerAbstract
{
    private $by = '';
    public function __construct($by) {
        $this->by = $by;
    }

    protected $defaultIncludes = [
        'user',
    ];

    public function transform($object)
    {
        $disk = \Storage::disk('gcs');
        $config_data = new Config();
        $trophy_icons_value = json_decode($config_data->getConfig('trophy_icons'), true);
        $trophy_icons = [];
        foreach ($trophy_icons_value as $trophy_icon) {
            array_push($trophy_icons, $disk->url($trophy_icon));
        }

        return [
            'quantity' => $object->quantity,
            'trophy_icons' => $trophy_icons
        ];
    }

    public function includeUser($object)
    {
        if($this->by=='cast' || $this->by=='requester') {
            $user = $object->user;
        }
        else if($this->by=='guest' || $this->by=='helper') {
            $user = $object->sender;
        }

        return $this->item($user, new UserTransform());
    }
}
