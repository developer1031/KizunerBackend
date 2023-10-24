<?php

namespace Modules\Admin\Http\Controllers\Help;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Helps\Models\Help;
use Modules\Kizuner\Models\EmailSupport;
use Modules\Kizuner\Models\Hangout;
use Yajra\DataTables\Facades\DataTables;

class EmailSupportController
{
    public function index()
    {
        return view('emailSupport::index');
    }

    public function data()
    {
        $emailSupportQuery = EmailSupport::with('media', 'user');
        return DataTables::eloquent($emailSupportQuery)->make(true);
    }

    public function destroy(string $id)
    {
        EmailSupport::destroy($id);
        return redirect()->back()->withSuccess('Delete emailSupport successful!');
    }
}
