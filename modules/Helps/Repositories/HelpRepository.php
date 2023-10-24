<?php

namespace Modules\Helps\Repositories;

use Illuminate\Contracts\Queue\EntityNotFoundException;
use Modules\Helps\Contracts\HelpRepositoryInterface;
use Modules\Helps\Models\Help;
use Modules\Kizuner\Models\Hangout;

class HelpRepository implements HelpRepositoryInterface {

    /**
     * @inheritDoc
     */
    public function get(string $id)
    {
        $help = Help::find($id);
        return $help;
    }

    public function create(array $helpData)
    {
        $help = new Help($helpData);
        $help->user_id = $helpData['user_id'];
        $help->available = array_key_exists('capacity', $helpData) ? $helpData['capacity'] : null;
        $help->save();
        return $help;
    }

    public function update(string $id, array $helpData)
    {
        if (!($help = $this->get($id))) {
            throw new EntityNotFoundException('Help does not exist', $id);
        }
        $help->update($helpData);
        return $help;
    }

    public function delete(string $id): bool
    {
        // TODO: Implement delete() method.
    }

    public function isHelpOwner(string $userId, string $helpId)
    {
        return Help::where('user_id', $userId)
            ->where('id', $helpId)
            ->firstOrFail();
    }

    public function getByUser(string $userId, int $perPage)
    {
        return Help::where('user_id', $userId)
            ->orderBy('updated_at', 'DESC')
            ->paginate($perPage);
    }
}
