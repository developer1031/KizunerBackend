<?php

namespace Modules\CommentApi\Http\Controllers\Comments;

use Illuminate\Http\Response;
use Modules\CommentApi\Contracts\CommentManagerInterface;
use Modules\CommentApi\Http\Requests\Comments\CreateCommentRequest;

class CreateController
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
     * @param CreateCommentRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function action(CreateCommentRequest $request)
    {
        if ($request->validated()) {
            $response = $this->commentManager->addComment($request);

            if ($response) {
                return response()->json(
                    $response,
                    Response::HTTP_CREATED
                );
            }
            return response()->json([
                'message' => 'Item has been deleted',
                'error'   => [
                    'message' => 'Item has beed deleted',
                    'status'  => false
                ]
            ], Response::HTTP_NOT_FOUND);
        }
    }
}
