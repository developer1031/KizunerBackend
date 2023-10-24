<?php

namespace Modules\KizunerApi\Transformers;

use Illuminate\Support\Facades\Log;
use League\Fractal\TransformerAbstract;
use Modules\Config\Config;
use Modules\Kizuner\Models\Country;
use Modules\Kizuner\Models\LeaderBoard;
use Modules\Kizuner\Models\UserSupport;

class UserSupportTransform extends TransformerAbstract
{

    protected $defaultIncludes = [
        'medias'
    ];
    public function transform(UserSupport $userSupport)
    {
        return [
            'name' => $userSupport->name,
            'email' => $userSupport->email,
            'subject' => $userSupport->subject,
            'message' => $userSupport->message,
            'help_offer_id' => $userSupport->help_offer_id,
            'hangout_offer_id' => $userSupport->hangout_offer_id,
        ];
    }

    public function includeMedias(UserSupport $userSupport)
    {
        $media = $userSupport->medias;
        if ($media) {
            return $this->collection($media, new MediaTransform());
        }
    }

}
