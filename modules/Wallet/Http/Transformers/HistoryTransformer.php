<?php

namespace Modules\Wallet\Http\Transformers;

use Carbon\Carbon;
use League\Fractal\TransformerAbstract;
use Modules\Wallet\Domains\Entities\HistoryEntity;

class HistoryTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        $name = $item->user_name;
        switch($item->type) {

            case HistoryEntity::TYPE_FIRST_POST:
                $name = 'First reward';
                break;

            case HistoryEntity::TYPE_LEVEL_UP:
                $name = 'Level reward';
                break;

            case HistoryEntity::TYPE_SHARE_POST:
                $name = 'Sharing reward';
                break;

            case HistoryEntity::TYPE_ADVANCE:
                $name = 'Advance';
                break;

            case HistoryEntity::TYPE_REFUND_ADVANCE:
                $name = 'Refund';
                break;

        }

        return [
            'id' => $item->id,
            'user' => [
                'id'        => $item->user_id,
                'name'      => $name,
                'avatar'    => $item->user_avatar ? \Storage::disk('gcs')->url($item->user_avatar) : $item->user_avatar
            ],
            'point'         => $item->balance_type === HistoryEntity::BALANCE_ADD ? "+ ".$item->point : "- ".$item->point,
            'created_at'    => Carbon::create($item->created_at)
        ];
    }
}
