<?php

namespace Modules\FeedApi\Http\Controllers;

use Illuminate\Http\Request;
use Modules\FeedApi\Contracts\TimelineManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class TimelineController
{
    public function getTimeline(TimelineManagerInterface $timelineManager, Request $request, string $id = null)
    {
        if ($request->input('scope') && $request->input('scope') == 'personal') {
            return response()->json(
                $timelineManager->getPersonalTimeline($id),
                Response::HTTP_OK
            );
        } else if ($request->input('scope') && $request->input('scope') == 'timeline') {
            return response()->json(
                $timelineManager->getTimeline(),
                Response::HTTP_OK
            );
        }
        return response()->json([
            'errors' => [
               'message' => 'Please add Scope to your URL'
            ]
        ], Response::HTTP_BAD_REQUEST);
    }
}
