<?php

namespace Modules\Feed\Contracts\Repositories;

use Modules\Feed\Contracts\Data\TimelineInterface;

interface TimelineRepositoryInterface
{

    /**
     * @param string $userId
     * @param string $referenceId
     * @param string $type
     * @param string $status
     * @param null $referenceUserId
     * @return TimelineInterface
     */
    public function create(string $userId, string $referenceId, string $type, string $status = 'new', string $referenceUserId = null): TimelineInterface;

    /**
     * @param string $userId
     * @param int $perPage
     * @return  mixed
     */
    public function getPersonalTimeline(string $userId, int $perPage);

    /**
     * @param string $userId
     * @param array $followList
     * @param int $perPage
     * @param string|null $type
     * @return mixed
     */
    public function getTimeline(string $userId, array $followList, int $perPage, string $type = null);

    /**
     * @param string $referenceId
     * @return bool
     */
    public function deleteByReference(string $referenceId): bool;
}
