<?php

namespace Modules\Category\Http\Controllers;

use Illuminate\Contracts\Queue\EntityNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Category\Http\Requests\CategoryCreateRequest;
use Modules\Category\Services\CategoryManager;
use Modules\KizunerApi\Http\Requests\Skill\SkillCreateRequest;
use Modules\KizunerApi\Services\SkillManager;
use Symfony\Component\HttpFoundation\Response;

class CategoryController
{
    /**
     * @param CategoryManager $categoryManager
     * @param CategoryCreateRequest $request
     * @return JsonResponse
     */
    public function addCategories(CategoryManager $categoryManager, CategoryCreateRequest $request)
    {
        if ($request->validated()) {
            $response = $categoryManager->addCategories($request);
            return new JsonResponse($response->toArray(), Response::HTTP_CREATED);
        }
    }

    public function search(CategoryManager $categoryManager) {
        return new JsonResponse($categoryManager->search(), Response::HTTP_OK);
    }
}
