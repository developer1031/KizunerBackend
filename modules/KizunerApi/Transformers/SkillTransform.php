<?php

namespace Modules\KizunerApi\Transformers;

use League\Fractal\TransformerAbstract;
use Modules\Kizuner\Models\Skill;

class SkillTransform extends TransformerAbstract
{
    public function transform(Skill $skill)
    {
        return [
            'id' => $skill->id,
            'name' => $skill->name,
            'admin' => $skill->admin == 0 ? false : true,
            'suggest' => $skill->suggest == 0 ? false : true,
        ];
    }
}
