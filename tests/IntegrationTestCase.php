<?php

declare(strict_types=1);

namespace Tests;

use App\Application\Api\ErrorHandleMiddleware;
use DI\ContainerBuilder;
use Exception;
use PHPUnit\Framework\TestCase as PHPUnit_TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Request as SlimRequest;
use Slim\Psr7\Uri;

class IntegrationTestCase extends PHPUnit_TestCase
{
    use ProphecyTrait;

    /**
     * @return App
     * @throws Exception
     */
    protected function getAppInstance(): App
    {
        // Instantiate DI and set up dependencies
        $containerBuilder = new ContainerBuilder();
        $dependencies = require __DIR__ . '/../app/dependencies.php';
        $dependencies($containerBuilder);
        $container = $containerBuilder->build();

        // Instantiate the app
        AppFactory::setContainer($container);
        $app = AppFactory::create();

        // Register routes
        $routes = require __DIR__ . '/../app/routes.php';
        $routes($app);

        $errorHandlerMiddleware = new ErrorHandleMiddleware($container->get(LoggerInterface::class));
        $app->addMiddleware($errorHandlerMiddleware);

        return $app;
    }

    /**
     * @param string $method
     * @param string $path
     * @param array $headers
     * @param array $cookies
     * @param array $serverParams
     * @return Request
     */
    protected function createRequest(
        string $method,
        string $path,
        array $headers = ['HTTP_ACCEPT' => 'application/json'],
        array $cookies = [],
        array $serverParams = []
    ): Request {
        $uri = new Uri('', '', 80, $path);
        $handle = fopen('php://temp', 'w+');
        $stream = (new StreamFactory())->createStreamFromResource($handle);

        $h = new Headers();
        foreach ($headers as $name => $value) {
            $h->addHeader($name, $value);
        }

        return new SlimRequest($method, $uri, $h, $cookies, $serverParams, $stream);
    }
}
