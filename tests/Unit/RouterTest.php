<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    public function testRouterClassExists(): void
    {
        $this->assertTrue(class_exists('Router'));
    }

    public function testRouterCanBeInstantiated(): void
    {
        $router = new \Router();
        $this->assertInstanceOf('Router', $router);
    }

    public function testAddRoute(): void
    {
        $router = new \Router();
        $router->add('/test', 'TestController', 'testMethod');
        
        // Route is added internally, we can't directly access it
        // This test ensures the method doesn't throw an error
        $this->assertTrue(true);
    }

    public function testDispatchNonExistentRoute(): void
    {
        $router = new \Router();
        $this->expectOutputString('404 Not Found: Route /nonexistent not found');
        $router->dispatch('/nonexistent');
    }

    public function testDispatchWithQueryString(): void
    {
        $router = new \Router();
        $router->add('/test', 'NonExistentController', 'index');
        
        // Test that query string is properly stripped
        $this->expectOutputString('404 Not Found: Controller NonExistentController not found');
        $router->dispatch('/test?param=value');
    }

    public function testDispatchRootRoute(): void
    {
        $router = new \Router();
        $router->add('/', 'NonExistentController', 'index');
        
        $this->expectOutputString('404 Not Found: Controller NonExistentController not found');
        $router->dispatch('/');
    }
}
