<?php

namespace Modules\Kizuner\Models;

class Media extends \Modules\Upload\Models\Upload
{
    public static $type = [
        'hangout' => [
            'cover' => 'cover'
        ],
        'status' => [
            'cover' => 'cover'
        ],
        'help' => [
            'cover' => 'cover'
        ],
        'helpOffer' => [
            'cover' => 'cancel.evidence'
        ],
        'hangoutOffer' => [
            'cover' => 'reject.evidence'
        ],
        'userSupport' => [
            'cover' => 'user.support'
        ]
    ];
}
