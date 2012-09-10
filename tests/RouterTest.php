<?php

use MistyRouting\Router;
use MistyRouting\Route\ExactMatchRoute;

class RouterTest extends MistyTesting\UnitTest
{
    /**
     * @expectedException MistyRouting\Exception\DuplicateRouteException
     */
    public function testDuplicateRoute()
    {
        new Router(array(
            new ExactMatchRoute('homepage', '/', 'Controller', 'Action'),
            new ExactMatchRoute('homepage', '/homepage', 'Controller', 'Action'),
        ));
    }

    /**
     * @expectedException MistyRouting\Exception\MalformedPathException
     */
    public function testPathStartsWithSlash()
    {
        $router = new Router(array());
        $router->decode('path-to-something');
    }

    /**
     * @expectedException MistyRouting\Exception\InvalidPathException
     */
    public function testCantDecode()
    {
        $router = new Router(array());
        $router->decode('/');
    }

    public function testDecodeOrdering()
    {
        $router = new Router(array(
            new ExactMatchRoute('rule1', '/', 'Controller1', 'Action'),
            new ExactMatchRoute('rule2', '/', 'Controller2', 'Action'),
        ));

        $controllerActionParams = $router->decode('/');
        $this->assertEquals('Controller1', $controllerActionParams->controller);
        $this->assertEquals('Action', $controllerActionParams->action);
    }

    public function testEncode()
    {
        $router = new Router(array(
            new ExactMatchRoute('route1', '/', 'Controller', 'Action'),
            new ExactMatchRoute('route2', '/news', 'Controller', 'Action'),
        ));

        $this->assertEquals('/', $router->encode('route1', array()));
        $this->assertEquals('/news', $router->encode('route2', array()));
    }

    /**
     * @expectedException MistyRouting\Exception\UnknownRouteException
     */
    public function testEncodeUnknownRoute()
    {
        $router = new Router(array());
        $router->encode('unknown-route', array());
    }

    /**
     * @expectedException MistyRouting\Exception\MalformedPathException
     */
    public function testEncodeRouteReturnBadUrl()
    {
        $router = new Router(array(
            new ExactMatchRoute('route1', 'news', 'Controller', 'Action'),
        ));

        $router->encode('route1', array());
    }
}
