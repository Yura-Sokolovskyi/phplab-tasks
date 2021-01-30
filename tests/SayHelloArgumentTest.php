<?php

use PHPUnit\Framework\TestCase;

class SayHelloArgumentTest extends TestCase
{
    /**
     * @dataProvider positiveDataProvider
     */
    public function testPositive($input)
    {
        $this->assertIsString(sayHelloArgument($input));
    }

    public function positiveDataProvider()
    {
        return [
           [1],
           ['World!'],
           [true]
        ];
    }
}