<?php

namespace App\Domain\Service;

use App\Domain\Exception\AlreadyUsedAliasException;
use App\Domain\Model\Url;
use App\Domain\Exception\UrlNotFoundException;
use App\Infrastructure\UrlRepositoryInterface;

class UrlService
{
    private UrlMapper $urlMapper;

    private UrlRepositoryInterface $urlRepository;

    public function __construct(UrlMapper $urlMapper, UrlRepositoryInterface $urlRepository)
    {
        $this->urlMapper = $urlMapper;
        $this->urlRepository = $urlRepository;
    }

    /**
     * @throws UrlNotFoundException
     */
    public function getUrlFromAlias(string $alias): Url
    {
        $result = $this->urlRepository->findByAlias($alias);
        if ($result === null) {
            throw new UrlNotFoundException($alias);
        }
        return $result;
    }

    /**
     * @throws AlreadyUsedAliasException
     */
    public function process(array $request): void
    {
        $existingUrlFromAlias = $this->urlRepository->findByAlias($request['alias']);
        if ($existingUrlFromAlias !== null) {
            throw new AlreadyUsedAliasException($request['alias']);
        }

        $existingUrlFromUrl = $this->urlRepository->findByUrl($request['url']);
        $existingUrlFromUrl === null ? $this->createUrl($request) : $this->updateUrl($existingUrlFromUrl, $request);
    }

    private function createUrl(array $request): void
    {
        $url = $this->urlMapper->fromRequest($request);
        $this->urlRepository->persist($url);
    }

    private function updateUrl(Url $existingUrl, array $request): void
    {
        $existingUrl = $this->urlMapper->update($existingUrl, $request);
        $this->urlRepository->persist($existingUrl);
    }
}
