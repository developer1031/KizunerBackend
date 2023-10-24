<?php

namespace Modules\FeedApi\Services;

use App\User;
use Modules\Feed\Contracts\Repositories\FeedFollowerRepositoryInterface;
use Modules\Feed\Contracts\Repositories\TimelineRepositoryInterface;
use Modules\Feed\Transformer\TimelineTransformer;
use Modules\FeedApi\Contracts\TimelineManagerInterface;
use Modules\Kizuner\Models\User\Follow;
use Spatie\Fractal\Fractal;

class TimelineManager implements TimelineManagerInterface
{
    /** @var TimelineRepositoryInterface  */
    private $timelineRepository;

    /** @var FeedFollowerRepositoryInterface  */
    private $feedFollowRepository;

    /**
     * TimelineManager constructor.
     * @param TimelineRepositoryInterface $timelineRepository
     * @param FeedFollowerRepositoryInterface $feedFollowerRepository
     */
    public function __construct(
        TimelineRepositoryInterface $timelineRepository,
        FeedFollowerRepositoryInterface $feedFollowerRepository
    ) {
        $this->feedFollowRepository = $feedFollowerRepository;
        $this->timelineRepository = $timelineRepository;
    }


    /**
     * @inheritDoc
     */
    public function getPersonalTimeline(string $id = null): Fractal
    {
        $perPage = app('request')->input('per_page');

        if (!$perPage) {
            $perPage = 5;
        }

        if (!$id) {
            $id = auth()->user()->id;
        }
        return fractal($this->timelineRepository->getPersonalTimeline($id, $perPage), new TimelineTransformer());
    }

    /**
     * @inheritDoc
     */
    public function getTimeline(): Fractal
    {
        $perPage = app('request')->input('per_page');
        $type    = app('request')->input('type');

        if (!$perPage) {
            $perPage = 5;
        }

        $currentUser = auth()->user();
        //$followList = $this->feedFollowRepository->getFollowingList($currentUser->id)->pluck('channel_id');
        //$followList = $followList->toArray();
        //array_push($followList, $currentUser->id);
        /*
        $followList = [];
        $follow_users = Follow::where('user_id', $currentUser->id)->pluck('follow_id');
        foreach ($follow_users as $follow_user ) {
            array_push($followList, $follow_user);
        }
        array_push($followList, $currentUser->id);
        */

        $followList = getFriendsFollows($currentUser->id);

        //return fractal($this->timelineRepository->getTimeline($currentUser->id, $followList->toArray(), $perPage, $type), new TimelineTransformer());
        return fractal($this->timelineRepository->getTimeline($currentUser->id, $followList, $perPage, $type), new TimelineTransformer());
    }
}
