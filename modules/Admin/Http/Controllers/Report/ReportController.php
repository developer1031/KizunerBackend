<?php

namespace Modules\Admin\Http\Controllers\Report;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Report\Report;
use Yajra\DataTables\Facades\DataTables;

class ReportController
{
    public function data()
    {
        return DataTables::eloquent(Report::with('user'))->make(true);
    }

    public function index()
    {
        return view('report::index');
    }

    public function update(Request $request)
    {
        $id = $request->id;
        $report = Report::find($id);
        $report->status = true;
        $report->save();
        return response()->json([
            'status' => true
        ], Response::HTTP_OK);
    }
}
