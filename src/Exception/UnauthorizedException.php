<?php

namespace Aixieluo\LaravelSso\Exception;

use Illuminate\Support\Facades\Response;
use \Illuminate\Validation\UnauthorizedException as Exception;

class UnauthorizedException extends Exception
{
    public function __construct($message = '未登录，请登陆', $code = 401, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function render()
    {
        return Response::json([
            'message' => $this->getMessage(),
        ], $this->code);
    }
}
