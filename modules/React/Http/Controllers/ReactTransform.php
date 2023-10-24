<?php

namespace Modules\React\Http\Controllers;

use League\Fractal\TransformerAbstract;
use Modules\Kizuner\Models\User\Friend;

class ReactTransform extends TransformerAbstract
{
    public function transform($item)
    {
        $currentUser = auth()->user()->id;

        if ($item->id === $currentUser) {
            $friendYes = true;
        } else {
            $isFriend = Friend::where(function ($query) use ($currentUser, $item) {
                            $query->where('user_id', $currentUser);
                            $query->where('friend_id', $item->id);
                        })->orWhere(function ($query) use ($currentUser, $item) {
                            $query->where('friend_id', $currentUser);
                            $query->where('user_id', $item->id);
                        })->first();
            $friendYes = $isFriend ? true : false;
        }

        return [
            'id'        => $item->id,
            'name'      => $item->name,
            'avatar'    => $item->avatar ? \Storage::disk('gcs')->url( $item->avatar ) : null,
            'is_friend' => $friendYes
        ];
    }
}
