<?php

namespace Modules\Comment\Services\Facades;

use Illuminate\Support\Facades\Facade;
use Modules\Comment\Services\CommentSecurity;

/**
 * @method static CommentSecurity check(string $id, string $userId)
 */
class CommentAuth extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'CommentSecurity';
    }
}
