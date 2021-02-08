<?php

namespace src\oop\Commands;

class DivideCommand implements CommandInterface
{
    /**
     * @inheritdoc
     */
    public function execute(...$args)
    {
        if (2 != sizeof($args) || $args[1] == 0) {
            throw new \InvalidArgumentException('Not enough parameters or second parameter is 0');
        }

        return $args[0] / $args[1];
    }
}