<?php

namespace App\Infrastructure\Repository;

use App\Domain\Model\Url;
use App\Domain\Service\UrlMapper;
use App\Infrastructure\UrlRepositoryInterface;

class InMemoryUrlRepository implements UrlRepositoryInterface
{
    private UrlMapper $urlMapper;

    private array $urls;

    private ?string $env;

    public function __construct(UrlMapper $urlMapper, $env = null)
    {
        $this->urlMapper = $urlMapper;
        $this->env = $env;
        if ($env === null) {
            $this->urls = file_exists(__DIR__ . '/urls.json') ? json_decode(
                file_get_contents(__DIR__ . '/urls.json'),
                true
            ) : [];
        } else {
            $this->urls = file_exists(__DIR__ . '/../../../tests/data/urls.json') ? json_decode(
                file_get_contents(__DIR__ . '/../../../tests/data/urls.json'),
                true
            ) : [];
        }
    }

    public function findByUrl(string $url): ?Url
    {
        $records = array_filter($this->urls, function (array $currentUrl) use ($url) {
            return $currentUrl['url'] === $url;
        });

        if (!empty($records)) {
            // Urls are unique, therefore one value available
            return $this->urlMapper->fromRecord(array_pop($records));
        }
        return null;
    }

    public function findByAlias(string $alias): ?Url
    {
        $records = array_filter($this->urls, function (array $currentUrl) use ($alias) {
            return count(array_filter($currentUrl['aliases'], function (array $currentAlias) use ($alias) {
                    return $currentAlias['alias'] === $alias;
            })) > 0;
        });

        if (!empty($records)) {
            // Aliases are unique, therefore one value available
            return $this->urlMapper->fromRecord(array_pop($records));
        }
        return null;
    }

    public function persist(Url $url): void
    {
        $records = array_filter($this->urls, function (array $currentUrl) use ($url) {
            return $currentUrl['id'] === $url->getId()->toString();
        });
        $recordPointer = array_keys($records);
        if (!empty($recordPointer)) {
            // Update existing
            $this->urls[$recordPointer[0]] = $url;
        } else {
            // Add new
            $this->urls[] = $url;
        }
        // Since $this->urls is an array of key/value, we need to refresh it after having added an Object
        // we do it by leveraging json serialization/deserialization
        if ($this->env === null) {
            file_put_contents(__DIR__ . '/urls.json', json_encode($this->urls, JSON_PRETTY_PRINT));
            $this->urls = json_decode(file_get_contents(__DIR__ . '/urls.json'), true);
        } else {
            file_put_contents(__DIR__ . '/../../../tests/data/urls.json', json_encode($this->urls, JSON_PRETTY_PRINT));
            $this->urls = json_decode(file_get_contents(__DIR__ . '/../../../tests/data/urls.json'), true);
        }
    }
}
