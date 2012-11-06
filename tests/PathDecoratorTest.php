<?php

use MistyRouting\PathDecorator;

class PathDecoratorTest extends MistyTesting\UnitTest
{
    public function testAnchor()
    {
        $decorator = new PathDecorator('www.test.it');
        $url = $decorator->decorate('/news', array(
            'anchor' => 'testanchor'
        ));

        $this->assertEquals('/news#testanchor', $url);
    }

    public function testEntryPoint()
    {
        $decorator = new PathDecorator('www.test.it');
        $url = $decorator->decorate('/news', array(
            'entryPoint' => 'index.php?q='
        ));

        $this->assertEquals('/index.php?q=/news', $url);
    }

    public function testAbsolute()
    {
        $decorator = new PathDecorator('www.test.it');

        $this->assertEquals('/news', $decorator->decorate('/news', array()));
        $this->assertEquals('/news', $decorator->decorate('/news', array('absolute' => false)));
        $this->assertEquals('//www.test.it/news', $decorator->decorate('/news', array('absolute' => true)));
        $this->assertEquals('https://www.test.it/news', $decorator->decorate('/news', array(
            'absolute' => true,
            'secure' => true
        )));
    }

    public function testHostname()
    {
        $decorator = new PathDecorator('www.test.it', array(
            'absolute' => true
        ));

        $this->assertEquals('//www.test.it/news', $decorator->decorate('/news', array()));
        $this->assertEquals('//test.org/news', $decorator->decorate('/news', array('hostname' => 'test.org')));
    }

    public function testPort()
    {
        $decorator = new PathDecorator('www.test.it', array(
            'absolute' => true,
            'port' => 8080
        ));

        $this->assertEquals('//www.test.it:8080/news', $decorator->decorate('/news'));
        $this->assertEquals('//www.test.it:8090/news', $decorator->decorate('/news', array('port' => '8090')));
    }

    public function testAutomaticAbsolute()
    {
        $decorator = new PathDecorator('www.test.it');

        $this->assertEquals('/news', $decorator->decorate('/news'));
        $this->assertEquals('//www.test.org/news', $decorator->decorate('/news', array('hostname' => 'www.test.org')));
        $this->assertEquals('//www.test.it:8080/news', $decorator->decorate('/news', array('port' => 8080)));
        $this->assertEquals('https://www.test.it/news', $decorator->decorate('/news', array('secure' => true)));
    }
}
