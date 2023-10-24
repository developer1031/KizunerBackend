<?php

namespace Modules\Chat\Domains\Dto;

class MessageDto
{

    public $userId;

    public $roomId;

    public $text;

    public $hangout;

    public $help;

    public $images;
    public $is_fake;
    public $related_user;

    public function __construct($userId, $roomId, $text, $hangout, $help, $images, $is_fake=null, $related_user=null)
    {
        $this->userId     = $userId;
        $this->roomId     = $roomId;
        $this->text       = $text;
        $this->hangout    = $hangout;
        $this->help       = $help;
        $this->images     = $images;
        $this->is_fake    = $is_fake;
        $this->related_user = $related_user;
    }
}
