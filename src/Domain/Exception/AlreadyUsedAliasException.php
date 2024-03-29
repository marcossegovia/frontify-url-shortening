<?php

namespace App\Domain\Exception;

class AlreadyUsedAliasException extends DomainException
{
    public function __construct(string $alias)
    {
        $this->message = 'There is a url already registered for given alias ' . $alias;
    }
}
