<?php

use MistyRouting\Urlifier;

class UrlifierTest extends MistyTesting\UnitTest
{
    public function testAnchor()
    {
        $decorator = new Urlifier('www.test.it');
        $url = $decorator->urlify('/news', array(
            'anchor' => 'testanchor'
        ));

        $this->assertEquals('/news#testanchor', $url);
    }

    public function testEntryPoint()
    {
        $decorator = new Urlifier('www.test.it');
        $url = $decorator->urlify('/news', array(
            'entryPoint' => 'index.php?q='
        ));

        $this->assertEquals('/index.php?q=/news', $url);
    }

    public function testAbsolute()
    {
        $decorator = new Urlifier('www.test.it');

        $this->assertEquals('/news', $decorator->urlify('/news', array()));
        $this->assertEquals('/news', $decorator->urlify('/news', array('absolute' => false)));
        $this->assertEquals('//www.test.it/news', $decorator->urlify('/news', array('absolute' => true)));
        $this->assertEquals('https://www.test.it/news', $decorator->urlify('/news', array(
            'absolute' => true,
            'secure' => true
        )));
    }

    public function testHostname()
    {
        $decorator = new Urlifier('www.test.it', array(
            'absolute' => true
        ));

        $this->assertEquals('//www.test.it/news', $decorator->urlify('/news', array()));
        $this->assertEquals('//test.org/news', $decorator->urlify('/news', array('hostname' => 'test.org')));
    }

    public function testPort()
    {
        $decorator = new Urlifier('www.test.it', array(
            'absolute' => true,
            'port' => 8080
        ));

        $this->assertEquals('//www.test.it:8080/news', $decorator->urlify('/news'));
        $this->assertEquals('//www.test.it:8090/news', $decorator->urlify('/news', array('port' => '8090')));
    }
}
