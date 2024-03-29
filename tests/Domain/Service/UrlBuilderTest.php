<?php

namespace Tests\Domain\Service;

use App\Domain\Service\UrlBuilder;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class UrlBuilderTest extends TestCase
{
    private UrlBuilder $urlBuilder;

    public function setUp(): void
    {
        $this->urlBuilder = new UrlBuilder();
    }

    public function testShouldBeAbleToGenerateNewUrl(): void
    {
        $url = $this->urlBuilder->id('72afee7e-4692-4a4f-a7e9-3b8bd0e34ee8')
            ->url('https://marcossegovia.me/')
            ->aliases([])
            ->createdAt(new DateTimeImmutable('1992-02-04'))
            ->build();

        $this->assertEquals('72afee7e-4692-4a4f-a7e9-3b8bd0e34ee8', $url->getId());
        $this->assertEquals('https://marcossegovia.me/', $url->getUrl());
        $this->assertEmpty($url->getAliases());
        $this->assertNotNull($url->getCreatedAt());
    }
}
