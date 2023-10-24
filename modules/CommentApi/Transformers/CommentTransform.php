<?php

namespace Modules\CommentApi\Transformers;

use League\Fractal\TransformerAbstract;
use Modules\Comment\Contracts\Data\CommentInterface;
use Modules\KizunerApi\Transformers\SimpleUserTransform;

class CommentTransform extends TransformerAbstract
{

    protected $defaultIncludes = [
        'user'
    ];

    public function transform(CommentInterface $comment)
    {
        return [
            'id'            => $comment->getId(),
            'body'          => $comment->getBody(),
            'updated_at'    => $comment->getUpdatedAt(),
        ];
    }

    public function includeUser(CommentInterface $comment)
    {
        $user = $comment->user;
        return $this->item($user, new SimpleUserTransform);
    }
}
