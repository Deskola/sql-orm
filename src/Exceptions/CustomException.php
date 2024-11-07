<?php

namespace App\Exceptions;

use Exception;

abstract class CustomException extends Exception implements ExceptionInterface
{

    public function __construct(string $message = "", int $code = 0)
    {
        if (!$message) {

            $message = 'Unknown '. get_class($this);
        }

        parent::__construct($message, $code);
    }

    public function __toString(): string
    {
        return get_class($this) . " '{$this->message}' in {$this->file}({$this->line})\n" . "{$this->getTraceAsString()}";
    }

    public function getCustomMessage(): string
    {
        return $this->message;
    }
}