<?php

namespace Modules\Kizuner\Contracts;

use Modules\Kizuner\Models\UserSupport;

interface UserSupportRepositoryInterface
{
    public function search($field, $keyword, $suggest, $perPage);
    public function getList(int $perPage, bool $isSystem);
}
