<?php

namespace Modules\KizunerApi\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Modules\Kizuner\Contracts\SkillRepositoryInterface;
use Modules\Kizuner\Contracts\UserSupportRepositoryInterface;
use Modules\Kizuner\Http\Requests\Skill\UserSupportRequest;
use Modules\Kizuner\Models\Skill;
use Modules\Kizuner\Models\UserSupport;
use Modules\KizunerApi\Http\Requests\Skill\SkillCreateRequest;
use Modules\KizunerApi\Transformers\HangoutTransform;
use Modules\KizunerApi\Transformers\SkillTransform;
use Modules\KizunerApi\Transformers\UserSupportTransform;
use Modules\KizunerApi\Transformers\UserTransform;
use Modules\User\Notifications\EmailSupport;
use Symfony\Component\HttpFoundation\Response;
use Modules\Kizuner\Contracts\MediaRepositoryInterface;
use Modules\Kizuner\Models\Media;

class UserSupportManager
{
    /** @var UserSupportRepositoryInterface */
    private $userSupportRepository;

    /**
     * SkillManager constructor.
     * @param UserSupportRepositoryInterface $userSupportRepository
     */

    /** @var MediaRepositoryInterface */
    private $mediaRepository;
    public function __construct(UserSupportRepositoryInterface $userSupportRepository, MediaRepositoryInterface $mediaRepository)
    {
        $this->userSupportRepository = $userSupportRepository;
        $this->mediaRepository = $mediaRepository;
    }

    /**
     * @param UserSupportRequest $request
     * @return \Spatie\Fractal\Fractal
     * @throws \Exception
     */
    public function addUserSupport(UserSupportRequest $request)
    {
        $userSupport = null;
        $user = auth()->user();
        
        $userSupport = $this->userSupportRepository->create([
            'name' => $request['name'],
            'subject' => $request['subject'],
            'message' => $request['message'],
            'email' => $request['email'],
            'user_id' => $user->id
        ]);
        if (isset($request['help_offer_id']) && $request['help_offer_id'] != null) {
            $userSupport->help_offer_id = $request['help_offer_id'];
            $userSupport->save();
        }
        if (isset($request['hangout_offer_id']) && $request['hangout_offer_id'] != null) {
            $userSupport->hangout_offer_id = $request['hangout_offer_id'];
            $userSupport->save();
        }

        if (isset($request['media']) && $request['media'] != null) {
            $userSupport->media = $request['media'];
            $userSupport->save();

            if (isset($request['media_id']) && $request['media_id'] != null) { 
                    
                if ($request->get('media_id')) {
                    
                    $cover = $request->get('media_id');
                    if (str_contains($cover, ';')) {
                        $coverArr = explode(";", $cover);
                        foreach ($coverArr as $coverArrItem) {
                         
                            $media = $this->mediaRepository->update($coverArrItem, [
                                'type' => Media::$type['userSupport']['cover']
                            ]);
    
                            $userSupport->medias()->save($media);
                        }
                    } else {
                        $media = $this->mediaRepository->update($cover, [
                            'type' => Media::$type['userSupport']['cover']
                        ]);

                        $userSupport->medias()->save($media);
                    }
                }
            }

            Notification::route('mail', 'support@kizuner.com')
                ->notify(new EmailSupport($request['name'] ?? '', $request['subject'] ?? '', $request['message'] ?? '', $request['email'] ?? '', $request['media'] ?? ''));
        } else {
           

            Notification::route('mail', 'support@kizuner.com')
                ->notify(new EmailSupport($request['name'] ?? '', $request['subject'] ?? '', $request['message'] ?? '', $request['email'] ?? '', ""));
        }
        return fractal($userSupport, new UserSupportTransform());
    }

}