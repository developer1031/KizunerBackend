<?php

namespace Modules\CommentApi\Http\Controllers\Comments;

use Illuminate\Http\Response;
use Modules\Comment\Exceptions\CommentTypeNotExist;
use Modules\CommentApi\Contracts\CommentManagerInterface;

class GetController
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


    public function action(string $id)
    {
       try {
           return response()->json(
               $this->commentManager->getCommentList($id),
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
