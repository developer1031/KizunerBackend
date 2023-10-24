<?php

namespace Modules\CommentApi\Http\Controllers\Comments;

use Illuminate\Http\Response;
use Modules\Comment\Exceptions\PermisionDenied;
use Modules\CommentApi\Contracts\CommentManagerInterface;
use Modules\CommentApi\Http\Requests\Comments\CreateCommentRequest;

class DeleteController
{
    /** @var CommentManagerInterface  */
    private $commentManager;

    /**
     * CreateController constructor.
     * @param CommentManagerInterface $commentManager
     */
    public function __construct(CommentManagerInterface $commentManager)
    {
        $this->commentManager = $commentManager;
    }

    /**
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function action(string $id)
    {
        try {
            return response()->json(
                $this->commentManager->deleteComment($id),
                Response::HTTP_CREATED
            );
        } catch (PermisionDenied $exception) {
            return response()
                ->json([
                    'errors' => [
                        'message' => $exception->getMessage(),
                    ]
                ], Response::HTTP_UNAUTHORIZED);
        }
    }
}
