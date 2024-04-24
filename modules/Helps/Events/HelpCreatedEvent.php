<?php

namespace Modules\Helps\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Helps\Models\Help;

class HelpCreatedEvent
{
  use SerializesModels;

  private $object;
  private $request;

  public function __construct(Help $help, $request = null)
  {
    $this->object = $help;
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
