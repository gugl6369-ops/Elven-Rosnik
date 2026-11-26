<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Disable auth HTTP throttling
    |--------------------------------------------------------------------------
    |
    | When true, Fortify login/registration limiters and route throttle middleware
    | on auth endpoints are turned off (avoids 429 during local development).
    | Set DISABLE_AUTH_THROTTLE=false in production.
    |
    */

    'disabled' => filter_var(env('DISABLE_AUTH_THROTTLE', true), FILTER_VALIDATE_BOOLEAN),

];