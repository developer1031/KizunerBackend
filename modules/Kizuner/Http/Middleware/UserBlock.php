<?php

namespace Modules\Kizuner\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Modules\Kizuner\Repositories\RelationRepository;
use Symfony\Component\HttpFoundation\Response;

class UserBlock
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /** @var RelationRepository $blockInstance */
        $blockInstance = app('Modules\Kizuner\Contracts\RelationshipRepositoryInterface');

        $currentUser = $request->user()->id;
        $userId      = $request->route('id');

        if ($userId && $blockInstance->checkBlock($currentUser, $userId)) {
            return new JsonResponse([
                'errors' => [
                    'message' => 'User not found'
                ]
            ], Response::HTTP_NOT_FOUND);
        }

        return $next($request);
    }
}
