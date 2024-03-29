<?php

namespace Tests\Domain\Service;

use App\Domain\Service\AliasBuilder;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class AliasBuilderTest extends TestCase
{
    private AliasBuilder $aliasBuilder;

    public function setUp(): void
    {
        $this->aliasBuilder = new AliasBuilder();
    }

    public function testShouldBeAbleToGenerateNewAlias(): void
    {
        $alias = $this->aliasBuilder->alias('test')
            ->createdAt(new DateTimeImmutable('1992-02-04'))
            ->build();

        $this->assertEquals('test', $alias->getAlias());
        $this->assertNotNull($alias->getCreatedAt());
    }
}
