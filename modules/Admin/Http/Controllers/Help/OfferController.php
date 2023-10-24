<?php

namespace Modules\Admin\Http\Controllers\Help;

use Modules\Helps\Models\HelpOffer;
use Modules\Kizuner\Models\Offer;
use Yajra\DataTables\Facades\DataTables;

class OfferController
{
    public function index()
    {
        return view('help::offer');
    }

    public function data(string $id)
    {
        $offer = HelpOffer::with('sender', 'receiver')->where('help_id', $id);
        return DataTables::eloquent($offer)->make(true);
    }
}
