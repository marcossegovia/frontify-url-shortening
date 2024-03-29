<?php

namespace App\Domain\Service;

use App\Domain\Model\Alias;

class AliasBuilder
{
    private Alias $alias;

    public function __construct()
    {
        $this->alias = new Alias();
    }

    public function alias(string $alias): AliasBuilder
    {
        $this->alias->setAlias($alias);
        return $this;
    }

    public function createdAt(\DateTimeImmutable $dateTimeImmutable): AliasBuilder
    {
        $this->alias->setCreatedAt($dateTimeImmutable);
        return $this;
    }

    public function build(): Alias
    {
        $previous = $this->alias;
        $this->alias = new Alias();
        return $previous;
    }
}
