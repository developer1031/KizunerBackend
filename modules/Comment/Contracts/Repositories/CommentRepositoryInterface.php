<?php

namespace Modules\Comment\Contracts\Repositories;

use Modules\Comment\Contracts\Data\CommentInterface;
use Modules\Comment\Exceptions\CommentNotFound;

interface CommentRepositoryInterface
{

    /**
     * Add new Comment
     * @param string $userId
     * @param string $body
     * @return mixed
     */
    public function create(string $userId, string $commentedUserId, string $body): CommentInterface ;

    /**
     * @param string $id
     * @param string $body
     * @return CommentInterface
     */
    public function update(string $id, string $body): CommentInterface ;

    /**
     * @param string $id
     * @return boolean
     * @throws \Exception
     */
    public function delete(string $id);

    /**
     * @param string $type
     * @param string $referenceId
     * @param int $perPage
     * @return mixed
     */
    public function getCommentsList(string $type, string $referenceId, int $perPage);

    /**
     * @param string $id
     * @param string $userId
     * @return CommentInterface
     * @throws CommentNotFound
     */
    public function findByIdAndUserId(string $id, string $userId): CommentInterface;
}
