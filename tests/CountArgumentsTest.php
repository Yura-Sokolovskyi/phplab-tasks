<?php

use PHPUnit\Framework\TestCase;

class CountArgumentsTest extends TestCase
{
    public function testPositive()
    {
        $this->assertEquals(['argument_count' => 0, 'argument_values' => []], countArguments());

        $this->assertEquals(['argument_count' => 1, 'argument_values' => ['test']], countArguments('test'));

        $this->assertEquals( ['argument_count' => 2, 'argument_values' => ['test', 'test1']], countArguments('test','test1'));
    }
}