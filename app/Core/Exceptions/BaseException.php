<?php

namespace App\Core\Exceptions;

use Exception;

abstract class BaseException extends Exception
{
    protected array $errors = [];
    protected int $statusCode = 400;

    public function __construct(string $message = '', array $errors = [], int $statusCode = 400)
    {
        parent::__construct($message);
        $this->errors = $errors;
        $this->statusCode = $statusCode;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}

