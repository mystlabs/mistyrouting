<?php

use MistyRouting\PathDecorator;

class PathDecoratorTest extends MistyTesting\UnitTest
{
    public function testAnchor()
    {
        $decorator = new PathDecorator(array(
            'hostname' => 'www.test.it',
        ));
        $url = $decorator->decorate('/news', array(
            'anchor' => 'testanchor'
        ));

        $this->assertEquals('/news#testanchor', $url);
    }

    public function testEntryPoint()
    {
        $decorator = new PathDecorator(array(
            'hostname' => 'www.test.it',
        ));
        $url = $decorator->decorate('/news', array(
            'entryPoint' => 'index.php?q='
        ));

        $this->assertEquals('/index.php?q=/news', $url);
    }

    public function testAbsolute()
    {
        $decorator = new PathDecorator(array(
            'hostname' => 'www.test.it',
        ));

        $this->assertEquals('/news', $decorator->decorate('/news', array()));
        $this->assertEquals('/news', $decorator->decorate('/news', array('absolute' => false)));
        $this->assertEquals('http://www.test.it/news', $decorator->decorate('/news', array('absolute' => true)));
        $this->assertEquals('https://www.test.it/news', $decorator->decorate('/news', array(
            'absolute' => true,
            'secure' => true
        )));
    }

    public function testHostname()
    {
        $decorator = new PathDecorator(array(
            'hostname' => 'www.test.it',
            'absolute' => true
        ));

        $this->assertEquals('http://www.test.it/news', $decorator->decorate('/news', array()));
        $this->assertEquals('http://test.org/news', $decorator->decorate('/news', array('hostname' => 'test.org')));
    }

    public function testPort()
    {
        $decorator = new PathDecorator(array(
            'hostname' => 'www.test.it',
            'absolute' => true,
            'port' => 8080
        ));

        $this->assertEquals('http://www.test.it:8080/news', $decorator->decorate('/news'));
        $this->assertEquals('http://www.test.it:8090/news', $decorator->decorate('/news', array('port' => '8090')));
    }

    public function testAutomaticAbsolute()
    {
        $decorator = new PathDecorator(array(
            'hostname' => 'www.test.it',
        ));

        $this->assertEquals('/news', $decorator->decorate('/news'));
        $this->assertEquals('http://www.test.org/news', $decorator->decorate('/news', array('hostname' => 'www.test.org')));
        $this->assertEquals('http://www.test.it:8080/news', $decorator->decorate('/news', array('port' => 8080)));
        $this->assertEquals('https://www.test.it/news', $decorator->decorate('/news', array('secure' => true)));
    }
}
