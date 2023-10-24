<?php

namespace Modules\Framework\Support\Requests;

class Pagination
{
    public static function normalize($perPage)
    {
        if (!$perPage) {
            $perPage = config('modules.pagination.per_page');
        }
        return $perPage;
    }
}
