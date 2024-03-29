<?php

namespace Tests\Domain\Application\Api;

use App\Domain\Service\UrlMapper;
use App\Infrastructure\Repository\InMemoryUrlRepository;
use App\Infrastructure\UrlRepositoryInterface;
use DI\Container;
use Psr\Container\ContainerInterface;
use Tests\IntegrationTestCase;

use function DI\factory;

class UrlControllerIntegrationTest extends IntegrationTestCase
{
    public function setUp(): void
    {
        // Clean up urls before any test
        file_put_contents(__DIR__ . '/../../../data/urls.json', json_encode([], JSON_PRETTY_PRINT));
    }

    /**
     * @throws \Exception
     */
    public function testGetEndpoint_happyPath()
    {
        $app = $this->getAppInstance();

        /** @var Container $container */
        $container = $app->getContainer();

        // We make sure there is a previous record data beforehand
        $urls = json_decode(file_get_contents(__DIR__ . '/../../../data/url_records.json'), true);
        file_put_contents(__DIR__ . '/../../../data/urls.json', json_encode($urls, JSON_PRETTY_PRINT));

        // We override the repository to write into test location
        $container->set(UrlRepositoryInterface::class, factory(function () use ($container) {
            return new InMemoryUrlRepository($container->get(UrlMapper::class), 'test');
        }));

        $request = $this->createRequest('GET', '/example-alias');
        $response = $app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(json_encode(['url' => 'example-url'], JSON_PRETTY_PRINT), $response->getBody());
    }

    /**
     * @throws \Exception
     */
    public function testGetEndpoint_notFound()
    {
        $app = $this->getAppInstance();

        /** @var Container $container */
        $container = $app->getContainer();

        // We override the repository to write into test location
        $container->set(UrlRepositoryInterface::class, factory(function (ContainerInterface $container) {
            return new InMemoryUrlRepository($container->get(UrlMapper::class), 'test');
        }));

        $request = $this->createRequest('GET', '/example-alias');
        $response = $app->handle($request);

        $payload = (string)$response->getBody();
        $expectedBodyResponse = json_encode(['message' => 'There is no url registered for given alias example-alias'],
            JSON_PRETTY_PRINT);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals($expectedBodyResponse, $payload);
    }

    /**
     * @throws \Exception
     */
    public function testPostEndpoint_createHappyPath()
    {
        $app = $this->getAppInstance();

        /** @var Container $container */
        $container = $app->getContainer();

        // We override the repository to write into test location
        $container->set(UrlRepositoryInterface::class, factory(function (ContainerInterface $container) {
            return new InMemoryUrlRepository($container->get(UrlMapper::class), 'test');
        }));

        $request = $this->createRequest('POST', '/url');
        $request->getBody()->write(
            file_get_contents(__DIR__ . '/../../../data/valid_post_new_url_request.json')
        );
        $response = $app->handle($request);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEmpty($response->getBody()->getContents());

        $request = $this->createRequest('GET', '/marketing-material-01');
        $response = $app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(json_encode(['url' => 'complex-frontify-domain.com/complex-url/1'], JSON_PRETTY_PRINT),
            $response->getBody());
    }

    /**
     * @throws \Exception
     */
    public function testPostEndpoint_updateHappyPath()
    {
        $app = $this->getAppInstance();

        /** @var Container $container */
        $container = $app->getContainer();

        // We make sure there is a previous record data beforehand
        $urls = json_decode(file_get_contents(__DIR__ . '/../../../data/url_records.json'), true);
        file_put_contents(__DIR__ . '/../../../data/urls.json', json_encode($urls, JSON_PRETTY_PRINT));

        // We override the repository to write into test location
        $container->set(UrlRepositoryInterface::class, factory(function (ContainerInterface $container) {
            return new InMemoryUrlRepository($container->get(UrlMapper::class), 'test');
        }));

        $request = $this->createRequest('POST', '/url');
        $request->getBody()->write(
            file_get_contents(__DIR__ . '/../../../data/valid_post_update_url_request.json')
        );
        $response = $app->handle($request);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEmpty($response->getBody()->getContents());

        $request = $this->createRequest('GET', '/example-alias2');
        $response = $app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(json_encode(['url' => 'example-url'], JSON_PRETTY_PRINT),
            $response->getBody());
    }

    /**
     * @throws \Exception
     */
    public function testPostEndpoint_validationException()
    {
        $app = $this->getAppInstance();

        /** @var Container $container */
        $container = $app->getContainer();

        // We override the repository to write into test location
        $container->set(UrlRepositoryInterface::class, factory(function (ContainerInterface $container) {
            return new InMemoryUrlRepository($container->get(UrlMapper::class), 'test');
        }));

        $request = $this->createRequest('POST', '/url');
        $request->getBody()->write(
            file_get_contents(__DIR__ . '/../../../data/invalid_post_url_request.json')
        );
        $response = $app->handle($request);
        $this->assertEquals(403, $response->getStatusCode());
        $this->assertEquals(
            json_encode(
                [
                    'message' => 'There was validation errors during the request',
                    'errors' => ['`alias` field needs to be set and not be empty']
                ],
                JSON_PRETTY_PRINT
            ),
            $response->getBody()
        );
    }
}