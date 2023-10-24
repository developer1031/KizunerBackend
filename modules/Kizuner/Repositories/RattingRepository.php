<?php

namespace Modules\Kizuner\Repositories;

use Modules\Kizuner\Contracts\RattingRepositoryInterface;
use Modules\Kizuner\Models\Ratting;

class RattingRepository implements RattingRepositoryInterface
{

    public function create(array $data)
    {
        $ratting = new Ratting($data);
        $ratting->save();
        return $ratting;
    }

    public function update(string $id, array $data)
    {
        $ratting = Ratting::find($id);
        $ratting->update($data);
        return $ratting;
    }

    public function delete(string $id)
    {
        $ratting = Ratting::find($id);
        return $ratting->delete();
    }

    public function isRatted(string $userId, string $targetUser)
    {
        return Ratting::where('user_id', $userId)
                        ->where('ratable_id', $targetUser)
                        ->first();
    }
}
