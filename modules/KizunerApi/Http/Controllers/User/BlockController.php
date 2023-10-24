<?php

namespace Modules\KizunerApi\Http\Controllers\User;

use Illuminate\Http\JsonResponse;
use Modules\Kizuner\Exceptions\PermissionDeniedException;
use Modules\KizunerApi\Http\Requests\User\Relations\BlockRequest;
use Modules\KizunerApi\Services\User\Relations\BlockManager;
use Symfony\Component\HttpFoundation\Response;

class BlockController
{
    public function blockUser(BlockManager $blockManager, BlockRequest $blockRequest)
    {
        if ($blockRequest->validated()) {
           try {
               return new JsonResponse(
                   $blockManager->blockUser($blockRequest->get('user_id')),
                   Response::HTTP_CREATED
               );
           } catch (PermissionDeniedException $e) {
               return new JsonResponse([
                   'errors' => [
                       'message' => $e->getMessage(),
                       'code'    => $e->getCode()
                   ]
               ], Response::HTTP_OK);
           }
        }
    }

    public function getBlockList(BlockManager $blockManager)
    {
        return new JsonResponse(
            $blockManager->getBlockList(),
            Response::HTTP_OK
        );
    }

    public function unBlock(BlockManager $blockManager, string $id)
    {
        try {
            return new JsonResponse(
                $blockManager->unBlock($id),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            return new JsonResponse([
                'errors' => [
                    'message' => $e->getMessage(),
                    'code'    => $e->getCode()
                ]
            ]);
        }
    }
}
