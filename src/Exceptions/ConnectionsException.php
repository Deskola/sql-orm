<?php

namespace App\Exceptions;


class ConnectionsException extends CustomException
{
    public function __construct(string $message = '', int $code = 111)
    {
    }
}