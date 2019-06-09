<?php

namespace App\Components\Operation;

abstract  class AbstractOperation
{
    private $type;

    final public function __construct($type)
    {
        $this->type = $type;
    }

    abstract function calculate(float $balance, float $sum) : float;

    final public function type()
    {
        return $this->type;
    }
}
