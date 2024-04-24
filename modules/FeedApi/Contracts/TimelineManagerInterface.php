<?php

namespace Modules\FeedApi\Contracts;

use Spatie\Fractal\Fractal;

interface TimelineManagerInterface
{
  /**
   * @param string|null $id
   * @return Fractal
   */
  public function getPersonalTimeline(string $id = null): Fractal;

  /**
   * @return Fractal
   */
  public function getTimeline(): Fractal;
}
