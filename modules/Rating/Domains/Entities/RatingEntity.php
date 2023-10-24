<?php

namespace Modules\Rating\Domains\Entities;

use Modules\Framework\Support\DB\UuidEntity;

class RatingEntity extends UuidEntity
{
    protected $table = 'rating_ratings';
}
