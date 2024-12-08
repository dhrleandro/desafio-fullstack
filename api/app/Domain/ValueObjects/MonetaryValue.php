<?php

namespace App\Domain\ValueObjects;

class MonetaryValue
{
    private float $value;

    public function __construct(float $value)
    {
        $this->value = $value;
        $this->round();
    }

    public function value(): float
    {
        return $this->value;
    }

    public function round()
    {
        $this->value = round($this->value, 2, PHP_ROUND_HALF_UP);
    }

    public function sum(float $amount): MonetaryValue
    {
        $this->value += $amount;
        return $this;
    }

    public function sub(float $amount): MonetaryValue
    {
        $this->value -= $amount;
        return $this;
    }
}
