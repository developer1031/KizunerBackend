<?php

namespace Modules\CommentApi\Services;

use Illuminate\Support\Facades\Auth;
use Modules\Comment\Contracts\Repositories\CommentRepositoryInterface;
use Modules\Comment\Events\NewCommentEvent;
use Modules\Comment\Exceptions\CommentTypeNotExist;
use Modules\Comment\Exceptions\PermisionDenied;
use Modules\Comment\Services\Facades\CommentAuth;
use Modules\CommentApi\Contracts\CommentManagerInterface;
use Modules\CommentApi\Http\Requests\Comments\CreateCommentRequest;
use Modules\CommentApi\Http\Requests\Comments\UpdateCommentRequest;
use Modules\CommentApi\Transformers\CommentTransform;
use Modules\Helps\Contracts\HelpRepositoryInterface;
use Modules\Helps\Models\Help;
use Modules\Kizuner\Contracts\HangoutRepositoryInterface;
use Modules\Kizuner\Contracts\StatusRepositoryInterface;
use Modules\Kizuner\Models\Hangout;
use Modules\Kizuner\Models\Status;
use Modules\Notification\Job\HangoutCommentJob;
use Modules\Notification\Job\HelpCommentJob;
use Modules\Notification\Job\StatusCommentJob;

class CommentManager implements CommentManagerInterface
{
    /** @var CommentRepositoryInterface  */
    private $commentRepository;

    /** @var HangoutRepositoryInterface  */
    private $hangoutRepository;

    /** @var StatusRepositoryInterface  */
    private $statusRepository;

    /** @var HelpRepositoryInterface  */
    private $helpRepository;

    /**
     * CommentManager constructor.
     * @param CommentRepositoryInterface $commentRepository
     * @param HangoutRepositoryInterface $hangoutRepository
     * @param StatusRepositoryInterface $statusRepository
     * @param HelpRepositoryInterface $helpRepository
     */
    public function __construct(
        CommentRepositoryInterface $commentRepository,
        HangoutRepositoryInterface $hangoutRepository,
        StatusRepositoryInterface  $statusRepository,
        HelpRepositoryInterface  $helpRepository
    ) {
        $this->commentRepository = $commentRepository;
        $this->hangoutRepository = $hangoutRepository;
        $this->statusRepository = $statusRepository;
        $this->helpRepository = $helpRepository;
    }

    /**
     * @inheritDoc
     */
    public function addComment(CreateCommentRequest $request)
    {
        $type = $request->input('type');
        $referenceId = $request->get('reference_id');
        $object = $this->getInstance($referenceId, $type);

        if ($object) {
            $user    = $request->user();
            $comment = $this->commentRepository
                ->create($user->id, $object->user_id, $request->get('body'));
            $object->comments()->save($comment);

            event(new NewCommentEvent($comment->getId(), $type));

            //Send notification
            if ($comment->user_id != $comment->commented_user_id) {
                if ($type === 'hangout') {
                    HangoutCommentJob::dispatch($comment);
                } else if ($type === 'status') {
                    StatusCommentJob::dispatch($comment);
                } else if ($type == 'help') {
                    HelpCommentJob::dispatch($comment);
                }
            }

            return fractal($comment, new CommentTransform());
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getCommentList(string $referenceId)
    {
        $type = app('request')->input('type');

        $perPage = app('request')->input('per_page');

        if (!$typeObject = $this->getType($type)) {
            throw new CommentTypeNotExist('Comment type MUST be "hangout" or "status"');
        }

        if (!$perPage) {
            $perPage = 5;
        }

        $comments = $this->commentRepository
            ->getCommentsList($typeObject, $referenceId, $perPage);
        return fractal($comments, new CommentTransform());
    }

    /**
     * @inheritDoc
     */
    public function updateComment(string $id, UpdateCommentRequest $request)
    {
        $comment = $this->commentRepository->update($id, $request->get('body'));
        return fractal($comment, new CommentTransform());
    }

    /**
     * @inheritDoc
     */
    public function deleteComment(string $id)
    {

        if (!CommentAuth::check($id, Auth::user()->id)) {
            throw new PermisionDenied("You dont have permission to delete this comment!");
        }

        return [
            'data' => [
                'status' => $this->commentRepository->delete($id)
            ]
        ];
    }

    /**
     * @param string $objectId
     * @param string $type
     * @return \Modules\Kizuner\Models\Hangout|\Modules\Kizuner\Models\Status|null
     */
    private function getInstance(string $objectId, string $type)
    {
        $object = null;
        switch ($type) {
            case 'status':
                $object = $this->statusRepository
                    ->get($objectId);
                break;

            case 'hangout':
                $object = $this->hangoutRepository
                    ->get($objectId);
                break;

            case 'help':
                $object = $this->helpRepository
                    ->get($objectId);
                break;
        }
        return $object;
    }

    /**
     * @param string $type
     * @return string|null
     */
    private function getType(string $type)
    {
        $typeObject = null;

        switch ($type) {
            case 'hangout':
                $typeObject = Hangout::class;
                break;
            case 'status':
                $typeObject = Status::class;
                break;
            case 'help':
                $typeObject = Help::class;
                break;
        }
        return $typeObject;
    }
}
