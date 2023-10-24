<?php

namespace Modules\Package\Domains;

use Modules\Framework\Support\Facades\EntityManager;
use Modules\Package\Domains\Entities\PackageEntity;
use function JmesPath\search;

class Package
{

    /** @var PackageEntity  */
    private $package;

    /**
     * Package constructor.
     * @param PackageEntity $package
     */
    public function __construct(PackageEntity $package)
    {
        $this->package = $package;
    }

    /**
     * @param float $point
     * @param float $price
     * @return PackageEntity
     */
    public static function create(float $point, float $price): PackageEntity
    {
        $package = EntityManager::create(PackageEntity::class);
        $package->point  = $point;
        $package->price  = $price;
        $package->save();
        return $package;
    }

    /**
     * @param string $id
     * @param float $point
     * @param float $price
     * @return mixed
     */
    public static function update(string $id, float $point, float  $price)
    {
        $package = self::find($id);
        $package->point = $point;
        $package->price = $price;
        $package->save();
        return $package;
    }

    /**
     * @param string $packageId
     * @return mixed
     */
    public static function find(string $packageId)
    {
        $packageManager = EntityManager::getManager(PackageEntity::class);
        return $packageManager->find($packageId);
    }
}
