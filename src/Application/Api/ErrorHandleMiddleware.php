<?php

namespace App\Application\Api;

use App\Domain\Exception\AlreadyUsedAliasException;
use App\Domain\Exception\RequestValidationException;
use App\Domain\Exception\UrlNotFoundException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;
use Throwable;

class ErrorHandleMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (RequestValidationException $e) {
            return $this->handleValidationException($request, $e);
        } catch (AlreadyUsedAliasException $e) {
            return $this->handleAlreadyUsedAliasException($request, $e);
        } catch (UrlNotFoundException $e) {
            return $this->handleNotFoundException($request, $e);
        } catch (Throwable $e) {
            return $this->handleFallbackException($request, $e);
        }
    }

    private function handleValidationException(
        ServerRequestInterface $request,
        RequestValidationException $e
    ): ResponseInterface {
        $response = new Response();
        $response->getBody()->write(
            json_encode(['message' => $e->getMessage(), 'errors' => $e->getErrors()], JSON_PRETTY_PRINT)
        );
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(403);
    }

    private function handleAlreadyUsedAliasException(
        ServerRequestInterface $request,
        \Exception|AlreadyUsedAliasException $e
    ): ResponseInterface {
        $response = new Response();
        $response->getBody()->write(
            json_encode(['message' => $e->getMessage()], JSON_PRETTY_PRINT)
        );
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(403);
    }

    private function handleNotFoundException(
        ServerRequestInterface $request,
        UrlNotFoundException $e
    ): ResponseInterface {
        $response = new Response();
        $response->getBody()->write(
            json_encode(['message' => $e->getMessage()], JSON_PRETTY_PRINT)
        );
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(404);
    }

    private function handleFallbackException(ServerRequestInterface $request, Throwable $e): ResponseInterface
    {
        $response = new Response();
        $response->getBody()->write(
            json_encode(['message' => $e->getMessage()], JSON_PRETTY_PRINT)
        );
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(500);
    }
}
