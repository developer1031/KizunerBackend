<?php

namespace Modules\Comment\Contracts\Data;

use \Illuminate\Database\Eloquent\Relations\MorphMany;

interface CommentableInterface
{
    /**
     * Define comment class handler
     * @return MorphMany
     */
    public function comments(): MorphMany;
}
