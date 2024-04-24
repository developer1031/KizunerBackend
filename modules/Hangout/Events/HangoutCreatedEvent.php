<?php

namespace Modules\Hangout\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Kizuner\Models\Hangout;

class HangoutCreatedEvent
{
  use SerializesModels;

  private $object;
  private $request;


  public function __construct(Hangout $hangout, $request = null)
  {
    $this->object = $hangout;
    $this->request = $request;
  }

  public function getObject()
  {
    return $this->object;
  }

  public function getRequest()
  {
    return $this->request;
  }
}
