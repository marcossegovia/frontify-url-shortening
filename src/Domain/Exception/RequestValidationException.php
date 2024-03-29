<?php

namespace App\Domain\Exception;

class RequestValidationException extends DomainException
{
    private array $errors;

    public function __construct(array $errors)
    {
        $this->message = 'There was validation errors during the request';
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
