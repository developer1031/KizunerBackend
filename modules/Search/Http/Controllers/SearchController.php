<?php

namespace Modules\Search\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Modules\Framework\Support\Requests\Pagination;
use Modules\Search\Domain\Actions\FullTextSearchAction;

class SearchController
{
  public function index(Request $request)
  {
    Log::info($request->all());

    $query      = $request->input('query');
    $perPage    = $request->input('per_page') ?? 30;
    $perPage    = Pagination::normalize($perPage);
    $type       = $request->input('type');
    $category   = $request->input('categories');

    $offerType       = $request->input('offer_type');
    $paymentMethod   = $request->input('payment_method');
    $location   = $request->input('location');
    $amount   = $request->input('amount');
    $minAmount   = $request->input('min_amount');
    $maxAmount   = $request->input('max_amount');
    $language   = $request->input('language');
    $date_filter = $request->input('date_filter');

    return response()->json((new FullTextSearchAction)->execute($type, $query, $perPage, $category, $offerType, $paymentMethod, $location, $amount, $minAmount, $maxAmount, $language, $date_filter), Response::HTTP_OK);
  }
}