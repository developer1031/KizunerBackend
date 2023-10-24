<?php


namespace Modules\Wallet\Http\Controllers;

use Illuminate\Http\Response;
use Modules\Framework\Support\Requests\Pagination;
use Modules\Wallet\Domains\Queries\HistoryQuery;
use Modules\Wallet\Http\Transformers\HistoryTransformer;

class HistoryController
{
    public function index()
    {
        $fromDate =  app('request')->input('from_date');
        $toDate   =  app('request')->input('to_date');
        $perPage  =  app('request')->input('per_page');
        $perPage  =  Pagination::normalize($perPage);

        return response()->json(
            fractal(
                (new HistoryQuery(auth()->user()->id, $fromDate, $toDate, $perPage))->execute(),
                new HistoryTransformer()
            ), Response::HTTP_OK);
    }
}
