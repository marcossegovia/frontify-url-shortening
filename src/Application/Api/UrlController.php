<?php

namespace App\Application\Api;

use App\Domain\Exception\AlreadyUsedAliasException;
use App\Domain\Exception\RequestValidationException;
use App\Domain\Exception\UrlNotFoundException;
use App\Domain\Service\UrlService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Response;

class UrlController
{
    private UrlService $urlService;

    public function __construct(UrlService $urlService)
    {
        $this->urlService = $urlService;
    }

    /**
     * @throws UrlNotFoundException
     * @throws RequestValidationException
     */
    public function get(Request $request, array $args): Response
    {
        $this->validateGetParameters($args);
        $url = $this->urlService->getUrlFromAlias($args['alias']);
        $response = new Response();
        $response->getBody()->write(json_encode(['url' => $url->getUrl()], JSON_PRETTY_PRINT));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    /**
     * @throws RequestValidationException
     * @throws AlreadyUsedAliasException
     */
    public function post(Request $request): Response
    {
        $requestBody = json_decode($request->getBody(), true);
        $this->validatePostBody($requestBody);
        $this->urlService->process($requestBody);
        $response = new Response();
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(201);
    }

    /**
     * @throws RequestValidationException
     */
    private function validateGetParameters(array $args): void
    {
        $errors = [];
        if (!isset($args['alias']) || $args['alias'] === '') {
            $errors[] = 'An alias needs to be provided';
        }

        if (!empty($errors)) {
            throw new RequestValidationException($errors);
        }
    }

    /**
     * @throws RequestValidationException
     */
    private function validatePostBody(array $requestBody): void
    {
        $errors = [];
        if (!isset($requestBody['url']) || $requestBody['url'] === '') {
            $errors[] = '`url` field needs to be set and not be empty';
        }
        if (!isset($requestBody['alias']) || $requestBody['alias'] === '') {
            $errors[] = '`alias` field needs to be set and not be empty';
        }

        if (!empty($errors)) {
            throw new RequestValidationException($errors);
        }
    }
}
