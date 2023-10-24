<?php

namespace Modules\Kizuner\Contracts;

interface SkillRepositoryInterface
{
    public function search($field, $keyword, $suggest, $perPage);

    public function getList(int $perPage, bool $isSystem);

    public function getHangoutsByIds(array $ids);
}
