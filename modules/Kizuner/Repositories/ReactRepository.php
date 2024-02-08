<?php

namespace Modules\Kizuner\Repositories;

use Modules\Helps\Models\Help;
use Modules\Kizuner\Contracts\ReactRepositoryInterface;
use Modules\Kizuner\Models\Hangout;
use Modules\Kizuner\Models\React;
use Modules\Kizuner\Models\Status;
use Modules\Notification\Job\HangoutLikeJob;
use Modules\Notification\Job\StatusLikeJob;

class ReactRepository implements ReactRepositoryInterface
{
    public function hangoutReact(string $userId, string $hangoutId, $react_type=null)
    {
        $reacted = React::where([
            'user_id'           => $userId,
            'reactable_id'      => $hangoutId,
            'reactable_type'    => Hangout::class,
            'react_type'        => $react_type ? $react_type : 'like'
        ])->first();

        if ($reacted) {
            $reacted->delete();
            return false;
        }

        $hangout = Hangout::find($hangoutId);

        if ($hangout) {
            $react_type = $react_type ? $react_type : 'like';
            $react = new React(['user_id' => $userId, 'reacted_user_id' => $hangout->user_id, 'react_type' => $react_type]);
            $react->save();
            $hangout->reacts()->save($react);

            // Send notification
            // if ($react->user_id != $react->reacted_user_id) {
            //     HangoutLikeJob::dispatch($react);
            // }
            return true;
        }
        return false;
    }

    public function statusReact(string $userId, string $statusId, $react_type=null)
    {
        $reacted = React::where([
            'user_id'           => $userId,
            'reactable_id'      => $statusId,
            'reactable_type'    => Status::class
        ])->first();

        if ($reacted) {
            $reacted->delete();
            return false;
        }

        $status = Status::find($statusId);

        if ($status) {
            $react_type = $react_type ? $react_type : 'like';
            $react = new React(['user_id' => $userId, 'reacted_user_id' => $status->user_id, 'react_type' => $react_type]);
            $react->save();

            $status->reacts()->save($react);
            // Send noti
            if ($react->user_id != $react->reacted_user_id) {
                StatusLikeJob::dispatch($react);
            }
            return true;
        }

        return false;
    }

    public function helpReact(string $userId, string $helpId, $react_type=null)
    {
        $reacted = React::where([
            'user_id'           => $userId,
            'reactable_id'      => $helpId,
            'reactable_type'    => Help::class
        ])->first();

        if ($reacted) {
            /*
            React::where([
                'user_id'           => $userId,
                'reactable_id'      => $helpId,
                'reactable_type'    => Help::class
            ])->each(function ($help, $key) {
                $help->delete();
            });
            */
            $reacted->delete();
            return false;
        }

        $help = Help::find($helpId);

        if ($help) {
            $react_type = $react_type ? $react_type : 'like';
            $react = new React(['user_id' => $userId, 'reacted_user_id' => $help->user_id, 'react_type' => $react_type]);
            $react->save();
            $help->reacts()->save($react);

            //Send notification
            if ($react->user_id != $react->reacted_user_id) {
                //HangoutLikeJob::dispatch($react);
            }
            return true;
        }
        return false;
    }
}
