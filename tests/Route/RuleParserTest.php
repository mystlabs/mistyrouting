<?php

use MistyRouting\Route\RuleParser;

class RuleParserTest extends MistyTesting\UnitTest
{
    /**
     * @expectedException MistyRouting\Exception\MalformedRuleException
     */
    public function testMalformedToken()
    {
        RuleParser::parseToken(':news:id');
    }

    public function testFixedToken()
    {
        $component = RuleParser::parseToken('news');
        $this->checkComponent($component, 'news', false);
    }

    public function testVariableToken()
    {
        $component = RuleParser::parseToken(':news');
        $this->checkComponent($component, 'news', true);
    }

    public function testVariableTokenWithChoices()
    {
        $component = RuleParser::parseToken(':news[ab|cd]');
        $this->checkComponent($component, 'news', true, array('ab', 'cd'));
    }

    public function testParseRule()
    {
        $rule = RuleParser::parseRule('/news/:seo-title/:date/:id/comments/:*');
        $this->assertEquals(5, count($rule['components']));
        $this->assertFalse($rule['components'][0]['isVar']);
        $this->assertTrue($rule['components'][1]['isVar']);
        $this->assertTrue($rule['components'][2]['isVar']);
        $this->assertTrue($rule['components'][3]['isVar']);
        $this->assertFalse($rule['components'][4]['isVar']);
        $this->assertTrue($rule['varargs']);

        $rule = RuleParser::parseRule('/news/:title');
        $this->assertEquals(2, count($rule['components']));
        $this->assertFalse($rule['components'][0]['isVar']);
        $this->assertTrue($rule['components'][1]['isVar']);
        $this->assertFalse($rule['varargs']);
    }

    /**
     * @expectedException MistyRouting\Exception\MalformedRuleException
     */
    public function testParseRule_catchAllNotAtTheEnd()
    {
        RuleParser::parseRule('/news/:*/:id');
    }

    private function checkComponent($component, $name, $isVar, $choices = null)
    {
        $this->assertNotNull($component);
        $this->assertTrue(is_array($component));
        $this->assertEquals($name, $component['name']);
        $this->assertEquals($isVar, $component['isVar']);
        $this->assertEquals($choices, $component['choices']);
    }
}
