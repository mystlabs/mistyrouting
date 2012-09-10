<?php

use MistyRouting\Route\VarargsHelper;

class VarargsHelperTest extends MistyTesting\UnitTest
{
    public function testSerialize()
    {
        $this->assertEquals('/var/value/0/value', VarargsHelper::serialize(array(
            'var' => 'value',
            0 => 'value',
        )));
    }

    public function testSerialize_notUrlEncodedParams()
    {
        $this->assertEquals('/title/This+is+a+title', VarargsHelper::serialize(array(
            'title' => 'This is a title',
        )));
    }

    /**
     * @expectedException MistyRouting\Exception\InvalidParamException
     */
    public function testSerialize_invalidParam()
    {
        VarargsHelper::serialize(array(
            '' => 'value'
        ));
    }

    /**
     * @expectedException MistyRouting\Exception\MalformedPathException
     */
    public function testDecoding_wrongNumberOfParams()
    {
        VarargsHelper::deserialize('/var1/val1/var2');
    }

    public function testDecoding()
    {
        $varargs = VarargsHelper::deserialize('/var1/val1/var2/val2');
        $this->assertEquals(array(
            'var1' => 'val1',
            'var2' => 'val2',
        ), $varargs);
    }
}
