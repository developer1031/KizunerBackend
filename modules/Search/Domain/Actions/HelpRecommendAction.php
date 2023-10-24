<?php


namespace Modules\Search\Domain\Actions;


use Modules\Search\Domain\Queries\HangoutNearByQuery;
use Modules\Search\Domain\Queries\HangoutRecommendQuery;

class HelpRecommendAction
{
    public function execute(string $userId, $perPage)
    {
        return [
            'data' => [
                'helps' => null,
                'hangouts' => (new HangoutRecommendQuery($userId, $perPage))->execute(),
            ]
        ];
        //return (new HangoutRecommendQuery($userId, $perPage))->execute();
    }
}
