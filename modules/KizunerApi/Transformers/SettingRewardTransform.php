<?php

namespace Modules\KizunerApi\Transformers;

use League\Fractal\TransformerAbstract;
use Modules\Config\ConfigEntity;

class SettingRewardTransform extends TransformerAbstract
{
    public function transform(ConfigEntity $config)
    {
        $disk = \Storage::disk('gcs');
        $value = json_decode($config->value, true);
        return [
            $config->path => [
                'name'          => (isset($value['name'])) ? $value['name'] : '',
                'description'   => (isset($value['description'])) ? $value['description'] : '',
                'name_reward'   => (isset($value['name_reward'])) ? $value['name_reward'] : '',
                'point'         => (isset($value['point'])) ? $value['point'] : '',
                'reward'        => (isset($value['reward'])) ? $value['reward'] : '',
                'icon'          => (isset($value['icon'])) ? $disk->url($value['icon']) : '',
                'inactive_icon' => (isset($value['inactive_icon'])) ? $disk->url($value['inactive_icon']) : '',
            ]
        ];
    }
}
