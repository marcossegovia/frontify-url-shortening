<?php

declare(strict_types=1);

use App\Application\Api\ErrorHandleMiddleware;
use DI\ContainerBuilder;
use Psr\Log\LoggerInterface;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

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

// Run the app
$app->run();
