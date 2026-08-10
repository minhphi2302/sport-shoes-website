<?php

namespace App\Exceptions;

class ValidationException extends AppException
{
    public function __construct(public readonly string $field, string $message)
    {
        parent::__construct($message);
    }
}
