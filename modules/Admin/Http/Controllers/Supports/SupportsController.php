<?php

namespace Modules\Admin\Http\Controllers\Supports;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Kizuner\Models\UserSupport;
use Modules\Kizuner\Models\Status;
use Yajra\DataTables\Facades\DataTables;

class SupportsController
{
    public function index()
    {
        return view('supports::index');
    }

    public function data()
    {

        $supportsQuery = UserSupport::with('medias')->whereNotNull('email')->orderBy('created_at' , 'desc');
        return DataTables::eloquent($supportsQuery)->make(true);
    }

    public function destroy(string $id)
    {
        UserSupport::destroy($id);
        return redirect()->back()->withSuccess('Delete support successful!');
    }
}
