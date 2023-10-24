<?php

namespace Modules\Admin\Http\Controllers\Help;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Helps\Models\Help;
use Modules\Kizuner\Models\Hangout;
use Yajra\DataTables\Facades\DataTables;

class HelpController
{
    public function index()
    {
        return view('help::index');
    }

    public function data()
    {
        $helpQuery = Help::with('user', 'media', 'location')->where('is_fake', '<>', 1)->orWhereNull('is_fake');
        return DataTables::eloquent($helpQuery)
            ->editColumn('updated_at', function($help) {
                return $help->updated_at;
            })
            ->make(true);
    }

    public function destroy($id)
    {
        Help::destroy($id);
        $currentTime = Carbon::now();
        DB::statement("UPDATE feed_timelines SET deleted_at = '".$currentTime."' WHERE reference_id = '". $id ."'");
        return redirect(route('admin.help.index'))->withSuccess('Delete Help successful!');
    }

    public function show(string $id)
    {
        $help = Help::with('user', 'media', 'location', 'skills', 'comments', 'reacts', 'offers')->find($id);
        if ($help) {
            return view('help::show')->with([
                'help' => $help
            ]);
        }
        return redirect()->back()->withError("Help has been deleted");
    }
}
