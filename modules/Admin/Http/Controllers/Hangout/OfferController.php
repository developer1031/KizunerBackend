<?php

namespace Modules\Admin\Http\Controllers\Hangout;

use Modules\Kizuner\Models\Offer;
use Yajra\DataTables\Facades\DataTables;

class OfferController
{
    public function index()
    {
        return view('hangout::offer');
    }

    public function data(string $id)
    {
        $offer = Offer::with('sender', 'receiver')->where('hangout_id', $id);
        return DataTables::eloquent($offer)->make(true);
    }
}
