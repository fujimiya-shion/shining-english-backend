<?php

return [
    'pagination' => [
        'default_per_page' => 15,
    ],

    'star' => [
        'init' => (int) env('REGISTER_STAR_INIT', 15),
    ]
];
