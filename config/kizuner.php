<?php
return [
    'offer' => [
        'remind_range' => [
            'min' => 20,
            'max' => 50
        ]
    ],
    'fake_cast' => [
      'number' => env('FAKE_CAST_NUM', 5),
      'radiusKm' => env('FAKE_CAST_RADIUS_KM', 5),
    ],

    'limit_file_size' => [
        'chat' => env('LIMIT_FILE_SIZE_CHAT', 1024),
        'others' => env('LIMIT_FILE_SIZE_OTHERS', 1024),
    ],
];
