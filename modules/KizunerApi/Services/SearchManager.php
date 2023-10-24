<?php

namespace Modules\KizunerApi\Services;

use Modules\KizunerApi\Search\SearchHandler;
use Modules\KizunerApi\Transformers\SearchTransform;

class SearchManager
{

    private $searchHandler;

    public function __construct(SearchHandler $searchHandler)
    {
        $this->searchHandler = $searchHandler;
    }

    public function handleSearch()
    {
        $type       =   app('request')->input('type');
        $user       =   app('request')->user();
        $perPage    =   app('request')->input('per_page');

        if (!$perPage) {
            $perPage = 5;
        }

        if ($type && $type == 'nearby') {
            $geo        = app('request')->input('geo');
            $distance   = app('request')->input('distance');
            return fractal($this->searchHandler->getNearByHangout(
                $geo,
                $distance,
                $perPage
            ), new SearchTransform());
        }

        if ($type && $type == 'recommend') {
            return fractal($this->searchHandler->getRecommendHangout($user, $perPage), new SearchTransform());
        }
    }
}
