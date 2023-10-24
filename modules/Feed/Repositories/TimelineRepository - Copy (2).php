<?php

namespace Modules\Feed\Repositories;

use Illuminate\Support\Facades\Log;
use Modules\Feed\Contracts\Data\TimelineInterface;
use Modules\Feed\Contracts\Data\TimelineInterfaceFactory;
use Modules\Feed\Contracts\Repositories\TimelineRepositoryInterface;
use Modules\Feed\Models\Timeline;
use Modules\Feed\Models\TimelineFactory;

class TimelineRepository implements TimelineRepositoryInterface
{
    /** @var TimelineFactory  */
    private $timelineFactory;

    public function __construct(TimelineInterfaceFactory $timelineInterfaceFactory)
    {
        $this->timelineFactory = $timelineInterfaceFactory;
    }

    /**
     * @inheritDoc
     */
    public function create(
        string $userId,
        string $referenceId,
        string $type,
        string $status = 'new',
        string $referenceUserId = null
    ): TimelineInterface {
        /** @var Timeline $timeline */
        $timeline = $this->timelineFactory->create();
        $timeline->setUserId($userId)
                ->setReferenceId($referenceId)
                ->setType($type)
                ->setStatus($status)
                ->setReferenceUserId($referenceUserId);
        $timeline->save();
        return $timeline;
    }

    /**
     * @inheritDoc
     */
    public function getPersonalTimeline(string $userId, int $perPage)
    {
        /** @var Timeline $timeline */
        $timelineManager = $this->timelineFactory->create();

        $sql = $timelineManager
                            ->select(
                                'feed_timelines.id as id',
                                'feed_timelines.user_id as user_id',
                                'feed_timelines.reference_id as reference_id',
                                'feed_timelines.reference_user_id as reference_user_id',
                                'feed_timelines.type as type',
                                'feed_timelines.status as status',
                                'feed_timelines.created_at as created_at',
                                'feed_timelines.updated_at as updated_at',
                                'feed_timelines.deleted_at as deleted_at'
                            )
                            ->leftJoin('hangout_hangouts as h', 'h.id', '=', 'feed_timelines.reference_id')
                            ->where('feed_timelines.user_id', $userId)
                            ->where('feed_timelines.status', '<>', 'inactive')
                            ->orderBy('feed_timelines.created_at', 'desc')
                            ->whereNull('feed_timelines.deleted_at');

        if ($userId != auth()->user()->id) {
            $sql->whereNull('h.room_id');
        }

        return $sql->paginate($perPage);
    }

    /**
     * @inheritDoc
     */
    public function getTimeline(string $userId, array $followList, int $perPage, string $type = null)
    {
        /** @var Timeline $timeline */
        $timelineManager = $this->timelineFactory->create();

        $me = auth()->user();
        $skills = $me->skills->pluck('id')->toArray();

        $private_timeline = $timelineManager->select(
            'feed_timelines.id as id',
            'feed_timelines.user_id as user_id',
            'feed_timelines.reference_id as reference_id',
            'feed_timelines.reference_user_id as reference_user_id',
            'feed_timelines.type as type',
            'feed_timelines.status as status',
            'feed_timelines.created_at as created_at',
            'feed_timelines.updated_at as updated_at',
            'feed_timelines.deleted_at as deleted_at',
            'h.available_status',
            'skillables.skillable_id',
            'skillables.skill_id'
        )
            ->leftJoin('hangout_hangouts as h', 'h.id', '=', 'feed_timelines.reference_id')
            ->leftJoin('help_helps as help', 'help.id', '=', 'feed_timelines.reference_id')
            ->leftJoin('skillables', 'feed_timelines.reference_id', '=', 'skillables.skillable_id')
            ->leftJoin('chat_members as chat', function($join) {
                $join->on('chat.room_id', '=', 'h.room_id')
                    ->orOn('chat.room_id', '=', 'help.room_id');
            })
            ->where(function($query) {
                $query->whereNotNull('h.room_id')
                    ->orWhereNotNull('help.room_id');
            })
            ->where(function($query) use ($me) {
                $query->where('chat.user_id', $me->id);
                $query->orWhere('h.friends', 'like', "%$me->id%");
                $query->orWhere('help.friends', 'like', "%$me->id%");
            })
            ->where(function($query) {
                $query->where('h.is_completed', 0);
                $query->where('help.is_completed', 0);
            });

        if ($type) {
            $private_timeline->where('feed_timelines.type', $type);
        }


        $timeline = $timelineManager->select(
            'feed_timelines.id as id',
            'feed_timelines.user_id as user_id',
            'feed_timelines.reference_id as reference_id',
            'feed_timelines.reference_user_id as reference_user_id',
            'feed_timelines.type as type',
            'feed_timelines.status as status',
            'feed_timelines.created_at as created_at',
            'feed_timelines.updated_at as updated_at',
            'feed_timelines.deleted_at as deleted_at',
            'h.available_status',
            'skillables.skillable_id',
            'skillables.skill_id'
        )
            ->leftJoin('hangout_hangouts as h', 'h.id', '=', 'feed_timelines.reference_id')
            ->leftJoin('help_helps as help', 'help.id', '=', 'feed_timelines.reference_id')
            ->leftJoin('skillables', 'feed_timelines.reference_id', '=', 'skillables.skillable_id')

            ->where('feed_timelines.status', '<>', 'inactive')
            ->whereNull('feed_timelines.deleted_at')
            ->where(function($query) {
                $query->whereNull('h.room_id')
                    ->whereNull('help.room_id');
            })
            ->where(function($query) {
                $query->where('h.is_completed', 0);
                $query->where('help.is_completed', 0);
            });

        if ($type) {
            $timeline->where('feed_timelines.type', $type);
        }
        else {
            $timeline->orWhere(function ($query) use ($followList) {
                $query->where('feed_timelines.type', 'status');
            })
                ->orWhere(function ($query) use ($followList) {
                    $query->where('feed_timelines.type', 'help')
                        ->whereIn('feed_timelines.user_id', $followList);
                })
                ->orWhere(function ($query) use ($skills) {
                    $query->where('feed_timelines.type', 'hangout')
                        ->whereNotIn('skillables.skill_id', $skills)
                        ->orWhereNull('skillables.skill_id');
                });
        }

        $timeline->union($private_timeline)
            ->groupBy('id')
            //->orderBy('feed_timelines.created_at', 'desc');
            ->orderBy('created_at', 'desc');

        return $timeline->paginate($perPage);
    }

    /**
     * @inheritDoc
     */
    public function deleteByReference(string $referenceId): bool
    {
        /** @var Timeline $timeline */
        $timelineManager = $this->timelineFactory->create();
        return $timelineManager->where('reference_id', $referenceId)->delete();
    }
}
