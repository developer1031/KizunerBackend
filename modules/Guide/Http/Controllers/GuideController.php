<?php

namespace Modules\Guide\Http\Controllers;

use Illuminate\Http\Response;
use Modules\Framework\Support\Requests\Pagination;
use Modules\Guide\Domains\Repositories\Contracts\GuideRepositoryInterface;
use Modules\Guide\Http\Transformers\GuideTransformer;

/**
 * Class GuideController
 * @package Modules\Guide\Http\Controllers
 * @description This class return list of Video Guides
 */
class GuideController
{
    public function index(GuideRepositoryInterface $guideRepository)
    {
        $perPage = Pagination::normalize(app('request')->input('per_page'));
        return response()->json(
            fractal($guideRepository->get($perPage),new GuideTransformer()),
            Response::HTTP_OK
        );
    }
}
