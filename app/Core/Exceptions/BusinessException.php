<?php

namespace App\Core\Exceptions;

class BusinessException extends BaseException
{
    /**
     * @param  array<string, mixed>  $errors
     */
    public function __construct(string $message = '', array $errors = [], int $statusCode = 400)
    {
        parent::__construct($message, $errors, $statusCode);
    }
}
