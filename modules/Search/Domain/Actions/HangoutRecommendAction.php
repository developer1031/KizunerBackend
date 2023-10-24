<?php


namespace Modules\Search\Domain\Actions;


use Modules\Search\Domain\Queries\HangoutNearByQuery;
use Modules\Search\Domain\Queries\HangoutRecommendQuery;
use Modules\Search\Domain\Queries\HelpRecommendQuery;
use Modules\Search\Http\Transformers\HangoutRecommendTransform;
use Modules\Search\Http\Transformers\HelpRecommendTransform;

class HangoutRecommendAction
{
    public function execute(string $userId, $perPage)
    {
        return [
            'data' => [
                'helps' => fractal((new HelpRecommendQuery($userId, $perPage))->execute(), new HelpRecommendTransform()),
                'hangouts' => fractal((new HangoutRecommendQuery($userId, $perPage))->execute(), new HangoutRecommendTransform())
            ]
        ];
        //return (new HangoutRecommendQuery($userId, $perPage))->execute();
    }
}
