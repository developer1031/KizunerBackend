<?php

namespace Modules\Package\Http\Controllers\Api;

use Illuminate\Http\Response;
use Modules\Framework\Support\Facades\EntityManager;
use Modules\Package\Http\Transformers\PackageTransformer;
use Modules\Package\Domains\Entities\PackageEntity;
use \Illuminate\Http\JsonResponse;

/**
 * Handle incoming request and return data related to package field - API Only
 * Class PackageController
 * @package Modules\Package\Http\Controllers\Api
 */
class PackageController
{
    /**
     * Return A list of Packages in JSON Format
     * @return JsonResponse
     */
    public function index()
    {
        $packages = EntityManager::getRepository(PackageEntity::class)->get();
        return response()->json(fractal($packages, new PackageTransformer()), Response::HTTP_OK);
    }
}
