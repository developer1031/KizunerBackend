<?php

namespace Modules\Search\Domain\Actions;

use Illuminate\Support\Facades\Log;
use Modules\Guide\Http\Transformers\GuideTransformer;
use Modules\Search\Domain\Queries\HangoutQuery;
use Modules\Search\Domain\Queries\HelpQuery;
use Modules\Search\Domain\Queries\StatusQuery;
use Modules\Search\Domain\Queries\UserQuery;
use Modules\Search\Domain\Queries\VideoQuery;
use Modules\Search\Http\Transformers\HangoutTransform;
use Modules\Search\Http\Transformers\HelpTransform;
use Modules\Search\Http\Transformers\StatusTransform;
use Modules\Search\Http\Transformers\UserTransform;

class FullTextSearchAction
{
    public function execute($type, $query, string $perPage, $category=null, $offerType=null, $paymentMethod=null, $location=null, $amount=null, $minAmount=null, $maxAmount=null)
    {
        if ($type) {
            if ($type === 'user') {
                return fractal((new UserQuery( $query, $perPage, $category))->execute(), new UserTransform());
            }
            if ($type === 'hangout') {
                return fractal((new HangoutQuery( $query, $perPage, $category))->execute(), new HangoutTransform());
            }
            if ($type === 'status') {
                return fractal((new StatusQuery( $query, $perPage, $category))->execute(), new StatusTransform());
            }
            if ($type === 'help') {
                return fractal((new HelpQuery( $query, $perPage, $category))->execute(), new HelpTransform());
            }
            if ($type === 'video') {
                return fractal((new VideoQuery( $query, $perPage, $category))->execute(), new GuideTransformer());
            }
        }


        //Log::info('available_status');
        //Log::info(app('request')->has('available_status'));

        return [
            'data' => [
                'users'     => fractal((new UserQuery( $query, $perPage, $category, $offerType, $paymentMethod, $location, $amount, $minAmount, $maxAmount))->execute(), new UserTransform()),
                'hangouts'  => fractal((new HangoutQuery( $query, $perPage, $category, $offerType, $paymentMethod, $location, $amount, $minAmount, $maxAmount))->execute(), new HangoutTransform()),
                'statuses'  => fractal((new StatusQuery( $query, $perPage, $category, $offerType, $paymentMethod, $location, $amount, $minAmount, $maxAmount))->execute(), new StatusTransform()),
                'helps'     => fractal((new HelpQuery( $query, $perPage, $category, $offerType, $paymentMethod, $location, $amount, $minAmount, $maxAmount))->execute(), new HelpTransform()),
                'videos'    => fractal((new VideoQuery( $query, $perPage, $category, $offerType, $paymentMethod, $location, $amount, $minAmount, $maxAmount))->execute(), new GuideTransformer()),
            ]
        ];
    }
}
