<?php

    return [

        'whitelist' => [
            'providers' => config('app.env') === 'local'
                ? ['127.0.0.1']
                : [
                    '18.191.5.158', // Adgem
                    '188.40.3.73', // CPX Research
                    '104.130.7.162', // Adgate
                    '52.42.57.125', // Adgate
                    '54.175.173.245', // OfferToro
                    '35.165.166.40', // Ayet Studios
                    '35.166.159.131', // Ayet Studios
                    '52.40.3.140', // Ayet Studios
                    '91.179.148.87', // gunter
                ],
        ],

        'blacklist' => [],

        // RESPONSE SETTINGS
        'redirect_to'      => '',   // URL TO REDIRECT IF BLOCKED (LEAVE BLANK TO THROW STATUS)
        'response_status'  => 404,  // STATUS CODE (403, 404 ...)
        'response_message' => '',    // MESSAGE (COMBINED WITH STATUS CODE)

    ];
