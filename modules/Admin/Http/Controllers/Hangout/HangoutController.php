<?php

namespace Modules\Admin\Http\Controllers\Hangout;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Kizuner\Models\Hangout;
use Yajra\DataTables\Facades\DataTables;

class HangoutController
{
    public function index()
    {
        return view('hangout::index');
    }

    public function data()
    {
        $hangoutQuery = Hangout::with('user', 'media', 'location')->where('is_fake', '<>', 1)->orWhereNull('is_fake');;
        return DataTables::eloquent($hangoutQuery)
            ->editColumn('updated_at', function($hangout) {
                return $hangout->updated_at;
            })
            ->make(true);
    }

    public function destroy($id)
    {
        Hangout::destroy($id);
        $currentTime = Carbon::now();
        DB::statement("UPDATE feed_timelines SET deleted_at = '".$currentTime."' WHERE reference_id = '". $id ."'");
        return redirect(route('admin.hangout.index'))->withSuccess('Delete Hangout successful!');
    }

    public function show(string $id)
    {
        $hangout = Hangout::with('user', 'media', 'location', 'skills', 'comments', 'reacts', 'offers')->find($id);
        if ($hangout) {
            return view('hangout::show')->with([
                'hangout' => $hangout
            ]);
        }
        return redirect()->back()->withError("Hangout has been deleted");
    }
}
