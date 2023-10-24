<?php

namespace Modules\KizunerApi\Services;

use Modules\Kizuner\Contracts\StatusRepositoryInterface;
use Modules\Kizuner\Models\Media;
use Modules\Kizuner\Models\React;
use Modules\Kizuner\Repositories\MediaRepository;
use Modules\KizunerApi\Http\Requests\Status\StatusCreateRequest;
use Modules\KizunerApi\Http\Requests\Status\StatusUpdateRequest;
use Modules\KizunerApi\Transformers\StatusTransform;
use Modules\Notification\Job\StatusLikeJob;
use Modules\Notification\Job\StatusTagJob;
use Modules\Status\Events\StatusCreatedEvent;
use Modules\Status\Events\StatusDeletedEvent;
use Modules\Upload\Models\UploadTrash;

class StatusManager
{
    /**
     * @var StatusRepositoryInterface
     */
    private $statusRepository;

    /**
     * @var MediaRepository
     */
    private $mediaRepository;

    /**
     * StatusManager constructor.
     * @param StatusRepositoryInterface $statusRepository
     * @param MediaRepository $mediaRepository
     */
    public function __construct(
        StatusRepositoryInterface $statusRepository,
        MediaRepository $mediaRepository
    ) {
        $this->statusRepository = $statusRepository;
        $this->mediaRepository  = $mediaRepository;
    }

    /**
     * @param StatusCreateRequest $request
     * @return \Spatie\Fractal\Fractal
     */
    public function addStatus(StatusCreateRequest $request)
    {
        $currentUser = app('request')->user()->id;
        $friends = $request->get('friends');

        $status = $this->statusRepository->addStatus($currentUser, $request->get('status'));

         /** Save Media */
         if ($request->get('cover')) {
            $cover = $request->get('cover');
            if (str_contains($cover, ';')) {
                $coverArr = explode(";",$cover);
                $arrayMedia = array();
                foreach ($coverArr as $coverArrItem) {
                    $media = $this->mediaRepository->update($coverArrItem, [
                        'type' => Media::$type['status']['cover']
                    ]);
                  
                    array_push($arrayMedia,$media);
                    $status->media()->save($media);
                }
               
            } else {
                $media = $this->mediaRepository->update($request->get('cover'), [
                    'type' => Media::$type['status']['cover']
                ]);
                $arrayMedia = array($media);
                $status->media()->save($media);
            }
        }

        if($friends) {
            $status->friends = json_encode($friends);
            $status->save();
        }

        event(new StatusCreatedEvent($status));

        //Send notification
        StatusTagJob::dispatch($status);

        return fractal($status, new StatusTransform());
    }

    /**
     * @param string $id
     * @param StatusUpdateRequest $request
     * @return \Spatie\Fractal\Fractal
     */
    public function updateStatus(string $id, StatusUpdateRequest $request)
    {
        $statusText = $request->get('status');
        $friends = $request->get('friends');
        $status = $this->statusRepository->updateStatus($id, $statusText);

        if ($request->has('cover')) {

            if ($request->get('cover') == null) {
                if ($status->media != null) {
                    $statusMedia = $status->media;

                    UploadTrash::create([
                        ['path' => $statusMedia->path],
                        ['path' => $statusMedia->thumb]
                    ]);

                    $status->media->delete();
                }
            } else {

                $cover = $request->get('cover');

                if ($status->media) {
                    $statusMedia = $status->media;
                    if ($statusMedia->id != $request->get('cover')) {

                        UploadTrash::create([
                            ['path' => $statusMedia->path],
                            ['path' => $statusMedia->thumb]
                        ]);

                        $statusMedia->delete();
                        /** Save Media */
                        $media = $this->mediaRepository->update($cover, [
                            'type' => Media::$type['status']['cover']
                        ]);
                        $status->media()->save($media);
                    }
                }
                $media = $this->mediaRepository->update($cover, [
                    'type' => Media::$type['status']['cover']
                ]);
                $status->media()->save($media);
            }
        }
        $status = $this->statusRepository->updateStatus($id, $statusText);

        $friends = $friends ?? [];
        $status->friends = json_encode($friends);
        $status->save();

        return fractal($status, new StatusTransform());
    }

    /**
     * @param string $id
     * @return \Spatie\Fractal\Fractal
     */
    public function getStatus(string $id)
    {
        $status = $this->statusRepository->get($id);
        return fractal($status, new StatusTransform());
    }

    /**
     * @param string $statusId
     * @return array
     */
    public function removeStatus(string $statusId)
    {
        $check = $this->statusRepository->removeStatus($statusId);

        if ($check) {
            event(new StatusDeletedEvent($statusId));
            return [
                'data' => [
                    'message' => 'Remove Status Successful',
                    'status'  => true
                ]
            ];
        }
    }
}
