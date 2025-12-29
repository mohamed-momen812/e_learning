<?php

namespace App\Core\Exceptions;

use Illuminate\Validation\ValidationException as LaravelValidationException;

class ValidationException extends BaseException
{
    public function __construct(LaravelValidationException $exception)
    {
        $message = $exception->getMessage();
        $errors = $exception->errors();
        parent::__construct($message, $errors, 422);
    }
}

