<?php

namespace Modules\CommentApi\Contracts;

use Modules\Comment\Exceptions\PermisionDenied;
use Modules\CommentApi\Http\Requests\Comments\CreateCommentRequest;
use Modules\CommentApi\Http\Requests\Comments\UpdateCommentRequest;

interface CommentManagerInterface
{
    /**
     * @param CreateCommentRequest $request
     * @return \Spatie\Fractal\Fractal
     */
    public function addComment(CreateCommentRequest $request);

    /**
     * @param string $referenceId
     * @return mixed
     */
    public function getCommentList(string $referenceId);

    /**
     * @param string $id
     * @param UpdateCommentRequest $request
     * @return \Spatie\Fractal\Fractal
     */
    public function updateComment(string $id, UpdateCommentRequest $request);

    /**
     * @param string $id
     * @return array
     * @throws PermisionDenied
     */
    public function deleteComment(string $id);
}
