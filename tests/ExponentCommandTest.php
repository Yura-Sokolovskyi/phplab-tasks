<?php

use PHPUnit\Framework\TestCase;
use src\oop\Commands\ExponentCommand;

class ExponentCommandTest extends TestCase
{
    /**
     * @var ExponentCommand
     */
    private $command;

    /**
     * @see https://phpunit.readthedocs.io/en/9.3/fixtures.html#more-setup-than-teardown
     *
     * @inheritdoc
     */
    public function setUp(): void
    {
        $this->command = new ExponentCommand();
    }

    /**
     * @return array
     */
    public function commandPositiveDataProvider()
    {
        return [
            [3, 2, 9],
            [4, -1, 0.25],
            [5, 3, 125],
            ['8', 3, 512],
        ];
    }

    /**
     * @dataProvider commandPositiveDataProvider
     */
    public function testCommandPositive($a, $b, $expected)
    {
        $result = $this->command->execute($a, $b);

        $this->assertEquals($expected, $result);
    }

    public function testCommandNegative()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->command->execute(1);
    }

    /**
     * @see https://phpunit.readthedocs.io/en/9.3/fixtures.html#more-setup-than-teardown
     *
     * @inheritdoc
     */
    public function tearDown(): void
    {
        unset($this->command);
    }
}