<?php

use MistyRouting\Route\ExactMatchRoute;

class ExactMatchRouteTest extends MistyTesting\UnitTest
{
    public function testExactMatch()
    {
        $route = new ExactMatchRoute('homepage', '/', 'Controller', 'Action');
    }

    public function testMatchesOnlyExactPath()
    {
        $route = new ExactMatchRoute('homepage', '/', 'Controller', 'Action');

        $this->assertNotNull($route->decode('/'));
        $this->assertNull($route->decode('/homepage'));
        $this->assertNull($route->decode(''));
    }

    public function testEncoding()
    {
        $route = new ExactMatchRoute('homepage', '/', 'Controller', 'Action');

        $this->assertEquals('/', $route->encode(array()));
    }

    /**
     * @expectedException MistyRouting\Exception\InvalidParamException
     */
    public function testCannotAcceptParams()
    {
        $route = new ExactMatchRoute('homepage', '/', 'Controller', 'Action');

        $this->assertEquals('/', $route->encode(array(
            'var' => 'value'
        )));
    }
}
