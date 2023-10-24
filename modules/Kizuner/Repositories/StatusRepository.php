<?php

namespace Modules\Kizuner\Repositories;

use Modules\Kizuner\Contracts\StatusRepositoryInterface;
use Modules\Kizuner\Models\Status;
use Modules\Status\Events\StatusDeletedEvent;

class StatusRepository implements StatusRepositoryInterface
{

    /**
     * @inheritDoc
     */
    public function get(string $id)
    {
        $status = Status::find($id);
        return $status;
    }

    /**
     * @inheritDoc
     */
    public function addStatus(string $userId, string $status)
    {
        $status = new Status([
            'user_id' => $userId,
            'status'  => $status
        ]);
        $status->save();
        return $status;
    }

    /**
     * @inheritDoc
     */
    public function updateStatus(string $statusId, string $statusText)
    {
        $status = Status::find($statusId);

        $status->update([
            'status' => $statusText
        ]);
        return $status;
    }

    /**
     * @inheritDoc
     */
    public function removeStatus(string $statusId)
    {
        $status = Status::find($statusId);
        event(new StatusDeletedEvent($statusId));
        return $status->delete();
    }
}
