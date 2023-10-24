<?php

namespace Modules\Admin\Http\Controllers\Status;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Kizuner\Models\Status;
use Yajra\DataTables\Facades\DataTables;

class StatusController
{
    public function index()
    {
        return view('status::index');
    }

    public function data()
    {
        if ($id = app('request')->input('id')) {
            $statusQuery = Status::with('media', 'user')->where('id', $id);
            return DataTables::eloquent($statusQuery)->make(true);
        }
        $statusQuery = Status::with('media', 'user');
        return DataTables::eloquent($statusQuery)->make(true);
    }

    public function destroy(string $id)
    {
        Status::destroy($id);
        $currentTime = Carbon::now();
        DB::statement("UPDATE feed_timelines SET deleted_at = '".$currentTime."' WHERE reference_id = '". $id ."'");
        return redirect()->back()->withSuccess('Delete Status successful!');
    }
}
