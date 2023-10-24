<?php

namespace Modules\Kizuner\Repositories;

use Illuminate\Contracts\Queue\EntityNotFoundException;
use Illuminate\Support\Facades\Log;
use Modules\Hangout\Events\HangoutDeletedEvent;
use Modules\Kizuner\Contracts\HangoutRepositoryInterface;
use Modules\Kizuner\Models\Hangout;
use Modules\Kizuner\Models\Offer;

class HangoutRepository implements HangoutRepositoryInterface
{
    /**
     * @param string $id
     * @return Hangout
     */
    public function get(string $id)
    {
        return Hangout::where('id', $id)->first();
    }

    /**
     * @param array $hangoutData
     * @return Hangout
     */
    public function create(array $hangoutData)
    {
        Log::debug(json_encode($hangoutData));
        $hangout = new Hangout($hangoutData);
        $hangout->user_id       = $hangoutData['user_id'];
        $hangout->available     = array_key_exists('capacity', $hangoutData) ? $hangoutData['capacity'] : null;
        $hangout->save();
        return $hangout;
    }

    /**
     * @param string $id
     * @param array $hangoutData
     * @return Hangout
     */
    public function update(string $id, array $hangoutData)
    {
        if (!($hangout = $this->get($id))) {
            throw new EntityNotFoundException('Hangout does not exist', $id);
        }
        $hangout->update($hangoutData);
        return $hangout;
    }

    /**
     * @param string $id
     * @return bool
     */
    public function delete(string $id): bool
    {
       $hangout = Hangout::find($id);
       event(new HangoutDeletedEvent($id));
       return $hangout->delete();
    }

    /**
     * @param string $userId
     * @param int $perPage
     * @return mixed
     */
    public function getByUser(string $userId, $perPage)
    {
        return Hangout::where('user_id', $userId)
                    ->orderBy('updated_at', 'DESC')
                    ->paginate($perPage);
    }

    /**
     * @param string $userId
     * @param string $hangoutId
     * @return mixed
     */
    public function isHangoutOwner(string $userId, string $hangoutId)
    {
        return Hangout::where('user_id', $userId)
                            ->where('id', $hangoutId)
                            ->firstOrFail();
    }
}
