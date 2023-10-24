<?php

namespace Modules\Comment\Repositories;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Comment\Contracts\Data\CommentInterface;
use Modules\Comment\Contracts\Data\CommentInterfaceFactory;
use Modules\Comment\Contracts\Repositories\CommentRepositoryInterface;
use Modules\Comment\Exceptions\CommentNotFound;
use Modules\Comment\Models\Comment;
use Modules\Comment\Models\CommentFactory;

class CommentRepository implements CommentRepositoryInterface
{

    /** @var CommentFactory $commentFactory  */
    private $commentFactory;

    /**
     * CommentRepository constructor.
     * @param CommentInterfaceFactory $commentFactory
     */
    public function __construct(CommentInterfaceFactory $commentFactory)
    {
        $this->commentFactory = $commentFactory;
    }

    /**
     * @inheritDoc
     */
    public function create(string $userId, string $commentedUserId, string $body): CommentInterface
    {
        $comment = $this->commentFactory->create([
            'user_id' => $userId,
            'body'    => $body,
            'commented_user_id' => $commentedUserId
        ]);
        $comment->save();
        return $comment;
    }

    /**
     * @inheritDoc
     */
    public function update(string $id, string $body): CommentInterface
    {
        /** @var Comment $comment */
        $comment = $this->findById($id);
        $comment->setBody($body)->save();
        return $comment;
    }


    /**
     * @inheritDoc
     */
    public function delete(string $id)
    {
        /** @var Comment $comment */
        $comment = $this->findById($id);
        return $comment->delete();
    }

    /**
     * @inheritDoc
     */
    public function findByIdAndUserId(string $id, string $userId): CommentInterface
    {
        $cmMan = $this->commentFactory->create();

        $comment = $cmMan->where('id', $id)
                        ->where('user_id', $userId)
                        ->first();

        if (!$comment) {
            throw new CommentNotFound('Comment does not exist');
        }
        return $comment;
    }

    /**
     * @inheritDoc
     */
    public function getCommentsList(string $type, string $referenceId, int $perPage)
    {
        $cmMan = $this->commentFactory->create();

        $result = $cmMan->where('commentable_type', $type)
                            ->where('commentable_id', $referenceId)
                            ->orderBy('created_at', 'desc')
                            ->paginate($perPage);
        $result->setCollection(collect($result->items())->sortBy('created_at'));
        return $result;
    }

    /**
     * @param string $id
     * @return CommentInterface
     */
    private function findById(string $id)
    {
        $cmMan = $this->commentFactory->create();
        $cm    = $cmMan->find($id);

        if (!$cm) {
            throw new ModelNotFoundException(
                "Comment with $id does not exists!"
            );
        }
        return $cm;
    }
}
