<?php

namespace App\Domain\Service;

use App\Domain\Model\Url;

class UrlMapper
{
    public function fromRecord(array $record): Url
    {
        $aliases = array_map(function ($alias) {
            $aliasBuilder = new AliasBuilder();
            return $aliasBuilder->alias($alias['alias'])
                ->createdAt(
                    \DateTimeImmutable::createFromFormat(\DateTimeImmutable::ATOM, $alias['createdAt'])
                )->build();
        }, $record['aliases']);

        $urlBuilder = new UrlBuilder();
        return $urlBuilder->id($record['id'])
            ->url($record['url'])
            ->createdAt(\DateTimeImmutable::createFromFormat(\DateTimeImmutable::ATOM, $record['createdAt']))
            ->aliases($aliases)
            ->build();
    }

    public function fromRequest(array $request): Url
    {
        $aliasBuilder = new AliasBuilder();
        $alias = $aliasBuilder->alias($request['alias'])->createdAt(new \DateTimeImmutable())->build();
        $urlBuilder = new UrlBuilder();
        return $urlBuilder->id()
            ->url($request['url'])
            ->aliases([$alias])
            ->createdAt(new \DateTimeImmutable())
            ->build();
    }

    public function update(Url $url, array $request): Url
    {
        $aliasBuilder = new AliasBuilder();
        $alias = $aliasBuilder->alias($request['alias'])->createdAt(new \DateTimeImmutable())->build();
        $url->setAliases([...$url->getAliases(), $alias]);
        return $url;
    }
}
