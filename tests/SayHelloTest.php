<?php

use PHPUnit\Framework\TestCase;

class SayHelloTest extends TestCase
{

    public function testPositive( )
    {
        $this->assertEquals('Hello', sayHello());
    }

}