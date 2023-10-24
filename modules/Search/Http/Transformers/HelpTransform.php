<?php

namespace Modules\Search\Http\Transformers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use League\Fractal\TransformerAbstract;
use Modules\Helps\Models\Help;
use Modules\Kizuner\Models\Hangout;
use Modules\Kizuner\Models\React;
use Modules\Kizuner\Models\Status;
use Modules\KizunerApi\Transformers\LocationTransform;
use Modules\KizunerApi\Transformers\MediaTransform;
use Modules\KizunerApi\Transformers\SkillTransform;
use Modules\KizunerApi\Transformers\UserTransform;

class HelpTransform extends TransformerAbstract {

    public function transform($item)
    {
        if( in_array($item->available_status, ['no_time', 'combine']) ) {
            $help_start = null;
            $help_end = null;
        }
        else {
            $help_start = Carbon::create($item->help_start);
            $help_end = Carbon::create($item->help_end);
        }

        $thumb = null;
        $path = null;
        if($item->is_fake) {
            $thumb = $item->cover_img ? \Storage::disk('gcs')->url($item->cover_img) : null;
            $path = $thumb;
        }
        else {
            $thumb = $item->help_cover_thumb ? \Storage::disk('gcs')->url($item->help_cover_thumb) : null;
            $path = $item->help_cover_path  ? \Storage::disk('gcs')->url($item->help_cover_path) : null;
        }

        return [
            'id'            => $item->help_id,
            'title'         => $item->help_title,
            'description'   => $item->help_description,
            'start'         => $help_start,
            'end'           => $help_end,
            'budget'        => $item->help_budget,
            'created_at'    => Carbon::create($item->help_created_at),
            'updated_at'    => Carbon::create($item->help_updated_at),
            'is_range_price'     => $item->help_is_range_price,
            'min_amount'     => $item->help_min_amount,
            'max_amount'     => $item->help_max_amount,
            'amount'     => $item->help_amount,
            'cover'         => [
                //'thumb'     => $item->help_cover_thumb ? \Storage::disk('gcs')->url($item->help_cover_thumb) : null,
                //'path'     => $item->help_cover_path  ? \Storage::disk('gcs')->url($item->help_cover_path) : null
                'thumb'     => $thumb,
                'path'     => $path
            ],
            'user'          => [
                'id'        => $item->user_id,
                'name'      => $item->user_name,
                'avatar'    => $item->user_avatar ? \Storage::disk('gcs')->url($item->user_avatar) : null,
                'social_avatar' => $item->social_avatar,
            ],
            'payment_method'     => $item->payment_method,
            'user_id' => $item->user_id,
        ];
    }
}
