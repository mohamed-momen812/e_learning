<?php

namespace App\Core\Exceptions;

use Exception;

abstract class BaseException extends Exception
{
    /** @var array<string, mixed> */
    protected array $errors = [];

    protected int $statusCode = 400;

    /**
     * @param  array<string, mixed>  $errors
     */
    public function __construct(string $message = '', array $errors = [], int $statusCode = 400)
    {
        parent::__construct($message);
        $this->errors = $errors;
        $this->statusCode = $statusCode;
    }

    /**
     * @return array<string, mixed>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
