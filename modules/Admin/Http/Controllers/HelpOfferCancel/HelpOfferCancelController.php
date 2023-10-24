<?php

namespace Modules\Admin\Http\Controllers\HelpOfferCancel;

use Illuminate\Support\Facades\Log;
use Modules\Helps\Models\Help;
use Modules\Helps\Models\HelpOffer;
use Yajra\DataTables\Facades\DataTables;

class HelpOfferCancelController
{
    public function index()
    {

        Log::info('asdasd');
        return view('help-offer-cancel::index');
    }

    public function data()
    {
        $helpOffer= HelpOffer::with('sender', 'receiver', 'media')->whereIn('status',[4, 6, 10, 11, 13]);

        return DataTables::eloquent($helpOffer)->make(true);
    }
    public function show(string $id)
    {
        $helpOffer = HelpOffer::with('sender', 'receiver', 'media')->where('id', $id);

        if ($helpOffer->first()) {
            $help = Help::with('user', 'media', 'location', 'skills', 'comments', 'reacts', 'offers')->find($helpOffer->first()->help_id);
            return view('help-offer-cancel::show')->with([
                'help' => $help,
                'offer' => $helpOffer->first()
            ]);
        }
        return redirect()->back()->withError("Offer has been deleted");
    }
}
