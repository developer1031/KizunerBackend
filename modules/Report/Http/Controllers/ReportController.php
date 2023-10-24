<?php

namespace Modules\Report\Http\Controllers;

use Illuminate\Http\Response;
use Modules\Report\Http\Requests\ReportRequest;
use Modules\Report\Report;

class ReportController
{
    public function store(ReportRequest $request)
    {
        if ($request->validated()) {
            $checkExist = Report::where([
                'reference_id' => $request->reference_id,
                'user_id'      => auth()->user()->id,
                'type'         => $request->type
            ])->first();

            if ($checkExist) {
                return response()->json([
                    'message' => 'You already report this!',
                    'error' => [
                        'message' => 'You already report this!'
                    ]
                ], Response::HTTP_BAD_REQUEST);
            }

            $report = new Report();
            $report->reference_id = $request->reference_id;
            $report->user_id      = auth()->user()->id;
            $report->type         = $request->type;
            $report->reason       = $request->reason;
            $report->save();
            return response()->json([
                'data' => [
                    'message' => 'Thanks for your report!'
                ]
            ], Response::HTTP_CREATED);
        }
    }
}
