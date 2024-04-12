<?php

namespace Modules\Search\Domain\Actions;

use Illuminate\Support\Facades\Log;
use Modules\Guide\Http\Transformers\GuideTransformer;
use Modules\Search\Domain\Queries\HangoutQuery;
use Modules\Search\Domain\Queries\HelpQuery;
use Modules\Search\Domain\Queries\StatusQuery;
use Modules\Search\Domain\Queries\UserQuery;
use Modules\Search\Domain\Queries\VideoQuery;
use Modules\Search\Http\Transformers\HangoutTransform;
use Modules\Search\Http\Transformers\HelpTransform;
use Modules\Search\Http\Transformers\StatusTransform;
use Modules\Search\Http\Transformers\UserTransform;

class FullTextSearchAction
{
  public function execute($type, $query, string $perPage, $category = null, $offerType = null, $paymentMethod = null, $location = null, $amount = null, $minAmount = null, $maxAmount = null, $language = null, $date_filter = null)
  {
    if ($type) {
      if ($type === 'user') {
        return fractal((new UserQuery($query, $perPage, $category))->execute(), new UserTransform());
      }
      if ($type === 'hangout') {
        return fractal((new HangoutQuery($query, $perPage, $category))->execute(), new HangoutTransform());
      }
      if ($type === 'status') {
        return fractal((new StatusQuery($query, $perPage, $category))->execute(), new StatusTransform());
      }
      if ($type === 'help') {
        return fractal((new HelpQuery($query, $perPage, $category))->execute(), new HelpTransform());
      }
      if ($type === 'video') {
        return fractal((new VideoQuery($query, $perPage, $category))->execute(), new GuideTransformer());
      }
    }

    $data = [];

    $age    =   app('request')->input('age');
    $gender =   app('request')->input('gender');
    $skills    =   app('request')->input('skills');
    $available_status    =   app('request')->input('available_status');

    if ($query) {
      $statuses = fractal((new StatusQuery($query, $perPage, $category, $offerType, $paymentMethod, $location, $amount, $minAmount, $maxAmount))->execute(), new StatusTransform());
      $data['statuses'] = $statuses;
    }

    if ($query || $age || $gender || $skills || $location || $language) {
      $users = fractal((new UserQuery($query, $perPage, $category, $offerType, $paymentMethod, $location, $amount, $minAmount, $maxAmount))->execute(), new UserTransform());
      $data['users'] = $users;
    }

    if ($query || $skills) {
      $videos = fractal((new VideoQuery($query, $perPage, $category, $offerType, $paymentMethod, $location, $amount, $minAmount, $maxAmount))->execute(), new GuideTransformer());
      $data['videos'] = $videos;
    }

    if ($query || $category || $skills || $location || $minAmount || $maxAmount || $amount || $paymentMethod || $offerType || $available_status || $date_filter) {
      $hangouts = fractal((new HangoutQuery($query, $perPage, $category, $offerType, $paymentMethod, $location, $amount, $minAmount, $maxAmount, $date_filter))->execute(), new HangoutTransform());
      $helps = fractal((new HelpQuery($query, $perPage, $category, $offerType, $paymentMethod, $location, $amount, $minAmount, $maxAmount, $date_filter))->execute(), new HelpTransform());

      $data['hangouts'] = $hangouts;
      $data['helps'] = $helps;
    }

    return [
      'data' => $data
    ];
  }
}