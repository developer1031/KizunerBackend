<?php

namespace Modules\Kizuner\Contracts;

use Modules\Kizuner\Models\Status;

interface StatusRepositoryInterface
{

    /**
     * @param string $id
     * @return Status
     */
    public function get(string $id);

    /**
     * @param string $userId
     * @param string $status
     * @return Status
     */
    public function addStatus(string $userId, string $status);

    /**
     * @param string $statusId
     * @param string $status
     * @return Status
     */
    public function updateStatus(string $statusId, string $status);

    /**
     * @param string $statusId
     * @return bool
     */
    public function removeStatus(string $statusId);
}
