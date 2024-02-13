<?php


namespace Modules\Search\Domain\Actions;


use Modules\Search\Domain\Queries\HangoutNearByQuery;
use Modules\Search\Domain\Queries\HangoutOnlineQuery;
use Modules\Search\Domain\Queries\HelpNearByQuery;
use Modules\Search\Domain\Queries\HelpOnlineQuery;
use Modules\Search\Http\Transformers\OnlineTransform;

class HangoutOnlineAction
{
    public function execute($perPage)
    {
        $helps = (new HelpOnlineQuery($perPage))->execute();
        $hangouts = (new HangoutOnlineQuery($perPage))->execute();

        $dataMerge = $helps->merge($hangouts);

        $sortedData = $dataMerge->sortBy('distance');

        $helps = fractal(null, new OnlineTransform());
        $hangouts = fractal($sortedData, new OnlineTransform());

        return [
            'data' => [
                'helps' => $helps,
                'hangouts' => $hangouts
            ]
        ];
    }
}
