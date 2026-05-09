<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Router;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    public function testRouterClassExistsUnderBothNamespaces(): void
    {
        // Composer autoload finds the canonical class…
        $this->assertTrue(class_exists(Router::class));
        // …and the legacy alias still resolves for older tooling.
        $this->assertTrue(class_exists('Router'));
    }

    public function testRouterCanBeInstantiated(): void
    {
        $router = new Router();
        $this->assertInstanceOf(Router::class, $router);
    }

    public function testAddRouteAcceptsHttpVerb(): void
    {
        $router = new Router();
        $router->add('GET', '/test', 'TestController', 'testMethod');
        // No exception means registration succeeded.
        $this->assertTrue(true);
    }

    public function testDispatchUnknownRouteReturns404(): void
    {
        $router = new Router();
        $this->expectOutputString('404 Not Found: Route /nonexistent not found');
        $router->dispatch('/nonexistent', 'GET');
    }

    public function testDispatchStripsQueryString(): void
    {
        $router = new Router();
        $router->add('GET', '/test', 'NonExistentController', 'index');

        $this->expectOutputString('404 Not Found: Controller NonExistentController not found');
        $router->dispatch('/test?param=value', 'GET');
    }

    public function testDispatchRootRoute(): void
    {
        $router = new Router();
        $router->add('GET', '/', 'NonExistentController', 'index');

        $this->expectOutputString('404 Not Found: Controller NonExistentController not found');
        $router->dispatch('/', 'GET');
    }

    public function testMethodMismatchReturns404(): void
    {
        $router = new Router();
        $router->add('POST', '/api/foo', 'NonExistentController', 'create');

        $this->expectOutputString('404 Not Found: Route /api/foo not found');
        $router->dispatch('/api/foo', 'GET');
    }

    public function testAnyMethodMatchesAllVerbs(): void
    {
        $router = new Router();
        $router->add('ANY', '/wild', 'NonExistentController', 'index');

        $this->expectOutputString('404 Not Found: Controller NonExistentController not found');
        $router->dispatch('/wild', 'DELETE');
    }
}
