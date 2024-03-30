<?php

namespace Tests\Domain\Service;

use App\Domain\Exception\AlreadyUsedAliasException;
use App\Domain\Exception\UrlNotFoundException;
use App\Domain\Model\Url;
use App\Domain\Service\UrlMapper;
use App\Domain\Service\UrlService;
use App\Infrastructure\UrlRepositoryInterface;
use PHPUnit\Framework\TestCase;

class UrlServiceTest extends TestCase
{
    private UrlMapper $urlMapper;
    private UrlRepositoryInterface $urlRepository;
    private UrlService $urlService;

    public function setUp(): void
    {
        $this->urlMapper = $this->getMockBuilder(UrlMapper::class)->disableOriginalConstructor()->getMock();
        $this->urlRepository = $this->getMockBuilder(UrlRepositoryInterface::class)->disableOriginalConstructor(
        )->getMock();
        $this->urlService = new UrlService($this->urlMapper, $this->urlRepository);
    }

    /**
     * @throws UrlNotFoundException
     */
    public function testShouldBeAbleToGetUrlFromAlias_happyPath(): void
    {
        $mockedUrl = $this->getMockBuilder(Url::class)->getMock();
        $this->urlRepository->expects($this->once())->method('findByAlias')->willReturn($mockedUrl);

        $url = $this->urlService->getUrlFromAlias('example-alias');

        $this->assertNotNull($url);
        $this->assertInstanceOf(Url::class, $url);
    }

    /**
     * @throws AlreadyUsedAliasException
     */
    public function testShouldBeAbleToCreateUrl_happyPath(): void
    {
        $request = [
            'url' => 'example-url',
            'alias' => 'example-alias'
        ];
        $mockedUrl = $this->getMockBuilder(Url::class)->getMock();
        $this->urlRepository->expects($this->once())->method('findByAlias')->willReturn(null);
        $this->urlRepository->expects($this->once())->method('findByUrl')->willReturn(null);
        $this->urlMapper->expects($this->once())->method('fromRequest')->with($request)->willReturn($mockedUrl);
        $this->urlRepository->expects($this->once())->method('persist')->with($mockedUrl);

        $this->urlService->process($request);
    }

    /**
     * @throws AlreadyUsedAliasException
     */
    public function testShouldBeAbleToUpdateUrl_happyPath(): void
    {
        $request = [
            'url' => 'example-url',
            'alias' => 'example-alias'
        ];
        $mockedUrl = $this->getMockBuilder(Url::class)->getMock();
        $this->urlRepository->expects($this->once())->method('findByAlias')->willReturn(null);
        $this->urlRepository->expects($this->once())->method('findByUrl')->willReturn($mockedUrl);
        $this->urlMapper->expects($this->once())->method('update')->with($mockedUrl, $request)->willReturn($mockedUrl);
        $this->urlRepository->expects($this->once())->method('persist')->with($mockedUrl);

        $this->urlService->process($request);
    }


    /**
     * @throws UrlNotFoundException
     */
    public function testShouldBeAbleToGetUrlFromAlias_throwNotFoundException(): void
    {
        $this->urlRepository->expects($this->once())->method('findByAlias')->willReturn(null);
        $this->expectException(UrlNotFoundException::class);
        $this->expectExceptionMessage('There is no url registered for given alias example-alias');

        $this->urlService->getUrlFromAlias('example-alias');
    }

    /**
     * @throws AlreadyUsedAliasException
     */
    public function testShouldBeAbleToCreateUrl_throwAlreadyUsedAliasException(): void
    {
        $request = [
            'url' => 'example-url',
            'alias' => 'example-alias'
        ];
        $mockedUrl = $this->getMockBuilder(Url::class)->getMock();
        $this->urlRepository->expects($this->once())->method('findByAlias')->willReturn($mockedUrl);
        $this->urlRepository->expects($this->never())->method('findByUrl');
        $this->urlRepository->expects($this->never())->method('persist');

        $this->expectException(AlreadyUsedAliasException::class);
        $this->expectExceptionMessage('There is a url already registered for given alias example-alias');

        $this->urlService->process($request);
    }
}
