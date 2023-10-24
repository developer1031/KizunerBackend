<?php

namespace Modules\Category\Contracts;

interface CategoryRepositoryInterface
{
    public function search($field, $keyword, $suggest, $perPage);
    public function getList(int $perPage, bool $isSystem);
}
