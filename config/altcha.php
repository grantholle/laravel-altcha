<?php

return [

    /*
     * The algorithm to use for hashing the challenge.
     * Should be SHA-256, SHA-384 or SHA-512.
     */
    'algorithm' => env('ALTCHA_ALGORITHM', 'SHA-256'),

    /*
     * The secret key to use for hashing the challenge.
     */
    'hmac_key' => env('ALTCHA_HMAC_KEY'),

    /*
     * The minimum and maximum values for the challenge.
     * The bigger larger the number, the more difficult the challenge.
     */
    'range_min' => env('ALTCHA_RANGE_MIN', 1e3),

    'range_max' => env('ALTCHA_RANGE_MAX', 1e5),

    /*
     * The expiration time for the challenge in seconds.
     * Set to null to disable expiration.
     */
    'expires' => env('ALTCHA_EXPIRES', 60),

    /*
     * The length of the salt to use for the challenge.
     */
    'salt_length' => env('ALTCHA_SALT_LENGTH', 12),

    /*
     * The route path to use for the challenge.
     * If you want to implement the logic yourself
     * set this to a null or empty value.
     */
    'route' => '/altcha-challenge',

    /*
     * The middleware to use for the challenge endpoint.
     */
    'middleware' => ['web', 'throttle:10,1'],
];
