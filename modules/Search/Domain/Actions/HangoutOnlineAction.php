<?php


namespace Modules\Search\Domain\Actions;


use Modules\Search\Domain\Queries\HangoutNearByQuery;
use Modules\Search\Domain\Queries\HangoutOnlineQuery;
use Modules\Search\Domain\Queries\HelpNearByQuery;
use Modules\Search\Domain\Queries\HelpOnlineQuery;
use Modules\Search\Http\Transformers\HangoutNearByTransform;
use Modules\Search\Http\Transformers\HelpNearByTransform;
use Modules\Search\Http\Transformers\NearByTransform;

class HangoutOnlineAction
{
    public function execute($perPage)
    {
        $helps = (new HelpOnlineQuery($perPage))->execute();
        $hangouts = (new HangoutOnlineQuery($perPage))->execute();

        $dataMerge = $helps->merge($hangouts);

        $sortedData = $dataMerge->sortBy('distance');


        //$helps = fractal((new HelpNearByQuery($lat, $long, $radius, $perPage))->execute(), new HelpNearByTransform());
        //$hangouts = fractal((new HangoutNearByQuery($lat, $long, $radius, $perPage))->execute(), new HangoutNearByTransform());
        //dd($sortedData);

        $helps = fractal(null, new NearByTransform());
        $hangouts = fractal($sortedData, new NearByTransform());

        return [
            'data' => [
                'helps' => $helps,
                'hangouts' => $hangouts
            ]
        ];
        //return (new HangoutNearByQuery($lat, $long, $radius, $perPage))->execute();
    }
}
