<?php

declare(strict_types=1);

use App\Application\Api\UrlController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

return function (App $app) {
    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello Frontify!');
        return $response;
    });

    $app->get('/{alias}', function(Request $request, Response $response, array $args) use ($app) {
        $urlController = $app->getContainer()->get(UrlController::class);
        return $urlController->get($request, $args);
    });

    $app->post('/url', function(Request $request) use ($app) {
        $urlController = $app->getContainer()->get(UrlController::class);
        return $urlController->post($request);
    });
};
