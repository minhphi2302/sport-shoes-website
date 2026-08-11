<?php

namespace App\Exceptions;

class ValidationException extends AppException
{
    public function __construct(public string $field, string $message)
    {
        parent::__construct($message);
    }
}
