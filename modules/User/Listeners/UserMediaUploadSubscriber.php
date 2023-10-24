<?php

namespace Modules\User\Listeners;

use Carbon\Carbon;
use Modules\Notification\Job\UpdateImageJob;
use Modules\Upload\Event\UploadEvent;
use Modules\Upload\Models\UploadTrash;

class UserMediaUploadSubscriber
{
    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen(
            UploadEvent::class,
            'Modules\User\Listeners\UserMediaUploadSubscriber@userUploadHandler'
        );
    }

    public function userUploadHandler($event)
    {
        $upload = $event->getObject();


        // if (
        //     strpos($upload->type, 'user') !== false
        //     && !strpos($upload->type, 'hangout')
        //     && !strpos($upload->type, 'help')
        //     && !strpos($upload->type, 'status')
        //     && !strpos($upload->type, 'cancel.evidence')
        //     && !strpos($upload->type, 'reject.evidence')
        //     && !strpos($upload->type, 'user.support')
        // ) {
        //     $user = app('request')->user();

        //     $usrOldMedia =  $user->medias()->where('type', $upload->type)->first();
        //     if ($usrOldMedia) {

        //         //Move to delete table
        //         UploadTrash::insert([
        //             ['path' =>  $usrOldMedia->thumb, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        //             ['path' =>  $usrOldMedia->path, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
        //         ]);
        //         //End move
        //         $usrOldMedia->delete();
        //     }
        //     $user->medias()->save($upload);

        //     if ($upload->type == 'user.avatar') {
        //         $user->avatar_id = $upload->id;
        //         UpdateImageJob::dispatch($user->id, $upload->id);
        //         $user->save();
        //     }
        // }
    }
}
