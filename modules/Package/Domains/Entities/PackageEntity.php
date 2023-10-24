<?php

namespace Modules\Package\Domains\Entities;

use Modules\Framework\Support\DB\UuidEntity as Entity;
use Modules\Package\Domains\Repositories\Contracts\PackageRepositoryInterface;

/**
 * This class present for package_packages table in Database
 * Class PackageEntity
 * @package Modules\Package\Domains\Entities
 */
class PackageEntity extends Entity
{
    public $repository  = PackageRepositoryInterface::class;

    protected $table    = 'package_packages';
}
