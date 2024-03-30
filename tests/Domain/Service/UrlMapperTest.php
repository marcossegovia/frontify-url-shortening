<?php

namespace Tests\Domain\Service;

use App\Domain\Service\AliasBuilder;
use App\Domain\Service\UrlBuilder;
use App\Domain\Service\UrlMapper;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class UrlMapperTest extends TestCase
{
    private UrlMapper $urlMapper;


    public function setUp(): void
    {
        $this->urlMapper = new UrlMapper();
    }

    public function testShouldBeAbleToCreateUrlFromRecord(): void
    {
        $record = [
            'id' => '833825f1-d8d1-4ff1-9747-c219868f2178',
            'url' => 'example-url',
            'createdAt' => '2024-03-29T18:35:29+00:00',
            'aliases' => [
                [
                    'alias' => 'example-alias',
                    'createdAt' => '2024-03-29T18:35:29+00:00'
                ]
            ]
        ];

        $url = $this->urlMapper->fromRecord($record);

        $this->assertEquals($record['id'], $url->getId()->toString());
        $this->assertEquals($record['url'], $url->getUrl());
        $this->assertEquals($record['createdAt'], $url->getCreatedAt()->format(\DateTimeImmutable::ATOM));
        $this->assertNotEmpty($url->getAliases());
        $this->assertEquals($record['aliases'][0]['alias'], $url->getAliases()[0]->getAlias());
        $this->assertEquals(
            $record['aliases'][0]['createdAt'],
            $url->getAliases()[0]->getCreatedAt()->format(\DateTimeImmutable::ATOM)
        );
    }

    public function testShouldBeAbleToCreateUrlFromRequest(): void
    {
        $request = [
            'url' => 'example-url',
            'alias' => 'example-alias'
        ];

        $url = $this->urlMapper->fromRequest($request);

        $this->assertNotNull($url->getId());
        $this->assertEquals($request['url'], $url->getUrl());
        $this->assertNotNull($url->getCreatedAt());
        $this->assertNotEmpty($url->getAliases());
        $this->assertEquals($request['alias'], $url->getAliases()[0]->getAlias());
        $this->assertNotNull($url->getAliases()[0]->getCreatedAt());
    }

    public function testShouldBeAbleToUpdateByAddingJustAliasToExistingUrlFromRequest(): void
    {
        $aliasBuilder = new AliasBuilder();

        $alias = $aliasBuilder->alias('test')
            ->createdAt(new DateTimeImmutable('1992-02-04'))
            ->build();

        $urlBuilder = new UrlBuilder();
        $url = $urlBuilder->id('72afee7e-4692-4a4f-a7e9-3b8bd0e34ee8')
            ->url('https://marcossegovia.me/')
            ->aliases([$alias])
            ->createdAt(new DateTimeImmutable('1992-02-04'))
            ->build();

        $request = [
            'url' => 'example-url',
            'alias' => 'example-alias'
        ];

        $url = $this->urlMapper->update($url, $request);

        $this->assertNotNull('72afee7e-4692-4a4f-a7e9-3b8bd0e34ee8', $url->getId());
        $this->assertEquals('https://marcossegovia.me/', $url->getUrl());
        $this->assertNotNull($url->getCreatedAt());
        $this->assertCount(2, $url->getAliases());
        $this->assertEquals($alias->getAlias(), $url->getAliases()[0]->getAlias());
        $this->assertEquals($request['alias'], $url->getAliases()[1]->getAlias());
        $this->assertNotNull($url->getAliases()[0]->getCreatedAt());
        $this->assertNotNull($url->getAliases()[1]->getCreatedAt());
    }
}
