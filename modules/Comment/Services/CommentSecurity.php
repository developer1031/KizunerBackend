<?php

namespace Modules\Comment\Services;

use Illuminate\Support\Facades\Auth;
use Modules\Comment\Contracts\Repositories\CommentRepositoryInterface;

class CommentSecurity
{

    /** @var CommentRepositoryInterface  */
    private $commentRepository;

    /**
     * CommentSecurity constructor.
     * @param CommentRepositoryInterface $commentRepository
     */
    public function __construct(
        CommentRepositoryInterface $commentRepository
    ) {
        $this->commentRepository = $commentRepository;
    }

    /**
     * @param string $id
     * @param string $userId
     * @return bool
     * @throws \Modules\Comment\Exceptions\CommentNotFound
     */
    public function check(string $id, string $userId)
    {
        $comment = $this->commentRepository
              ->findByIdAndUserId($id, $userId);

        if ($comment) {
            return true;
        }
    }
}
