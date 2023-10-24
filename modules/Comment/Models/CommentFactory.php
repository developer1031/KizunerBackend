<?php

namespace Modules\Comment\Models;

use Modules\Framework\Contracts\AbstractFactory;

class CommentFactory extends AbstractFactory implements \Modules\Comment\Contracts\Data\CommentInterfaceFactory
{
    protected $concreate = \Modules\Comment\Models\Comment::class;
}
