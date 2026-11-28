<?php

return [
    'disabled' => filter_var(env('DISABLE_AUTH_THROTTLE', false), FILTER_VALIDATE_BOOLEAN),
];
