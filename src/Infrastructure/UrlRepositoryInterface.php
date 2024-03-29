<?php

namespace App\Infrastructure;

use App\Domain\Model\Url;

interface UrlRepositoryInterface
{
    public function findByUrl(string $url): ?Url;

    public function findByAlias(string $alias): ?Url;

    public function persist(Url $url): void;
}
