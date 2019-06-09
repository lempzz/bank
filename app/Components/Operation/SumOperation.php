<?php

namespace App\Components\Operation;

class SumOperation extends AbstractOperation
{
    public function calculate(float $balance, float $sum): float
    {
        return $balance + $sum;
    }
}
