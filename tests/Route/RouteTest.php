<?php

use MistyRouting\Route\Route;
use MistyRouting\ControllerActionParams;

class RouteTest extends MistyTesting\UnitTest
{
    /**
     * @expectedException MistyRouting\Exception\MissingParamException
     */
    public function testEncoding_missingParams()
    {
        $route = new Route('name', '/:module/:page/id/:id', 'Controller', 'action');
        $route->encode(array(
            'module' => 'news',
            'id' => 1
        ));
    }

    /**
     * @expectedException MistyRouting\Exception\InvalidParamException
     */
    public function testEncoding_tooManyParams()
    {
        $route = new Route('name', '/:module/:page', 'Controller', 'action');
        $this->assertEquals('/news/view', $route->encode(array(
            'module' => 'news',
            'page' => 'view',
            'var' => 'value',
        )));
    }

    public function testEncoding()
    {
        $route = new Route('name', '/:module/:page', 'Controller', 'action');
        $this->assertEquals('/news/view', $route->encode(array(
            'module' => 'news',
            'page' => 'view'
        )));

        $route = new Route('name', '/:module/:page/:*', 'Controller', 'action');
        $this->assertEquals('/news/view/var1/value1/var2/value2', $route->encode(array(
            'module' => 'news',
            'page' => 'view',
            'var1' => 'value1',
            'var2' => 'value2',
        )));
    }

    /**
     * @expectedException MistyRouting\Exception\InvalidParamException
     */
    public function testEncoding_illegalChoice()
    {
        $route = new Route('name', '/:module/:page[archive|view]', 'Controller', 'action');
        $this->assertEquals('/news/view', $route->encode(array(
            'module' => 'news',
            'page' => 'comments',
        )));
    }

    public function testEncoding_choices()
    {
        $route = new Route('name', '/:module/:page[archive|view]', 'Controller', 'action');
        $this->assertEquals('/news/archive', $route->encode(array(
            'module' => 'news',
            'page' => 'archive',
        )));

        $route = new Route('name', '/:module/:page[archive|view]/:*', 'Controller', 'action');
        $this->assertEquals('/news/view/var1/value1/var2/value2', $route->encode(array(
            'module' => 'news',
            'page' => 'view',
            'var1' => 'value1',
            'var2' => 'value2',
        )));
    }

    public function testEncoding_urlencoded()
    {
        $route = new Route('name', '/:var/:value', 'Controller', 'action');
        $this->assertEquals('/this+value/%2Fmust-be-encoded', $route->encode(array(
            'var' => 'this value',
            'value' => '/must-be-encoded',
        )));
    }

    public function testDecode_fixedTokens()
    {
        $route = new Route('name', '/module/action', 'Controller', 'action');
        $this->assertNotNull($route->decode('/module/action'));
    }

    public function testDecode_variableTokens()
    {
        $route = new Route('name', '/:module/:action', 'Controller', 'action');
        $this->assertNotNull($route->decode('/news/view'));
    }

    public function testDecode_withVarargs()
    {
        $route = new Route('name', '/:module/:action/:*', 'Controller', 'action');
        $cap = $route->decode('/news/view/id/1');
        $this->assertNotNull($cap);
        $this->assertEquals(array(
            'module' => 'news',
            'action' => 'view',
            'id' => '1',
        ), $cap->params);
    }

    public function testDecode_noVarargs()
    {
        $route = new Route('name', '/:module/:action', 'Controller', 'action');
        $this->assertNull($route->decode('/news/view/id/1'));
    }

    public function testDecode_withSubstitution()
    {
        $route = new Route('name', '/:module/:uid/:page', ':module\Controller\:pageController', ':page');
        $cap = $route->decode('/test/user/profile');

        $this->assertNotNull($cap);
        $this->assertTrue($cap instanceof ControllerActionParams);
        $this->assertEquals('Test\Controller\ProfileController',  $cap->controller );
        $this->assertEquals('profile', $cap->action);
        $this->assertEquals(3, count($cap->params));
        $this->assertEquals('user', $cap->params['uid']);
        $this->assertEquals('profile', $cap->params['page']);
    }

    public function testDecode_invalidChoice()
    {
        $route = new Route('name', '/:module/:uid/:page[profile|pictures]', ':module\Controller\:pageController', ':page');
        $this->assertNull($route->decode('/test/user/timeline'));
    }

    public function testDecode_urldecoded()
    {
        $route = new Route('name', '/:var/:value', 'Controller', 'action');
        $cap = $route->decode('/this+value/%2Fmust-be-decoded');

        $this->assertNotNull($cap);
        $this->assertTrue($cap instanceof ControllerActionParams);
        $this->assertEquals(2, count($cap->params));
        $this->assertEquals('this value', $cap->params['var']);
        $this->assertEquals('/must-be-decoded', $cap->params['value']);
    }
}
