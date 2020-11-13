<?php

if (! function_exists('account_url')) {
    function account_url($uri)
    {
        return rtrim(config('sso.account'), DIRECTORY_SEPARATOR) .
               (DIRECTORY_SEPARATOR . ltrim($uri, DIRECTORY_SEPARATOR));
    }
}
