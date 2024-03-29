<?php

namespace App\Domain\Exception;

class UrlNotFoundException extends DomainException
{
    public function __construct(string $field)
    {
        $this->message = 'There is no url registered for given alias ' . $field;
    }
}
