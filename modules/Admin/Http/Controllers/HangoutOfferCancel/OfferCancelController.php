<?php

namespace Modules\Admin\Http\Controllers\HangoutOfferCancel;

use Illuminate\Support\Facades\Log;
use Modules\Kizuner\Models\Hangout;
use Modules\Kizuner\Models\Offer;
use Yajra\DataTables\Facades\DataTables;

class OfferCancelController
{
    public function index()
    {
        return view('hangout-offer-cancel::index');
    }

    public function data()
    {
        $offer = Offer::with('sender', 'receiver', 'media')->whereIn('status', [4, 6, 10, 11, 13, 14]);

        return DataTables::eloquent($offer)->make(true);
    }
    public function show(string $id)
    {
        $offer = Offer::with('sender', 'receiver', 'media')->where('id', $id);
        if ($offer->first()) {
            $hangout = Hangout::with('user', 'media', 'location', 'skills', 'comments', 'reacts', 'offers')->find($offer->first()->hangout_id);
            return view('hangout-offer-cancel::show')->with([
                'hangout' => $hangout,
                'offer' => $offer->first()
            ]);
        }
        return redirect()->back()->withError("Offer has been deleted");
    }
}
