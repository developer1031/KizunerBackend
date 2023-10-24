<?php

namespace Modules\CommentApi\Http\Controllers\Comments;

use Illuminate\Http\Response;
use Modules\Comment\Exceptions\CommentTypeNotExist;
use Modules\CommentApi\Contracts\CommentManagerInterface;
use Modules\CommentApi\Http\Requests\Comments\UpdateCommentRequest;

class UpdateController
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


    public function action(UpdateCommentRequest $request, string $id)
    {
        if ($request->validated()) {
            try {
                return response()->json(
                    $this->commentManager->updateComment($id, $request),
                    Response::HTTP_CREATED
                );
            } catch (CommentTypeNotExist $exception) {
                return response()
                    ->json([
                        'errors' => [
                            'message' => $exception->getMessage(),
                        ]
                    ], Response::HTTP_BAD_REQUEST);
            }
        }
    }
}
