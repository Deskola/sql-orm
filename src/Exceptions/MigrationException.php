<?php

namespace DeskolaOrm\Exceptions;


class MigrationException extends CustomException
{
    public function __construct(string $message = '', int $code = 112)
    {
    }
}