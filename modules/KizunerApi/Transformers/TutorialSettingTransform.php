<?php

namespace Modules\KizunerApi\Transformers;

use League\Fractal\TransformerAbstract;
use Modules\Config\Config;
use Modules\Kizuner\Models\Country;
use Modules\Kizuner\Models\LeaderBoard;

class TutorialSettingTransform extends TransformerAbstract
{
    public function transform($tutorialSetting)
    {
        return $tutorialSetting;
    }
}
