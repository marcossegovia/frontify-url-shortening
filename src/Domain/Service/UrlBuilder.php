<?php

namespace App\Domain\Service;

use App\Domain\Model\Url;
use Ramsey\Uuid\Uuid;

class UrlBuilder
{
    private Url $url;

    public function __construct()
    {
        $this->url = new Url();
    }

    public function id(string $id = null): UrlBuilder
    {
        $this->url->setId($id ? Uuid::fromString($id) : Uuid::uuid4());
        return $this;
    }

    public function url(string $url): UrlBuilder
    {
        $this->url->setUrl($url);
        return $this;
    }

    public function createdAt(\DateTimeImmutable $dateTimeImmutable): UrlBuilder
    {
        $this->url->setCreatedAt($dateTimeImmutable);
        return $this;
    }

    public function aliases(array $aliases): UrlBuilder
    {
        $this->url->setAliases($aliases);
        return $this;
    }

    public function build(): Url
    {
        $previous = $this->url;
        $this->url = new Url();
        return $previous;
    }
}
