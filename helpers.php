<?php

if (! function_exists('account_url')) {
    function account_url($uri)
    {
        return rtrim(config('app.account_url'), DIRECTORY_SEPARATOR) .
               (DIRECTORY_SEPARATOR . ltrim($uri, DIRECTORY_SEPARATOR));
    }
}

if (! function_exists('account_api')) {
    function account_api($uri)
    {
        return rtrim(config('app.account_url'), DIRECTORY_SEPARATOR) .
               DIRECTORY_SEPARATOR .
               'api' .
               (DIRECTORY_SEPARATOR . ltrim($uri, DIRECTORY_SEPARATOR));
    }
}
