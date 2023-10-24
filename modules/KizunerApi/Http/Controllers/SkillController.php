<?php

namespace Modules\KizunerApi\Http\Controllers;

use Illuminate\Contracts\Queue\EntityNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\KizunerApi\Http\Requests\Skill\SkillCreateRequest;
use Modules\KizunerApi\Services\SkillManager;
use Symfony\Component\HttpFoundation\Response;

class SkillController
{
    /**
     * @param SkillManager $skillManager
     * @param SkillCreateRequest $request
     * @return JsonResponse
     */
    public function addSkills(SkillManager $skillManager, SkillCreateRequest $request)
    {
        if ($request->validated()) {
            $response = $skillManager->addSkills($request);
            return new JsonResponse($response->toArray(), Response::HTTP_CREATED);
        }
    }

    /**
     * @param SkillManager $skillManager
     * @param string $keyword
     * @return JsonResponse
     */
    public function search(SkillManager $skillManager)
    {
        return new JsonResponse($skillManager->search(), Response::HTTP_OK);
    }

    /**
     * @param SkillManager $skillManager
     * @param Request $request
     * @param string $type
     * @return JsonResponse
     */
    public function getSkillsList(SkillManager $skillManager, Request $request, string $type = null)
    {
        $isSystem = false;
        if ($type == 'system') {
            $isSystem = true;
        }

        $response = $skillManager->getSkills($request, $isSystem);
        return new JsonResponse($response, Response::HTTP_OK);
    }

    /**
     * @param SkillManager $skillManager
     * @param string $ids
     * @return JsonResponse
     */
    public function getHangoutsList(SkillManager $skillManager, string $ids)
    {
        try {
            $response = $skillManager->getHangouts($ids);
            return new JsonResponse($response, Response::HTTP_OK);
        } catch (EntityNotFoundException $exception) {
            return new JsonResponse([
                'message' => $exception->getMessage(),
                'errors' => [
                    'message' => $exception->getMessage(),
                    'code' => $exception->getCode()
                ]
            ]);
        }
    }
}
